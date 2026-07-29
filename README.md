# SmartKrishi – Farming Management System

SmartKrishi is a PHP and MySQL web application that connects farmers, customers,
suppliers, labourers, administrators, and agricultural specialists.

## Features

- Role-based registration, login, and dashboards
- Crop and inventory management
- Product marketplace and order management
- Supplier product and equipment management
- Labour job posting and applications
- Agrologist articles, appointments, and responses
- Crop and insect identification through the Kindwise APIs
- Admin and analytics pages
- bKash, Nagad, Rocket, and cash-on-delivery payment flows

## Technology

- PHP 8.0 or newer
- MySQL or MariaDB
- HTML, CSS, JavaScript, and Bootstrap
- PHP extensions: `mysqli`, `curl`, `fileinfo`, and `mbstring`

## Local setup with XAMPP

1. Clone the repository into the XAMPP web directory:

   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/Hasibur303/farming_management_system.git
   cd farming_management_system
   ```

2. Start Apache and MySQL from the XAMPP control panel.

3. Create a database named `farming_management`.

4. Import `farming_management.sql` using phpMyAdmin or the MySQL command line:

   ```bash
   mysql -u root -p farming_management < farming_management.sql
   ```

5. Copy the example environment file:

   ```powershell
   Copy-Item .env.example .env
   ```

6. Update `.env` with your local database credentials and API keys. Never commit
   `.env`.

7. Open the application:

   ```text
   http://localhost/farming_management_system/dashboard.php
   ```

## Environment variables

| Variable | Purpose | Default |
|---|---|---|
| `DB_HOST` | Database server | `localhost` |
| `DB_PORT` | Database port | `3306` |
| `DB_NAME` | Database name | `farming_management` |
| `DB_USER` | Database username | `root` |
| `DB_PASSWORD` | Database password | empty |
| `KINDWISE_API_KEY` | Crop identification API key | none |
| `KINDWISE_ENDPOINT` | Crop API endpoint | Kindwise crop endpoint |
| `INSECT_API_KEY` | Insect identification API key | none |
| `INSECT_ENDPOINT` | Insect API endpoint | Kindwise insect endpoint |

## Project layout

```text
admin/       Administration pages
analytics/   Reporting and analytics
css/         Shared stylesheets
farmer/      Farmer-specific pages
images/      Static application images
supplier/    Supplier-specific pages
uploads/     Runtime user uploads
*.php        Current page controllers and views
```

The application currently uses page-oriented PHP files. When adding new code,
reuse `database.php` for the database connection and keep credentials in `.env`.

## Security notes

- Rotate any API key that was previously committed to Git history.
- Validate uploaded files and all request input.
- Use prepared statements for every query containing user input.
- Do not enable PHP error display in production.
- Ensure `uploads/` cannot execute PHP files in the production web server.

## Team

- Masum — frontend, UI/UX, integration, and testing
- Hasib — backend, AI integration, and DevOps

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE).
