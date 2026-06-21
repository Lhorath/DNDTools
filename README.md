Icon: fa-brands fa-d-and-d

Description: Original D&D Tools website — PHP app with shared includes and database-backed pages.

GitHub: https://github.com/Lhorath/DNDTools

Live: http://dndtools.dackdns.ddns.net/

# DND Tools

> PHP-based D&D utility site with shared includes, a database backend, and a whitelist page router.

## Overview

DND Tools is the original D&D Tools website. The entry point `index.php` loads `includes/core/includes.php` and assembles a **header**, **body**, and **footer** from `core/`. Pages are selected via a whitelist router.

## Requirements

- PHP
- MySQL (as wired in `includes/`)
- A web server (Apache, Nginx, etc.)

## Setup

1. Configure your database credentials in `includes/`.
2. Deploy the project folder to your web host.
3. Point the document root at the project directory.

## License

MIT — see [LICENSE](LICENSE).  
Copyright © 2026 [MacWeb Canada](https://macweb.ca) | Professional Online Solutions.
