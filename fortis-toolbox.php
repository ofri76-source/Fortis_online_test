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

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
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

    public function enqueue_assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_' . self::SLUG) {
            return;
        }

        wp_enqueue_style(
            'fortis-toolbox-admin',
            plugin_dir_url(__FILE__) . 'fortis-toolbox.css',
            [],
            '0.1.0'
        );
        wp_enqueue_script(
            'fortis-toolbox-admin',
            plugin_dir_url(__FILE__) . 'fortis-toolbox.js',
            [],
            '0.1.0',
            true
        );
        wp_localize_script(
            'fortis-toolbox-admin',
            'FortisToolboxConfig',
            [
                'nonce' => wp_create_nonce(self::NONCE_ACTION),
            ]
        );
    }

    private function render_section_header(string $title, string $description = ''): void
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

        echo '<div class="wrap fortis-toolbox">';
        echo '<h1>' . esc_html__('Fortis Toolbox', 'fortis-toolbox') . '</h1>';
        printf('<input type="hidden" id="%s" value="%s" />', esc_attr(self::NONCE_NAME), esc_attr(wp_create_nonce(self::NONCE_ACTION)));

        echo '<nav class="ft-tabs">';
        $tabs = [
            'new-forti' => __('New Forti', 'fortis-toolbox'),
            'virtual-ip' => __('Virtual-IP', 'fortis-toolbox'),
            'address' => __('Address', 'fortis-toolbox'),
            'security' => __('Security', 'fortis-toolbox'),
            'alerts' => __('Alerts', 'fortis-toolbox'),
            'import' => __('Import', 'fortis-toolbox'),
            'settings' => __('Settings', 'fortis-toolbox'),
        ];
        foreach ($tabs as $id => $label) {
            printf('<button class="ft-tab" data-tab="%s">%s</button>', esc_attr($id), esc_html($label));
        }
        echo '</nav>';

        echo '<div class="ft-tab-content">';
        $this->render_new_forti_tab();
        $this->render_virtual_ip_tab();
        $this->render_address_tab();
        $this->render_security_tab();
        $this->render_alerts_tab();
        $this->render_import_tab();
        $this->render_settings_tab();
        echo '</div>';

        echo '</div>';
    }

    private function render_cli_panel(string $id_prefix, string $title, array $actions = []): void
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

    private function render_new_forti_tab(): void
    {
        echo '<section id="tab-new-forti" class="ft-tab-pane is-active">';
        $this->render_section_header(__('New Forti', 'fortis-toolbox'), __('Build a base FortiGate configuration.', 'fortis-toolbox'));

        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';

        echo '<div class="ft-card">';
        $this->render_section_header(__('General Settings', 'fortis-toolbox'));
        echo '<label>' . esc_html__('Hostname', 'fortis-toolbox') . '<input type="text" id="nf-hostname" /></label>';
        echo '<label>' . esc_html__('Model', 'fortis-toolbox') . '<input type="text" id="nf-model" placeholder="FortiGate 40F" /></label>';
        echo '<label>' . esc_html__('Timezone', 'fortis-toolbox') . '<input type="text" id="nf-timezone" placeholder="Europe/London" /></label>';
        echo '<button class="button" id="nf-initial-setup">' . esc_html__('Initial Setup', 'fortis-toolbox') . '</button>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('LAN', 'fortis-toolbox'));
        echo '<label>' . esc_html__('Interface', 'fortis-toolbox') . '<input type="text" id="nf-lan-if" value="lan" /></label>';
        echo '<label>' . esc_html__('IP', 'fortis-toolbox') . '<input type="text" id="nf-lan-ip" placeholder="192.168.1.1" /></label>';
        echo '<label>' . esc_html__('Subnet', 'fortis-toolbox') . '<input type="text" id="nf-lan-mask" placeholder="255.255.255.0" /></label>';
        echo '<label>' . esc_html__('DHCP from', 'fortis-toolbox') . '<input type="text" id="nf-dhcp-from" placeholder="192.168.1.10" /></label>';
        echo '<label>' . esc_html__('DHCP to', 'fortis-toolbox') . '<input type="text" id="nf-dhcp-to" placeholder="192.168.1.100" /></label>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('WAN Interfaces', 'fortis-toolbox'));
        foreach (['wan1', 'wan2'] as $wan) {
            printf('<fieldset class="ft-fieldset"><legend>%s</legend>', esc_html(strtoupper($wan)));
            printf('<label><input type="checkbox" id="%s-enabled" checked /> %s</label>', esc_attr($wan), esc_html__('Enabled', 'fortis-toolbox'));
            printf('<label>%s<select id="%s-mode"><option value="dhcp">DHCP</option><option value="static">Static</option><option value="pppoe">PPPoE</option></select></label>', esc_html__('Mode', 'fortis-toolbox'), esc_attr($wan));
            printf('<label>%s<input type="text" id="%s-if" value="%s" /></label>', esc_html__('Interface', 'fortis-toolbox'), esc_attr($wan), esc_attr($wan));
            printf('<label>%s<input type="text" id="%s-ip" placeholder="203.0.113.10" /></label>', esc_html__('IP', 'fortis-toolbox'), esc_attr($wan));
            printf('<label>%s<input type="text" id="%s-mask" placeholder="255.255.255.248" /></label>', esc_html__('Mask', 'fortis-toolbox'), esc_attr($wan));
            printf('<label>%s<input type="text" id="%s-gw" placeholder="203.0.113.1" /></label>', esc_html__('Gateway', 'fortis-toolbox'), esc_attr($wan));
            printf('<label>%s<input type="text" id="%s-pppoe-user" placeholder="user@isp" /></label>', esc_html__('PPPoE User', 'fortis-toolbox'), esc_attr($wan));
            printf('<label>%s<input type="password" id="%s-pppoe-pass" placeholder="********" /></label>', esc_html__('PPPoE Password', 'fortis-toolbox'), esc_attr($wan));
            echo '</fieldset>';
        }
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('VPN Settings (SSL VPN)', 'fortis-toolbox'));
        echo '<label>' . esc_html__('VPN Prefix', 'fortis-toolbox') . '<input type="text" id="nf-vpn-prefix" placeholder="10.212.134" /></label>';
        echo '<label>' . esc_html__('From', 'fortis-toolbox') . '<input type="number" id="nf-vpn-from" value="100" /></label>';
        echo '<label>' . esc_html__('To', 'fortis-toolbox') . '<input type="number" id="nf-vpn-to" value="120" /></label>';
        echo '<label>' . esc_html__('Port', 'fortis-toolbox') . '<input type="number" id="nf-vpn-port" value="10443" /></label>';
        echo '<div class="ft-list">';
        echo '<label>' . esc_html__('Interfaces', 'fortis-toolbox') . '<input type="text" id="nf-vpn-if" placeholder="wan1,wan2" /></label>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Network Segmentation', 'fortis-toolbox'));
        foreach (['camera' => 'Camera', 'phones' => 'Phones', 'wifi' => 'WIFI'] as $key => $label) {
            printf('<label><input type="checkbox" id="seg-%s" /> %s</label>', esc_attr($key), esc_html__($label, 'fortis-toolbox'));
        }
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('LDAP', 'fortis-toolbox'));
        echo '<label><input type="checkbox" id="nf-ldap-enabled" /> ' . esc_html__('Enabled', 'fortis-toolbox') . '</label>';
        echo '<label>' . esc_html__('Server', 'fortis-toolbox') . '<input type="text" id="nf-ldap-server" /></label>';
        echo '<label>' . esc_html__('Base DN', 'fortis-toolbox') . '<input type="text" id="nf-ldap-base" /></label>';
        echo '<label>' . esc_html__('Group', 'fortis-toolbox') . '<input type="text" id="nf-ldap-group" /></label>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('CLI Block Filtering', 'fortis-toolbox'));
        echo '<label><input type="checkbox" id="nf-block-filter" /> ' . esc_html__('Enable block filtering', 'fortis-toolbox') . '</label>';
        echo '<label><input type="checkbox" id="nf-exist" /> ' . esc_html__('Existing FortiGate (delta)', 'fortis-toolbox') . '</label>';
        $blocks = ['General Settings', 'Local interface', 'Wan Interfaces', 'sd-wan', 'Address', 'Address Group'];
        foreach ($blocks as $block) {
            printf('<label><input type="checkbox" class="nf-block" value="%s" /> %s</label>', esc_attr($block), esc_html__($block, 'fortis-toolbox'));
        }
        echo '<div class="ft-row">';
        echo '<button class="button" id="nf-block-all">' . esc_html__('All', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="nf-block-clear">' . esc_html__('Clear', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'build', 'label' => __('Build CLI', 'fortis-toolbox')],
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'save', 'label' => __('Save TXT', 'fortis-toolbox')],
            ['action' => 'clear', 'label' => __('Clear', 'fortis-toolbox')],
            ['action' => 'test', 'label' => __('Test', 'fortis-toolbox')],
            ['action' => 'push', 'label' => __('Push', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('nf', __('Build CLI', 'fortis-toolbox'), $actions);
        echo '<label>' . esc_html__('Port', 'fortis-toolbox') . '<input type="text" id="nf-port" placeholder="COM3" /></label>';
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_virtual_ip_tab(): void
    {
        echo '<section id="tab-virtual-ip" class="ft-tab-pane">';
        $this->render_section_header(__('Virtual-IP', 'fortis-toolbox'), __('Create and manage VIPs.', 'fortis-toolbox'));
        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';
        echo '<div class="ft-card">';
        echo '<div class="ft-row">';
        echo '<button class="button" id="vip-add-row">' . esc_html__('Add row', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="vip-export">' . esc_html__('Export (print)', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '<table class="ft-table" id="vip-table">';
        echo '<thead><tr><th>#</th><th>' . esc_html__('Name', 'fortis-toolbox') . '</th><th>' . esc_html__('Interface', 'fortis-toolbox') . '</th><th>' . esc_html__('External IP', 'fortis-toolbox') . '</th><th>' . esc_html__('External Port', 'fortis-toolbox') . '</th><th>' . esc_html__('Mapped IP', 'fortis-toolbox') . '</th><th>' . esc_html__('Mapped Port', 'fortis-toolbox') . '</th><th>' . esc_html__('Proto', 'fortis-toolbox') . '</th><th>' . esc_html__('OK?', 'fortis-toolbox') . '</th><th>' . esc_html__('Actions', 'fortis-toolbox') . '</th></tr></thead>';
        echo '<tbody></tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'gen-vip', 'label' => __('Generate VIP CLI', 'fortis-toolbox')],
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'save', 'label' => __('Save', 'fortis-toolbox')],
            ['action' => 'append', 'label' => __('Append', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('vip', __('VIP CLI', 'fortis-toolbox'), $actions);
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_address_tab(): void
    {
        echo '<section id="tab-address" class="ft-tab-pane">';
        $this->render_section_header(__('Address', 'fortis-toolbox'), __('Create address objects and groups.', 'fortis-toolbox'));
        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';
        echo '<div class="ft-row">';
        echo '<button class="button" id="addr-delete">' . esc_html__('Delete', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="addr-delete-all">' . esc_html__('Delete All', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="addr-import">' . esc_html__('Import', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="addr-export">' . esc_html__('Export', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="addr-create-vip-group">' . esc_html__('Create VIP Group', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '<table class="ft-table" id="addr-table">';
        echo '<thead><tr><th>' . esc_html__('Sel', 'fortis-toolbox') . '</th><th>' . esc_html__('Name', 'fortis-toolbox') . '</th><th>' . esc_html__('Type', 'fortis-toolbox') . '</th><th>' . esc_html__('Value', 'fortis-toolbox') . '</th><th>' . esc_html__('Allow routing', 'fortis-toolbox') . '</th><th>' . esc_html__('OK?', 'fortis-toolbox') . '</th><th>' . esc_html__('Actions', 'fortis-toolbox') . '</th></tr></thead>';
        echo '<tbody></tbody>';
        echo '</table>';
        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'gen-addr', 'label' => __('Generate Address CLI', 'fortis-toolbox')],
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'save', 'label' => __('Save As', 'fortis-toolbox')],
            ['action' => 'append', 'label' => __('Append', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('addr', __('Address CLI', 'fortis-toolbox'), $actions);
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_security_tab(): void
    {
        echo '<section id="tab-security" class="ft-tab-pane">';
        $this->render_section_header(__('Security', 'fortis-toolbox'), __('Build security profiles and policies.', 'fortis-toolbox'));
        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';
        echo '<div class="ft-card">';
        echo '<label>' . esc_html__('External Connections', 'fortis-toolbox') . '<select id="sec-external"><option value="private">' . esc_html__('Private', 'fortis-toolbox') . '</option><option value="public">' . esc_html__('Public', 'fortis-toolbox') . '</option></select></label>';
        echo '<label>' . esc_html__('Security Level', 'fortis-toolbox') . '<select id="sec-level"><option value="safe">' . esc_html__('Safe', 'fortis-toolbox') . '</option><option value="strict">' . esc_html__('Strict', 'fortis-toolbox') . '</option><option value="relaxed">' . esc_html__('Relaxed', 'fortis-toolbox') . '</option></select></label>';
        echo '<label>' . esc_html__('SSL Inspection', 'fortis-toolbox') . '<select id="sec-ssl"><option value="none">' . esc_html__('No inspection', 'fortis-toolbox') . '</option><option value="certificate">' . esc_html__('Certificate inspection', 'fortis-toolbox') . '</option><option value="deep">' . esc_html__('Deep inspection', 'fortis-toolbox') . '</option></select></label>';
        echo '<fieldset class="ft-fieldset"><legend>' . esc_html__('Security Filters', 'fortis-toolbox') . '</legend>';
        foreach (['dns' => 'DNS', 'antivirus' => 'Antivirus', 'ips' => 'Intrusion Prevention', 'web' => 'Web', 'app' => 'Application'] as $key => $label) {
            printf('<label><input type="checkbox" class="sec-filter" value="%s" checked /> %s</label>', esc_attr($key), esc_html__($label, 'fortis-toolbox'));
        }
        echo '</fieldset>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'gen-security', 'label' => __('Generate CLI', 'fortis-toolbox')],
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'clear', 'label' => __('Clear', 'fortis-toolbox')],
            ['action' => 'append', 'label' => __('Append', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('sec', __('Security CLI', 'fortis-toolbox'), $actions);
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_alerts_tab(): void
    {
        echo '<section id="tab-alerts" class="ft-tab-pane">';
        $this->render_section_header(__('Alerts', 'fortis-toolbox'), __('Create alerting automation rules.', 'fortis-toolbox'));
        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';
        echo '<div class="ft-card">';
        echo '<label><input type="checkbox" id="alert-wan" /> ' . esc_html__('Alert on WAN status change', 'fortis-toolbox') . '</label>';
        echo '<p class="ft-help">' . esc_html__('Alert on wan down and move to secondary wan.', 'fortis-toolbox') . '</p>';
        echo '<label><input type="checkbox" id="alert-admin" /> ' . esc_html__('Alert on admin login', 'fortis-toolbox') . '</label>';
        echo '<p class="ft-help">' . esc_html__('Send mail after admin login bad or good.', 'fortis-toolbox') . '</p>';
        echo '<label><input type="checkbox" id="alert-s2s" /> ' . esc_html__('Alert on Site to Site status change', 'fortis-toolbox') . '</label>';
        echo '<p class="ft-help">' . esc_html__('If the site to site change status up or down you send mail.', 'fortis-toolbox') . '</p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'gen-alerts', 'label' => __('Generate Alerts CLI', 'fortis-toolbox')],
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'save', 'label' => __('Save As', 'fortis-toolbox')],
            ['action' => 'append', 'label' => __('Append', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('alerts', __('Alerts CLI', 'fortis-toolbox'), $actions);
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_import_tab(): void
    {
        echo '<section id="tab-import" class="ft-tab-pane">';
        $this->render_section_header(__('Import', 'fortis-toolbox'), __('Parse FortiGate backups and map interfaces.', 'fortis-toolbox'));
        echo '<div class="ft-grid">';
        echo '<div class="ft-column">';
        echo '<div class="ft-card">';
        echo '<label>' . esc_html__('Backup File', 'fortis-toolbox') . '<input type="file" id="import-file" accept=".txt,.conf" /></label>';
        echo '<fieldset class="ft-fieldset"><legend>' . esc_html__('Filter Types', 'fortis-toolbox') . '</legend>';
        $filters = ['Address', 'Virtual IPs', 'Services', 'Schedulers', 'User Groups', 'SD-WAN', 'Addr Groups', 'VIP Groups', 'IP Pools', 'Users', 'Firewall Policy', 'IPSec VPN'];
        foreach ($filters as $filter) {
            printf('<label><input type="checkbox" class="import-filter" value="%s" checked /> %s</label>', esc_attr($filter), esc_html__($filter, 'fortis-toolbox'));
        }
        echo '<div class="ft-row">';
        echo '<button class="button" id="import-all">' . esc_html__('All', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="import-none">' . esc_html__('None', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '</fieldset>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Interface Mapping', 'fortis-toolbox'));
        echo '<table class="ft-table" id="import-map">';
        echo '<thead><tr><th>' . esc_html__('Old Interface', 'fortis-toolbox') . '</th><th>' . esc_html__('New Interface', 'fortis-toolbox') . '</th><th></th></tr></thead>';
        echo '<tbody></tbody>';
        echo '</table>';
        echo '<button class="button" id="import-add-map">' . esc_html__('Add mapping', 'fortis-toolbox') . '</button>';
        echo '</div>';

        echo '<div class="ft-row">';
        echo '<button class="button" id="import-cli-btn">' . esc_html__('Import CLI', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="import-clear">' . esc_html__('Clear', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-column">';
        $actions = [
            ['action' => 'copy', 'label' => __('Copy', 'fortis-toolbox')],
            ['action' => 'save', 'label' => __('Save As', 'fortis-toolbox')],
            ['action' => 'append', 'label' => __('Append to NEW', 'fortis-toolbox')],
            ['action' => 'test', 'label' => __('Test', 'fortis-toolbox')],
            ['action' => 'push', 'label' => __('Push', 'fortis-toolbox')],
        ];
        $this->render_cli_panel('import', __('Import CLI', 'fortis-toolbox'), $actions);
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }

    private function render_settings_tab(): void
    {
        echo '<section id="tab-settings" class="ft-tab-pane">';
        $this->render_section_header(__('Settings', 'fortis-toolbox'), __('Global settings and defaults.', 'fortis-toolbox'));
        echo '<div class="ft-grid three">';

        echo '<div class="ft-card">';
        $this->render_section_header(__('General settings', 'fortis-toolbox'));
        echo '<label>' . esc_html__('Hostname', 'fortis-toolbox') . '<input type="text" id="set-hostname" /></label>';
        echo '<label>' . esc_html__('Admin Port', 'fortis-toolbox') . '<input type="number" id="set-admin-port" value="443" /></label>';
        echo '<label>' . esc_html__('Model (name)', 'fortis-toolbox') . '<input type="text" id="set-model" /></label>';
        echo '<label>' . esc_html__('Timezone (IANA)', 'fortis-toolbox') . '<input type="text" id="set-timezone" /></label>';
        echo '<div class="ft-list" id="trusted-hosts">';
        echo '<label>' . esc_html__('Admin trusted hosts (CIDR)', 'fortis-toolbox') . '<input type="text" id="set-trusted" placeholder="192.168.1.0/24" /></label>';
        echo '<button class="button" id="trusted-add">' . esc_html__('Add', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="trusted-remove">' . esc_html__('Remove', 'fortis-toolbox') . '</button>';
        echo '<ul id="trusted-list"></ul>';
        echo '</div>';
        echo '<label><input type="checkbox" id="set-include-lan" /> ' . esc_html__('Include LAN subnet automatically', 'fortis-toolbox') . '</label>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Console (serial)', 'fortis-toolbox'));
        echo '<label>' . esc_html__('COM', 'fortis-toolbox') . '<input type="text" id="set-com" placeholder="COM4" /></label>';
        echo '<label>' . esc_html__('User', 'fortis-toolbox') . '<input type="text" id="set-serial-user" /></label>';
        echo '<label>' . esc_html__('Pass', 'fortis-toolbox') . '<input type="password" id="set-serial-pass" /></label>';
        echo '<label>' . esc_html__('Baud rate', 'fortis-toolbox') . '<input type="number" id="set-serial-baud" value="115200" /></label>';
        echo '<div class="ft-row">';
        echo '<button class="button" id="serial-test">' . esc_html__('Test connection', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="serial-push">' . esc_html__('Push to console', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="serial-refresh">' . esc_html__('Refresh', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Backup (SFTP)', 'fortis-toolbox'));
        echo '<label>' . esc_html__('SFTP host/IP', 'fortis-toolbox') . '<input type="text" id="set-sftp-host" /></label>';
        echo '<label>' . esc_html__('Port (Internal)', 'fortis-toolbox') . '<input type="number" id="set-sftp-port" value="22" /></label>';
        echo '<label>' . esc_html__('User', 'fortis-toolbox') . '<input type="text" id="set-sftp-user" /></label>';
        echo '<label>' . esc_html__('Pass', 'fortis-toolbox') . '<input type="password" id="set-sftp-pass" /></label>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Skins', 'fortis-toolbox'));
        $skins = ['default' => 'Default', 'dark' => 'Dark', 'blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple'];
        echo '<div class="ft-row">';
        foreach ($skins as $slug => $label) {
            printf('<button class="button ft-skin" data-skin="%s">%s</button>', esc_attr($slug), esc_html__($label, 'fortis-toolbox'));
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="ft-card">';
        $this->render_section_header(__('Logs & config', 'fortis-toolbox'));
        echo '<div class="ft-row">';
        echo '<button class="button" id="open-log">' . esc_html__('Open Log', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="open-folder">' . esc_html__('Logs Folder', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '<div class="ft-row">';
        echo '<button class="button" id="settings-save">' . esc_html__('Save', 'fortis-toolbox') . '</button>';
        echo '<button class="button" id="settings-export">' . esc_html__('Export config', 'fortis-toolbox') . '</button>';
        echo '</div>';
        echo '<p class="ft-version">v3.0.0 · Python 3.13.x · Fortis Toolbox</p>';
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }
}

new Fortis_Toolbox_Plugin();

?>
