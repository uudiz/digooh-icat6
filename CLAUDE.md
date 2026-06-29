# icat6-digooh — Digital Signage CMS

## Project Overview

iCAT6 is a digital signage / DOOH (Digital Out-Of-Home) content management system built on **CodeIgniter 3** (PHP framework). It manages media players, playlists, campaigns, content scheduling, firmware updates, SSP (Supply-Side Platform) integrations, and real-time player communication via a Swoole TCP/UDP socket server.

The platform supports multi-tenancy (companies/folders), multi-language (English/German), and features a Bootstrap-based admin UI with CRUD views for all major entities.

## Technology Stack

- **Backend:** PHP 8.5 (currently on branch `php85`, upgrading from earlier PHP versions)
- **Framework:** CodeIgniter 3 (modified system core in `system/`)
- **Database:** MySQL/MariaDB via `mysqli` driver
- **Socket Server:** Swoole (`swoole/` directory) — TCP/UDP server for real-time player communication (port 4702 by default)
- **Frontend:** Bootstrap-based views in `application/views/bootstrap/`, jQuery, TinyMCE, various jQuery plugins in `assets/`
- **Libraries (Composer):** Guzzle HTTP, PhpSpreadsheet, PHPMailer, ArrayToXml, FTP client, HTML DOM parser, CSV library, FastExcelWriter, JSON Machine, 2FA (RobThree), QR Code (Bacon)
- **CLI Entry Point:** `cli.php` — used for cron jobs (RSS updates, offline processing, etc.)

## Directory Structure

```
├── application/           # CI3 application directory
│   ├── config/            # Config files (config.php, database.php, routes.php, etc.)
│   │   ├── config.mia.php # Instance-specific config (gitignored)
│   │   └── config.sample.php / database.sample.php  # Templates for new deployments
│   ├── controllers/       # CI controllers ( Receive = device API, MY_Controller base)
│   ├── core/              # MY_Controller (base with auth, JS/CSS, i18n)
│   ├── helpers/           # Custom helpers (media, weather, date, file, serial, etc.)
│   ├── libraries/         # Custom libs (Image, Mailer, RSS, SSO JWT, TimeSlot, etc.)
│   ├── models/            # CI_Model subclasses (table-prefix naming: cat_*, cr_*, etc.)
│   ├── views/             # PHP views — bootstrap/ subfolder for admin UI
│   ├── vendor/            # Application-level Composer dependencies
│   └── language/          # English & Germany translation files
├── swoole/                # Swoole socket server
│   ├── icat6serverphp85.php  # Current PHP 8.5 server
│   ├── swoole_config.php     # DB & port config (gitignored)
│   └── utils.php             # Shared server utilities
├── system/                # Modified CI3 system core
├── assets/                # Frontend assets (JS, CSS, Bootstrap, jQuery plugins, logos)
├── static/                # Static files served directly
├── resources/             # Uploaded/generated resources (writable by www-data)
├── upload/                # File uploads (writable by www-data)
├── plugins/               # jQuery plugins (sliders, editors, maps, etc.)
├── doc/                   # Documentation (SSO token generation, layout templates)
├── vendor/                # Root-level Composer (currently empty)
├── index.php              # Web entry point (CI_ENV sets environment)
├── cli.php                # CLI entry point (cron jobs)
└── .htaccess              # Apache mod_rewrite rules
```

## Key Architecture Patterns

### Controllers
- **`MY_Controller`** (`application/core/MY_Controller.php`) — Base controller with login filtering, i18n (English/German), JS/CSS asset management, and auth level helpers. Most admin controllers extend this.
- **`Receive`** — Main device/player API. Handles player registration, heartbeat, content requests, status reporting. Parses custom binary protocol. Does NOT extend MY_Controller (no login filter — devices authenticate via serial number).
- **`Api`** — Lightweight JSON API endpoints for AJAX calls.
- **`Cron`** — CLI-only controller for scheduled tasks (RSS, offline detection). Called via `cli.php cron/<method>`.
- **`SspController`** — SSP (Supply-Side Platform) dashboard and data APIs.

### Models
- Extend `CI_Model`, use CI Query Builder (`$this->db->select()`, etc.)
- Table naming convention: `cat_*` (catalog), `cr_*` (commerce), `ssp_*` (SSP), etc.
- No ORM — raw Query Builder patterns with manual SQL.

### Views
- Admin UI in `application/views/bootstrap/` — one subfolder per entity (players, media, campaigns, etc.)
- Layout system: `bootstrap/layout/basiclayout.php` wraps content
- Auth-based rendering: `$this->get_auth()` returns permission level; 401 view shown for insufficient access

### Swoole Socket Server
- Binary protocol for player communication (login, heartbeat, control, instant schedule, etc.)
- Constants for packet types: `CLOGIN=0x01`, `CHEARTBEAT=0x02`, `CCONTROL=0x03`, etc.
- Uses `Swoole\Database\MysqliPool` for connection pooling
- Config in `swoole/swoole_config.php` (gitignored — contains DB credentials and port)

### URL Rewriting
- Apache `.htaccess` maps `cdmsA/receive!<action>.action` and `cdms/receive!<action>.action` to `/receive/<action>` (legacy API compatibility)

## Config & Deployment

- **Environment:** Set via `$_SERVER['CI_ENV']` — `development`, `testing`, or `production`
- **Instance config:** `application/config/config.mia.php` — per-deployment overrides (gitignored)
- **Database:** `application/config/database.php` (gitignored; `database.sample.php` is template)
- **Swoole:** `swoole/swoole_config.php` (gitignored; `swoole_config.sample.php` is template)
- **Gitignored paths:** config.mia.php, database.php, swoole_config.php, resources/, upload/, logs/, cached/

## PHP 8.5 Migration (Current Branch)

The `php85` branch is actively migrating the codebase to PHP 8.5 compatibility. Key changes visible in git status:
- CI3 system core modifications for PHP 8.5 support
- Models updated for stricter type handling
- New Swoole server implementation (`icat6serverphp85.php`)
- Application vendor directory removed (Composer deps need reinstall)
- Various controller and library updates for PHP 8.5 compatibility

## Development Notes

- **No automated tests** — no PHPUnit or similar test framework detected
- **No migration system active** — SQL schema changes documented manually in README.md
- **Two-factor authentication:** Supported via `RobThree/TwoFactorAuth` + `Bacon/BaconQrCode`
- **SSO:** JWT-based SSO integration (`application/libraries/Sso_jwt.php`)
- **File permissions:** `resources/` and `upload/` directories must be writable by `www-data`
- **Cron jobs:** Run via `cli.php cron/<method>` — RSS updates, offline detection, etc.
