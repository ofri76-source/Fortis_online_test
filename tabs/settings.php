<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-settings" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('Settings', 'fortis-toolbox'), __('Global settings and defaults.', 'fortis-toolbox'));
    echo '<div class="ft-grid three">';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('General settings', 'fortis-toolbox'));
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
    $plugin->render_section_header(__('Model table', 'fortis-toolbox'), __('Capture LAN/WAN defaults per model.', 'fortis-toolbox'));
    echo '<table class="ft-table" id="model-table">';
    echo '<thead><tr><th>' . esc_html__('Model', 'fortis-toolbox') . '</th><th>' . esc_html__('LAN', 'fortis-toolbox') . '</th><th>' . esc_html__('WAN1', 'fortis-toolbox') . '</th><th>' . esc_html__('WAN2', 'fortis-toolbox') . '</th><th></th></tr></thead>';
    echo '<tbody></tbody>';
    echo '</table>';
    echo '<button class="button" id="model-add-row">' . esc_html__('Add model row', 'fortis-toolbox') . '</button>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('Console (serial)', 'fortis-toolbox'));
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
    $plugin->render_section_header(__('Backup (SFTP)', 'fortis-toolbox'));
    echo '<label>' . esc_html__('SFTP host/IP', 'fortis-toolbox') . '<input type="text" id="set-sftp-host" /></label>';
    echo '<label>' . esc_html__('Port (Internal)', 'fortis-toolbox') . '<input type="number" id="set-sftp-port" value="22" /></label>';
    echo '<label>' . esc_html__('User', 'fortis-toolbox') . '<input type="text" id="set-sftp-user" /></label>';
    echo '<label>' . esc_html__('Pass', 'fortis-toolbox') . '<input type="password" id="set-sftp-pass" /></label>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('Skins', 'fortis-toolbox'));
    $skins = ['default' => 'Default', 'dark' => 'Dark', 'blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple'];
    echo '<div class="ft-row">';
    foreach ($skins as $slug => $label) {
        printf('<button class="button ft-skin" data-skin="%s">%s</button>', esc_attr($slug), esc_html__($label, 'fortis-toolbox'));
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('Logs & config', 'fortis-toolbox'));
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
};
