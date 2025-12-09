<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-virtual-ip" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Virtual-IP', 'fortis-toolbox'), __('Create and manage VIPs.', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('vip', __('VIP CLI', 'fortis-toolbox'), $actions);
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
