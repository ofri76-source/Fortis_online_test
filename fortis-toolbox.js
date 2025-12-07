(function () {
    const tabs = document.querySelectorAll('.ft-tab');
    const panes = document.querySelectorAll('.ft-tab-pane');
    const mainBuffer = { nf: '' };

    function setActiveTab(id) {
        tabs.forEach((tab) => tab.classList.toggle('is-active', tab.dataset.tab === id));
        panes.forEach((pane) => pane.classList.toggle('is-active', pane.id === `tab-${id}`));
    }

    function copyToClipboard(text) {
        if (!text) {
            return;
        }
        navigator.clipboard?.writeText(text);
    }

    function saveText(text, filename) {
        if (!text) return;
        const blob = new Blob([text], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || 'fortis-cli.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    function appendToMain(targetId) {
        const textarea = document.getElementById(`${targetId}-cli`);
        if (textarea) {
            mainBuffer.nf = `${mainBuffer.nf}\n${textarea.value}`.trim();
            document.getElementById('nf-cli').value = mainBuffer.nf;
        }
    }

    function buildNewFortiCLI() {
        const hostname = document.getElementById('nf-hostname').value || 'fortigate';
        const model = document.getElementById('nf-model').value || 'FortiGate';
        const lanIf = document.getElementById('nf-lan-if').value || 'lan';
        const lanIp = document.getElementById('nf-lan-ip').value;
        const lanMask = document.getElementById('nf-lan-mask').value;
        const dhcpFrom = document.getElementById('nf-dhcp-from').value;
        const dhcpTo = document.getElementById('nf-dhcp-to').value;
        const vpnPrefix = document.getElementById('nf-vpn-prefix').value;
        const vpnFrom = document.getElementById('nf-vpn-from').value;
        const vpnTo = document.getElementById('nf-vpn-to').value;
        const vpnPort = document.getElementById('nf-vpn-port').value;
        const vpnInterfaces = document.getElementById('nf-vpn-if').value;
        const ldapEnabled = document.getElementById('nf-ldap-enabled').checked;
        const segmentation = ['camera', 'phones', 'wifi']
            .filter((id) => document.getElementById(`seg-${id}`).checked)
            .map((id) => `VLAN ${id}`);

        let cli = `config system global\n    set hostname ${hostname}\nend\n`;
        cli += `\n# Model: ${model}`;
        if (lanIp && lanMask) {
            cli += `\nconfig system interface\n    edit ${lanIf}\n        set ip ${lanIp} ${lanMask}\n    next\nend`;
        }
        if (dhcpFrom && dhcpTo) {
            cli += `\nconfig system dhcp server\n    edit 1\n        set interface ${lanIf}\n        config ip-range\n            edit 1\n                set start-ip ${dhcpFrom}\n                set end-ip ${dhcpTo}\n            next\n        end\n    next\nend`;
        }
        ['wan1', 'wan2'].forEach((wan) => {
            const enabled = document.getElementById(`${wan}-enabled`).checked;
            const mode = document.getElementById(`${wan}-mode`).value;
            const wanIf = document.getElementById(`${wan}-if`).value;
            const ip = document.getElementById(`${wan}-ip`).value;
            const mask = document.getElementById(`${wan}-mask`).value;
            const gw = document.getElementById(`${wan}-gw`).value;
            const pppoeUser = document.getElementById(`${wan}-pppoe-user`).value;
            if (!enabled) return;
            cli += `\nconfig system interface\n    edit ${wanIf}\n        set mode ${mode}`;
            if (mode === 'static' && ip && mask) {
                cli += `\n        set ip ${ip} ${mask}`;
                if (gw) cli += `\n        set allowaccess ping`;
            }
            if (mode === 'pppoe' && pppoeUser) {
                cli += `\n        set mode pppoe\n        set username ${pppoeUser}`;
            }
            cli += `\n    next\nend`;
        });

        if (vpnPrefix) {
            cli += `\nconfig vpn ssl settings\n    set servercert default\n    set port ${vpnPort || 10443}\n    config authentication-rule\n        edit 1\n            set source-address all\n        next\n    end\nend`;
            cli += `\nconfig firewall address\n    edit SSL_POOL\n        set subnet ${vpnPrefix}.0 255.255.255.0\n    next\nend`;
            if (vpnInterfaces) {
                cli += `\n# SSL VPN interfaces: ${vpnInterfaces}`;
            }
        }

        if (segmentation.length) {
            cli += `\n# Segmentation\n` + segmentation.map((seg) => `config system interface\n    edit ${seg.toLowerCase().replace(' ', '_')}\n        set vlanid 10\n    next\nend`).join('\n');
        }

        if (ldapEnabled) {
            const server = document.getElementById('nf-ldap-server').value;
            const base = document.getElementById('nf-ldap-base').value;
            cli += `\nconfig user ldap\n    edit corp_ldap\n        set server ${server}\n        set dn ${base}\n    next\nend`;
        }

        document.getElementById('nf-cli').value = cli.trim();
        mainBuffer.nf = document.getElementById('nf-cli').value;
    }

    function addTableRow(tableId, columns) {
        const table = document.getElementById(tableId).querySelector('tbody');
        const index = table.children.length + 1;
        const tr = document.createElement('tr');
        columns.forEach((col) => {
            const td = document.createElement('td');
            td.innerHTML = typeof col === 'function' ? col(index) : col;
            tr.appendChild(td);
        });
        table.appendChild(tr);
    }

    function generateVipCLI() {
        const rows = Array.from(document.querySelectorAll('#vip-table tbody tr'));
        let cli = 'config firewall vip\n';
        rows.forEach((row, idx) => {
            const name = row.querySelector('.vip-name')?.value || `vip_${idx + 1}`;
            const iface = row.querySelector('.vip-if')?.value || 'wan1';
            const extIp = row.querySelector('.vip-ext-ip')?.value || '';
            const extPort = row.querySelector('.vip-ext-port')?.value || '';
            const mapIp = row.querySelector('.vip-map-ip')?.value || '';
            const mapPort = row.querySelector('.vip-map-port')?.value || '';
            cli += `    edit ${name}\n        set extintf ${iface}`;
            if (extIp) cli += `\n        set extip ${extIp}`;
            if (extPort) cli += `\n        set extport ${extPort}`;
            if (mapIp) cli += `\n        set mappedip ${mapIp}`;
            if (mapPort) cli += `\n        set mappedport ${mapPort}`;
            cli += `\n    next\n`;
        });
        cli += 'end';
        document.getElementById('vip-cli').value = cli.trim();
    }

    function generateAddrCLI() {
        const rows = Array.from(document.querySelectorAll('#addr-table tbody tr'));
        let cli = 'config firewall address\n';
        rows.forEach((row, idx) => {
            const name = row.querySelector('.addr-name')?.value || `addr_${idx + 1}`;
            const type = row.querySelector('.addr-type')?.value || 'subnet';
            const value = row.querySelector('.addr-value')?.value || '';
            const allowRouting = row.querySelector('.addr-routing')?.checked;
            cli += `    edit ${name}\n        set type ${type}`;
            if (value) cli += `\n        set subnet ${value}`;
            if (allowRouting) cli += `\n        set allow-routing enable`;
            cli += `\n    next\n`;
        });
        cli += 'end';
        document.getElementById('addr-cli').value = cli.trim();
    }

    function generateSecurityCLI() {
        const external = document.getElementById('sec-external').value;
        const level = document.getElementById('sec-level').value;
        const ssl = document.getElementById('sec-ssl').value;
        const filters = Array.from(document.querySelectorAll('.sec-filter:checked')).map((el) => el.value);
        let cli = `# Security baseline\nset external ${external}\nset level ${level}\nset ssl ${ssl}\n`;
        cli += '# Filters\n' + filters.map((f) => `enable ${f}`).join('\n');
        document.getElementById('sec-cli').value = cli.trim();
    }

    function generateAlertsCLI() {
        let cli = '';
        if (document.getElementById('alert-wan').checked) {
            cli += 'config system automation-action\n    edit wan_alert\nend\n';
        }
        if (document.getElementById('alert-admin').checked) {
            cli += 'config log eventfilter\n    set admin-login enable\nend\n';
        }
        if (document.getElementById('alert-s2s').checked) {
            cli += 'config system automation-action\n    edit s2s_alert\nend\n';
        }
        document.getElementById('alerts-cli').value = cli.trim();
    }

    function bindActions() {
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => setActiveTab(tab.dataset.tab));
        });

        document.querySelectorAll('.ft-action').forEach((btn) => {
            btn.addEventListener('click', () => {
                const targetId = btn.dataset.target;
                const textarea = document.getElementById(`${targetId}-cli`);
                switch (btn.dataset.action) {
                    case 'build':
                        buildNewFortiCLI();
                        break;
                    case 'gen-vip':
                        generateVipCLI();
                        break;
                    case 'gen-addr':
                        generateAddrCLI();
                        break;
                    case 'gen-security':
                        generateSecurityCLI();
                        break;
                    case 'gen-alerts':
                        generateAlertsCLI();
                        break;
                    case 'copy':
                        copyToClipboard(textarea.value);
                        break;
                    case 'save':
                        saveText(textarea.value, `${targetId}-cli.txt`);
                        break;
                    case 'clear':
                        textarea.value = '';
                        if (targetId === 'nf') mainBuffer.nf = '';
                        break;
                    case 'append':
                        appendToMain(targetId);
                        break;
                    case 'test':
                    case 'push':
                        alert(`${btn.dataset.action} action requested for ${targetId}`);
                        break;
                }
            });
        });

        document.getElementById('nf-block-all')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.nf-block').forEach((el) => (el.checked = true));
        });
        document.getElementById('nf-block-clear')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.nf-block').forEach((el) => (el.checked = false));
        });

        document.getElementById('vip-add-row')?.addEventListener('click', (e) => {
            e.preventDefault();
            addTableRow('vip-table', [
                (idx) => idx,
                '<input class="vip-name" type="text" />',
                '<input class="vip-if" type="text" value="wan1" />',
                '<input class="vip-ext-ip" type="text" />',
                '<input class="vip-ext-port" type="text" />',
                '<input class="vip-map-ip" type="text" />',
                '<input class="vip-map-port" type="text" />',
                '<select class="vip-proto"><option>tcp</option><option>udp</option></select>',
                '<input type="checkbox" class="vip-ok" />',
                '<button class="button link-button vip-delete">×</button>',
            ]);
        });

        document.getElementById('vip-table')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('vip-delete')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            }
        });

        document.getElementById('addr-delete-all')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('#addr-table tbody').innerHTML = '';
        });

        document.getElementById('addr-delete')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('#addr-table tbody tr').forEach((row) => {
                if (row.querySelector('.addr-sel')?.checked) {
                    row.remove();
                }
            });
        });

        document.getElementById('addr-import')?.addEventListener('click', (e) => {
            e.preventDefault();
            addTableRow('addr-table', [
                '<input type="checkbox" class="addr-sel" />',
                '<input class="addr-name" type="text" />',
                '<select class="addr-type"><option value="subnet">Subnet</option><option value="range">Range</option><option value="fqdn">FQDN</option></select>',
                '<input class="addr-value" type="text" />',
                '<input type="checkbox" class="addr-routing" />',
                '<input type="checkbox" class="addr-ok" />',
                '<button class="button link-button addr-delete-row">×</button>',
            ]);
        });

        document.getElementById('addr-table')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('addr-delete-row')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            }
        });

        document.getElementById('import-add-map')?.addEventListener('click', (e) => {
            e.preventDefault();
            addTableRow('import-map', [
                '<input type="text" class="map-old" />',
                '<input type="text" class="map-new" />',
                '<button class="button link-button map-delete">×</button>',
            ]);
        });

        document.getElementById('import-map')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('map-delete')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            }
        });

        document.getElementById('import-all')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.import-filter').forEach((el) => (el.checked = true));
        });
        document.getElementById('import-none')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.import-filter').forEach((el) => (el.checked = false));
        });

        document.getElementById('import-cli-btn')?.addEventListener('click', (e) => {
            e.preventDefault();
            const filters = Array.from(document.querySelectorAll('.import-filter:checked')).map((el) => el.value).join(', ');
            const mappings = Array.from(document.querySelectorAll('#import-map tbody tr'))
                .map((row) => `${row.querySelector('.map-old')?.value}:${row.querySelector('.map-new')?.value}`)
                .filter(Boolean)
                .join(', ');
            const file = document.getElementById('import-file').files?.[0]?.name || 'backup.conf';
            const cli = `# Parsed from ${file}\n# Filters: ${filters || 'none'}\n# Interface map: ${mappings || 'none'}`;
            document.getElementById('import-cli').value = cli;
        });

        document.getElementById('import-clear')?.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('import-cli') && (document.getElementById('import-cli').value = '');
        });

        document.querySelectorAll('.ft-skin').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.documentElement.setAttribute('data-ft-skin', btn.dataset.skin);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (!tabs.length) return;
        setActiveTab('new-forti');
        bindActions();
    });
})();
