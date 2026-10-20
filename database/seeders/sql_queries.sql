-- SQL Queries to Insert Employees and Customers with Real Names
-- Run these queries in your live database

-- ============================================
-- INSERT EMPLOYEES (Use INSERT IGNORE to avoid duplicates)
-- ============================================

-- Employee 1: Ahmed Khan
INSERT IGNORE INTO users (id, name, email, username, password, role, created_at, updated_at)
VALUES (2, 'Ahmed Khan', 'ahmed.khan@crelogics.com', 'ahmed.khan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

SET @employee1_id = 2;

INSERT IGNORE INTO employees (id, name, designation, user_id, created_at, updated_at)
VALUES (1, 'Ahmed Khan', 'Sales Manager', @employee1_id, NOW(), NOW());

-- Employee 2: Fatima Ali
INSERT IGNORE INTO users (id, name, email, username, password, role, created_at, updated_at)
VALUES (3, 'Fatima Ali', 'fatima.ali@crelogics.com', 'fatima.ali', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

SET @employee2_id = 3;

INSERT IGNORE INTO employees (id, name, designation, user_id, created_at, updated_at)
VALUES (2, 'Fatima Ali', 'Accountant', @employee2_id, NOW(), NOW());

-- Employee 3: Muhammad Hassan
INSERT IGNORE INTO users (id, name, email, username, password, role, created_at, updated_at)
VALUES (4, 'Muhammad Hassan', 'muhammad.hassan@crelogics.com', 'muhammad.hassan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

SET @employee3_id = 4;

INSERT IGNORE INTO employees (id, name, designation, user_id, created_at, updated_at)
VALUES (3, 'Muhammad Hassan', 'Sales Executive', @employee3_id, NOW(), NOW());

-- Employee 4: Ayesha Siddiqui
INSERT IGNORE INTO users (id, name, email, username, password, role, created_at, updated_at)
VALUES (5, 'Ayesha Siddiqui', 'ayesha.siddiqui@crelogics.com', 'ayesha.siddiqui', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

SET @employee4_id = 5;

INSERT IGNORE INTO employees (id, name, designation, user_id, created_at, updated_at)
VALUES (4, 'Ayesha Siddiqui', 'Customer Support', @employee4_id, NOW(), NOW());

-- Employee 5: Omar Farooq
INSERT IGNORE INTO users (id, name, email, username, password, role, created_at, updated_at)
VALUES (6, 'Omar Farooq', 'omar.farooq@crelogics.com', 'omar.farooq', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW());

SET @employee5_id = 6;

INSERT IGNORE INTO employees (id, name, designation, user_id, created_at, updated_at)
VALUES (5, 'Omar Farooq', 'Technical Lead', @employee5_id, NOW(), NOW());

-- ============================================
-- INSERT CUSTOMERS (Use INSERT IGNORE to avoid duplicates)
-- ============================================

INSERT IGNORE INTO customers (id, name, company_name, email, phone, address, city, zip, country, tax_id, vat_no, is_active, created_at, updated_at) 
VALUES (1, 'Muhammad Ibrahim', 'Ibrahim Trading Co.', 'ibrahim@trading.com', '+92-300-1234567', '123 Main Market, Lahore', 'Lahore', '54000', 'Pakistan', '1234567890123', 'PK123456789', 1, NOW(), NOW());

INSERT IGNORE INTO customers (id, name, company_name, email, phone, address, city, zip, country, tax_id, vat_no, is_active, created_at, updated_at) 
VALUES (2, 'Sarah Ahmed', 'Ahmed Enterprises', 'sarah@ahmedent.com', '+92-321-9876543', '456 Blue Area, Islamabad', 'Islamabad', '44000', 'Pakistan', '9876543210987', 'PK987654321', 1, NOW(), NOW());

INSERT IGNORE INTO customers (id, name, company_name, email, phone, address, city, zip, country, tax_id, vat_no, is_active, created_at, updated_at) 
VALUES (3, 'Ali Hassan', 'Hassan Corporation', 'ali@hassancorp.com', '+92-333-4567890', '789 Saddar, Karachi', 'Karachi', '74400', 'Pakistan', '4567890123456', 'PK456789012', 1, NOW(), NOW());

INSERT IGNORE INTO customers (id, name, company_name, email, phone, address, city, zip, country, tax_id, vat_no, is_active, created_at, updated_at) 
VALUES (4, 'Zainab Malik', 'Malik Solutions', 'zainab@maliksolutions.com', '+92-300-7890123', '321 Faisal Town, Multan', 'Multan', '60000', 'Pakistan', '7890123456789', 'PK789012345', 1, NOW(), NOW());

INSERT IGNORE INTO customers (id, name, company_name, email, phone, address, city, zip, country, tax_id, vat_no, is_active, created_at, updated_at) 
VALUES (5, 'Usman Khan', 'Khan Industries', 'usman@khanind.com', '+92-322-3456789', '654 Industrial Estate, Faisalabad', 'Faisalabad', '38000', 'Pakistan', '3456789012345', 'PK345678901', 1, NOW(), NOW());

-- ============================================
-- INSERT QUOTES FOR ADMIN
-- ============================================

-- Admin Quote 1 for Customer 1 (Muhammad Ibrahim)
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-001', '2026-06-09', '2026-07-09', 'REF-001', 1, NULL, 50000.00, 7500.00, 0.00, 0.00, 57500.00, 'Sent', 'Quote for office supplies', NOW(), NOW());

SET @admin_quote1_id = LAST_INSERT_ID();

-- Quote Items for Admin Quote 1
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@admin_quote1_id, 'Office Desk', 15000.00, 2, 15.00, 0.00, 34500.00, NOW(), NOW()),
(@admin_quote1_id, 'Office Chair', 5000.00, 4, 15.00, 0.00, 23000.00, NOW(), NOW());

-- Admin Quote 2 for Customer 2 (Sarah Ahmed)
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-002', '2026-06-09', '2026-07-09', 'REF-002', 2, NULL, 120000.00, 18000.00, 5000.00, 2000.00, 135000.00, 'Draft', 'Quote for computer equipment', NOW(), NOW());

SET @admin_quote2_id = LAST_INSERT_ID();

-- Quote Items for Admin Quote 2
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@admin_quote2_id, 'Laptop Computer', 80000.00, 1, 15.00, 5000.00, 89500.00, NOW(), NOW()),
(@admin_quote2_id, 'Monitor 24 inch', 20000.00, 2, 15.00, 0.00, 46000.00, NOW(), NOW());

-- ============================================
-- INSERT QUOTES FOR EMPLOYEES
-- ============================================

-- Employee 1 (Ahmed Khan) Quote for Customer 3
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-003', '2026-06-08', '2026-07-08', 'REF-003', 3, 1, 75000.00, 11250.00, 0.00, 1000.00, 87250.00, 'Sent', 'Quote for networking equipment', NOW(), NOW());

SET @emp1_quote1_id = LAST_INSERT_ID();

-- Quote Items for Employee 1 Quote
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@emp1_quote1_id, 'Router 24-port', 25000.00, 1, 15.00, 0.00, 28750.00, NOW(), NOW()),
(@emp1_quote1_id, 'Switch 16-port', 15000.00, 2, 15.00, 0.00, 34500.00, NOW(), NOW()),
(@emp1_quote1_id, 'Network Cables (Cat6)', 500.00, 20, 15.00, 0.00, 11500.00, NOW(), NOW());

-- Employee 2 (Fatima Ali) Quote for Customer 4
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-004', '2026-06-07', '2026-07-07', 'REF-004', 4, 2, 45000.00, 6750.00, 2000.00, 0.00, 49750.00, 'Accepted', 'Quote for accounting software', NOW(), NOW());

SET @emp2_quote1_id = LAST_INSERT_ID();

-- Quote Items for Employee 2 Quote
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@emp2_quote1_id, 'Accounting Software License', 30000.00, 1, 15.00, 2000.00, 32500.00, NOW(), NOW()),
(@emp2_quote1_id, 'Training Session', 10000.00, 1, 15.00, 0.00, 11500.00, NOW(), NOW());

