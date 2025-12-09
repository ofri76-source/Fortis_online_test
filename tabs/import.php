<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-import" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Import', 'fortis-toolbox'), __('Parse FortiGate backups and map interfaces.', 'fortis-toolbox'));
    echo '<div class="ft-grid">';
    echo '<div class="ft-column">';
    echo '<div class="ft-card">';
    echo '<label>' . esc_html__('Backup File', 'fortis-toolbox') . '<input type="file" id="import-file" accept=".txt,.conf"/></label>';
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
    $plugin->render_section_header(__('Interface Mapping', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('import', __('Import CLI', 'fortis-toolbox'), $actions);
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
