# Elite-Zone

**Elite-Zone** is a full-featured e-commerce web application built with native PHP, MySQL, and JavaScript. It provides a seamless shopping experience for customers and a robust dashboard for administrators to manage products, orders, and user interactions.

## Features

### User Features
*   **User Authentication**: Secure Login and Registration system.
*   **Product Browsing**:
    *   Interactive Home Page with Brand and Latest Product sliders.
    *   Search and filter products by category, brand, and price.
    *   Detailed product views with specifications and descriptions.
*   **Shopping Cart**:
    *   Add items to cart, update quantities, and remove items.
    *   Real-time subtotal and total calculation (including VAT and shipping).
*   **Checkout**: Streamlined checkout process for placing orders.
*   **Reviews**: detailed product review system with star ratings.
*   **Customer Support**: Integrated chat system for real-time communication with admins.

### Admin Features
*   **Dashboard Analytics**:
    *   Visual graphs for User Growth, Order Trends, and Revenue (powered by Chart.js).
    *   Filter data by Day, Week, Month, or Year.
    *   Brand-wise sales performance.
*   **Product Management**:
    *   Add, Edit, and Delete products.
    *   Manage Brands, Categories, and Sub-categories.
    *   Inventory management (quantities, cost prices, net prices).
*   **Order Management**: View and process customer orders (Accept, Reject, Hold).
*   **Customer Interaction**: View and reply to customer chats and "Contact Us" inquiries.

## Technology Stack

*   **Backend**: PHP (Native, OOP & Procedural), MySQL.
*   **Frontend**: HTML5, CSS3, JavaScript (ES6+), jQuery.
*   **UI Framework**: Bootstrap 5.
*   **Libraries/Plugins**:
    *   [Slick Slider](https://kenwheeler.github.io/slick/) (for carousels).
    *   [Chart.js](https://www.chartjs.org/) (for admin analytics).
*   **Database**: MySQL/MariaDB.

## Project Structure

```text
Elite-Zone/
├── admin/              # Admin panel pages (Dashboard, Product Management, etc.)
├── classes/            # PHP Classes (Product, Dashboard, Brands, etc.)
├── scripts/            # JavaScript files (Cart logic, Slider init, etc.)
├── style/              # CSS Stylesheets
├── images/             # Product and System images
├── config.php          # Database connection configuration
├── functions.php       # General helper functions
├── index.php           # Main landing page
├── login.php           # User login page
├── register.php        # User registration page
├── cart.php            # Shopping cart page
├── checkout.php        # Order placement page
└── db_elite_zone.sql  # Database Schema Import File
```

## Installation & Setup

1.  **Clone or Download** the repository to your local web server directory (e.g., `htdocs` for XAMPP or `www` for WAMP).

2.  **Database Setup**:
    *   Open your database management tool (e.g., phpMyAdmin).
    *   Create a new database named `elite-zone` (or update `config.php` if you choose a different name).
    *   Import the provided SQL file: `if0_37995345_elite_zone.sql`.

3.  **Configure Connection**:
    *   Open `config.php` in the root directory.
    *   Update the database credentials if necessary:
        ```php
        $db_host = "localhost";
        $db_user = "root";      // Your DB Username
        $db_password = "";      // Your DB Password
        $db_name = "elite-zone"; // Your Database Name
        ```

4.  **Run the Project**:
    *   Open your browser and navigate to `http://localhost/Elite-Zone`.

## Database Schema Overview

The application uses the following key tables:
*   `accounts`: Stores user and admin credentials.
*   `products`: Contains all product details (pricing, stock, specs).
*   `orders` & `orderinfo`: Manages order headers and line items.
*   `cart`: Persists user shopping cart items.
*   `brands`, `categories`, `sub_categories`: Product taxonomy.
*   `chats` & `chat_info`: Messaging system data.

## Credits

This project was developed using pure PHP, JavaScript, Ajax, HTML & CSS.
Special thanks to the open-source libraries (Bootstrap, Slick, Chart.js) that enhanced the UI/UX.