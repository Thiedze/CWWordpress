# Campuswoche WordPress Plugin

A WordPress plugin for managing campus week event registrations, courses, participants, and schedules.

## Features

### Admin
- **Participant Management** — View, edit, and export registered participants to Excel
- **Course Management** — Create and manage courses with capacity limits, images, and visibility controls
- **Event Calendar** — Drag-and-drop scheduling with color coding and overlap detection
- **Options** — Configure pricing (student/alumni), registration window, and email templates

### Frontend (via Shortcodes)
- **`[front_register]`** — Registration form with course selection, dietary preferences, parental consent, and email confirmation
- **`[front_kurse]`** — Public course listing with descriptions and enrollment status
- **`[front_calendar]`** — Visual event schedule

## Requirements

- WordPress
- PHP with Composer
- [Members](https://wordpress.org/plugins/members/) plugin (for capability management)

## Installation

1. Clone or copy this directory into `wp-content/plugins/campuswoche/`
2. Install PHP dependencies:
   ```bash
   cd classes
   composer install
   ```
3. Activate the plugin in the WordPress admin
4. Add the `cw_allow` capability to the roles that should have admin access
5. Use the shortcodes on any page to expose the frontend

## Database

The plugin creates the following tables on activation:

| Table | Description |
|---|---|
| `cw_user` | Participant records |
| `cw_kurse` | Courses |
| `cw_user_kurs` | Course enrollments (many-to-many) |
| `cw_options` | Plugin configuration |
| `cw_events` | Event schedule entries |

## Email Template Variables

The following variables can be used in email templates configured under **Options**:

| Variable | Value |
|---|---|
| `{{name}}` | Last name |
| `{{vorname}}` | First name |
| `{{fullname}}` | Full name |
| `{{betrag}}` | Amount due |
| `{{kurs}}` | Course name |

## Development

- Admin JS: [js/cw-admin.js](js/cw-admin.js), [js/kurs.js](js/kurs.js), [js/teilnehmer.js](js/teilnehmer.js)
- Frontend JS: [js/front_register.js](js/front_register.js), [js/cal.js](js/cal.js)
- Main plugin file: [campuswoche.php](campuswoche.php)
- Database schema: [db/db.php](db/db.php)
