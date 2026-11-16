# 🏢 Crelogics ERP - New Shop Creation Workflow

Is document mein mukammal tafseel darj hai ke jab Super Admin **Add New Shop** (`/super-admin/shops/create`) se nayi shop create kare ga to shuru se aakhri step tak system ke andar kya kya hoga.

---

## 📌 Workflow Overview (Khulasa)

```
[Super Admin Creates Shop]
        │
        ├─► 1. Industry / Category Choose karta hai (e.g. Umrah & Travel, Advertising, General Trading)
        ├─► 2. Modules Auto-Tick hotay hain (Super Admin mazeed on/off b kar sakta hai)
        ├─► 3. Shop Profile & Country Phone enter karta hai
        ├─► 4. Shop Admin Login Credentials set karta hai
        └─► 5. Subscription Package & Cash assign karta hai
        │
[Submit Button Click]
        │
        ├─► Database mein Shop + Settings create hoti hain
        ├─► Us Category ke Starter Departments auto-seed hotay hain
        ├─► Admin User create ho kar shop ke sath link hota hai
        └─► Subscription activate ho jati hai
        │
[Shop Admin Login]
        │
        ├─► Sirf Allowed Modules sidebar mein nazar aatay hain
        ├─► Disabled modules mukammal hide aur protected hotay hain
        └─► Umrah shop ko 49-Seat Bus Booking + Boarding Pass milta hai
```

---

## 📝 Step 1: Shop Creation Screen Par Super Admin Kya Kare Ga?

### 1. Business Category / Industry Type Ka Intekhab:
Super Admin ke samnay 100% database-driven interactive cards aatay hain (jo [Shop Categories](file:///c:/xampp/htdocs/cre_quote/resources/views/super_admin/business_types/index.blade.php) se aatay hain):
- **Printing, Signage & Advertising**
- **Umrah, Travel & Tourism Agency**
- **General Trading & Retail Store**
- **Digital & Professional Services**
- **Custom / Full Enterprise Suite**
- *(Ya koi b nayi category jo Super Admin ne khud add ki ho)*

### 2. Modular Permissions Ka Auto-Selection:
- Jaisay hi Super Admin kisi card par click kare ga (maslan **Umrah, Travel & Tourism Agency**), to niche Section 2 ke andar us category ke **relevent modules khud-ba-khud tick (ON) ho jayen gy**.
- Ghair zaroori modules auto-untick ho jayen gy.
- **Super Admin ka Control:** Super Admin apni marzi se kisi bhi checkbox ko mazeed on ya off kar sakta hai (Select All, Deselect All, ya Reset Defaults).

### 3. Branch Profile & Identity:
- **Shop / Branch Name:** (e.g. Al-Noor Travel & Umrah)
- **URL Slug:** (Auto-generate hota hai)
- **Shop Email & Shop Phone:** Naye country picker ke sath jahan country code `[🇸🇦 +966 ▾]` aur number `[50 123 4567]` aik hi line mein perfect format hotay hain.
- **Address, City, Country**
- **Currency Symbol, Quote Prefix, Invoice Prefix**
- **Company Logo:** (PNG/JPG upload with instant preview)

### 4. Primary Shop Administrator:
- Admin ka Full Name, Username, Email, aur Password.
- Yeh user is nayi shop ka pehla main Super-User banay ga.

### 5. Package & Subscription:
- Package select kare ga (Trial 7-Days, Monthly, Yearly).
- Duration Days aur Cash Paid (Cash In Hand).

---

## ⚙️ Step 2: Form Submit Honay Par Backend Mein Kya Hoga?

Jab Super Admin **Create Shop & Admin** button par click kare ga, to backend controller (`ShopController@store`) aik safe Database Transaction ke andar yeh 5 kaam kare ga:

1. **`shops` Table:**
   - Naya shop record banay ga.
   - `business_type` column mein selected category save hogi.
   - `enabled_modules` JSON column mein Super Admin ke allow kiye huay modules ka array save hoga.
   - Subscription dates (`subscription_starts_at`, `subscription_expires_at`) active set hongi.

2. **`settings` Table:**
   - Is shop ka apna isolated settings record banay ga jahan currency, logo, email, phone, address aur prefixes save hongi.

3. **Starter Departments Auto-Seeding (Dynamic):**
   - System database ki `business_types` table se us category ke `default_departments` check kare ga aur foran is shop ke liye auto-create kar dega:
     - **Umrah Shop ke liye:** *Transport & Fleet Operations, Ticketing & Visa Operations, Hotels & Accommodations, Customer Relations*.
     - **Advertising Shop ke liye:** *Printing & Production, Graphic Design & Creative, Installation & Fabrication, Sales & Marketing*.
     - **Retail Shop ke liye:** *Sales & Showroom, Warehouse & Inventory, Accounts & Finance*.

4. **`users` Table (Shop Admin Account):**
   - Shop admin ka user create hoga aur uski `shop_id` is nayi shop ke sath permanently link ho jayegi.
   - Role automatically `admin` assign hoga.

5. **`subscriptions` Table:**
   - Package subscription record create hoga aur status `active` ho jayega.

---

## 👤 Step 3: Jab Shop Admin Apne Account Mein Login Karay Ga

Shop Admin apne username aur password se login kare ga:

### 1. Tailored Navigation Sidebar (Sirf Allowed Modules):
Sidebar helper `shop_has_module()` check karta hai ke Super Admin ne is shop ko kon kon se modules diye hain:
- Jo modules allow kiye gaye hain, sirf wahi sidebar mein nazar ayenge.
- **Example 1 (Umrah Shop):** Sidebar mein *Dashboard*, *Travel & Umrah*, *Customers*, *Invoices*, *Employees*, *Departments* nazar ayenge. Advertising walay *Job Orders* ya *Delivery Notes* bilkul nazar nahi ayenge.
- **Example 2 (Advertising Shop):** Sidebar mein *Job Orders*, *Quotations*, *Invoices*, *Delivery Notes*, *Products*, *Services* nazar ayenge. Umrah wala module hide rahay ga.

### 2. URL Route Security Protection (`CheckShopModule` Middleware):
- Agar koi user browser ke URL bar mein zabardasti kisi disabled page ka link likh kar enter karay ga (maslan disabled invoices ka link):
- To system foran access block kar dega aur user ko message dega:
  > *"This module is not enabled for your shop. Please contact your administrator."*

### 3. Special Industry Workflows:
- **Agar Umrah Module Enabled Hai:**
  - Shop Admin coach buses (49 seats) ki trips schedule kar sakta hai.
  - Interactive Visual Bus Seat Map par click karke seat reserve kar sakta hai.
  - **Anti-Collision Seat Lock:** Agar Seat #14 book ho gayi to koi doosra user usay book nahi kar sakta.
  - **Expense Calculation:** *Without Hotel* (sirf bus fare) ya *With Hotel* (kamra, sharing type, duration) ka live hisab hoga.
  - **Boarding Pass Voucher:** Official A4 Boarding pass aur ticket voucher 1-click par print hoga.

---

## 🔄 Step 4: Future Changes (Super Admin Edit Control)

Agar kisi shop walay ko baad mein koi naya module chahiye ya koi module band karwana ho:
1. Super Admin **Manage Shops** -> **Edit Shop** par jaye ga.
2. Section 2 (Modular Permissions) mein us module ka switch toggle on ya off kare ga.
3. Save karte hi shop ke dashboard par foran wo module activate ya deactivate ho jaye ga.
