<?php
return function (Fortis_Toolbox_Plugin $plugin, bool $is_active): void {
    $active = $is_active ? ' is-active' : '';
    echo '<section id="tab-new-forti" class="ft-tab-pane' . $active . '">';
    $plugin->render_section_header(__('New Forti', 'fortis-toolbox'), __('Build a base FortiGate configuration.', 'fortis-toolbox'));

    echo '<div class="ft-grid">';
    echo '<div class="ft-column">';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('General Settings', 'fortis-toolbox'));
    echo '<label>' . esc_html__('Hostname', 'fortis-toolbox') . '<input type="text" id="nf-hostname" /></label>';
    echo '<label>' . esc_html__('Model', 'fortis-toolbox') . '<input type="text" id="nf-model" placeholder="FortiGate 40F" /></label>';
    echo '<label>' . esc_html__('Timezone', 'fortis-toolbox') . '<input type="text" id="nf-timezone" placeholder="Europe/London" /></label>';
    echo '<button class="button" id="nf-initial-setup">' . esc_html__('Initial Setup', 'fortis-toolbox') . '</button>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('LAN', 'fortis-toolbox'));
    echo '<label>' . esc_html__('Interface', 'fortis-toolbox') . '<input type="text" id="nf-lan-if" value="lan" /></label>';
    echo '<label>' . esc_html__('IP', 'fortis-toolbox') . '<input type="text" id="nf-lan-ip" placeholder="192.168.1.1" /></label>';
    echo '<label>' . esc_html__('Subnet', 'fortis-toolbox') . '<input type="text" id="nf-lan-mask" placeholder="255.255.255.0" /></label>';
    echo '<label>' . esc_html__('DHCP from', 'fortis-toolbox') . '<input type="text" id="nf-dhcp-from" placeholder="192.168.1.10" /></label>';
    echo '<label>' . esc_html__('DHCP to', 'fortis-toolbox') . '<input type="text" id="nf-dhcp-to" placeholder="192.168.1.100" /></label>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('WAN Interfaces', 'fortis-toolbox'));
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
    $plugin->render_section_header(__('VPN Settings (SSL VPN)', 'fortis-toolbox'));
    echo '<label>' . esc_html__('VPN Prefix', 'fortis-toolbox') . '<input type="text" id="nf-vpn-prefix" placeholder="10.212.134" /></label>';
    echo '<label>' . esc_html__('From', 'fortis-toolbox') . '<input type="number" id="nf-vpn-from" value="100" /></label>';
    echo '<label>' . esc_html__('To', 'fortis-toolbox') . '<input type="number" id="nf-vpn-to" value="120" /></label>';
    echo '<label>' . esc_html__('Port', 'fortis-toolbox') . '<input type="number" id="nf-vpn-port" value="10443" /></label>';
    echo '<div class="ft-list">';
    echo '<label>' . esc_html__('Interfaces', 'fortis-toolbox') . '<input type="text" id="nf-vpn-if" placeholder="wan1,wan2" /></label>';
    echo '</div>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('Network Segmentation', 'fortis-toolbox'));
    foreach (['camera' => 'Camera', 'phones' => 'Phones', 'wifi' => 'WIFI'] as $key => $label) {
        printf('<label><input type="checkbox" id="seg-%s" /> %s</label>', esc_attr($key), esc_html__($label, 'fortis-toolbox'));
    }
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('LDAP', 'fortis-toolbox'));
    echo '<label><input type="checkbox" id="nf-ldap-enabled" /> ' . esc_html__('Enabled', 'fortis-toolbox') . '</label>';
    echo '<label>' . esc_html__('Server', 'fortis-toolbox') . '<input type="text" id="nf-ldap-server" /></label>';
    echo '<label>' . esc_html__('Base DN', 'fortis-toolbox') . '<input type="text" id="nf-ldap-base" /></label>';
    echo '<label>' . esc_html__('Group', 'fortis-toolbox') . '<input type="text" id="nf-ldap-group" /></label>';
    echo '</div>';

    echo '<div class="ft-card">';
    $plugin->render_section_header(__('CLI Block Filtering', 'fortis-toolbox'));
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
    $plugin->render_cli_panel('nf', __('Build CLI', 'fortis-toolbox'), $actions);
    echo '<label>' . esc_html__('Port', 'fortis-toolbox') . '<input type="text" id="nf-port" placeholder="COM3" /></label>';
    echo '</div>';

    echo '</div>';
    echo '</section>';
};
