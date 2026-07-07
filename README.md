# Computer Reservation Management System (CRMS)

A complete, production-ready, secure, and responsive web application built using **PHP (PDO, MVC, OOP)** and a modern **Bootstrap 5 / AdminLTE 3** user interface. Designed for department computer laboratories to handle workstation bookings, RBAC authorization, approvals, check-ins/outs, and reports.

---

## 🌟 Key Features

1. **Role-Based Access Control (RBAC)**: Supports Super Admin, Dept Admin, Lecturer, Staff, and Student accounts with granular permission checks.
2. **Prevent Double Bookings**: Database transaction locks and time-overlapping mathematical queries prevent overlapping schedules for the same workstation.
3. **Interactive Booking Calendar**: Integrated with `FullCalendar.js` showing schedules color-coded by reservation status.
4. **Check-In/Check-Out System**: Allows users to check in up to 15 mins before booking starts. Releases inactive seats automatically if they fail to check in within the configured window.
5. **Asset QR/Barcode Printing**: Generates sticker labels dynamically using `JsBarcode` and `QR Server` APIs.
6. **Programmatic DB Backups**: Generates and downloads SQL dumps directly from the admin dashboard without shell dependencies, and handles restoring sql backup uploads.
7. **CSV Exports & Reports**: Streams clean CSV Excel-compatible data for computers, laboratories, and user performances.

---

## 🔑 Default Test Accounts

Use the password **`admin123`** to log in to any account:

| Role | Email | User Type |
|---|---|---|
| **Super Administrator** | `admin@lab.edu` | Full System Control |
| **Department Administrator** | `dept_admin@lab.edu` | Lab & Reservation Control |
| **Lecturer** | `lecturer@lab.edu` | Auto-Approved bookings |
| **Staff** | `staff@lab.edu` | Approvals & Asset maintenance |
| **Student** | `student@lab.edu` | Standard seat reservations |

---

## 🚀 Installation & Running

### 1. Prerequisites
* **PHP 8.3+** (Extensions: `pdo_mysql`, `mbstring`, `openssl`)
* **MySQL 8.0+**
* **Composer**

### 2. Setup Configuration
Run the automated database installation script:
```bash
php database/setup.php
```

### 3. Start Development Server
Run PHP's built-in web server pointing to the `public/` directory:
```bash
php -S localhost:8000 -t public
```

Open `http://localhost:8000` in your web browser.

---

## 📂 Project Structure

```text
computer-booking/
├── app/
│   ├── Config/          # Database configuration, app settings
│   ├── Core/            # Framework core: Router, Request, Response, Controller, Database
│   ├── Controllers/     # MVC Controllers
│   ├── Repositories/    # Data Access Layer (PDO SQL queries)
│   ├── Services/        # Business Logic Layer (business rules, validation)
│   ├── Middleware/      # Auth, Role, CSRF protection
│   └── Helpers/         # Utility functions
├── views/
│   ├── layouts/         # Layout templates (Main, Auth, Print)
│   ├── auth/            # Authentication forms
│   ├── dashboard/       # Main admin analytics board
│   ├── reservations/    # Booking forms & check-in triggers
│   ├── computers/       # Workstation asset CRUD
│   ├── laboratories/    # Lab room configurations
│   ├── reports/         # Export views & charts
│   ├── settings/        # System configs panel
│   └── errors/          # 404 / 500 error templates
├── public/
│   ├── assets/          # Static CSS & JS resources
│   ├── uploads/         # Uploaded workstation images
│   └── index.php        # Front Controller
├── database/
│   ├── schema.sql       # Database table setup
│   ├── seed.sql         # Seed data
│   └── setup.php        # Installation script
├── storage/
│   └── logs/            # Activity & Simulated mail logs
├── .env                 # Environment variables
└── composer.json        # Project packages configuration
```

---

## 🛡️ Security Details
* **SQL Injection Protection**: Uses PDO prepared statements with disabled emulated parameters.
* **XSS Mitigation**: Strict HTML output escaping using custom global helper `esc()`.
* **CSRF Mitigation**: Uses session CSRF tokens validated on all state-changing POST endpoints.
* **Secure Sessions**: Uses HTTP-Only and SameSite cookie policies to prevent session hijacking.
