# Quick Start Guide - Allergen Checker System

## 5-Minute Setup

### Step 1: Database Import
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create new database: `foodsystem`
3. Click "Import" → Select `foodsystem.sql` → Click "Import"

### Step 2: File Setup
1. Files should be in: `c:\xampp\htdocs\Allergen_Checker_System\`
2. Create folder: `uploads` (for food images)

### Step 3: Start Services
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL

### Step 4: Access System
1. Open browser: http://localhost/Allergen_Checker_System/
2. Login with test account:
   - Username: `admin`
   - Password: (you'll need to check the database for the hashed password or create a new admin)

---

## Test Accounts

### Admin Account
- Username: `admin`
- Role: Administrator
- Access: Food management, user management, sales reports

### Staff Accounts
- Username: `Kim`, `skibidi`, `cris`
- Role: Staff/Cashier
- Access: Order queue, payment processing

### Client Account
- Username: `Dominic`
- Role: Client
- Access: Food ordering, profile management
- Allergies: Dairy, Gluten, Peanuts

---

## Quick Features Overview

### For Clients
1. **Register** → Go to login page, click "Create Client Account"
2. **Add Allergies** → Dashboard → My Profile → Add Allergies
3. **Order Food** → Dashboard → Order Food → Select items → Place Order
4. **Track Order** → Dashboard → My Orders → View status

### For Staff
1. **View Orders** → Staff Dashboard → Order Queue
2. **Accept Order** → Click "Receive & Accept Order"
3. **Complete Order** → Go to Payment & Completion → Mark as Completed

### For Admin
1. **Manage Food** → Admin Dashboard → Manage Food Menu
2. **Add New Item** → Fill form → Upload image → Add Food Item
3. **View Sales** → Admin Dashboard → Sales Reports → Select date
4. **Manage Users** → Admin Dashboard → Manage Users → Edit/Delete

---

## Login Credentials Reference

| Username | Password | Role | Purpose |
|----------|----------|------|---------|
| admin | (bcrypt hash) | Admin | System administration |
| Dominic | (bcrypt hash) | Client | Test client account |
| Kim | (bcrypt hash) | Staff | Test staff account |

**Note:** Passwords are hashed with bcrypt. Reset passwords by:
1. Open phpMyAdmin
2. Find user in `users` table
3. Generate new hash: Use online bcrypt generator or PHP: `password_hash('newpass', PASSWORD_DEFAULT)`
4. Update password field directly

---

## Key Features at a Glance

✅ **Client Features**
- Full profile management (name, age, contact)
- Allergy tracking with real-time warnings
- Browse foods with allergen highlighting
- Order history and status tracking
- Easy order cancellation

✅ **Staff Features**
- View pending orders with client allergies
- Accept orders from queue
- Process payments
- Mark orders as complete

✅ **Admin Features**
- Complete food menu management
- User account management
- Sales reports by date
- System-wide order monitoring
- Staff account creation

✅ **Security**
- Encrypted passwords (bcrypt)
- SQL injection prevention (prepared statements)
- Session-based authentication
- Role-based access control

---

## File Organization

```
Allergen_Checker_System/
├── Core Files
│   ├── login.php              (Authentication)
│   ├── logout.php             (Session cleanup)
│   └── db.php                 (Database connection)
│
├── Client Pages
│   ├── client_register.php    (Sign up)
│   ├── client_dashboard.php   (Home)
│   ├── client_profile.php     (Profile & allergies)
│   ├── client_orders.php      (Order history)
│   └── kiosk.php              (Food ordering)
│
├── Staff Pages
│   ├── staff_dashboard.php    (Home)
│   ├── staff_queue.php        (Orders to serve)
│   ├── update_order.php       (Payment & completion)
│   └── staff_register.php     (Create staff)
│
├── Admin Pages
│   ├── admin_dashboard.php    (Home)
│   ├── admin_food.php         (Food management)
│   ├── admin_payments.php     (Sales reports)
│   ├── view_users.php         (User management)
│   └── queue.php              (Order monitoring)
│
├── Assets
│   ├── styles.css             (Styling)
│   └── uploads/               (Food images)
│
└── Documentation
    ├── README.md              (Full guide)
    ├── foodsystem.sql         (Database schema)
    ├── MIGRATION_UPDATE.sql   (Schema updates)
    └── IMPLEMENTATION_REPORT.md (Verification)
```

---

## Common Tasks

### Add a New Food Item
1. Login as admin
2. Go to "Manage Food Menu"
3. Fill in: Name, Ingredients, Price
4. Upload image (optional)
5. Click "Add Food Item"

### Create New Staff Account
1. Login as admin
2. Go to "Create Staff Account"
3. Enter: Username, Password, Name (optional)
4. Click "Create Staff Account"

### View Today's Sales
1. Login as admin
2. Go to "Sales Reports"
3. Today's date is pre-selected
4. View orders and total revenue

### Cancel an Order
1. Login as client
2. Go to "My Orders"
3. Find the pending order
4. Click "Cancel Order"

---

## Troubleshooting

**Q: "Database connection failed"**
- A: MySQL not running. Start it in XAMPP Control Panel

**Q: "File not found" error**
- A: Check files are in correct folder: `c:\xampp\htdocs\Allergen_Checker_System\`

**Q: Can't upload food images**
- A: Create `uploads` folder with write permissions: `mkdir uploads`

**Q: Login not working**
- A: Check username/password in phpMyAdmin `users` table

**Q: Can't see allergies**
- A: Make sure allergies are separated by commas (e.g., "Dairy,Gluten,Peanuts")

---

## Next Steps

1. ✅ Test all user roles (client, staff, admin)
2. ✅ Add test food items
3. ✅ Place a test order
4. ✅ Process order through staff
5. ✅ View sales report
6. ✅ Verify allergen detection works

---

## Production Deployment

To deploy on a live server (not XAMPP):

1. **Choose Hosting** → InfinityFree, Heroku, AWS, DigitalOcean
2. **Upload Files** → Use FTP to upload all files
3. **Setup Database** → Create `foodsystem` database and import SQL
4. **Update db.php** → Set correct hostname, username, password
5. **Create uploads folder** → Ensure it's writable
6. **Test** → Access your domain and verify all functions work

---

## Support Resources

- Check README.md for detailed documentation
- Review IMPLEMENTATION_REPORT.md for feature verification
- Check phpMyAdmin directly to verify database structure
- Test login with sample accounts provided above

---

**Ready to use! Start with the 5-minute setup above and enjoy the system.** 🍔🥗

Last Updated: December 7, 2025
