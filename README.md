# Campuswoche WordPress Plugin

A WordPress plugin for managing campus week event registrations, courses, participants, and schedules.

**Author:** Joachim E.


## Features

### Admin

| Menu | Description |
|---|---|
| Allgemeine Optionen | Global settings: registration on/off, pricing, dates, text templates |
| Kurse | Create and manage courses with capacity, image, and visibility |
| Teilnehmer:innen | View, edit, and export all registered participants |
| Teilnehmer:innen Kurse | View course–participant assignments |
| Programm | Drag-and-drop event schedule calendar |

### Frontend

| Shortcode | Description |
|---|---|
| `[front_register]` | Registration form (course selection, dietary prefs, parental consent, CAPTCHA, email confirmation) |
| `[front_kurse]` | Public course listing with descriptions, images, and enrollment status |
| `[front_calendar]` | Read-only event schedule display |

---

## Requirements

- WordPress
- PHP with Composer
- [Members](https://wordpress.org/plugins/members/) plugin (for capability management)

---

## Installation

1. Clone or copy this directory into `wp-content/plugins/campuswoche/`
2. Install PHP dependencies:
   ```bash
   cd classes
   composer install
   ```
3. Activate the plugin in the WordPress admin
4. Assign the `cw_allow` capability to the roles that should have admin access (via the Members plugin)
5. Place the shortcodes on the desired frontend pages

---

## Configuration (Options)

All settings are under **Campuswoche → Allgemeine Optionen**.

| Setting | Description |
|---|---|
| Anmeldung aktiviert | Enable or disable the registration form |
| Nur für eingeloggte Nutzer | Allow early access before `register_start` for logged-in WordPress users |
| Teilnahmepreis (Student) | Fee in EUR for regular participants |
| Teilnahmepreis (Alumni) | Fee in EUR for alumni participants |
| Campuswoche Startdatum | First day of the event (used in the calendar, Sunday–Friday span) |
| Anmeldestart | Date from which public registration opens |
| Text (Anmeldung geschlossen) | Message shown when registration is disabled or not yet open |
| Text (Anmeldeseite) | Introductory text shown above the registration form |
| E-Mail Text | Body of the confirmation email sent to participants |

### Registration window logic

```
register_enabled = 0  →  always show "closed" message
register_enabled = 1  →  show form once register_start date is reached
                          (logged-in users can access the form early if
                           register_logged_in_only = 1)
```

---

## Email Template Variables

Use these placeholders in the email and registration page text fields:

| Variable | Replaced with |
|---|---|
| `{{name}}` | Last name |
| `{{vorname}}` | First name |
| `{{fullname}}` | Full name |
| `{{betrag}}` | Amount due (EUR) |
| `{{kurs}}` | Course name |

---

## Database Schema

Tables are created automatically on plugin activation via `dbDelta`.

### `cw_user` — Participants

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key |
| `vorname` / `nachname` | varchar | First / last name |
| `email` | varchar | Email address |
| `str` / `plz` / `ort` | varchar | Street, postal code, city |
| `geb` | date | Date of birth |
| `schule` | varchar | School / university |
| `essen` | varchar | Dietary preference |
| `sonstiges` | text | Additional notes |
| `uuid` | varchar | Unique registration ID |
| `regdate` | datetime | Registration timestamp |
| `to_pay` | int | Amount owed |
| `paytype` | int | 0 = student, 1 = alumni |
| `payed` | int | Payment received flag |
| `is_course_leader` | int | Course leader flag |

### `cw_kurse` — Courses

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key |
| `name` | varchar | Internal name |
| `beschreibung` | text | Description (HTML allowed) |
| `max_teilnehmer` | int | Capacity limit |
| `bild` | varchar | Image URL |
| `show_front` | int | Show on frontend |
| `is_open` | int | Open for new registrations |
| `needs_course_leader` | int | Requires a designated leader |

### `cw_user_kurs` — Enrollments

Many-to-many between `cw_user` and `cw_kurse`.

### `cw_options` — Configuration

Single-row settings table. See [Configuration](#configuration-options) above.

### `cw_events` — Schedule entries

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key |
| `event_day` | int | Day index 0–5 (Sun–Fri) |
| `event_start` / `event_end` | int | 24-hour time as integer (e.g. `900`, `1730`) |
| `event_name` | text | Title |
| `event_subtext` | text | Short subtitle |
| `event_description` | text | Long description (HTML) |
| `event_color` | text | Hex color (e.g. `#d7e7a1`) |

---

## AJAX Endpoints

All endpoints require the `cw_allow` capability and a valid WordPress nonce.

| Action | Nonce | Description |
|---|---|---|
| `cw_kurs_load` | `cw_kurs_nonce` | Load course data |
| `cw_kurs_save` | `cw_kurs_nonce` | Create or update a course |
| `cw_kurs_delete` | `cw_kurs_nonce` | Delete a course |
| `cw_cal_create` | `cw_cal_nonce` | Create a schedule event |
| `cw_cal_move` | `cw_cal_nonce` | Move / resize a schedule event |
| `cw_cal_delete` | `cw_cal_nonce` | Delete a schedule event |
| `cw_event_load` | `cw_cal_nonce` | Load event details |
| `cw_event_save` | `cw_cal_nonce` | Update event details |
| `ajax_action` | `cw_teilnehmer_nonce` | Edit / delete participants |

---

## Excel Export

Navigate to **Teilnehmer:innen** or **Teilnehmer:innen Kurse** and click the export button. The plugin uses [PhpOffice/PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/) (≥ 1.28) via Composer.

---

## Security

- All admin AJAX handlers verify `cw_allow` capability and a nonce
- User input is sanitized with `sanitize_text_field`, `wp_kses_post`, `esc_url_raw`, and `sanitize_hex_color`
- All DB queries use `$wpdb->prepare()` with placeholders
- Bulk-delete uses a session-based CSRF token
- Frontend registration validates a math CAPTCHA stored in `$_SESSION`

---

## Codebase Overview

```
campuswoche/
├── campuswoche.php          # Plugin bootstrap, hooks, shortcodes, AJAX handlers
├── db/db.php                # Database schema (dbDelta)
├── classes/
│   ├── Teilnehmer.php       # Participant model
│   ├── Kurs.php             # Course model
│   ├── Event.php            # Schedule event model
│   ├── Options.php          # Plugin options model
│   ├── functions.php        # Shared helper functions
│   ├── Export_XLS.php       # Excel export (PhpSpreadsheet)
│   └── Autoloader.php       # Composer autoloader
├── views/                   # Admin and frontend HTML/PHP templates
├── js/                      # jQuery-based admin and frontend scripts
└── css/                     # Admin and frontend stylesheets
```

### Key files

- Main plugin file: [campuswoche.php](campuswoche.php)
- Database schema: [db/db.php](db/db.php)
- Admin JS: [js/cw-admin.js](js/cw-admin.js), [js/kurs.js](js/kurs.js), [js/teilnehmer.js](js/teilnehmer.js)
- Frontend JS: [js/front_register.js](js/front_register.js), [js/cal.js](js/cal.js)

---

## License

[MIT](LICENSE.md)
