# Fortis Toolbox WordPress Plugin Rewrite Blueprint

This document captures the product and UX requirements from the original Windows/Tkinter "Fortis Toolbox" application and frames them as a WordPress plugin. It is intended as a blueprint for implementation planning and estimates.

## Functional Scope

* Seven main tabs mirroring the desktop UI: **New Forti**, **Virtual-IP**, **Address**, **Security**, **Alerts**, **Import**, and **Settings**.
* Each tab follows the same workflow: users enter data into forms/tables on the left, the plugin renders the generated FortiOS CLI on the right, and common actions (Generate/Build, Copy, Save, Append, Test, Push) sit beneath the CLI panel.【F:WORDPRESS_PLUGIN_PLAN.md†L6-L12】
* A global "Append bus" lets any tab send its CLI snippet into the main CLI document on the **New Forti** tab for consolidation.【F:WORDPRESS_PLUGIN_PLAN.md†L13-L17】

## Tab-by-Tab Requirements

### 1) New Forti

* Build a full base configuration for a new FortiGate, including hostname/model defaults, LAN interface + DHCP, WAN1/WAN2 (DHCP/Static/PPPoE), SSL VPN pool and interfaces, segmentation VLANs (Camera/Phones/WIFI), optional LDAP, and block-filtering checkboxes to include/exclude config sections for fresh deploy vs. delta updates.【F:WORDPRESS_PLUGIN_PLAN.md†L19-L29】
* CLI panel actions: Build CLI, Copy, Save TXT, Clear, Test (serial), Push (serial). Port dropdown under the action row.【F:WORDPRESS_PLUGIN_PLAN.md†L30-L32】

### 2) Virtual-IP

* Tabular editor with add/duplicate/delete rows for VIP objects (name, interface, external IP/port, mapped IP/port, protocol, OK flag). Export/print list button. Generate CLI for `config firewall vip` and optional `vipgrp`; allow Append to main CLI.【F:WORDPRESS_PLUGIN_PLAN.md†L34-L40】

### 3) Address

* Table for address objects (select, name, type, value, allow-routing, OK, row actions) with Delete/Delete All/Import/Export/Create VIP Group controls. Generate CLI for `config firewall address` and `config firewall addrgrp`; Copy/Save/Append buttons.【F:WORDPRESS_PLUGIN_PLAN.md†L42-L47】

### 4) Security

* Presets for External Connections (Private/Public), Security Level (Safe/Strict/etc.), SSL Inspection mode, and per-filter checkboxes (DNS/AV/IPS/Web/App). Generate policies and profiles accordingly; provide Copy/Clear/Append.【F:WORDPRESS_PLUGIN_PLAN.md†L49-L53】

### 5) Alerts

* Checkboxes for alerts on WAN status change, admin login, and site-to-site status change. Generate automation/alert CLI; Copy/Save/Append actions.【F:WORDPRESS_PLUGIN_PLAN.md†L55-L58】

### 6) Import

* Upload FortiGate backup text file. Filter types (Address, VIPs, Services, Schedulers, User Groups, SD-WAN, Addr/VIP groups, IP pools, Users, Firewall Policy, IPSec VPN) with All/None toggles. Interface mapping table (old → new). Actions: Import CLI (parse, filter, map), Clear, and right-side CLI panel with Copy/Save/Append/Test/Push.【F:WORDPRESS_PLUGIN_PLAN.md†L60-L66】

### 7) Settings

* Global settings: hostname, admin port, model, timezone, trusted hosts list with add/remove and optional auto-include LAN. Console (serial) settings (port dropdown, user/pass, baud, Test connection, Push to console, Refresh). SFTP backup settings (host, port, user, pass). Theme/skin picker. Log controls (open log, open folder), Save/Export config, version string display.【F:WORDPRESS_PLUGIN_PLAN.md†L68-L74】

## Web Plugin Architecture

* **Frontend (WordPress admin page):** React/TypeScript or vanilla JS with a tabbed interface mirroring the desktop flows. Right-hand CLI panel is a reusable component with shared actions (Generate/Copy/Save/Append/Test/Push). Store CLI fragments in browser state and allow cross-tab Append to a main document buffer.
* **Backend (PHP or headless service):**
  * Template-based CLI builders for each tab. Builders accept structured form data and produce FortiOS CLI strings.
  * Parser for FortiGate backup files that applies filters and interface mapping before returning cleaned CLI.
  * Serial/SSH/HTTPS push service (optional, behind capability/role checks) plus test-connection endpoint.
  * File save/export endpoints for CLI snippets and for exporting plugin configuration.
* **Persistence:** Use WordPress options or custom tables to store user defaults (models, themes, trusted hosts, serial/SFTP settings) and saved CLI bundles. Provide import/export of plugin config.
* **Security/Permissions:** Restrict plugin pages and push actions to high-privilege roles. Sanitize/escape all inputs, validate uploaded backups, and gate serial/SSH credentials. Support nonce checks on AJAX actions.
* **Theming:** Provide several color themes analogous to the desktop skins; persist the selection per user or globally.

## Open Questions / Follow-Ups

* Which transport should be used for Push (serial gateway service vs. SSH/HTTPS direct to FortiGate)?
* Should generated CLI be versioned per FortiOS release?
* Do we need multi-user collaboration on the main CLI document?
* Should uploads be limited to specific file size/type and scanned server-side?
