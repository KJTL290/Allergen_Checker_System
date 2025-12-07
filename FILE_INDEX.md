# File Index & Reference Guide

## 📋 Quick File Reference

### 🔐 Authentication & Configuration (2 files)
| File | Purpose | Key Features |
|------|---------|--------------|
| `login.php` | User login & registration | Prepared statements, bcrypt hashing, role-based routing |
| `logout.php` | Session termination | Secure session cleanup |
| `db.php` | Database connection | Error handling, charset setup |

### 👥 Client Pages (5 files)
| File | Purpose | Key Features |
|------|---------|--------------|
| `client_register.php` | New account creation | Full profile fields, validation, styling |
| `client_dashboard.php` | Client home page | Navigation cards, modern design |
| `client_profile.php` | Profile & allergy management | Full CRUD for profile, allergen management, delete account |
| `kiosk.php` | Food ordering interface | Allergen detection, multiple selection, order placement |
| `client_orders.php` | Order history & tracking | Status display, cancellation, allergen warnings |

### 👨‍💼 Staff Pages (4 files)
| File | Purpose | Key Features |
|------|---------|--------------|
| `staff_dashboard.php` | Staff home page | Navigation to queue and payment |
| `staff_queue.php` | Pending orders view | Client info, allergies display, accept orders |
| `staff_register.php` | Create staff accounts | Admin only, username & password |
| `update_order.php` | Payment & order completion | Accept, process, complete orders |

### ⚙️ Admin Pages (5 files)
| File | Purpose | Key Features |
|------|---------|--------------|
| `admin_dashboard.php` | Admin control panel | Organized card layout, main hub |
| `admin_food.php` | Food menu management | Full CRUD, image upload, responsive grid |
| `admin_payments.php` | Sales reports | Date filtering, statistics, revenue tracking |
| `view_users.php` | User management | Edit all fields, delete accounts, role management |
| `queue.php` | System order monitoring | View all orders (pending, accepted, completed) |

### 🎨 Styling & Assets (1 file)
| File | Purpose | Key Features |
|------|---------|--------------|
| `styles.css` | Global styling | Responsive design, colors, transitions, mobile-optimized |

### 📊 Database Files (2 files)
| File | Purpose | Contents |
|------|---------|----------|
| `foodsystem.sql` | Complete database schema | 4 tables, test data, auto-increment settings |
| `MIGRATION_UPDATE.sql` | Schema migration script | Adds profile fields to existing database |

### 📚 Documentation (4 files)
| File | Purpose | Read Time |
|------|---------|-----------|
| `README.md` | Complete user guide | ~15 minutes |
| `QUICK_START.md` | Fast setup guide | ~5 minutes |
| `IMPLEMENTATION_REPORT.md` | PRD verification | ~10 minutes |
| `PROJECT_COMPLETION.md` | Final summary | ~5 minutes |

### ⚠️ Additional Files
- `makehash.php` - Legacy password hashing utility (kept for reference)
- `.git/` - Version control directory

**Total Active Files:** 25 files (17 PHP + 1 CSS + 2 SQL + 4 Documentation + 1 other)

---

## 🚀 Quick Navigation

### To Set Up Database
1. Open `foodsystem.sql` in phpMyAdmin
2. Or run `MIGRATION_UPDATE.sql` for existing database

### To Understand the System
1. Start with `README.md` (complete guide)
2. Then `QUICK_START.md` (5-minute overview)
3. Check `IMPLEMENTATION_REPORT.md` (verify PRD)

### To Deploy
1. Copy all files to web server
2. Import `foodsystem.sql`
3. Update credentials in `db.php`
4. Create `uploads/` folder
5. Access from browser

### To Troubleshoot
1. Check `README.md` Troubleshooting section
2. Verify `db.php` connection
3. Check database in phpMyAdmin
4. Review error messages in browser console

---

## 📱 User Workflows

### Client Workflow Path
```
login.php → client_dashboard.php 
    ↓
    ├→ client_profile.php (manage profile & allergies)
    ├→ kiosk.php (order food)
    └→ client_orders.php (view & cancel orders)
```

### Staff Workflow Path
```
login.php → staff_dashboard.php
    ├→ staff_queue.php (view orders)
    └→ update_order.php (process payment)
```

### Admin Workflow Path
```
login.php → admin_dashboard.php
    ├→ admin_food.php (manage food items)
    ├→ view_users.php (manage users)
    ├→ admin_payments.php (view sales)
    ├→ queue.php (monitor orders)
    └→ staff_register.php (create staff)
```

---

## 🔒 Security Features by File

| File | Security Measures |
|------|------------------|
| login.php | Prepared statements, bcrypt hash verification |
| client_register.php | Input trim & validation, prepared statements |
| client_profile.php | HTML escaping, prepared statements, session check |
| kiosk.php | Integer casting for IDs, session verification |
| client_orders.php | Prepared statements, user ID validation |
| staff_queue.php | Role verification, prepared statements |
| update_order.php | Integer casting, role check, prepared statements |
| admin_food.php | File upload handling, prepared statements |
| view_users.php | Prepared statements, role filtering |
| admin_payments.php | Prepared statements, date validation |

