# Inventory Management System (PHP + MySQL + Bootstrap 5)

A responsive full-stack inventory management web app for the Web Application Development final project.

## Features
- Secure login/logout with PHP sessions and `password_hash()` / `password_verify()`
- Admin and standard-user roles (only admins can delete products and manage users)
- Product CRUD: create, list/search, edit, delete (products with transaction history are protected)
- Stock-in and stock-out transactions update stock and preserve transaction history
- Dashboard metrics: product count, units in stock, low-stock count, inventory value, recent transactions
- Prepared SQL statements, server-side validation, HTML escaping, CSRF tokens, and transaction locking
- Responsive Bootstrap 5 interface

## Requirements
- XAMPP with PHP 8+ and MySQL/MariaDB
- Browser
- Internet connection for Bootstrap CDN styling (you can download Bootstrap for offline use if required)

## Run on XAMPP
1. Copy the whole `Inventory_Management_System` folder into `C:\\xampp\\htdocs\\`.
2. Open XAMPP Control Panel and start **Apache** and **MySQL**.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database/schema.sql`, and click **Import/Go**. The SQL creates the `inventory_system` database and tables.
5. Check `config/database.php`. Default XAMPP credentials are commonly username `root` and blank password. Change them if your setup differs.
6. Open `http://localhost/Inventory_Management_System/setup_admin.php`.
7. Create your own admin username and password (minimum 8 characters).
8. Open `http://localhost/Inventory_Management_System/login.php` and sign in.
9. **Delete `setup_admin.php` after creating the admin account.** Do not leave setup scripts on a public server.
10. Add products, then use **Stock Transactions** to add starting stock.

## Role guide
- **Admin:** all standard features, product deletion, and user account creation/role assignment.
- **Standard user:** dashboard, product viewing/creation/editing, and stock transactions; cannot delete products or manage users.

## Deploying online
Use a host that supports PHP 8+ and MySQL/MariaDB, such as a suitable free PHP/MySQL host. Create a database and user from the hosting control panel, import `database/schema.sql`, update `config/database.php` with the host-provided database credentials, upload the PHP files, create the admin through the setup page, then delete `setup_admin.php`. Enable HTTPS if the host provides it. Never upload real passwords or production credentials to GitHub.

The assignment requires the URL to include your name. After deployment, follow your instructor's naming format, for example `https://firstname-lastname.hosting-platform.com`. The actual domain depends on the hosting service and availability.

## GitHub upload
1. Create a new GitHub repository, e.g. `inventory-management-system`.
2. Upload the project files and folders. Include `README.md` and `database/schema.sql`.
3. Do not upload passwords, private credentials, or real user data.
4. In README, add your live website URL once deployment is complete.

## Suggested test checklist
- [ ] Login works with correct credentials and rejects incorrect credentials.
- [ ] Standard user cannot access `users.php` or delete products.
- [ ] Admin can create a user and choose its role.
- [ ] Product form rejects missing fields, negative price, and invalid reorder level.
- [ ] SKU duplicates are rejected.
- [ ] Stock-in increases quantity.
- [ ] Stock-out decreases quantity.
- [ ] Stock-out cannot make quantity negative.
- [ ] Dashboard totals update after adding products/transactions.
- [ ] Product with transaction history cannot be deleted.
- [ ] Layout works at phone and desktop widths.

## Submission items
- Live Website URL: add your deployed URL
- GitHub Repository URL: add your repository URL
- Database file: `database/schema.sql`
- Admin test credentials: create a dedicated grading account and share only as instructed by your teacher; change/delete it after grading.
