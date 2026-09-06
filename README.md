# Finova - Personal Salary & Earnings Tracker 💰📈

Finova is a comprehensive, full-stack personal finance application designed to help users track, analyze, and manage their income streams across different workplaces over multiple years. Built using Core PHP and modern frontend tools, the application offers data-driven insights through dynamic dashboards and professional PDF reporting features.

## 🚀 Live Demo
* [Insert your live deployment link here if you have one, or delete this line]*

## ✨ Key Features
* **Multi-Year Income Tracking:** Log and filter salaries by specific employment locations, contract types, and financial years.
* **Interactive Dashboards:** Visual overview of income distributions, averages, and historical earning trends over time.
* **Dynamic Data Tables:** Clean, structured presentation of financial data with sorting and filtering capabilities powered by Bootstrap and JavaScript.
* **PDF Export Utility:** One-click functionality to convert and download dynamic data tables into structured, print-ready PDF reports.
* **Secure Session Management:** Built with core PHP backend security practices for user authentication and data privacy.

## 🛠️ Tech Stack
* **Backend:** Core PHP
* **Frontend:** JavaScript (ES6+), HTML5, CSS3, Bootstrap 5
* **Database:** MySQL
* **Reporting:** Dompdf (HTML-to-PDF converter for PHP)


## 📦 How to Run Locally
1. Clone the repository into your local server environment (e.g., XAMPP htdocs, WAMP www):
   ```bash
   git clone https://github.com
   ```
2. Import the database configuration:
   * Open phpMyAdmin (`http://localhost/phpmyadmin`).
   * Create a new database named `finova`.
   * Import the provided `.sql` file located in your project folder.
3. Configure your environment:
   * Open the database connection config file (e.g., `config.php` or `db.php`) and update your local MySQL credentials.
4. Run the project:
   * Start Apache and MySQL modules in your control panel.
   * Open your browser and navigate to `http://localhost/finova`.
