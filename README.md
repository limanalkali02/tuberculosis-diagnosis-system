# Tuberculosis (TB) Diagnosis Support System

A PHP and MySQL web application that demonstrates a rule-based approach to tuberculosis screening. Users can complete a symptom questionnaire, view screening results, review previous screenings, and request appointments.

> **Important:** This is an academic demonstration project, not a medical diagnostic tool. Its results must not replace evaluation by a qualified healthcare professional or appropriate TB testing.

## Features

- User registration and login
- Symptom and risk-factor questionnaire
- Rule-based screening score and risk classification
- Screening history for users
- Appointment booking
- Administrative dashboard for managing appointments and enquiries
- MySQL database for application records

## Technology Stack

- PHP 8+
- MySQL or MariaDB
- HTML, CSS and JavaScript
- Apache via XAMPP

## Local Installation

1. Install XAMPP with PHP and MySQL/MariaDB.
2. Copy this repository into the XAMPP `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open phpMyAdmin and create a database named `tb_diagnosis_db`.
5. Import `sql/schema.sql` into that database.
6. Configure the local database connection in `config/db.php`.
7. Configure an administrator account securely before using administrative features. Do not publish passwords or reuse demo credentials.
8. Open `http://localhost/tb-diagnosis-system/` in your browser.

## Project Structure

- `admin/` — administrative pages
- `assets/` — styles and frontend assets
- `config/` — database configuration
- `includes/` — shared components and application functions
- `sql/schema.sql` — database schema and initial screening rules
- `diagnose.php` — symptom questionnaire
- `process_diagnosis.php` — screening rule processing
- `result.php` — screening result page
- `history.php` — screening history
- `appointment.php` — appointment features
- `register.php` and `login.php` — user authentication

## How Screening Works

The application uses a configurable, rule-based scoring approach. Answers to symptom and risk-factor questions contribute to a score, which is compared with rules stored in the database to produce a screening category and guidance.

This approach is intended for software demonstration and learning. The rules have not been established as a clinically validated diagnostic model.

## Privacy and Security

- Use synthetic test data when demonstrating the project.
- Never commit real patient records, passwords, private keys, or personal identifiers.
- Keep local database credentials out of public repositories.
- Review authentication and access controls before any deployment.

## Future Improvements

- Add automated tests for screening rules.
- Improve input validation and security auditing.
- Add a secure administrator provisioning workflow.
- Validate screening logic with qualified healthcare professionals before any clinical use.

## License

No license has been specified. Contact the repository owner before reusing this project's code.
