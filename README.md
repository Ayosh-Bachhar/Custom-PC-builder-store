````markdown
# Custom PC Builder Store

A database-driven academic web application developed for **CSE370: Database Systems** at **BRAC University**.

**Custom PC Builder Store** is a PHP and MySQL-based web application that allows users to browse computer components, build a custom PC, manage carts, place orders, generate invoices, and interact with separate Customer, Staff, and Owner/Admin dashboards.

---

## Project Information

- **Course:** CSE370 - Database Systems
- **University:** BRAC University
- **Project Title:** Custom PC Builder Store
- **Semester:** Spring 2026
- **Developer:** R. N. Bachhar Ayosh

---

## Technology Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL
- **Server Environment:** XAMPP / Apache
- **Database Management Tool:** phpMyAdmin

---

## System Overview

The project is designed for a computer hardware store where customers can browse PC components, search and filter products, build a custom PC, add selected components to cart, place orders, and generate invoices.

The system also includes Staff and Owner/Admin modules for managing products, orders, users, reviews, and staff IDs.

---

## User Roles

The system supports three types of users:

1. **Customer**
2. **Staff**
3. **Owner/Admin**

Each role has separate access permissions and dashboard functionality.

---

## Key Features

### Authentication and Access Control

- Role selection gateway
- Customer registration
- Staff registration using valid Staff ID
- Owner registration using Owner Code
- Login and logout system
- Password hashing using PHP `password_hash()`
- Password verification using PHP `password_verify()`
- Session-based role access control

---

### Customer Features

- Customer dashboard
- Product browsing
- Product search by name, brand, or model
- Category-wise product listing
- Brand and price filtering
- Product details page
- Product specifications display
- Product reviews and ratings
- Profile management
- Cart management
- Checkout system
- Order history
- Order details
- Invoice generation and print option

---

### PC Builder Features

- Step-by-step PC component selection
- Compatibility checking using product specifications
- Estimated wattage calculation
- Total price calculation
- Build summary page
- Add full PC build to cart

---

### Cart and Checkout Features

- Add product to cart
- Add complete PC build to cart
- Update product quantity
- Remove product from cart
- Delivery option selection
- Delivery charge calculation:
  - Inside City: 60 Tk
  - Outside City: 120 Tk
  - Store Pickup: 0 Tk
- Cash on Delivery payment method
- Order information saving in database
- Automatic stock update after order placement

---

### Staff Features

- Staff dashboard
- Add new products
- Edit product information
- Delete products
- Manage customer orders
- Update order status
- Manage users
- Block or unblock users
- Moderate reviews

---

### Owner/Admin Features

- Owner dashboard
- Create staff IDs
- Activate or deactivate staff IDs
- View staff list
- Manage users
- Manage products
- Manage orders
- Moderate reviews

---

## Project Folder Structure

```text
CUSTOM-PC-BUILDER-STORE
│
├── category_images
│
├── config
│   └── db.php
│
├── dashboards
│   ├── admin
│   │   ├── manage_staff_ids.php
│   │   ├── manage_users.php
│   │   ├── moderate_reviews.php
│   │   └── owner_dashboard.php
│   │
│   ├── cart
│   │   ├── add_build_to_cart.php
│   │   ├── add_to_cart.php
│   │   ├── update_cart.php
│   │   └── view_cart.php
│   │
│   ├── checkout_order
│   │   ├── checkout.php
│   │   ├── delete_order.php
│   │   ├── invoice.php
│   │   ├── manage_orders.php
│   │   ├── order_details.php
│   │   ├── order_history.php
│   │   ├── place_order.php
│   │   └── staff_order_details.php
│   │
│   ├── customer
│   │   ├── customer_dashboard.php
│   │   ├── customer_home.php
│   │   └── profile.php
│   │
│   ├── pc_builder
│   │   ├── build_summary.php
│   │   └── pc_build.php
│   │
│   ├── product
│   │   ├── add_product.php
│   │   ├── delete_product.php
│   │   ├── edit_product.php
│   │   ├── manage_products.php
│   │   ├── product_details.php
│   │   └── products.php
│   │
│   ├── review
│   │   └── add_review.php
│   │
│   └── staff
│       └── staff_dashboard.php
│
├── webstyle
│   └── style.css
│
├── index.php
└── logout.php
````

---

## Database Tables

The MySQL database includes the following main tables:

* `users`
* `customer`
* `owner`
* `employee_staff`
* `category`
* `product`
* `product_spec`
* `cart`
* `cart_item`
* `orders`
* `order_item`
* `delivery_option`
* `review`
* `build`
* `build_item`

---

## Database Relationship Summary

* A user can be a Customer, Staff, or Owner.
* A customer can have one cart.
* A cart can contain multiple cart items.
* A customer can place multiple orders.
* An order can contain multiple order items.
* A product belongs to one category.
* A product can have multiple specifications.
* A customer can review products.
* Owner can create and manage staff IDs.
* Staff can manage products, orders, users, and reviews.

---

## Installation and Setup

### 1. Install XAMPP

Install XAMPP and start:

* Apache
* MySQL

---

### 2. Move Project Folder

Place the project folder inside the XAMPP `htdocs` directory:

```text
xampp/htdocs/
```

Example:

```text
xampp/htdocs/Custom-PC-builder-store
```

---

### 3. Create Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
pc_builder
```

---

### 4. Import Database

Import the provided SQL file into the `pc_builder` database.

---

### 5. Configure Database Connection

Open:

```text
config/db.php
```

Default local configuration:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "pc_builder";
```

---

### 6. Run the Project

Open the browser and visit:

```text
http://localhost/Custom-PC-builder-store/index.php
```

---

## Important Files

### `index.php`

Main entry point of the system. It handles role selection, user registration, login, and role-based redirection.

### `config/db.php`

Database connection file. It creates the `$conn` variable used throughout the project to execute MySQL queries.

### `logout.php`

Destroys the active session and redirects the user to the main login page.

### `dashboards/customer/customer_dashboard.php`

Main dashboard for customers.

### `dashboards/customer/customer_home.php`

Customer home page containing product search, PC Builder shortcut, and category navigation.

### `dashboards/product/products.php`

Displays products with search, category, brand, and price filtering options.

### `dashboards/product/product_details.php`

Displays detailed product information, specifications, reviews, ratings, and add-to-cart option.

### `dashboards/cart/view_cart.php`

Displays cart items, quantities, and total price.

### `dashboards/checkout_order/place_order.php`

Processes checkout, saves order data, updates stock, stores order items, and clears the cart.

### `dashboards/pc_builder/pc_build.php`

Handles PC component selection and compatibility-based filtering.

### `dashboards/admin/manage_staff_ids.php`

Allows the Owner/Admin to create, activate, deactivate, and manage staff IDs.

---

## Security Features

* Password hashing using `password_hash()`
* Password verification using `password_verify()`
* Session-based authentication
* Role-based access control
* Account block/unblock functionality
* Staff login validation using active Staff ID

---

## Limitations

* Product images are handled using category-based images.
* The system is designed for local academic demonstration using XAMPP.
* Payment method is limited to Cash on Delivery.
* Build summary is generated, but advanced build history can be improved in future versions.

---

## Future Improvements

* Add individual product image upload system
* Add online payment gateway
* Improve product specification management
* Add more advanced compatibility checking
* Add email verification
* Add order tracking notifications
* Add admin analytics dashboard
* Improve security using prepared statements

---

## Contributor

* R. N. Bachhar Ayosh

---

## License

This project is developed for academic purposes at BRAC University.

---
