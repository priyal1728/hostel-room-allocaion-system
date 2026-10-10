 HOSTEL ROOM ALLOCATION SYSTEM - SETUP GUIDE

=== STEP 1: DATABASE SETUP ===
1. Open your browser and go to: http://localhost/phpmyadmin/
2. Visit: http://localhost/hostel-room-allocation-system/db/setup.php
3. This will automatically create the required database tables

=== STEP 2: FEATURES IMPLEMENTED ===
✓ Student Registration (auth/register.php)
✓ Student Login (auth/login.php)
✓ Student Dashboard (student/dashboard.php)
✓ Student Profile Management (student/profile.php)
✓ Password Change Functionality
✓ Room Request & Allocation (student/room-request.php)
✓ Room Status Display
✓ Logout Functionality

=== STEP 3: FILE STRUCTURE ===
/assets/
  /css/
    - auth.css (Login/Register styles)
    - student.css (Dashboard and Profile styles)

/auth/
  - register.php (Student Registration)
  - login.php (Student Login)
  - logout.php (Logout Handler)

/student/
  - dashboard.php (Main Dashboard)
  - profile.php (Profile & Password Change)
  - room-request.php (Room Allocation)

/includes/
  - config.php (Database configuration)
  - auth.php (Authentication functions)

/db/
  - setup.php (Database setup script)

=== STEP 4: CREDENTIALS FOR TESTING ===
After registration, use the same email and password to login.

Example:
- Email: student@example.com
- Password: password123

=== STEP 5: DATABASE CONFIGURATION ===
Default settings in includes/config.php:
- Database Host: localhost
- Database User: root
- Database Password: (empty)
- Database Name: hostel_db

If your credentials are different, edit includes/config.php

=== STEP 6: HOW TO USE ===
1. Go to: http://localhost/hostel-room-allocation-system/
2. Click "Student Registration" to create account
3. Fill all required fields
4. Click "Register"
5. Go back and login with your credentials
6. Access Dashboard, View Profile, Request Room
7. Request a room and it will be allocated

=== STEP 7: FEATURES DETAILS ===

Registration Page:
- Name, Enrollment Number, Email, Phone
- Branch, Year, Gender, Date of Birth
- Address, Parent Info
- Secure password with confirmation

Login Page:
- Email and password authentication
- Session management
- Redirect to dashboard on success

Dashboard:
- Welcome message
- Room allocation status
- Student information display
- Quick stats (Year, Branch)

Profile Page:
- View and edit personal info
- Cannot edit: Enrollment, Email, Branch, Year, Gender, DOB
- Can edit: Name, Phone, Address, Parent Info
- Password change functionality

Room Request:
- Browse available rooms
- Select and allocate room
- Show room details (Block, Floor, Capacity)
- Occupancy status

=== NEXT STEPS (For Admin Module) ===
Will add:
- Admin Dashboard
- Room Management
- Student Management
- Reports & Analytics

=== DATABASE TABLES CREATED ===
1. students - Student account information
2. rooms - Hostel room details
3. room_allocations - Room assignment records

=== SECURITY FEATURES ===
✓ Password hashing (BCRYPT)
✓ SQL Injection prevention (Prepared Statements)
✓ Session-based authentication
✓ Email validation
✓ Phone number validation
✓ XSS protection (htmlspecialchars)

=== TROUBLESHOOTING ===

Problem: "Connection failed"
Solution: Check if MySQL is running, verify credentials in config.php

Problem: "Table doesn't exist"
Solution: Run: http://localhost/hostel-room-allocation-system/db/setup.php

Problem: "Page shows blank"
Solution: Check PHP error logs, enable error reporting

Problem: "Login not working"
Solution: Verify email is registered, check database entries in phpmyadmin

=== TESTING CHECKLIST ===
✓ Database setup completed
✓ Student can register
✓ Student can login
✓ Dashboard displays correctly
✓ Profile can be edited
✓ Password can be changed
✓ Room can be allocated
✓ Logout works correctly
✓ Session expires properly

All Set! 🎉 Your Student Management system is ready to use!