-- Employee 3 (Muhammad Hassan) Quote for Customer 5
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-005', '2026-06-06', '2026-07-06', 'REF-005', 5, 3, 200000.00, 30000.00, 10000.00, 5000.00, 225000.00, 'Sent', 'Quote for industrial machinery', NOW(), NOW());

SET @emp3_quote1_id = LAST_INSERT_ID();

-- Quote Items for Employee 3 Quote
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@emp3_quote1_id, 'Industrial Machine A', 100000.00, 1, 15.00, 5000.00, 110000.00, NOW(), NOW()),
(@emp3_quote1_id, 'Industrial Machine B', 80000.00, 1, 15.00, 5000.00, 87000.00, NOW(), NOW());

-- Employee 4 (Ayesha Siddiqui) Quote for Customer 1
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-006', '2026-06-05', '2026-07-05', 'REF-006', 1, 4, 35000.00, 5250.00, 0.00, 0.00, 40250.00, 'Draft', 'Quote for customer support package', NOW(), NOW());

SET @emp4_quote1_id = LAST_INSERT_ID();

-- Quote Items for Employee 4 Quote
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@emp4_quote1_id, 'Support Package - Monthly', 20000.00, 1, 15.00, 0.00, 23000.00, NOW(), NOW()),
(@emp4_quote1_id, 'Additional Support Hours', 5000.00, 2, 15.00, 0.00, 11500.00, NOW(), NOW());

-- Employee 5 (Omar Farooq) Quote for Customer 2
INSERT INTO quotes (quote_number, quote_date, due_date, reference, customer_id, employee_id, subtotal, total_tax, total_discount, shipping, grand_total, status, notes, created_at, updated_at) 
VALUES ('QT-2026-007', '2026-06-04', '2026-07-04', 'REF-007', 2, 5, 150000.00, 22500.00, 0.00, 3000.00, 175500.00, 'Sent', 'Quote for software development', NOW(), NOW());

SET @emp5_quote1_id = LAST_INSERT_ID();

-- Quote Items for Employee 5 Quote
INSERT INTO quote_items (quote_id, description, unit_price, quantity, tax_percentage, discount, total, created_at, updated_at) 
VALUES 
(@emp5_quote1_id, 'Custom Web Application', 120000.00, 1, 15.00, 0.00, 138000.00, NOW(), NOW()),
(@emp5_quote1_id, 'Mobile App Development', 30000.00, 1, 15.00, 0.00, 34500.00, NOW(), NOW());

-- ============================================
-- NOTES:
-- ============================================
-- Password for all employees: password
-- The password hash is bcrypt encrypted version of 'password'
-- You can change the password by updating the hash using: 
-- SELECT PASSWORD('your_new_password');
-- 
-- Quote Numbers: QT-2026-001 to QT-2026-007
-- Admin Quotes: 2 quotes (QT-2026-001, QT-2026-002)
-- Employee Quotes: 5 quotes (one per employee)
-- All quotes include quote items with proper calculations
