# Allergen Checker System

A comprehensive web-based platform for food ordering with real-time allergen checking, designed to help clients safely order food while being aware of their allergies.

## Features

### Client Features
- **User Registration & Authentication** - Create account with username and password
- **Complete Profile Management** - Add/edit First Name, Middle Name, Last Name, Age, Contact Info
- **Allergy Management** - Add, view, and remove allergies from profile
- **Food Ordering** - Browse all available foods with real-time allergen warnings
- **Allergen Checking** - Foods containing user allergens are highlighted in red
- **Order Management** - View order history, status, and cancel orders anytime
- **Profile Deletion** - Users can delete their own accounts

### Staff Features
- **Order Queue** - View all pending orders with client information and allergies
- **Order Management** - Accept orders from the queue
- **Payment Processing** - Mark orders as paid and completed
- **Order History** - View completed orders from the current day

### Admin Features
- **Food Menu Management** - Create, edit, delete food items with images
- **Ingredient Management** - Add/edit ingredients and prices
- **User Management** - View, edit, and delete client and staff accounts
- **Staff Account Creation** - Create new staff accounts
- **Order Queue View** - Monitor all pending, accepted, and completed orders
- **Sales Reports** - View completed orders by date with sales analytics
- **Payment Tracking** - Track all transactions and completed orders

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap (Responsive Design)
- **Backend:** PHP 8.2+
- **Database:** MySQL/MariaDB
- **Authentication:** PHP Password Hashing (bcrypt), Session Management
- **Security:** Prepared Statements (SQL Injection Prevention), Password Hashing

## Installation & Setup

### Prerequisites
- XAMPP (PHP 8.0+, MySQL)
- Web Browser (Chrome, Firefox, Safari, Edge)
- Git (optional, for version control)

### Step 1: Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `foodsystem`
3. Import the `foodsystem.sql` file:
   - In phpMyAdmin, select the `foodsystem` database
   - Go to "Import" tab
   - Select the `foodsystem.sql` file
   - Click "Import"

**If upgrading from existing system:**
1. Run the `MIGRATION_UPDATE.sql` file in phpMyAdmin to add missing profile columns

### Step 2: File Placement

1. Download/clone the project to your XAMPP htdocs folder:
   ```
   c:\xampp\htdocs\Allergen_Checker_System\
   ```

2. Create an `uploads` folder for food images:
   ```
   mkdir c:\xampp\htdocs\Allergen_Checker_System\uploads
   ```

### Step 3: Start XAMPP

1. Open XAMPP Control Panel
2. Start Apache and MySQL services
3. Access the system at: `http://localhost/Allergen_Checker_System/`

### Step 4: Test Accounts

**Default Admin Account:**
- Username: `admin`
- Password: (check database or create new one via registration)

**Create test accounts:**
- Go to `http://localhost/Allergen_Checker_System/client_register.php` to create a client account
- Admin creates staff accounts via Admin Dashboard → Create Staff Account

## File Structure

```
Allergen_Checker_System/
├── login.php                 # Authentication & Login
├── logout.php                # Session termination
├── client_register.php       # Client registration
├── client_dashboard.php      # Client home page
├── client_profile.php        # Profile & allergy management
├── client_orders.php         # View orders & order history
├── kiosk.php                 # Food ordering interface
├── staff_dashboard.php       # Staff home page
├── staff_queue.php           # Order queue view
├── update_order.php          # Order completion & payments
├── staff_register.php        # Create staff accounts (admin only)
├── admin_dashboard.php       # Admin home page
├── admin_food.php            # Food menu management (CRUD)
├── admin_payments.php        # Sales reports by date
├── view_users.php            # User management (CRUD)
├── queue.php                 # Admin order queue view
├── db.php                    # Database connection
├── styles.css                # Global styling
├── foodsystem.sql            # Database schema
├── MIGRATION_UPDATE.sql      # Schema migration script
└── uploads/                  # Food item images
```

## User Workflows

### Client Workflow
1. **Register** → Create account with username, password, and profile info
2. **Profile Setup** → Add First Name, Middle Name, Last Name, Age, Contact
3. **Add Allergies** → Add allergies to profile
4. **Browse & Order** → View food items, allergen warnings appear automatically
5. **Track Order** → Monitor order status and cancel if needed

