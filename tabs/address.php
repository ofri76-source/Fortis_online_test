<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-address" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Address', 'fortis-toolbox'), __('Create address objects and groups.', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('addr', __('Address CLI', 'fortis-toolbox'), $actions);
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
