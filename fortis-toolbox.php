<?php
/**
 * Plugin Name: Fortis Toolbox
 * Description: WordPress admin plugin that mirrors the Fortis Toolbox workflows for building FortiGate CLI snippets.
 * Version: 0.1.0
 * Author: Fortis Toolbox
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fortis_Toolbox_Plugin
{
    private const SLUG = 'fortis-toolbox';
    private const NONCE_ACTION = 'fortis_toolbox_nonce_action';
    private const NONCE_NAME = 'fortis_toolbox_nonce';
    private array $tabs;

    public function __construct()
    {
        $this->tabs = $this->load_tabs();
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_shortcode('fortis_toolbox', [$this, 'render_shortcode']);
    }

    private function load_tabs(): array
    {
        return [
            'new-forti' => [
                'label' => __('New Forti', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/new-forti.php'),
            ],
            'virtual-ip' => [
                'label' => __('Virtual-IP', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/virtual-ip.php'),
            ],
            'address' => [
                'label' => __('Address', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/address.php'),
            ],
            'security' => [
                'label' => __('Security', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/security.php'),
            ],
            'alerts' => [
                'label' => __('Alerts', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/alerts.php'),
            ],
            'import' => [
                'label' => __('Import', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/import.php'),
            ],
            'settings' => [
                'label' => __('Settings', 'fortis-toolbox'),
                'renderer' => $this->load_tab_renderer('tabs/settings.php'),
            ],
        ];
    }

    private function load_tab_renderer(string $relative_path): callable
    {
        $path = plugin_dir_path(__FILE__) . $relative_path;
        $renderer = file_exists($path) ? include $path : null;

        if (!is_callable($renderer)) {
            return function (): void {
                echo '<section class="ft-tab-pane"><p class="ft-error">' . esc_html__('Tab unavailable.', 'fortis-toolbox') . '</p></section>';
            };
        }

        return $renderer;
    }

    public function register_menu(): void
    {
        add_menu_page(
            __('Fortis Toolbox', 'fortis-toolbox'),
            __('Fortis Toolbox', 'fortis-toolbox'),
            'manage_options',
            self::SLUG,
            [$this, 'render_page'],
            'dashicons-shield-alt',
            65
        );
    }

    public function enqueue_admin_assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_' . self::SLUG) {
            return;
        }

        $this->enqueue_shared_assets();
    }

    private function enqueue_shared_assets(): void
    {
        $style_handle = 'fortis-toolbox-admin';
        $script_handle = 'fortis-toolbox-admin';

        wp_enqueue_style(
            $style_handle,
            plugin_dir_url(__FILE__) . 'fortis-toolbox.css',
            [],
            '0.1.0'
        );
        wp_enqueue_script(
            $script_handle,
            plugin_dir_url(__FILE__) . 'fortis-toolbox.js',
            [],
            '0.1.0',
            true
        );
        wp_localize_script(
            $script_handle,
            'FortisToolboxConfig',
            [
                'nonce' => wp_create_nonce(self::NONCE_ACTION),
            ]
        );
    }

    public function render_section_header(string $title, string $description = ''): void
    {
        printf('<h2 class="section-title">%s</h2>', esc_html($title));
        if ($description) {
            printf('<p class="section-description">%s</p>', esc_html($description));
        }
    }

    public function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'fortis-toolbox'));
        }

        $this->render_interface();
    }

    public function render_shortcode(): string
    {
        $this->enqueue_shared_assets();

        ob_start();
        $this->render_interface(false);
        return ob_get_clean();
    }

    private function render_interface(bool $wrap = true): void
    {
        $container_class = $wrap ? 'wrap fortis-toolbox' : 'fortis-toolbox';
        printf('<div class="%s">', esc_attr($container_class));
        if ($wrap) {
            echo '<h1>' . esc_html__('Fortis Toolbox', 'fortis-toolbox') . '</h1>';
        }

        printf('<input type="hidden" id="%s" value="%s" />', esc_attr(self::NONCE_NAME), esc_attr(wp_create_nonce(self::NONCE_ACTION)));

        echo '<nav class="ft-tabs">';
        $first = array_key_first($this->tabs);
        foreach ($this->tabs as $id => $tab) {
            $active_class = $id === $first ? ' is-active' : '';
            printf('<button class="ft-tab%s" data-tab="%s">%s</button>', esc_attr($active_class), esc_attr($id), esc_html($tab['label']));
        }
        echo '</nav>';

        echo '<div class="ft-tab-content">';
        foreach ($this->tabs as $id => $tab) {
            $renderer = $tab['renderer'];
            $renderer($this, $id === $first);
        }
        echo '</div>';

        echo '</div>';
    }

    public function render_cli_panel(string $id_prefix, string $title, array $actions = []): void
    {
        echo '<div class="ft-cli-panel">';
        printf('<h3>%s</h3>', esc_html($title));
        printf('<textarea id="%s-cli" class="ft-cli" rows="18" readonly></textarea>', esc_attr($id_prefix));
        echo '<div class="ft-cli-actions">';
        foreach ($actions as $action) {
            printf('<button class="button ft-action" data-action="%s" data-target="%s">%s</button>', esc_attr($action['action']), esc_attr($id_prefix), esc_html($action['label']));
        }
        echo '</div>';
        echo '</div>';
    }
}

new Fortis_Toolbox_Plugin();

?>
