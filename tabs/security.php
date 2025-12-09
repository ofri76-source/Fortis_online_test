<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-security" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Security', 'fortis-toolbox'), __('Build security profiles and policies.', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('sec', __('Security CLI', 'fortis-toolbox'), $actions);
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
