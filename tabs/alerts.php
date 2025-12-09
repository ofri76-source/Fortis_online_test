<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-alerts" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Alerts', 'fortis-toolbox'), __('Create alerting automation rules.', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('alerts', __('Alerts CLI', 'fortis-toolbox'), $actions);
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