---

## 🗄️ Database Schema Reference

### Users Table (Primary)
```
id (PK) | username | password | role | first_name | middle_name | 
last_name | age | contact_info | allergies | created_at
```

### Food Table
```
id (PK) | name | ingredients | price | image
```

### Orders Table (Pending)
```
id (PK) | user_id (FK) | food_id | status | created_at
```

### Received Orders Table (Accepted/Completed)
```
id (PK) | user_id (FK) | food_id | status | payment_status | created_at
```

---

## 📝 Code Quality Checklist

### Prepared Statements ✅
- login.php - 2 prepared statements
- client_register.php - 1 prepared statement
- client_profile.php - Multiple prepared statements
- kiosk.php - 1 prepared statement
- client_orders.php - 3 prepared statements
- staff_queue.php - 1 prepared statement
- update_order.php - 3 prepared statements
- admin_food.php - 3 prepared statements
- view_users.php - 2 prepared statements
- admin_payments.php - 1 prepared statement
- queue.php - 3 prepared statements

### Input Validation ✅
- All POST/GET inputs trimmed
- Integer values cast with intval()
- String values sanitized with htmlspecialchars()
- Password verification with password_verify()

### Session Management ✅
- session_start() on all protected pages
- Role verification on dashboards
- Proper logout with session_destroy()
- Session variables for user identification

### Error Handling ✅
- Database connection errors caught
- Query failures handled
- User-friendly error messages
- No sensitive information exposed

---

## 🎯 Feature Completeness Matrix

| Feature | File | Status |
|---------|------|--------|
| User Registration | client_register.php | ✅ Complete |
| User Login | login.php | ✅ Complete |
| Profile Management | client_profile.php | ✅ Complete |
| Allergy Management | client_profile.php | ✅ Complete |
| Food Ordering | kiosk.php | ✅ Complete |
| Allergen Detection | kiosk.php, client_orders.php | ✅ Complete |
| Order Tracking | client_orders.php | ✅ Complete |
| Order Cancellation | client_orders.php | ✅ Complete |
| Order Queue (Staff) | staff_queue.php | ✅ Complete |
| Order Processing | update_order.php | ✅ Complete |
| Payment Handling | update_order.php | ✅ Complete |
| Food Management | admin_food.php | ✅ Complete |
| User Management | view_users.php | ✅ Complete |
| Staff Creation | staff_register.php | ✅ Complete |
| Order Monitoring | queue.php | ✅ Complete |
| Sales Reports | admin_payments.php | ✅ Complete |
| Role-Based Access | All files | ✅ Complete |
| Responsive Design | styles.css | ✅ Complete |

---

## 🔄 Development Notes

### Code Style
- PHP follows PSR-12 conventions
- Meaningful variable names
- Function/file names describe purpose
- Comments for complex logic

### Browser Compatibility
- Modern CSS (Grid, Flexbox)
- No browser prefixes needed
- Mobile-first responsive design
- Touch-friendly button sizes

### Database Performance
- Indexes on primary keys
- Foreign keys defined
- Auto-increment for IDs
- UTF-8 encoding for all text

### Scalability Considerations
- Food items: Unlimited (INT primary key)
- Users: Unlimited (INT primary key)
- Orders: Unlimited (INT primary key)
- String fields: TEXT for large content (allergies, ingredients)

---

## 📞 Support Resources

### For Installation Issues
→ See `README.md` Installation section

### For Quick Setup
→ See `QUICK_START.md` (5 minutes)

### For Feature Verification
→ See `IMPLEMENTATION_REPORT.md`

### For Complete Information
→ See `PROJECT_COMPLETION.md`

### For Code Issues
→ Check prepared statements usage
→ Verify session setup
→ Check database connection in `db.php`

---

## 🎓 Learning Resources

This project demonstrates:
- ✅ PHP OOP basics
- ✅ Prepared statement usage
- ✅ Session management
- ✅ Password hashing (bcrypt)
- ✅ Responsive CSS design
- ✅ Database design patterns
- ✅ Role-based access control
- ✅ Form validation
- ✅ File upload handling
- ✅ SQL JOINs and queries

---

## 🚀 Production Checklist

Before deploying to production:

- [ ] Backup existing database
- [ ] Test all features in staging
- [ ] Update `db.php` with production credentials
- [ ] Create `uploads` folder with proper permissions
- [ ] Set up SSL/HTTPS
- [ ] Enable error logging
- [ ] Disable error display to users
- [ ] Test on target PHP version
- [ ] Verify image upload path
- [ ] Test email functionality (if added)
- [ ] Monitor performance metrics
- [ ] Set up database backups
- [ ] Enable access logs
- [ ] Test cross-browser compatibility

---

## 📈 Version History

| Version | Date | Status | Notes |
|---------|------|--------|-------|
| 1.0 | Dec 7, 2025 | ✅ Complete | MVP complete, all PRD requirements met |

---

**For any questions or issues, refer to the comprehensive documentation provided with this project.**

Last Updated: December 7, 2025
