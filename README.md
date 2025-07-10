# Private Vault

Private Vault is a PHP based web application that manages notes, tasks, events and file uploads. The project is organized as a small MVC structure with API endpoints and HTML templates. The `public/` directory acts as the front controller for browser requests.

## Features
- **User authentication** with sessions (see `src/lib/auth.php`).
- **Notes API** for creating, updating and deleting notes (`src/api/notes.php`).
- **Task management** including groups and assignments (`src/controllers/dashboard.php`, `src/models/Task.php`).
- **File explorer** to upload and download documents (`public/file-explorer.php`, templates).
- **Dashboard** displaying tasks, documents and calendar events (`src/controllers/dashboard.php`).

## Structure Overview
```
/                    Root redirect to /public
/api                 Legacy API test scripts (now removed)
/public              Front controller and page PHP files
/src
  /api               JSON API endpoints
  /controllers       Page controllers rendering templates
  /lib               Database and auth helpers
  /models            Domain models (e.g., Task.php)
/templates           HTML/PHP view templates
```

Routing rules are defined in `routes.php` and used by `public/index.php`.

## Database
SQL files for setting up the schema reside in `database/`. Connection parameters are configured in `config.php`.

## Version History
- **0.1.0** – Initial project with notes, tasks and file management features.
- **0.1.1** – Cleanup of unused debug and test scripts, added initial README.
