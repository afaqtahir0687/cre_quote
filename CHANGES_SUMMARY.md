# Crelogics ERP - Changes & New Files Documentation

Is document mein un tamam files ki tafseelat darj hain jo haliya feature update (**Shop Business Categories**, **Modular Feature Permissions**, aur **Umrah & Travel Agency 49-Seat Coach Booking System**) ke doran nayi banai gayi hain ya jin mein tabdeeliyan (modifications) ki gayi hain.

---

## 1. 🆕 Nayi Banai Gayi Files (Newly Created Files - Total: 16)

### A. Controllers & Middleware
1. **`app/Http/Controllers/TravelTripController.php`**
   - **Maqsad:** Umrah / Travel trips ki complete management.
   - **Features:** Trip schedule create/edit/delete, 49-seat bus configuration, route (Departure City -> Destination City), rates (Fare with hotel vs Fare without hotel), visual occupancy summary, aur real-time seat status AJAX endpoint (`seatsJson`).

2. **`app/Http/Controllers/TravelBookingController.php`**
   - **Maqsad:** Passenger bookings aur bus seat allocation.
   - **Features:**
     - Strict **Anti-Collision Seat Lock** (aik trip par booked seat kisi doosray passenger ko book nahi hone deta).
     - Live expense breakdown calculation (Fare + Hotel + Extra charges - Discount + Tax = Total, Paid, Balance Due).
     - Direct CRM Customer linking (ya walk-in passenger).
     - Automatic official VAT Invoice generation (Invoices module ke sath integrated).
     - Booking cancel hone par seat ko automatically free karna.

3. **`app/Http/Middleware/CheckShopModule.php`**
   - **Maqsad:** Shop level par module access control.
   - **Features:** Agar Super Admin ne kisi shop ke liye koi module disable kiya ho (maslan `invoices` ya `travel_tourism`), to yeh middleware direct URL access ko block kar ke user ko bilingual warning message ke sath redirect karta hai.

---

4. **`app/Http/Controllers/SuperAdmin/BusinessTypeController.php`**
   - **Maqsad:** Super Admin Business Categories / Industry Types CRUD management.
   - **Features:** Full Create, Read, Update, Delete, Toggle Active status, default module selections, starter departments seeding, aur shop association guards.

---

### B. Database Models
5. **`app/Models/BusinessType.php`**
   - **Maqsad:** Dynamic Business Types & Industries database model.
   - **Features:** Casts for default modules/departments, active/ordered scopes, localized name/description accessors (`name_ar`), aur shops relationship.

6. **`app/Models/TravelTrip.php`**
   - **Maqsad:** Travel Trip model.
   - **Features:** Shop ke sath relation (`belongsTo`), Bookings relation (`hasMany`), seat tracking methods (`bookedSeatNumbers()`, `isSeatBooked()`, `availableSeatsCount()`), aur unique trip code generator (`TRP-YYYYMMDD-XXX`).

5. **`app/Models/TravelBooking.php`**
   - **Maqsad:** Passenger Booking model.
   - **Features:** Relations with Shop, Trip, Customer, Invoice, aur Creator. Status tracking scopes aur reference number generation (`BK-YYYYMMDD-XXX`).

---

### C. Database Migrations
6. **`database/migrations/2026_11_15_140000_add_business_type_and_modules_to_shops_table.php`**
   - **Maqsad:** `shops` table mein 2 naye columns add kiye:
     - `business_type`: Shop ki industry category (default: `printing_advertising`).
     - `enabled_modules`: JSON array jo shop ke active modules ki list store karta hai.

7. **`database/migrations/2026_11_15_141000_create_travel_and_umrah_tables.php`**
   - **Maqsad:** 2 nayi tables banayin:
     - `travel_trips`: Bus capacity (default 49), routes, dates, hotel rates.
     - `travel_bookings`: Passenger info, allocated seat number, package type (with/without hotel), financial breakdown, aur `(travel_trip_id, seat_number)` par unique compound anti-collision indexes.

8. **`database/migrations/2026_11_15_162000_create_business_types_table.php`**
   - **Maqsad:** Nayi `business_types` table create ki (slug, name, name_ar, icon, description, default_modules, default_departments, sort_order, is_active).

---

### D. Blade Views (Umrah & Travel System & Business Types Management)
9. **`resources/views/super_admin/business_types/index.blade.php`**
   - Super Admin Business Types datatable listing, module pills, shop count badge, active toggle switch, edit/delete actions.

10. **`resources/views/super_admin/business_types/create.blade.php`**
    - Super Admin form to create a new category (name, Arabic name, icon picker, description, default module checkboxes, starter departments).

11. **`resources/views/super_admin/business_types/edit.blade.php`**
    - Edit existing category details, adjust default module presets, and starter departments.

12. **`resources/views/travel/trips/index.blade.php`**
   - Trips ki listing table, KPI cards (Total Trips, Active Bookings, Total Revenue, Avg Occupancy), search aur status filters.

9. **`resources/views/travel/trips/create.blade.php`**
   - Nayi Umrah trip schedule karne ka form (Bus name, plate number, total seats: 49, hotel info, departure/return dates, with-hotel aur without-hotel pricing).

10. **`resources/views/travel/trips/edit.blade.php`**
    - Existing trip ke details aur schedule update karne ka form.

