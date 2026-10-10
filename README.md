# PropManage 🏠

**PropManage** is a web-based property management system developed as a **Web Technologies course project**. It brings property listings and common rental-management tasks into one application, with separate areas for tenants, landlords, and administrators.

## 🎨 UI/UX Design

The project's design reference is available in Figma:

- [Home Rental Website — Figma Design](https://www.figma.com/design/dSEydGPVjJsU5xm0P44zPO/Home-Rental-Website?node-id=0-1&p=f)

## ✨ Features

- **User accounts:** Registration, login, logout, and password-recovery pages.
- **Property listings:** Browse available properties and view property details.
- **Tenant dashboard:** Explore properties, manage requests, view payment records, submit maintenance requests, and access messages and profile settings.
- **Landlord dashboard:** Manage property listings, tenant-related information, rent requests, maintenance requests, payments, messages, and profile settings.
- **Admin dashboard:** Manage users and properties, and access financial and support sections.
- **Booking and rent requests:** Request a property and manage request status.
- **Maintenance tracking:** Submit maintenance issues and track their status.
- **Database-backed application:** Store users, properties, bookings, payments, and related records in MySQL.

## 🧰 Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local development environment:** XAMPP or another PHP/MySQL environment

## 📁 Project Structure

```text
PropManage/
├── admin/          # Administrator pages
├── api/            # Request-processing endpoints
├── auth/           # Registration, login, logout, and password recovery
├── config/         # Database connection and session helpers
├── css/            # Stylesheets
├── database/       # SQL database schema and sample data
├── images/         # Application images and default avatar
├── js/             # JavaScript files
├── landlord/       # Landlord dashboard and management pages
├── properties/     # Property listing and details pages
├── tenant/         # Tenant dashboard and management pages
├── uploads/        # User-uploaded files (local runtime data)
└── index.php       # Application home page
```

## 🚀 Run Locally

### Requirements

- PHP with **PDO MySQL** enabled
- MySQL or MariaDB
- Apache or another PHP-capable web server

### Setup with XAMPP

1. Install and open XAMPP.
2. Start **Apache** and **MySQL**.
3. Copy the `PropManage` project folder into XAMPP's `htdocs` directory. For example:

   ```text
   C:\xampp\htdocs\PropManage
   ```

4. Check the database settings in `config/db.php`. The default local settings are:

   ```php
   DB_HOST = localhost
   DB_PORT = 3306
   DB_NAME = propmanage_db
   DB_USER = root
   DB_PASS = ''
   ```

   Update these values to match your local MySQL configuration if necessary. Do not use default development credentials on a public server.

5. The application attempts to create `propmanage_db` and initialize the schema on first use when the database connection works and the `users` table is not present. If initialization fails, review the PHP/MySQL error and database permissions.
6. Open the application in your browser:

   ```text
   http://localhost/PropManage/
   ```

### Database setup note

The SQL file is `database/schema.sql`. **Use it only with a new or disposable development database:** it contains `DROP TABLE IF EXISTS` statements and can delete existing tables with matching names. Back up any important data before running it manually.

## 🔐 GitHub and Uploads

User avatars and other uploaded files are runtime data, not source code. Avoid committing real users' images or other private information. A `.gitignore` entry for `uploads/avatars/*` that keeps `uploads/avatars/.gitkeep` is recommended.

Before deploying publicly, review database credentials, file-upload validation, permissions, and other security settings. This project is intended for learning and local development unless it has been separately reviewed and secured for production.

## 🎓 Course Project

Created as a **Web Technologies course project**.

### Contributors

- Farhan Nadim
- Mashfi Rahman
- Arafatullah Rifat
- Fariha Akter Prity

---

*PropManage — Property management made easier.*