### Staff Workflow
1. **Login** → Access staff dashboard with credentials
2. **View Queue** → See pending orders with client allergies
3. **Accept Order** → Click "Receive & Accept Order" to move to payment processing
4. **Process Payment** → Mark order as complete and paid
5. **View History** → See completed orders from today

### Admin Workflow
1. **Login** → Access admin dashboard
2. **Manage Food** → Create, edit, delete food items and ingredients
3. **Manage Users** → Edit client/staff details, manage allergies
4. **Monitor Orders** → View all orders in various stages
5. **View Sales** → Select a date and view completed orders with sales totals
6. **Create Staff** → Add new staff accounts for the system

## Security Features

- **Password Hashing** - All passwords hashed with bcrypt (PASSWORD_DEFAULT)
- **SQL Injection Prevention** - All queries use prepared statements
- **Input Validation** - All user inputs validated and sanitized
- **Session Management** - Secure session-based authentication
- **Role-Based Access** - Different dashboards for client, staff, and admin roles
- **HTML Escaping** - All outputs escaped to prevent XSS attacks

## Database Schema

### Users Table
```sql
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','staff','client') DEFAULT 'client',
  `first_name` VARCHAR(100),
  `middle_name` VARCHAR(100),
  `last_name` VARCHAR(100),
  `age` INT,
  `contact_info` VARCHAR(20),
  `allergies` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Food Table
```sql
CREATE TABLE `food` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100),
  `ingredients` TEXT,
  `price` DECIMAL(10,2),
  `image` VARCHAR(255)
);
```

### Orders Table (Pending Orders)
```sql
CREATE TABLE `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `food_id` VARCHAR(255), -- Comma-separated food IDs
  `status` ENUM('Pending') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
```

### Received Orders Table (Accepted & Completed)
```sql
CREATE TABLE `received_orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `food_id` VARCHAR(255), -- Comma-separated food IDs
  `status` VARCHAR(50) DEFAULT 'Pending', -- Accepted, Completed
  `payment_status` VARCHAR(50) DEFAULT 'Pending', -- Unpaid, Paid
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
```

## Order Status Flow

1. **Client Orders** → Placed in `orders` table with "Pending" status
2. **Staff Reviews** → Staff views queue and clicks "Receive & Accept Order"
3. **Order Transferred** → Moved to `received_orders` table with "Accepted" status
4. **Payment Processed** → Staff collects payment
5. **Order Completed** → Staff clicks "Mark as Completed & Paid"
6. **Final Status** → Order marked as "Completed" in `received_orders` table
7. **Sales Report** → Completed orders appear in Admin Sales Reports by date

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP Control Panel
- Check db.php has correct credentials
- Verify database name is `foodsystem`

### Images Not Displaying
- Ensure `uploads` folder exists and is writable
- Check file permissions: `chmod 755 uploads/`
- Verify image paths in uploaded files

### Login Issues
- Clear browser cookies and cache
- Check that user account exists in database
- Verify password is correct (case-sensitive)

### Order Not Appearing
- Check if order was placed successfully (success message appears)
- Verify client is logged in with correct session
- Check database `orders` table directly in phpMyAdmin

## Future Enhancements

- [ ] Google Sign-In Integration
- [ ] Email Notifications for Orders
- [ ] Payment Gateway Integration (Stripe, PayPal)
- [ ] Mobile App (React Native/Flutter)
- [ ] Real-time Order Updates (WebSockets)
- [ ] Advanced Reporting & Analytics
- [ ] Bulk Food Item Import
- [ ] QR Code Menu System
- [ ] Order Recommendations based on Allergies

## Support & Documentation

For issues or questions:
1. Check the Troubleshooting section above
2. Review SQL schema in phpMyAdmin
3. Check PHP error logs in XAMPP
4. Verify all files are properly uploaded

## License

This project is developed for educational purposes.

## Team

Developed by: Dominic Mari Luis A. Con-ui, Cris Allen Oroc, Ian Bristan M. Pana, Kim Joshua T. Lopez, Jeramae Pace

---

**Last Updated:** December 2025
**Version:** 1.0 (MVP Complete)