11. **`resources/views/travel/trips/show.blade.php`**
    - **Interactive Visual 49-Seat Coach Bus Map:**
      - 11 rows of 4 seats (2 Left + Aisle + 2 Right = 44 seats) + Back Bench of 5 seats = **Total 49 seats**.
      - Green = Available, Red = Booked (passenger info tooltip ke sath), Driver cabin, front entrance, center exit.
      - Niche complete **Passenger Manifest Table** (list of all booked travelers).

12. **`resources/views/travel/bookings/index.blade.php`**
    - Tamam passenger bookings ki list, payment status badges (Paid / Partial / Unpaid), search bar, trip filter.

13. **`resources/views/travel/bookings/create.blade.php`**
    - **Seat Booking Form:**
      - Interactive live 49-seat bus map jahan click karke seat number select hota hai.
      - Passenger personal details (Name, Phone, ID/Passport/Iqama, Gender).
      - Package selector (With Hotel vs Without Hotel) jo dynamic hotel aur room type fields show karta hai.
      - Live financial calculator (Fare, Hotel, Extra, Discount, Tax, Total, Paid, Balance Due).
      - Optional checkbox: Auto-generate official VAT invoice.

14. **`resources/views/travel/bookings/show.blade.php`**
    - Booking summary card, passenger profile, seat number badge, hotel accommodation details, financial settlement, action buttons (Print Voucher, Edit, View Invoice, Cancel Booking).

15. **`resources/views/travel/bookings/voucher.blade.php`**
    - **Printable A4 Boarding Pass / Ticket Voucher:**
      - Clean print layout (`@media print`).
      - High-visibility seat number badge (`#XX`), bus name, departure point, destination, departure schedule, passenger details, financial receipt, barcode/QR area, aur passenger guidelines.

16. **`resources/views/travel/bookings/edit.blade.php`**
    - Passenger details update karne, seat number switch karne (anti-collision validation ke sath), ya payment adjust karne ka form.

---

## 2. ✏️ Modify Shuda Files (Existing Files Updated - Total: 10)

1. **`app/Models/Shop.php`**
   - Added fillable: `business_type`, `enabled_modules`.
   - Cast: `enabled_modules => 'array'`.
   - Helper methods: `availableBusinessTypes()`, `availableModules()`, `defaultModulesForType()`, `hasModule()`, `travelTrips()`, `travelBookings()`.

2. **`app/Http/Controllers/SuperAdmin/ShopController.php`**
   - `create()`: Business categories aur all modules list view ko pass ki.
   - `store()`: Selected category aur enabled modules validate aur save kiye; Umrah shops ke liye specialized departments (Transport, Ticketing, Hotels, Customer Relations) auto-seed kiye.
   - `edit()` & `update()`: Shop ki category aur permissions switchboard load aur update karne ka logic add kiya.

3. **`app/Helpers/helpers.php`**
   - Added helper function `shop_has_module(string $moduleKey): bool`.
   - Added helper function `shop_business_type(): string`.
   - Multi-tenant aware (Super Admin jab global view mein hota hai to platform management ke liye all modules visible rehte hain).

4. **`bootstrap/app.php`**
   - Middleware alias register kiya: `'shop.module' => \App\Http\Middleware\CheckShopModule::class`.

5. **`routes/web.php`**
   - Travel & Umrah routes register kiye:
     - `travel/trips/*` (index, create, store, show, edit, update, destroy, seats-json).
     - `travel/bookings/*` (index, create, store, show, edit, update, cancel, destroy, voucher).
   - Tamam routes ko `shop.module:travel_tourism` middleware ke tehat protect kiya.

6. **`resources/views/partials/sidebar.blade.php`**
   - Tamam standard modules (Job Orders, Quotations, Invoices, Delivery Notes, Customers, Products, Services, Categories, Units, Employees, Departments) ko `@if(shop_has_module('...'))` check ke andar wrap kiya.
   - Naya **Umrah & Travel Agency** sidebar navigation section add kiya (Trips, Bookings, New Booking).

7. **`resources/views/super_admin/shops/create.blade.php`**
   - Industry Category selection cards add kiye (with icons & badges).
   - Modular Permissions switchboard add kiya (checkboxes + Select All / Deselect All buttons).
   - JavaScript code add kiya jo category change hone par relevant modules automatically select/deselect kar deta hai.

8. **`resources/views/super_admin/shops/edit.blade.php`**
   - Shop update screen par Industry Category aur active Modular Permissions switchboard add kiya taake Super Admin kisi b waqt kisi shop ke modules on/off kar sakay.

9. **`resources/views/super_admin/shops/index.blade.php`**
   - Shops list table mein har shop ke naam ke sath uski Business Category ka colored badge add kiya.

10. **`lang/ar.json`**
    - 150+ naye bilingual Arabic translations add kiye (Umrah, 49-Seat coach, passenger guidelines, seat statuses, room sharing types, financial breakdown, categories, modular permissions).

---

## 3. 🛠 Terminal se File Changes Check Karne Ki Commands

Aap apne terminal / command prompt par yeh commands chala kar kisi b waqt changes dekh saktay hain:

```bash
# 1. Kon si file add (A) hui aur kon si modify (M) hui:
git show --name-status -1

# 2. Har file mein kitni lines change huin (Summary):
git show --stat -1

# 3. Exact line-by-line code changes dekhne ke liye:
git show -1

# 4. Kisi specific file ka difference dekhne ke liye:
git diff HEAD~1 HEAD app/Models/Shop.php
```
