# 🏸 Chhabra Sports E-Commerce Platform

> **India's Preferred Online Racquet & Sports Store**  
> Authentic Yonex, Head, Babolat & Li-Ning racquets, SS English Willow cricket bats, tournament shoes, and certified stringing services.

---

## 🌟 Key Features

- **🎨 Premium UI & Responsive Design System**: Pitch Dark & Parchment design tokens with smooth glassmorphism, mobile-optimized navigation drawer, and zero layout overflow.
- **🏸 Interactive Racquet Finder**: 3-step recommendation tool helping athletes find their ideal racquet based on playstyle and skill level.
- **🛒 Dynamic Shopping Cart & Checkout**: Slide-out cart drawer, promo coupon code support (`CHHABRA10`), and real-time subtotal/discount calculation.
- **❤️ Wishlist & Quick View**: Save favorite gear and preview product details without leaving the catalog.
- **👤 User Authentication & Account Portal**: User registration, login, profile management, and order history tracking.
- **⚙️ Full Admin Management Console**: Reactive product catalog management (Create, Read, Update, Delete) with category & brand filtering.
- **📱 Fully Mobile Optimized**: Clean mobile drawer navigation with dynamic user account link and responsive action controls.

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.4 / 8.x, Laravel Framework
- **Frontend**: Vue.js 3 (Composition API), Custom Vanilla CSS (Pitch-Dark Theme System)
- **Asset Bundler**: Vite 6, @vitejs/plugin-vue
- **Icons & Effects**: Feather SVG Icons, Canvas Confetti

---

## 🚀 Getting Started & Setup Guide

### 1. Prerequisites
- **PHP** >= 7.4 (included in XAMPP: `C:\xampp\php`)
- **Composer** installed globally or accessible in PATH (`C:\xampp\php\composer.phar`)
- **Node.js** >= 18.x and **npm**

---

### 2. Global Composer Setup (Windows / XAMPP)

If `composer` command is not recognized in your terminal, set up Composer globally in XAMPP:

```powershell
# Download Composer PHAR to XAMPP PHP directory
Invoke-WebRequest -Uri "https://getcomposer.org/composer-stable.phar" -OutFile "C:\xampp\php\composer.phar"

# Create Batch script for CMD / PowerShell execution
[System.IO.File]::WriteAllText('C:\xampp\php\composer.bat', '@"C:\xampp\php\php.exe" "%~dp0composer.phar" %*', [System.Text.Encoding]::ASCII)
```

Verify Composer installation:
```bash
composer --version
```

---

### 3. Project Installation

1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd chhabra-sport-e-commerce
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**:
   ```bash
   npm install
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run Database Migrations**:
   ```bash
   php artisan migrate
   ```

---

### 4. Running Development Server

- **Start Laravel Backend**:
  ```bash
  php artisan serve
  ```
  *(Or access via XAMPP Apache: `http://localhost/chhabra-sport-e-commerce/public/`)*

- **Start Frontend Asset Bundler**:
  ```bash
  npm run dev
  ```

- **Build Production Assets**:
  ```bash
  npm run build
  # Or direct Node command if npm PATH is restricted:
  & "C:\Program Files\nodejs\node.exe" "node_modules/vite/bin/vite.js" build
  ```

---

## 📂 Project Structure

```
chhabra-sport-e-commerce/
├── app/                  # Laravel Controllers, Models & Middleware
├── config/               # Application & Package Configurations
├── public/               # Publicly accessible assets & compiled Vite bundles
├── resources/
│   ├── css/
│   │   └── app.css       # Core Design Tokens, Typography & Layout CSS
│   ├── js/
│   │   ├── App.vue       # Main SPA View Router & Reactive State
│   │   ├── app.js        # Vue Application Entry Point
│   │   └── components/   # Modular Vue Components (Navbar, Catalog, Admin, etc.)
│   └── views/            # Laravel Blade Templates (index.blade.php)
├── routes/
│   ├── api.php           # API Routes for Products & Auth
│   └── web.php           # Web Application Routes
├── README.md             # Project Documentation
└── package.json          # Node Dependencies & Build Scripts
```

---

## 📜 License

This project is open-source under the [MIT License](LICENSE).
