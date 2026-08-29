<template>
  <div class="admin-wrapper">
    <!-- ADMIN AUTHENTICATION GUARD LOGIN SCREEN -->
    <div v-if="!isAdminAuthenticated" class="admin-auth-overlay">
      <div class="admin-auth-card">
        <div class="auth-lock-badge">🔐 CHHABRA SPORTS PRO ADMIN</div>
        
        <div class="auth-header-text">
          <h2>ADMINISTRATOR LOGIN</h2>
          <p>Restricted Area · Enter Security Credentials to Access Store Control Panel</p>
        </div>

        <form @submit.prevent="handleAdminLogin" class="admin-auth-form">
          <div v-if="adminAuthError" class="auth-error-alert">
            ⚠️ {{ adminAuthError }}
          </div>

          <div class="form-group-clean">
            <label class="form-label-clean">Admin Username / Email</label>
            <input 
              type="text" 
              v-model="adminLoginEmail" 
              placeholder="admin@chhabrasports.com" 
              required 
              maxlength="80"
              class="auth-input-field"
            />
          </div>

          <div class="form-group-clean">
            <label class="form-label-clean">Admin Security Password</label>
            <input 
              type="password" 
              v-model="adminLoginPassword" 
              placeholder="••••••••" 
              value="123456"
              required 
              maxlength="50"
              class="auth-input-field"
            />
          </div>

          <button type="submit" class="btn-auth-submit">
            UNLOCK ADMIN DASHBOARD →
          </button>

          <button type="button" class="btn-auth-cancel" @click="$emit('exit-admin')">
            ← Return to Public Storefront
          </button>
        </form>
      </div>
    </div>

    <!-- MAIN admin-tem ADMIN DASHBOARD (RENDERED ONLY WHEN AUTHENTICATED) -->
    <div v-else class="admin-tem-wrapper">
      
      <!-- MOBILE BACKDROP OVERLAY -->
      <div v-if="isMobileMenuOpen" class="mobile-sidebar-backdrop" @click="isMobileMenuOpen = false"></div>

      <!-- admin-tem TOP NAVIGATION BAR -->
      <header class="admin-tem-topbar">
        <div class="topbar-left">
          <div class="brand-box">
            <img src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg" alt="Logo" class="admin-tem-logo-img" />
          </div>
          <!-- SIDEBAR COLLAPSE & MOBILE TOGGLE BUTTON -->
          <button class="admin-tem-toggle-btn" @click="isSidebarCollapsed = !isSidebarCollapsed; isMobileMenuOpen = !isMobileMenuOpen" title="Toggle Sidebar Collapse / Expand">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
          <div class="admin-tem-search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2.5">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" v-model="filters.search" placeholder="Search catalog..." @input="fetchFilteredProducts" class="admin-tem-search-input" />
          </div>
        </div>

        <div class="topbar-right">
          <div class="admin-tem-icon-btn" title="Notifications">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge-dot"></span>
          </div>

          <!-- ADMIN PROFILE DROPDOWN MENU WRAPPER -->
          <div class="admin-tem-user-profile-wrapper" style="position:relative;">
            <div class="admin-tem-user-profile" @click="isProfileDropdownOpen = !isProfileDropdownOpen" title="Click for Admin Profile & Actions">
              <img src="https://ui-avatars.com/api/?name=Admin+Manager&background=4B49AC&color=ffffff&bold=true" alt="Admin User" class="user-avatar-img" />
              <span class="user-name-text">Admin Manager ▾</span>
            </div>

            <!-- PROFILE DROPDOWN CARD -->
            <div v-if="isProfileDropdownOpen" class="profile-dropdown-card">
              <div class="dp-user-header">
                <img src="https://ui-avatars.com/api/?name=Admin+Manager&background=4B49AC&color=ffffff&bold=true" class="dp-avatar" />
                <div class="dp-details">
                  <strong class="dp-name">Admin Manager</strong>
                  <span class="dp-role">👑 Super Administrator</span>
                  <span class="dp-email">admin@chhabrasports.com</span>
                </div>
              </div>
              <div class="dp-divider"></div>
              <div class="dp-menu-list">
                <button class="dp-menu-item" @click="showToast('Profile view opened! 👤'); isProfileDropdownOpen = false;">
                  👤 View Admin Profile
                </button>
                <button class="dp-menu-item" @click="showToast('Account Settings opened! ⚙️'); isProfileDropdownOpen = false;">
                  ⚙️ Account Settings
                </button>
                <button class="dp-menu-item dp-logout-item" @click="handleAdminLogout">
                  🚪 Logout Session
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div class="admin-tem-layout">
        <!-- admin-tem SIDEBAR NAVIGATION (FIXED / NON-SCROLLABLE WITH COLLAPSE TOGGLE) -->
        <aside class="admin-tem-sidebar" :class="{ 'collapsed': isSidebarCollapsed, 'mobile-open': isMobileMenuOpen }">
          <nav class="admin-tem-menu">
            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'dashboard' }"
              @click.prevent="currentTab = 'dashboard'; isMobileMenuOpen = false;"
              title="Dashboard Analytics & Reports"
            >
              <span class="menu-icon">📊</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Dashboard</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'all-products' }"
              @click.prevent="currentTab = 'all-products'; isMobileMenuOpen = false;"
              title="Catalog Products Management"
            >
              <span class="menu-icon">📦</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">All Products</span>
              <span class="menu-arrow" v-if="!isSidebarCollapsed && productTotalItems > 0">{{ productTotalItems }}</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'users' }"
              @click.prevent="currentTab = 'users'; isMobileMenuOpen = false;"
              title="Registered Customer Accounts List"
            >
              <span class="menu-icon">👥</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Users List</span>
              <span class="menu-arrow" v-if="!isSidebarCollapsed && usersList.length > 0">{{ usersList.length }}</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'orders' }"
              @click.prevent="currentTab = 'orders'; isMobileMenuOpen = false;"
              title="Customer Orders Management"
            >
              <span class="menu-icon">🛒</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Orders</span>
              <span class="menu-arrow" v-if="!isSidebarCollapsed && ordersList.length > 0">{{ ordersList.length }}</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'categories' }"
              @click.prevent="currentTab = 'categories'; isMobileMenuOpen = false;"
              title="Categories"
            >
              <span class="menu-icon">📁</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Categories</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'brands' }"
              @click.prevent="currentTab = 'brands'; isMobileMenuOpen = false;"
              title="Brands"
            >
              <span class="menu-icon">🏷️</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Brands</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'tags' }"
              @click.prevent="currentTab = 'tags'; isMobileMenuOpen = false;"
              title="Tags"
            >
              <span class="menu-icon">🔖</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Tags</span>
            </a>

            <a 
              href="#" 
              class="admin-tem-menu-link" 
              :class="{ active: currentTab === 'attributes' }"
              @click.prevent="currentTab = 'attributes'; isMobileMenuOpen = false;"
              title="Global Attributes"
            >
              <span class="menu-icon">⚙️</span>
              <span v-if="!isSidebarCollapsed" class="menu-label">Attributes</span>
            </a>
          </nav>

          <div class="sidebar-storefront-box">
            <button class="btn-admin-tem-store" @click="$emit('exit-admin')">
              <span class="menu-icon">🌐</span>
              <span v-if="!isSidebarCollapsed">Live Storefront</span>
            </button>
          </div>
        </aside>

        <!-- MAIN admin-tem CONTENT -->
        <main class="admin-tem-main-content">

          <!-- ========================================================= -->
          <!-- 1. DASHBOARD ANALYTICS TAB (REPORTS & GRAPHS ONLY) -->
          <!-- ========================================================= -->
          <div v-if="currentTab === 'dashboard'">
            <!-- DASHBOARD HEADER BAR WITH MONTH FILTER -->
            <div class="admin-tem-welcome-banner">
              <div class="welcome-text-side">
                <h1 class="welcome-title">Store Analytics & Monthly Sales Reports</h1>
                <p class="welcome-sub">Viewing store sales performance, monthly metrics, and category revenue distribution.</p>
              </div>

              <!-- MONTH SELECTOR FILTER (DYNAMIC FROM DB ORDERS) -->
              <div class="report-month-filter-box">
                <label class="filter-lbl">📅 Select Analytics Range:</label>
                <select v-model="selectedReportMonth" class="month-select-dropdown">
                  <option value="all">🌐 All-Time Store Analytics</option>
                  <option value="2026-08">August 2026 (Current Month)</option>
                  <option value="2026-07">July 2026</option>
                  <option value="2026-06">June 2026</option>
                  <option value="2026-05">May 2026</option>
                  <option value="2026-04">April 2026</option>
                  <option value="2026-03">March 2026</option>
                  <option value="2026-02">February 2026</option>
                  <option value="2026-01">January 2026</option>
                </select>
              </div>
            </div>

            <!-- MONTHLY METRIC CARDS FOR SELECTED / CURRENT MONTH (4 CARDS) -->
            <div class="admin-tem-hero-grid">
              <!-- LEFT BANNER: SELECTED MONTH OVERVIEW -->
              <div class="admin-tem-weather-card">
                <div class="weather-info-header">
                  <span class="weather-icon">📊 {{ monthlyReportData.monthLabel }}</span>
                  <span class="weather-location">Monthly Performance</span>
                </div>
                <div class="illustration-caption">
                  <strong>⚡ Store Sales Snapshot</strong>
                  <p>Real-time analytics for {{ monthlyReportData.monthLabel }}. Highest performing category: <strong>{{ monthlyReportData.topCategory }}</strong>.</p>
                </div>
              </div>

              <!-- RIGHT 4 MONTHLY KPI STAT CARDS -->
              <div class="admin-tem-kpi-quad">
                <div class="kpi-card card-purple-light">
                  <span class="kpi-title">Monthly Revenue (₹)</span>
                  <h3 class="kpi-value">₹{{ monthlyReportData.totalRevenue.toLocaleString() }}</h3>
                  <span class="kpi-trend">{{ monthlyReportData.monthLabel }} Total</span>
                </div>

                <div class="kpi-card card-purple-dark">
                  <span class="kpi-title">Total Orders</span>
                  <h3 class="kpi-value">{{ monthlyReportData.totalOrders }} Orders</h3>
                  <span class="kpi-trend">Orders Placed</span>
                </div>

                <div class="kpi-card card-blue-soft">
                  <span class="kpi-title">Avg Order Value</span>
                  <h3 class="kpi-value">₹{{ monthlyReportData.avgOrderValue.toLocaleString() }}</h3>
                  <span class="kpi-trend">Revenue / Order</span>
                </div>

                <div class="kpi-card card-pink-coral">
                  <span class="kpi-title">Top Category</span>
                  <h3 class="kpi-value" style="font-size: 19px; font-weight: 800;">{{ monthlyReportData.topCategory }}</h3>
                  <span class="kpi-trend">Highest Revenue</span>
                </div>
              </div>
            </div>

            <!-- YEARLY MONTH-BY-MONTH SALES GRAPH & CATEGORY BREAKDOWN -->
            <div class="admin-tem-charts-grid">
              
              <!-- FULL YEAR (JAN - DEC) MONTH-BY-MONTH SALES GRAPH -->
              <div class="admin-tem-chart-card">
                <div class="chart-header">
                  <div class="chart-title-group">
                    <h3>Full Year Month-by-Month Sales Graph (2026) <span class="crown-badge">👑</span></h3>
                    <p class="chart-desc">Exact sales revenue (₹) for every month of the year (Jan to Dec).</p>
                  </div>
                </div>

                <!-- INTERACTIVE MONTHLY BAR GRAPH -->
                <div class="yearly-bar-graph-container">
                  <div class="bar-graph-bars">
                    <div 
                      v-for="m in yearlyMonthlySales" 
                      :key="m.month" 
                      class="bar-item-col"
                      :class="{ 'highlight-month': m.month === 'Aug' }"
                    >
                      <div class="bar-val-tooltip">₹{{ (m.revenue / 1000).toFixed(1) }}k</div>
                      <div class="bar-pillar" :style="{ height: m.heightPct + '%' }"></div>
                      <span class="bar-month-lbl">{{ m.month }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- CATEGORY-WISE SALES DISTRIBUTION REPORT -->
              <div class="admin-tem-chart-card">
                <div class="chart-header">
                  <div>
                    <h3>Category-Wise Sales Distribution</h3>
                    <p class="chart-desc">Revenue breakdown by product categories for {{ monthlyReportData.monthLabel }}.</p>
                  </div>
                </div>

                <div class="category-sales-list">
                  <div v-for="cat in monthlyReportData.categoryBreakdown" :key="cat.name" class="category-sale-row">
                    <div class="cat-sale-header">
                      <span class="cat-name">{{ cat.name }}</span>
                      <strong class="cat-revenue">₹{{ cat.amount.toLocaleString() }} <span class="cat-pct">({{ cat.percent }}%)</span></strong>
                    </div>
                    <div class="progress-bar-bg">
                      <div class="progress-bar-fill" :style="{ width: cat.percent + '%', background: cat.color }"></div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- SUCCESS TOAST BANNER -->
          <div v-if="adminToast" class="toast-banner-success">
            ✅ {{ adminToast }}
          </div>

        <!-- 1. REGISTERED USERS MANAGEMENT TABLE -->
        <div v-if="currentTab === 'users'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2 class="card-heading">Registered Customer Accounts</h2>
              <span class="card-sub-count">Showing <strong>{{ filteredUsersList.length }}</strong> of <strong>{{ usersList.length }}</strong> registered users</span>
            </div>
            <button class="btn-primary-blue" @click="isInviteUserModalOpen = true">+ Invite / Add New Admin</button>
          </div>

          <!-- SEARCH & STATUS FILTER TOOLBAR -->
          <div class="filter-toolbar" style="background:#F8FAFC; border:1px solid #E2E8F0; padding:16px; border-radius:10px; margin-bottom:20px;">
            <div style="display:flex; gap:14px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
              <div style="display:flex; gap:12px; align-items:center; flex:1; min-width:280px;">
                <span style="font-weight:700; font-size:13px; color:#475569;">🔍 Search Users:</span>
                <input 
                  type="text" 
                  v-model="userSearchQuery" 
                  placeholder="Search by name, email or phone..." 
                  class="clean-input"
                  style="width:100%; max-width:380px;"
                />
              </div>

              <div style="display:flex; gap:10px; align-items:center;">
                <span style="font-weight:700; font-size:13px; color:#475569;">Status Filter:</span>
                <select v-model="userStatusFilter" class="clean-select" style="padding:6px 12px; font-size:13px; border-radius:8px; font-weight:700;">
                  <option value="all">All Accounts ({{ usersList.length }})</option>
                  <option value="active">🟢 Active Users</option>
                  <option value="inactive">🔴 Inactive Users</option>
                </select>
              </div>
            </div>
          </div>

          <!-- USERS DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>User Details</th>
                  <th>Contact Info</th>
                  <th>Account Role</th>
                  <th>Total Orders</th>
                  <th>Account Status</th>
                  <th>Registered On</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredUsersList.length === 0">
                  <td colspan="7" style="text-align:center; color:#94A3B8; padding:30px; font-weight:600;">
                    No user accounts found matching search filter.
                  </td>
                </tr>
                <tr v-for="u in filteredUsersList" :key="u.id" class="table-row-item">
                  <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                      <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=4B49AC&color=ffffff&bold=true`" class="user-avatar-img" />
                      <div>
                        <strong style="color:#0F172A; font-size:14px; display:block;">{{ u.name }}</strong>
                        <span style="font-size:11px; color:#64748B;">ID: #USR-{{ u.id }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span style="font-weight:600; font-size:13px; display:block; color:#334155;">📧 {{ u.email }}</span>
                    <span style="font-size:12px; color:#64748B;">📞 {{ u.phone }}</span>
                  </td>
                  <td>
                    <span class="pro-badge" :style="{ background: u.role.includes('VIP') ? '#FEF3C7' : '#EFF6FF', color: u.role.includes('VIP') ? '#D97706' : '#2563EB', borderColor: 'transparent' }">
                      {{ u.role }}
                    </span>
                  </td>
                  <td>
                    <strong style="color:#4B49AC; font-size:14px;">{{ u.orders_count }} Orders</strong>
                  </td>
                  <td>
                    <button 
                      class="status-toggle-btn"
                      :class="u.status === 'active' ? 'btn-status-active' : 'btn-status-inactive'"
                      @click="toggleUserStatus(u)"
                      title="Click to toggle User Account Status"
                    >
                      {{ u.status === 'active' ? '🟢 Active' : '🔴 Inactive' }}
                    </button>
                  </td>
                  <td>
                    <span style="font-size:12.5px; color:#64748B; font-weight:600;">{{ u.registered_at }}</span>
                  </td>
                  <td>
                    <div style="display:flex; gap:8px;">
                      <button class="btn-text-blue" @click="openUserProfile(u)" style="font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:4px; background:#EEF2FF; padding:6px 12px; border-radius:6px; border:1px solid #C7D2FE;">👁️ View Profile & Orders</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 2. ALL PRODUCTS CATALOG WITH DYNAMIC BACKEND FILTERS -->
        <div v-else-if="currentTab === 'all-products'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2 class="card-heading">Catalog Inventory</h2>
              <span class="card-sub-count">
                Showing <strong>{{ displayProducts.length }}</strong> of <strong>{{ totalCount }}</strong> products in database
              </span>
            </div>
            <button class="btn-primary-blue" @click="currentTab = 'add-product'">+ Add New Product</button>
          </div>

          <!-- DYNAMIC FILTER BAR (FETCHES DIRECTLY FROM BACKEND DATABASE) -->
          <div class="filter-toolbar" style="background:#F8FAFC; border:1px solid #E2E8F0; padding:16px; border-radius:10px; margin-bottom:20px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:end;">
              <!-- LIVE DATABASE SEARCH -->
              <div class="filter-field" style="grid-column: span 2; min-width:240px;">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">🔍 Live Database Search</label>
                <input 
                  type="text" 
                  v-model="filters.search" 
                  placeholder="Search product name, SKU, specs..." 
                  class="filter-input"
                  style="width:100%;"
                  @input="applyFilters"
                />
              </div>

              <!-- CATEGORY FILTER (DYNAMIC FROM DB) -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">📁 Category</label>
                <select v-model="filters.category" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="all">All Categories</option>
                  <option v-for="c in categories" :key="c.id || c.name" :value="c.slug || c.name.toLowerCase()">
                    {{ c.icon || '📁' }} {{ c.name }}
                  </option>
                </select>
              </div>

              <!-- BRAND FILTER (DYNAMIC FROM DB) -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">🏷️ Brand</label>
                <select v-model="filters.brand" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="all">All Brands</option>
                  <option v-for="b in brands" :key="b.id || b.name" :value="b.name">
                    {{ b.name }}
                  </option>
                </select>
              </div>

              <!-- TAG FILTER (DYNAMIC FROM DB) -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">🏷️ Product Tag</label>
                <select v-model="filters.tag" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="all">All Tags</option>
                  <option v-for="t in tags" :key="t" :value="t">
                    🏷️ {{ t }}
                  </option>
                </select>
              </div>

              <!-- PRODUCT TYPE FILTER -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">⚙️ Product Type</label>
                <select v-model="filters.type" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="all">All Types</option>
                  <option value="simple">📦 Simple Products</option>
                  <option value="variable">⚙️ Variable Products</option>
                </select>
              </div>

              <!-- STOCK STATUS FILTER -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">📦 Stock Status</label>
                <select v-model="filters.stock" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="all">All Stock Statuses</option>
                  <option value="in_stock">🟢 In Stock Only</option>
                  <option value="out_of_stock">🔴 Out of Stock Only</option>
                </select>
              </div>

              <!-- SORT FILTER -->
              <div class="filter-field">
                <label style="font-weight:700; font-size:12px; color:#475569; display:block; margin-bottom:4px;">🔃 Database Sort</label>
                <select v-model="filters.sort" class="filter-select" style="width:100%;" @change="applyFilters">
                  <option value="newest">Newest First</option>
                  <option value="oldest">Oldest First</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="rating">Top Rated</option>
                </select>
              </div>

              <!-- RESET BUTTON -->
              <div class="filter-field">
                <button class="btn-reset-filters" style="width:100%; height:38px;" @click="resetFilters">🔄 Reset Filters</button>
              </div>
            </div>
          </div>

          <!-- CLEAN DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>Product Details</th>
                  <th>SKU Code</th>
                  <th>Category</th>
                  <th>Brand</th>
                  <th>Price</th>
                  <th>Type</th>
                  <th>Stock Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoading" class="loading-row">
                  <td colspan="8">Loading inventory from database... ⌛</td>
                </tr>
                <tr v-else-if="displayProducts.length === 0" class="empty-row">
                  <td colspan="8">No products found matching active filters. Try resetting filters!</td>
                </tr>
                <tr v-for="p in displayProducts" :key="p.id || p.code_id" class="table-row-item">
                  <td>
                    <div class="prod-cell">
                      <img :src="p.img" class="table-thumb" />
                      <div>
                        <strong class="prod-name-title">{{ p.name }}</strong>
                        <div class="prod-specs-sub">{{ p.specs || 'No specs added' }}</div>
                      </div>
                    </div>
                  </td>
                  <td><code>{{ p.code_id || p.id }}</code></td>
                  <td><span class="badge-blue">{{ p.category }}</span></td>
                  <td><strong class="brand-text">{{ p.brand }}</strong></td>
                  <td>
                    <span class="price-main">₹{{ (p.price || 0).toLocaleString() }}</span>
                    <span v-if="p.old_price" class="price-old">₹{{ p.old_price.toLocaleString() }}</span>
                  </td>
                  <td>
                    <span class="badge-type" :class="p.is_variable || p.isVariable ? 'variable' : 'simple'">
                      {{ p.is_variable || p.isVariable ? '⚙️ Variable' : '📦 Simple' }}
                    </span>
                  </td>
                  <td>
                    <span 
                      class="badge-type" 
                      :style="p.in_stock !== false && p.in_stock !== 0 && p.stockStatus !== 'Out of stock' 
                        ? 'background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; padding:4px 8px; font-weight:700;' 
                        : 'background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; padding:4px 8px; font-weight:700;'"
                    >
                      {{ p.in_stock !== false && p.in_stock !== 0 && p.stockStatus !== 'Out of stock' ? '🟢 In Stock' : '🔴 Out of Stock' }}
                    </span>
                  </td>
                  <td style="text-align:right;">
                    <div class="action-btn-group">
                      <button class="btn-table-edit" @click="editProduct(p)">Edit</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ENTERPRISE DATABASE PRODUCT PAGINATION FOOTER -->
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-top:20px; background:#ffffff; padding:16px 20px; border-radius:12px; border:1px solid #E2E8F0; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:12px; font-size:13px; color:#475569;">
              <span>Showing <strong>{{ backendProducts.length }}</strong> of <strong>{{ productTotalItems }}</strong> products fetched live from Database (Page {{ productCurrentPage }} of {{ productTotalPages }})</span>
              <span style="color:#CBD5E1;">|</span>
              <label style="display:flex; align-items:center; gap:6px;">
                Show:
                <select v-model="productItemsPerPage" @change="changeProductPage(1)" style="padding:4px 8px; border-radius:6px; border:1px solid #CBD5E1; font-size:12.5px; background:#F8FAFC; cursor:pointer;">
                  <option :value="10">10 per page</option>
                  <option :value="25">25 per page</option>
                  <option :value="50">50 per page</option>
                  <option :value="100">100 per page</option>
                </select>
              </label>
            </div>

            <!-- PAGINATION NAV BUTTONS -->
            <div style="display:flex; align-items:center; gap:6px;">
              <button 
                @click="changeProductPage(1)" 
                :disabled="productCurrentPage <= 1"
                class="btn-page-nav"
                :style="productCurrentPage <= 1 ? 'opacity:0.5; cursor:not-allowed;' : ''"
              >
                « First
              </button>
              <button 
                @click="changeProductPage(productCurrentPage - 1)" 
                :disabled="productCurrentPage <= 1"
                class="btn-page-nav"
                :style="productCurrentPage <= 1 ? 'opacity:0.5; cursor:not-allowed;' : ''"
              >
                ‹ Prev
              </button>
              
              <span style="font-weight:700; font-size:13px; color:#0F172A; padding:0 10px;">
                Page {{ productCurrentPage }} / {{ productTotalPages }}
              </span>

              <button 
                @click="changeProductPage(productCurrentPage + 1)" 
                :disabled="productCurrentPage >= productTotalPages"
                class="btn-page-nav"
                :style="productCurrentPage >= productTotalPages ? 'opacity:0.5; cursor:not-allowed;' : ''"
              >
                Next ›
              </button>
              <button 
                @click="changeProductPage(productTotalPages)" 
                :disabled="productCurrentPage >= productTotalPages"
                class="btn-page-nav"
                :style="productCurrentPage >= productTotalPages ? 'opacity:0.5; cursor:not-allowed;' : ''"
              >
                Last »
              </button>
            </div>
          </div>
        </div>

        <!-- 1. ORDERS MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'orders'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Customer Orders Management</h2>
              <span class="card-sub-count">Showing {{ paginatedOrdersList.length }} of {{ filteredOrdersList.length }} orders (Total {{ ordersList.length }})</span>
            </div>
            <div style="display:flex; gap:10px;">
              <button class="btn-primary-blue" @click="fetchOrdersFromBackend">🔄 Refresh Orders</button>
            </div>
          </div>

          <!-- SUMMARY METRICS CARDS -->
          <div class="metrics-grid" style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:20px;">
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:16px; border-radius:8px;">
              <span style="font-size:12px; color:#64748B; font-weight:700; display:block;">TOTAL ORDERS</span>
              <strong style="font-size:24px; color:#0F172A; font-family:'Outfit',sans-serif;">{{ ordersList.length }}</strong>
            </div>
            <div style="background:#FEF3C7; border:1px solid #FCD34D; padding:16px; border-radius:8px;">
              <span style="font-size:12px; color:#B45309; font-weight:700; display:block;">PENDING / CONFIRMED</span>
              <strong style="font-size:24px; color:#78350F; font-family:'Outfit',sans-serif;">{{ countOrdersByStatus('Confirmed') }}</strong>
            </div>
            <div style="background:#E0F2FE; border:1px solid #7DD3FC; padding:16px; border-radius:8px;">
              <span style="font-size:12px; color:#0369A1; font-weight:700; display:block;">PROCESSING / SHIPPED</span>
              <strong style="font-size:24px; color:#0c4a6e; font-family:'Outfit',sans-serif;">{{ countOrdersByStatus('Processing') + countOrdersByStatus('Shipped') }}</strong>
            </div>
            <div style="background:#DCFCE7; border:1px solid #86EFAC; padding:16px; border-radius:8px;">
              <span style="font-size:12px; color:#15803D; font-weight:700; display:block;">TOTAL REVENUE</span>
              <strong style="font-size:24px; color:#14532D; font-family:'Outfit',sans-serif;">₹{{ totalOrderRevenue.toLocaleString() }}</strong>
            </div>
          </div>

          <!-- QUICK STATUS TAB PILLS -->
          <div class="order-status-tabs" style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'all' }"
              @click="orderStatusFilter = 'all'"
            >
              📋 All Orders ({{ ordersList.length }})
            </button>
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'Confirmed' }"
              @click="orderStatusFilter = 'Confirmed'"
            >
              🟡 Confirmed ({{ countOrdersByStatus('Confirmed') }})
            </button>
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'Processing' }"
              @click="orderStatusFilter = 'Processing'"
            >
              🔵 Processing ({{ countOrdersByStatus('Processing') }})
            </button>
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'Shipped' }"
              @click="orderStatusFilter = 'Shipped'"
            >
              🟣 Shipped ({{ countOrdersByStatus('Shipped') }})
            </button>
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'Delivered' }"
              @click="orderStatusFilter = 'Delivered'"
            >
              🟢 Delivered ({{ countOrdersByStatus('Delivered') }})
            </button>
            <button 
              class="tab-pill-btn" 
              :class="{ active: orderStatusFilter === 'Cancelled' }"
              @click="orderStatusFilter = 'Cancelled'"
            >
              🔴 Cancelled ({{ countOrdersByStatus('Cancelled') }})
            </button>
          </div>

          <!-- ADVANCED SEARCH & FILTER TOOLBAR -->
          <div class="filter-toolbar" style="margin-bottom:16px; background:#F8FAFC; border:1px solid #E2E8F0; padding:12px 16px; border-radius:8px;">
            <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
              <!-- LIVE SEARCH -->
              <div style="display:flex; gap:8px; align-items:center; flex:1; min-width:240px;">
                <span style="font-weight:700; font-size:12.5px; color:#475569; white-space:nowrap;">🔍 Live Search:</span>
                <input 
                  type="text" 
                  v-model="orderSearchQuery" 
                  placeholder="Search by Order #, Customer Name, Email, Phone..." 
                  class="filter-input" 
                  style="width:100%;"
                />
              </div>

              <!-- DATE RANGE FILTER -->
              <div style="display:flex; gap:6px; align-items:center;">
                <span style="font-weight:700; font-size:12.5px; color:#475569; white-space:nowrap;">📅 Date:</span>
                <select v-model="orderDateFilter" class="filter-select">
                  <option value="all">All Time</option>
                  <option value="today">Today</option>
                  <option value="week">Past 7 Days</option>
                  <option value="month">This Month</option>
                </select>
              </div>

              <!-- SORT BY -->
              <div style="display:flex; gap:6px; align-items:center;">
                <span style="font-weight:700; font-size:12.5px; color:#475569; white-space:nowrap;">⇅ Sort:</span>
                <select v-model="orderSortBy" class="filter-select">
                  <option value="newest">Newest First</option>
                  <option value="oldest">Oldest First</option>
                  <option value="amount-high">Amount: High → Low</option>
                  <option value="amount-low">Amount: Low → High</option>
                </select>
              </div>

              <!-- PER PAGE -->
              <div style="display:flex; gap:6px; align-items:center;">
                <span style="font-weight:700; font-size:12.5px; color:#475569; white-space:nowrap;">Page Size:</span>
                <select v-model="orderItemsPerPage" class="filter-select" style="min-width:75px;">
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
              </div>
            </div>
          </div>

          <!-- ORDERS DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>Order # & Date</th>
                  <th>Customer Information</th>
                  <th>Ordered Items</th>
                  <th>Total Amount</th>
                  <th>Order Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="paginatedOrdersList.length === 0">
                  <td colspan="6" style="text-align:center; color:#94A3B8; padding:30px;">No customer orders found matching filter criteria.</td>
                </tr>
                <tr v-for="ord in paginatedOrdersList" :key="ord.id || ord.order_number" class="table-row-item">
                  <td>
                    <strong style="font-size:14px; color:#0284C7; display:block;">{{ ord.order_number }}</strong>
                    <span style="font-size:11.5px; color:#64748B;">{{ formatOrderDate(ord.created_at) }}</span>
                  </td>
                  <td>
                    <strong style="font-size:14px; color:#0F172A; display:block;">{{ ord.customer_name }}</strong>
                    <div style="font-size:11.5px; color:#64748B;">📞 {{ ord.customer_phone }}</div>
                    <div style="font-size:11.5px; color:#64748B;">✉️ {{ ord.customer_email }}</div>
                  </td>
                  <td>
                    <div v-for="(item, iIdx) in (ord.items || []).slice(0, 2)" :key="iIdx" style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                      <img :src="item.img || 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=100&q=80'" style="width:30px; height:30px; border-radius:4px; object-fit:cover; border:1px solid #CBD5E1;" />
                      <span style="font-size:12px; color:#334155;">{{ item.name }} <strong>(x{{ item.quantity || 1 }})</strong></span>
                    </div>
                    <span v-if="(ord.items || []).length > 2" style="font-size:11px; color:#0284C7; font-weight:700;">+{{ ord.items.length - 2 }} more item(s)</span>
                  </td>
                  <td>
                    <strong style="font-size:15px; color:#0F172A; display:block;">₹{{ (ord.total || 0).toLocaleString() }}</strong>
                    <span style="font-size:11px; color:#64748B;">💳 {{ ord.payment_method }}</span>
                  </td>
                  <td>
                    <select 
                      v-model="ord.status" 
                      class="clean-select-sm" 
                      :style="getStatusDropdownStyle(ord.status)"
                      @change="updateOrderStatusOnBackend(ord)"
                    >
                      <option value="Confirmed">🟡 Confirmed</option>
                      <option value="Processing">🔵 Processing</option>
                      <option value="Shipped">🟣 Shipped</option>
                      <option value="Delivered">🟢 Delivered</option>
                      <option value="Cancelled">🔴 Cancelled</option>
                    </select>
                  </td>
                  <td style="text-align:right;">
                    <button class="btn-table-edit" @click="viewOrderDetailsModal(ord)">
                      👁️ View Details
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- PAGINATION FOOTER BAR -->
          <div v-if="filteredOrdersList.length > 0" class="pagination-bar" style="display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-top:1px solid #E2E8F0; margin-top:12px; flex-wrap:wrap; gap:12px;">
            <div style="font-size:13px; color:#64748B;">
              Showing <strong>{{ ((orderCurrentPage - 1) * orderItemsPerPage) + 1 }}</strong> to <strong>{{ Math.min(orderCurrentPage * orderItemsPerPage, filteredOrdersList.length) }}</strong> of <strong>{{ filteredOrdersList.length }}</strong> orders
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
              <button 
                class="btn-page-nav" 
                :disabled="orderCurrentPage <= 1" 
                @click="orderCurrentPage--"
              >
                ← Prev
              </button>
              <span style="font-size:13px; font-weight:700; color:#334155; padding:0 8px;">
                Page {{ orderCurrentPage }} of {{ orderTotalPages }}
              </span>
              <button 
                class="btn-page-nav" 
                :disabled="orderCurrentPage >= orderTotalPages" 
                @click="orderCurrentPage++"
              >
                Next →
              </button>
            </div>
          </div>

          <!-- FULL ORDER DETAILS MODAL -->
          <div 
            v-if="selectedOrderModal" 
            class="modal-backdrop" 
            @click.self="selectedOrderModal = null"
            style="position:fixed !important; top:0 !important; left:0 !important; right:0 !important; bottom:0 !important; width:100vw !important; height:100vh !important; background:rgba(15,23,42,0.65) !important; backdrop-filter:blur(4px); display:flex !important; align-items:center !important; justify-content:center !important; z-index:999999 !important; padding:20px !important; margin:0 !important;"
          >
            <div 
              class="modal-card" 
              style="width:100% !important; max-width:650px !important; max-height:90vh !important; overflow-y:auto !important; background:#ffffff !important; padding:24px !important; border-radius:16px !important; border:1px solid #CBD5E1 !important; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3) !important; margin:auto !important;"
            >
              <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E2E8F0; padding-bottom:14px; margin-bottom:18px;">
                <div>
                  <h3 style="margin:0; font-family:'Outfit',sans-serif; color:#0F172A;">Order Details #{{ selectedOrderModal?.order_number || 'N/A' }}</h3>
                  <span style="font-size:12px; color:#64748B;">Placed on {{ formatOrderDate(selectedOrderModal?.created_at) }}</span>
                </div>
                <button @click="selectedOrderModal = null" style="background:none; border:none; font-size:24px; cursor:pointer; color:#64748B;">×</button>
              </div>

              <!-- CUSTOMER INFO CARD -->
              <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:16px; border-radius:8px; margin-bottom:18px;">
                <h4 style="margin:0 0 8px; color:#0F172A; font-size:13.5px;">👤 Customer & Shipping Address</h4>
                <div style="font-size:13px; color:#334155; line-height:1.5;">
                  <strong>{{ selectedOrderModal?.customer_name || 'Customer' }}</strong><br />
                  📞 {{ selectedOrderModal?.customer_phone || 'N/A' }} | ✉️ {{ selectedOrderModal?.customer_email || 'N/A' }}<br />
                  📍 {{ selectedOrderModal?.address || 'N/A' }}
                </div>
              </div>

              <!-- ORDER ITEMS LIST -->
              <h4 style="margin:0 0 10px; color:#0F172A; font-size:13.5px;">📦 Itemized Products</h4>
              <div style="border:1px solid #E2E8F0; border-radius:8px; overflow:hidden; margin-bottom:18px;">
                <div v-for="(item, i) in (selectedOrderModal?.items || [])" :key="i" style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #F1F5F9;">
                  <div style="display:flex; align-items:center; gap:12px;">
                    <img :src="item?.img || 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=100&q=80'" style="width:40px; height:40px; border-radius:6px; object-fit:cover;" />
                    <div>
                      <strong style="font-size:13px; color:#0F172A; display:block;">{{ item?.name || 'Product' }}</strong>
                      <span style="font-size:11.5px; color:#64748B;">{{ item?.specs || 'Standard' }}</span>
                    </div>
                  </div>
                  <div style="text-align:right;">
                    <strong style="font-size:13px; color:#0F172A;">₹{{ (Number(item?.price || 0) * Number(item?.quantity || 1)).toLocaleString() }}</strong>
                    <div style="font-size:11.5px; color:#64748B;">Qty: {{ item?.quantity || 1 }}</div>
                  </div>
                </div>
              </div>

              <!-- FINANCIAL BREAKDOWN -->
              <div style="background:#F1F5F9; padding:14px 16px; border-radius:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#475569;">
                  <span>Subtotal:</span>
                  <strong>₹{{ Number(selectedOrderModal?.subtotal || 0).toLocaleString() }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#475569;">
                  <span>Discount:</span>
                  <span style="color:#DC2626;">-₹{{ Number(selectedOrderModal?.discount || 0).toLocaleString() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px; color:#475569;">
                  <span>Shipping Charge:</span>
                  <span>₹{{ Number(selectedOrderModal?.shipping || 0).toLocaleString() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:1px solid #CBD5E1; font-size:15px; color:#0F172A; font-weight:800;">
                  <span>Total Amount Paid:</span>
                  <span style="color:#059669;">₹{{ Number(selectedOrderModal?.total || 0).toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. ADD NEW PRODUCT (CLEAN WHITE SINGLE PAGE EDITOR) -->
        <div v-else-if="currentTab === 'add-product'" class="white-card">
          <!-- TOP PRODUCT TYPE SELECTOR BAR -->
          <div class="type-selector-card">
            <label class="type-label">Select Product Type:</label>
            <select v-model="productType" class="type-dropdown">
              <option value="simple">📦 Simple Product (Single Price & Image)</option>
              <option value="variable">⚙️ Variable Product (Dynamic Sizes, Colors & Matrix Pricing)</option>
            </select>
            <span class="type-badge-info">
              {{ productType === 'simple' ? 'Standard single item catalog entry' : 'Dynamic attributes permutation matrix engine' }}
            </span>
          </div>

          <!-- GENERAL INFORMATION SECTION -->
          <div class="form-section-card">
            <h3 class="section-card-title">1. General Information</h3>
            <div class="grid-2">
              <div class="field-box">
                <label>Product Name *</label>
                <input type="text" v-model="newProd.name" placeholder="e.g. Cosco Anti-Burst Pro Gym Ball 65cm" class="clean-input" required maxlength="100" />
              </div>
              <div class="field-box">
                <label>Base SKU / Code ID *</label>
                <input type="text" v-model="newProd.code_id" placeholder="e.g. GYM-BALL-COSCO-65" class="clean-input" required maxlength="30" />
              </div>
            </div>

            <div class="grid-3" style="margin-top:16px;">
              <div class="field-box">
                <label>Category * (DB Synced)</label>
                <select v-model="newProd.category" class="clean-select" required>
                  <option value="" disabled>-- Select Category from Database --</option>
                  <option v-for="c in categories" :key="c.id || c.slug || c.name" :value="c.slug || c.name.toLowerCase()">
                    {{ c.icon || '📁' }} {{ c.name }}
                  </option>
                </select>
              </div>

              <div class="field-box">
                <label>Brand * (DB Synced)</label>
                <select v-model="newProd.brand" class="clean-select" required>
                  <option value="" disabled>-- Select Brand from Database --</option>
                  <option v-for="b in brands" :key="b.id || b.slug || b.name" :value="b.name">
                    {{ b.name }}
                  </option>
                </select>
              </div>

              <div class="field-box">
                <label>Tag / Badge (DB Synced)</label>
                <select v-model="newProd.tag" class="clean-select">
                  <option value="">None / Default</option>
                  <option v-for="t in tags" :key="typeof t === 'object' ? (t.id || t.name) : t" :value="typeof t === 'object' ? t.name : t">
                    🏷️ {{ typeof t === 'object' ? t.name : t }}
                  </option>
                </select>
              </div>
            </div>

            <div class="field-box" style="margin-top:16px;">
              <label>Technical Specifications & Details</label>
              <textarea v-model="newProd.specs" rows="2" placeholder="e.g. Heavy Duty Rubber · Includes Foot Pump · 55cm to 95cm Sizes" class="clean-textarea" maxlength="300"></textarea>
            </div>
          </div>

          <!-- A. IF SIMPLE PRODUCT -->
          <div v-if="productType === 'simple'" class="form-section-card" style="margin-top:24px;">
            <h3 class="section-card-title">2. Pricing, Stock & Media (Simple Product)</h3>
            
            <div class="grid-3">
              <div class="field-box">
                <label>Regular Price (₹) *</label>
                <input type="number" v-model="newProd.price" required placeholder="5000" class="clean-input highlight" />
              </div>
              <div class="field-box">
                <label>Sale Price (₹)</label>
                <input type="number" v-model="newProd.salePrice" placeholder="4500" class="clean-input" />
              </div>
              <div class="field-box">
                <label>Stock Status</label>
                <select v-model="newProd.stockStatus" class="clean-select">
                  <option value="In stock">In stock</option>
                  <option value="Out of stock">Out of stock</option>
                </select>
              </div>
            </div>

            <div class="grid-2" style="margin-top:16px;">
              <div class="field-box">
                <label>Main Product Image *</label>
                <div style="display:flex; gap:10px; align-items:center;">
                  <input type="text" v-model="newProd.img" placeholder="Paste image URL or choose file from device →" class="clean-input" style="flex:1;" required />
                  <label class="btn-secondary btn-sm" style="cursor:pointer; white-space:nowrap; margin:0; display:inline-flex; align-items:center; gap:6px;">
                    📁 Choose File
                    <input type="file" accept="image/*" style="display:none;" @change="onMainImageSelected" />
                  </label>
                </div>
                <div v-if="newProd.img" style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                  <img :src="newProd.img" style="width:44px; height:44px; border-radius:6px; object-fit:cover; border:1px solid #CBD5E1;" />
                  <span style="font-size:11.5px; color:#059669; font-weight:700;">✓ Main image ready</span>
                </div>
              </div>

              <div class="field-box">
                <label>Gallery Images</label>
                <div style="display:flex; gap:10px; align-items:center;">
                  <input type="text" v-model="newProd.galleryImg" placeholder="Paste comma separated URLs or pick files →" class="clean-input" style="flex:1;" />
                  <label class="btn-secondary btn-sm" style="cursor:pointer; white-space:nowrap; margin:0; display:inline-flex; align-items:center; gap:6px;">
                    📁 Choose Files
                    <input type="file" accept="image/*" multiple style="display:none;" @change="onGalleryImageSelected" />
                  </label>
                </div>
                <div v-if="newProd.galleryImg" style="margin-top:8px; font-size:11.5px; color:#0284C7; font-weight:700;">
                  ✓ Gallery images selected
                </div>
              </div>
            </div>

            <button @click="saveNewProduct" class="btn-submit-main" :disabled="isSubmitting">
              {{ isSubmitting ? 'SAVING TO DATABASE...' : '+ SAVE SIMPLE PRODUCT TO CATALOG NOW →' }}
            </button>
          </div>

          <!-- B. IF VARIABLE PRODUCT -->
          <div v-else-if="productType === 'variable'" style="margin-top:24px;">
            <!-- ATTRIBUTES SECTION -->
            <div class="form-section-card">
              <h3 class="section-card-title">2. Attributes (Height, Size, Color, Gym Ball)</h3>
              
              <div class="action-btn-row">
                <button class="btn-secondary" @click="showNewAttrBox = !showNewAttrBox">
                  {{ showNewAttrBox ? 'Cancel' : '+ Add New Attribute / Variable' }}
                </button>

                <select v-model="selectedGlobalAttr" class="clean-select-inline" @change="addPredefinedAttr">
                  <option value="">Add Predefined Global Attribute...</option>
                  <option value="Height">Height (5-Fit, 6-Fit, 7-Fit)</option>
                  <option value="Material">Material (Latex, Rubber, Carbon)</option>
                  <option value="Flexibility">Flexibility (Stiff, Flexible)</option>
                  <option value="Grip Size">Grip Size (G4, G5, G6)</option>
                </select>
              </div>

              <!-- INLINE NEW ATTRIBUTE BOX -->
              <div v-if="showNewAttrBox" class="inline-box-white">
                <h4 style="margin:0 0 12px; color:#0F172A;">Create & Save New Variable/Attribute to Database</h4>
                <div class="grid-2">
                  <input type="text" v-model="newAttrForm.name" placeholder="Attribute Name (e.g. Height, Size)" class="clean-input" maxlength="50" />
                  <input type="text" v-model="newAttrForm.values" placeholder="Initial Values (e.g. 5-Fit, 6-Fit, 7-Fit)" class="clean-input" maxlength="150" />
                </div>
                <button class="btn-primary-blue btn-sm" style="margin-top:12px;" @click="saveNewAttributeToDb">
                  + SAVE VARIABLE TO DATABASE NOW
                </button>
              </div>

              <!-- ATTRIBUTES LIST -->
              <div class="attributes-stack">
                <div v-for="(attr, idx) in globalAttributes" :key="attr.id || attr.name" class="attr-card">
                  <div class="attr-card-header">
                    <div>
                      <strong class="attr-name">{{ attr.name }}</strong> 
                      <span class="count-sub">({{ attr.values.length }} values)</span>
                    </div>
                    <button class="btn-text-danger" @click="deleteAttributeFromDb(attr, idx)">Delete Attribute</button>
                  </div>

                  <div class="attr-card-body">
                    <div class="tag-chips-wrap">
                      <span v-for="(valObj, vIdx) in attr.values" :key="typeof valObj === 'object' ? valObj.id : valObj" class="chip-removable" @click="deleteValueFromDb(attr, valObj, vIdx)">
                        × {{ typeof valObj === 'object' ? valObj.value : valObj }}
                      </span>
                    </div>

                    <div class="inline-add-row">
                      <input 
                        type="text" 
                        v-model="attr.inputValue" 
                        maxlength="50"
                        placeholder="Type new value (e.g. 6-Fit or 105cm) and click Add" 
                        class="clean-input-sm"
                        @keyup.enter="commitNewValueToDb(attr)"
                      />
                      <button class="btn-secondary btn-sm" @click="commitNewValueToDb(attr)">
                        + Save Value to DB
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- VARIATIONS MATRIX SECTION -->
            <div class="form-section-card" style="margin-top:24px;">
              <h3 class="section-card-title">3. Variations & Matrix Pricing (Per-SKU Pricing, Stock & Images)</h3>

              <div class="action-btn-row">
                <button class="btn-secondary" @click="addManualVariation">+ Add New Variation Row</button>
                <button class="btn-primary-blue" @click="generateAllVariations">⚡ Generate All Combinations Matrix</button>
                <span class="var-count-sub">({{ variationsList.length }} active variations)</span>
              </div>

              <!-- VARIATION ROWS LIST -->
              <div class="variations-stack">
                <div v-for="v in variationsList" :key="v.id" class="var-card" :class="{ open: v.expanded }">
                  <div class="var-header">
                    <div class="var-title">
                      <span class="var-id-hash">#{{ v.id }}</span>
                      
                      <div v-for="attr in globalAttributes" :key="attr.name" class="var-select-inline">
                        <span class="var-label">{{ attr.name }}:</span>
                        <select v-model="v.attributes[attr.name]" class="var-select">
                          <option v-for="val in getAttrValuesList(attr)" :key="val" :value="val">{{ val }}</option>
                        </select>
                      </div>
                    </div>

                    <div class="var-actions">
                      <span class="var-price-display">₹{{ (v.price || 0).toLocaleString() }}</span>
                      <button class="btn-text-danger" @click="removeVariation(v.id)">Remove</button>
                      <button class="btn-text-blue" @click="v.expanded = !v.expanded">
                        {{ v.expanded ? 'Close ▲' : 'Edit ▾' }}
                      </button>
                    </div>
                  </div>

                  <!-- EXPANDED EDIT DRAWER -->
                  <div v-if="v.expanded" class="var-drawer">
                    <div class="drawer-grid">
                      <div class="img-col">
                        <div class="img-picker-box" @click="changeVarImage(v)">
                          <img :src="v.img" v-if="v.img" class="uploaded-thumb" />
                          <div v-else class="img-placeholder">
                            <span>Add Image</span>
                          </div>
                        </div>
                        <button class="btn-secondary btn-sm" style="margin-top:10px; width:100%;" @click="changeVarImage(v)">Image URL</button>
                      </div>

                      <div class="fields-col">
                        <div class="checkbox-row">
                          <label><input type="checkbox" v-model="v.enabled" /> Enabled</label>
                          <label><input type="checkbox" v-model="v.manageStock" /> Manage stock?</label>
                        </div>

                        <div class="grid-2">
                          <div class="field-box">
                            <label>SKU *</label>
                            <input type="text" v-model="v.sku" placeholder="e.g. GYM-HEIGHT-5FIT-5000" class="clean-input" />
                          </div>
                          <div class="field-box">
                            <label>Regular price (₹) *</label>
                            <input type="number" v-model="v.price" placeholder="5000" class="clean-input highlight" />
                          </div>
                        </div>

                        <div class="grid-2" style="margin-top:12px;">
                          <div class="field-box">
                            <label>Sale price (₹)</label>
                            <input type="number" v-model="v.salePrice" placeholder="4500" class="clean-input" />
                          </div>
                          <div class="field-box">
                            <label>Stock status</label>
                            <select v-model="v.stockStatus" class="clean-select">
                              <option value="In stock">In stock</option>
                              <option value="Out of stock">Out of stock</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <button @click="saveNewProduct" class="btn-submit-main" :disabled="isSubmitting">
              {{ isSubmitting ? 'SAVING TO DATABASE...' : '+ SAVE VARIABLE PRODUCT TO CATALOG NOW →' }}
            </button>
          </div>
        </div>

        <!-- 3. ATTRIBUTES MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'attributes'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Global Attributes & Variables Table</h2>
              <span class="card-sub-count">Showing {{ filteredAttributesList.length }} of {{ globalAttributes.length }} active variables</span>
            </div>
            <button class="btn-primary-blue" @click="showNewAttrBox = !showNewAttrBox">+ Add New Attribute</button>
          </div>

          <!-- TOP INLINE ADD FORM -->
          <div v-if="showNewAttrBox" class="inline-box-white" style="margin-bottom:20px;">
            <h4 style="margin:0 0 12px; color:#0F172A;">Create & Save New Variable/Attribute to Database</h4>
            <div class="grid-2">
              <input type="text" v-model="newAttrForm.name" placeholder="Attribute Name (e.g. Height, Size)" class="clean-input" />
              <input type="text" v-model="newAttrForm.values" placeholder="Initial Values (comma separated, e.g. 5-Fit, 6-Fit)" class="clean-input" />
            </div>
            <button class="btn-primary-blue btn-sm" style="margin-top:12px;" @click="saveNewAttributeToDb">
              + SAVE VARIABLE TO DATABASE NOW
            </button>
          </div>

          <!-- SEARCH FILTER BAR -->
          <div class="filter-toolbar" style="margin-bottom:16px;">
            <div style="display:flex; gap:12px; align-items:center;">
              <span style="font-weight:700; font-size:12.5px; color:#475569;">🔍 Filter Attributes:</span>
              <input 
                type="text" 
                v-model="attributeSearchQuery" 
                placeholder="Type attribute name or value..." 
                class="filter-input" 
                style="max-width:400px;"
              />
            </div>
          </div>

          <!-- ATTRIBUTES DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>Attribute Name</th>
                  <th>Configured Values & Options</th>
                  <th>Total Values</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredAttributesList.length === 0">
                  <td colspan="4" style="text-align:center; color:#94A3B8; padding:20px;">No attributes match search query.</td>
                </tr>
                <tr v-for="attr in filteredAttributesList" :key="attr.id || attr.name" class="table-row-item">
                  <td>
                    <strong style="font-size:15px; color:#0F172A; display:block;">{{ attr.name }}</strong>
                  </td>
                  <td>
                    <div class="tag-chips-wrap" style="margin-bottom:8px;">
                      <span v-for="(valObj, vIdx) in attr.values" :key="typeof valObj === 'object' ? valObj.id : valObj" class="chip-removable" @click="deleteValueFromDb(attr, valObj, vIdx)">
                        × {{ typeof valObj === 'object' ? valObj.value : valObj }}
                      </span>
                    </div>

                    <div class="inline-add-row">
                      <input 
                        type="text" 
                        v-model="attr.inputValue" 
                        placeholder="Add new value (e.g. 6-Fit)" 
                        class="clean-input-sm"
                        style="max-width:220px;"
                        @keyup.enter="commitNewValueToDb(attr)"
                      />
                      <button class="btn-secondary btn-sm" @click="commitNewValueToDb(attr)">
                        + Add Value
                      </button>
                    </div>
                  </td>
                  <td>
                    <span class="badge-blue" style="background:#F1F5F9; color:#475569; font-weight:700;">
                      {{ attr.values ? attr.values.length : 0 }} Values
                    </span>
                  </td>
                  <td><span class="badge-blue">Active Variable</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 4. BRANDS MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'brands'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Official Brand Portfolio</h2>
              <span class="card-sub-count">Showing {{ filteredBrands.length }} of {{ brands.length }} brands</span>
            </div>
            <button class="btn-primary-blue" @click="addBrand">+ Add New Brand</button>
          </div>

          <div v-if="showBrandForm" class="inline-box-white" style="margin-bottom:20px;">
            <h4 style="margin:0 0 12px; color:#0F172A;">Register New Brand</h4>
            <div class="grid-2">
              <input type="text" v-model="newBrandForm.name" placeholder="Brand Name (e.g. Puma, Dunlop)" class="clean-input" maxlength="50" />
              <input type="text" v-model="newBrandForm.desc" placeholder="Brand Tagline / Description" class="clean-input" maxlength="150" />
            </div>
            <button class="btn-primary-blue btn-sm" style="margin-top:12px;" @click="saveBrand">
              + SAVE BRAND NOW
            </button>
          </div>

          <!-- SEARCH FILTER BAR -->
          <div class="filter-toolbar" style="margin-bottom:16px;">
            <div style="display:flex; gap:12px; align-items:center;">
              <span style="font-weight:700; font-size:12.5px; color:#475569;">🔍 Filter Brands:</span>
              <input 
                type="text" 
                v-model="brandSearchQuery" 
                placeholder="Type brand name or description..." 
                class="filter-input" 
                style="max-width:400px;"
              />
            </div>
          </div>

          <!-- BRANDS DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>Brand Name</th>
                  <th>Description / Portfolio</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredBrands.length === 0">
                  <td colspan="4" style="text-align:center; color:#94A3B8; padding:20px;">No brands match search query.</td>
                </tr>
                <tr 
                  v-for="b in filteredBrands" 
                  :key="b.id || b.name" 
                  class="table-row-item"
                  :style="{ opacity: (editingBrandKey === (b.id || b.name) ? editingBrandForm.status === 'active' : isBrandActive(b.name)) ? 1 : 0.65 }"
                >
                  <!-- NAME -->
                  <td>
                    <strong v-if="editingBrandKey !== (b.id || b.name)" class="brand-text" style="font-size:15px;">{{ b.name }}</strong>
                    <input 
                      v-else 
                      type="text" 
                      v-model="editingBrandForm.name" 
                      class="clean-input-sm" 
                      style="max-width:200px; font-weight:700;"
                      @keyup.enter="saveEditBrand(b)"
                      @keyup.esc="cancelEditBrand"
                    />
                  </td>

                  <!-- DESC -->
                  <td>
                    <span v-if="editingBrandKey !== (b.id || b.name)" style="color:#64748B;">{{ b.desc || b.description }}</span>
                    <input v-else type="text" v-model="editingBrandForm.desc" class="clean-input-sm" style="max-width:300px;" />
                  </td>

                  <!-- STATUS -->
                  <td>
                    <div v-if="editingBrandKey === (b.id || b.name)">
                      <select v-model="editingBrandForm.status" class="clean-select" style="padding:4px 8px; font-weight:700; font-size:12px; border-radius:6px; cursor:pointer;">
                        <option value="active">🟢 Active</option>
                        <option value="inactive">🔴 Inactive</option>
                      </select>
                    </div>
                    <div v-else>
                      <button 
                        class="status-toggle-btn"
                        :class="isBrandActive(b.name) ? 'btn-status-active' : 'btn-status-inactive'"
                        @click="toggleBrandStatus(b)"
                        title="Click to toggle Active / Inactive"
                      >
                        {{ isBrandActive(b.name) ? '🟢 Active' : '🔴 Inactive' }}
                      </button>
                    </div>
                  </td>

                  <!-- ACTION -->
                  <td>
                    <div v-if="editingBrandKey !== (b.id || b.name)" style="display:flex; gap:8px;">
                      <button class="btn-text-blue" @click="startEditBrand(b)" style="font-weight:700; font-size:13px;">✏️ Edit Brand</button>
                    </div>
                    <div v-else style="display:flex; gap:8px; align-items:center;">
                      <button class="btn-secondary btn-sm" @click="saveEditBrand(b)">✓ Save</button>
                      <button class="btn-text-danger" @click="cancelEditBrand">Cancel</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 5. CATEGORIES MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'categories'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Product Categories List</h2>
              <span class="card-sub-count">Showing {{ filteredCategories.length }} of {{ categories.length }} categories</span>
            </div>
            <button class="btn-primary-blue" @click="addCategory">+ Add New Category</button>
          </div>

          <div v-if="showCatForm" class="inline-box-white" style="margin-bottom:20px;">
            <h4 style="margin:0 0 12px; color:#0F172A;">Create New Category</h4>
            <div class="grid-3">
              <input type="text" v-model="newCatForm.name" placeholder="Category Name (e.g. Squash)" class="clean-input" maxlength="50" />
              <input type="text" v-model="newCatForm.icon" placeholder="Emoji Icon (e.g. 🏐)" class="clean-input" maxlength="10" />
              <input type="text" v-model="newCatForm.slug" placeholder="URL Slug (e.g. squash-gear)" class="clean-input" maxlength="50" />
            </div>
            <button class="btn-primary-blue btn-sm" style="margin-top:12px;" @click="saveCategory">
              + SAVE CATEGORY NOW
            </button>
          </div>

          <!-- SEARCH FILTER BAR -->
          <div class="filter-toolbar" style="margin-bottom:16px;">
            <div style="display:flex; gap:12px; align-items:center;">
              <span style="font-weight:700; font-size:12.5px; color:#475569;">🔍 Filter Categories:</span>
              <input 
                type="text" 
                v-model="categorySearchQuery" 
                placeholder="Type category name or slug..." 
                class="filter-input" 
                style="max-width:400px;"
              />
            </div>
          </div>

          <!-- CATEGORIES DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th style="width:70px;">Icon</th>
                  <th>Category Name</th>
                  <th>URL Slug</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredCategories.length === 0">
                  <td colspan="5" style="text-align:center; color:#94A3B8; padding:20px;">No categories match search query.</td>
                </tr>
                <tr 
                  v-for="c in filteredCategories" 
                  :key="c.id || c.slug || c.name" 
                  class="table-row-item"
                  :style="{ opacity: (editingCategoryKey === (c.id || c.slug || c.name) ? editingCategoryForm.status === 'active' : isCategoryActive(c.slug || c.name)) ? 1 : 0.65 }"
                >
                  <!-- ICON -->
                  <td style="font-size:22px;">
                    <span v-if="editingCategoryKey !== (c.id || c.slug || c.name)">{{ c.icon }}</span>
                    <input v-else type="text" v-model="editingCategoryForm.icon" class="clean-input-sm" style="width:50px; text-align:center; font-size:18px;" />
                  </td>

                  <!-- NAME -->
                  <td>
                    <strong v-if="editingCategoryKey !== (c.id || c.slug || c.name)" style="font-size:14px; color:#0F172A;">{{ c.name }}</strong>
                    <input 
                      v-else 
                      type="text" 
                      v-model="editingCategoryForm.name" 
                      class="clean-input-sm" 
                      style="max-width:200px; font-weight:700;"
                      @keyup.enter="saveEditCategory(c)"
                      @keyup.esc="cancelEditCategory"
                    />
                  </td>

                  <!-- SLUG -->
                  <td>
                    <code v-if="editingCategoryKey !== (c.id || c.slug || c.name)">/category/{{ c.slug }}</code>
                    <input v-else type="text" v-model="editingCategoryForm.slug" class="clean-input-sm" style="max-width:180px;" />
                  </td>

                  <!-- STATUS -->
                  <td>
                    <div v-if="editingCategoryKey === (c.id || c.slug || c.name)">
                      <select v-model="editingCategoryForm.status" class="clean-select" style="padding:4px 8px; font-weight:700; font-size:12px; border-radius:6px; cursor:pointer;">
                        <option value="active">🟢 Active</option>
                        <option value="inactive">🔴 Inactive</option>
                      </select>
                    </div>
                    <div v-else>
                      <button 
                        class="status-toggle-btn"
                        :class="isCategoryActive(c.slug || c.name) ? 'btn-status-active' : 'btn-status-inactive'"
                        @click="toggleCategoryStatus(c)"
                        title="Click to toggle Active / Inactive"
                      >
                        {{ isCategoryActive(c.slug || c.name) ? '🟢 Active' : '🔴 Inactive' }}
                      </button>
                    </div>
                  </td>

                  <!-- ACTION -->
                  <td>
                    <div v-if="editingCategoryKey !== (c.id || c.slug || c.name)" style="display:flex; gap:8px;">
                      <button class="btn-text-blue" @click="startEditCategory(c)" style="font-weight:700; font-size:13px;">✏️ Edit Category</button>
                    </div>
                    <div v-else style="display:flex; gap:8px; align-items:center;">
                      <button class="btn-secondary btn-sm" @click="saveEditCategory(c)">✓ Save</button>
                      <button class="btn-text-danger" @click="cancelEditCategory">Cancel</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 6. TAGS MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'tags'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Product Badges & Tags Table</h2>
              <span class="card-sub-count">Showing {{ filteredTagsList.length }} of {{ tags.length }} active tags</span>
            </div>
            <button class="btn-primary-blue" @click="showTagForm = !showTagForm">+ Add New Tag</button>
          </div>

          <div v-if="showTagForm" class="inline-box-white" style="margin-bottom:20px;">
            <h4 style="margin:0 0 12px; color:#0F172A;">Register New Tag / Badge</h4>
            <div class="grid-2">
              <input 
                type="text" 
                v-model="newTagInput" 
                maxlength="30"
                placeholder="Enter Tag Name (e.g. HOT DEAL, TOP RATED, LIMITED)" 
                class="clean-input"
                @keyup.enter="saveTagFromForm"
              />
            </div>
            <button class="btn-primary-blue btn-sm" style="margin-top:12px;" @click="saveTagFromForm">
              + SAVE TAG NOW
            </button>
          </div>

          <!-- SEARCH FILTER BAR -->
          <div class="filter-toolbar" style="margin-bottom:16px;">
            <div style="display:flex; gap:12px; align-items:center;">
              <span style="font-weight:700; font-size:12.5px; color:#475569;">🔍 Filter Tags:</span>
              <input 
                type="text" 
                v-model="tagSearchQuery" 
                placeholder="Type tag name..." 
                class="filter-input" 
                style="max-width:400px;"
              />
            </div>
          </div>

          <!-- TAGS DATA TABLE -->
          <div class="table-responsive">
            <table class="white-data-table">
              <thead>
                <tr>
                  <th>Tag / Badge Name</th>
                  <th>Preview Badge</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredTagsList.length === 0">
                  <td colspan="4" style="text-align:center; color:#94A3B8; padding:20px;">No tags match search query.</td>
                </tr>
                <tr v-for="t in filteredTagsList" :key="t" class="table-row-item" :style="{ opacity: (editingTagOldName === t ? editingTagStatus === 'active' : isTagActive(t)) ? 1 : 0.65 }">
                  <td v-if="editingTagOldName !== t">
                    <strong style="font-size:14px; color:#0F172A;">{{ t }}</strong>
                  </td>
                  <td v-else>
                    <input 
                      type="text" 
                      v-model="editingTagNewValue" 
                      class="clean-input-sm" 
                      style="max-width:220px; font-weight:700;"
                      @keyup.enter="saveEditTag(t)"
                      @keyup.esc="cancelEditTag"
                    />
                  </td>

                  <td>
                    <span 
                      class="badge-blue" 
                      :style="{
                        background: (editingTagOldName === t ? editingTagStatus === 'active' : isTagActive(t)) ? '#FEF3C7' : '#F1F5F9',
                        color: (editingTagOldName === t ? editingTagStatus === 'active' : isTagActive(t)) ? '#B45309' : '#64748B',
                        fontWeight: 800
                      }"
                    >
                      🏷️ {{ editingTagOldName === t ? (editingTagNewValue.toUpperCase() || t) : t }}
                      <span v-if="(editingTagOldName === t ? editingTagStatus === 'inactive' : !isTagActive(t))" style="font-size:11px; font-weight:600; opacity:0.8; margin-left:4px;">(Inactive)</span>
                    </span>
                  </td>

                  <td>
                    <div v-if="editingTagOldName === t">
                      <select v-model="editingTagStatus" class="clean-select" style="padding:4px 8px; font-weight:700; font-size:12px; border-radius:6px; cursor:pointer;">
                        <option value="active">🟢 Active</option>
                        <option value="inactive">🔴 Inactive</option>
                      </select>
                    </div>
                    <div v-else>
                      <button 
                        class="status-toggle-btn"
                        :class="isTagActive(t) ? 'btn-status-active' : 'btn-status-inactive'"
                        @click="toggleTagStatus(t)"
                        title="Click to toggle Active / Inactive"
                      >
                        {{ isTagActive(t) ? '🟢 Active' : '🔴 Inactive' }}
                      </button>
                    </div>
                  </td>

                  <td>
                    <div v-if="editingTagOldName !== t" style="display:flex; gap:8px;">
                      <button class="btn-text-blue" @click="startEditTag(t)" style="font-weight:700; font-size:13px;">✏️ Edit Tag</button>
                    </div>
                    <div v-else style="display:flex; gap:8px; align-items:center;">
                      <button class="btn-secondary btn-sm" @click="saveEditTag(t)">✓ Save</button>
                      <button class="btn-text-danger" @click="cancelEditTag">Cancel</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ENTERPRISE ADMIN FOOTER -->
        <footer class="admin-footer-clean">
          <div class="footer-left">
            <strong class="footer-brand">CHHABRA SPORTS</strong>
            <span>© 2026 Enterprise Admin Portal · All Rights Reserved</span>
          </div>
          <div class="footer-center">
            <span class="system-badge-pill">⚙️ Engine: Laravel 12</span>
          </div>
        </footer>
      </main>
    </div>
  </div>

  <!-- USER PROFILE & ORDER HISTORY MODAL -->
  <div v-if="selectedUserProfileModal" class="user-profile-modal-backdrop" @click.self="selectedUserProfileModal = null">
    <div class="user-profile-modal-card">
      <div class="user-profile-modal-header">
        <div style="display:flex; align-items:center; gap:14px;">
          <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(selectedUserProfileModal.name)}&background=4B49AC&color=ffffff&bold=true`" style="width:52px; height:52px; border-radius:50%; border:3px solid #7DA0FA; box-shadow:0 4px 10px rgba(0,0,0,0.15);" />
          <div>
            <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#ffffff;">
              {{ selectedUserProfileModal.name }}
            </h3>
            <span style="font-size:12px; color:#94A3B8; font-family:'Space Mono',monospace;">
              User ID: #USR-{{ selectedUserProfileModal.id }} · Registered: {{ selectedUserProfileModal.registered_at }}
            </span>
          </div>
        </div>
        <button @click="selectedUserProfileModal = null" class="close-profile-modal-btn">✕</button>
      </div>

      <div class="user-profile-modal-body">
        <!-- USER METRICS SUMMARY -->
        <div class="user-metrics-grid">
          <div class="user-metric-card" style="border-left-color:#4B49AC;">
            <span class="user-metric-label">Email Address</span>
            <strong class="user-metric-val">📧 {{ selectedUserProfileModal.email }}</strong>
          </div>
          <div class="user-metric-card" style="border-left-color:#10B981;">
            <span class="user-metric-label">Phone Number</span>
            <strong class="user-metric-val">📞 {{ selectedUserProfileModal.phone || 'N/A' }}</strong>
          </div>
          <div class="user-metric-card" style="border-left-color:#F59E0B;">
            <span class="user-metric-label">Total Orders</span>
            <strong class="user-metric-val" style="color:#4B49AC;">📦 {{ selectedUserOrders.length }} Orders</strong>
          </div>
          <div class="user-metric-card" style="border-left-color:#6366F1;">
            <span class="user-metric-label">Total Spend</span>
            <strong class="user-metric-val" style="color:#10B981;">💰 ₹{{ totalUserSpend.toLocaleString('en-IN') }}</strong>
          </div>
        </div>

        <!-- CUSTOMER ORDER HISTORY SECTION -->
        <div class="user-orders-box">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h4 style="margin:0; font-family:'Outfit',sans-serif; font-size:17px; font-weight:800; color:#0F172A; display:flex; align-items:center; gap:8px;">
              🛒 Order History of {{ selectedUserProfileModal.name }}
            </h4>
            <span class="pro-badge" style="background:#EEF2FF; color:#4338CA; border:1px solid #C7D2FE; font-weight:800; font-size:12px;">
              {{ selectedUserOrders.length }} Orders Placed
            </span>
          </div>

          <!-- NO ORDERS STATE -->
          <div v-if="selectedUserOrders.length === 0" class="empty-user-orders">
            <span style="font-size:44px; display:block; margin-bottom:10px;">📦</span>
            <strong style="display:block; font-size:16px; color:#334155; margin-bottom:4px;">No Orders Placed Yet</strong>
            <span style="font-size:13px; color:#94A3B8;">This customer has not placed any orders on the store yet.</span>
          </div>

          <!-- ORDER CARDS LIST -->
          <div v-else style="display:flex; flex-direction:column; gap:16px;">
            <div v-for="ord in selectedUserOrders" :key="ord.id || ord.order_number" class="user-order-card">
              <div class="user-order-card-head">
                <div>
                  <strong style="font-size:15px; color:#4B49AC; font-family:'Space Mono',monospace;">Order #{{ ord.order_number }}</strong>
                  <span style="font-size:12px; color:#64748B; margin-left:12px;">🗓️ {{ ord.created_at ? ord.created_at.slice(0, 10) : 'Recent' }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                  <span :style="getStatusDropdownStyle(ord.status)">
                    {{ ord.status }}
                  </span>
                  <strong style="font-size:16px; color:#0F172A; font-family:'Outfit',sans-serif;">₹{{ (ord.total || 0).toLocaleString('en-IN') }}</strong>
                </div>
              </div>

              <!-- ORDER ITEMS BREAKDOWN -->
              <div style="margin-bottom:12px;">
                <span style="font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase; display:block; margin-bottom:8px;">Purchased Items:</span>
                <div style="display:flex; flex-direction:column; gap:6px;">
                  <div v-for="(item, idx) in (ord.items || [])" :key="idx" class="user-order-item-row">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <span style="font-size:18px;">🏸</span>
                      <div>
                        <strong style="font-size:13px; color:#1E293B; display:block;">{{ item.name }}</strong>
                        <span style="font-size:11px; color:#64748B;">Qty: {{ item.qty || item.quantity || 1 }}</span>
                      </div>
                    </div>
                    <strong style="font-size:13px; color:#059669;">₹{{ ((item.price || 0) * (item.qty || item.quantity || 1)).toLocaleString('en-IN') }}</strong>
                  </div>
                </div>
              </div>

              <!-- DETAILS & NOTES -->
              <div class="user-order-footer-details">
                <div>
                  <span>💳 <strong>Payment Method:</strong> {{ ord.payment_method || 'COD' }}</span>
                </div>
                <div>
                  <span>📍 <strong>Address:</strong> {{ ord.address }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- INVITE / CREATE NEW USER OR ADMIN MODAL -->
  <div v-if="isInviteUserModalOpen" class="user-profile-modal-backdrop" @click.self="isInviteUserModalOpen = false">
    <div class="user-profile-modal-card" style="max-width:540px;">
      <div class="user-profile-modal-header" style="background:#0F172A;">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:24px;">👤⚡</span>
          <div>
            <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:20px; font-weight:800; color:#ffffff;">
              Add / Invite New Admin & User
            </h3>
            <span style="font-size:12px; color:#94A3B8;">Create a new Admin, Store Manager, or Customer account</span>
          </div>
        </div>
        <button @click="isInviteUserModalOpen = false" class="close-profile-modal-btn">✕</button>
      </div>

      <form @submit.prevent="handleInviteUser" class="user-profile-modal-body" style="background:#FFFFFF; gap:16px;">
        <div v-if="inviteUserError" style="background:#FEE2E2; color:#B91C1C; padding:10px 14px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid #FCA5A5;">
          ⚠️ {{ inviteUserError }}
        </div>

        <div class="form-group-clean">
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Full Name *</label>
          <input type="text" v-model="inviteForm.name" placeholder="Enter Full Name" required class="clean-input-sm" style="width:100%; padding:10px 12px; font-size:14px; font-weight:600;" />
        </div>

        <div class="form-group-clean">
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Email Address *</label>
          <input type="email" v-model="inviteForm.email" placeholder="Enter Email Address" required class="clean-input-sm" style="width:100%; padding:10px 12px; font-size:14px; font-weight:600;" />
        </div>

        <div class="form-group-clean">
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Phone Number</label>
          <input type="text" v-model="inviteForm.phone" placeholder="Enter Phone Number" class="clean-input-sm" style="width:100%; padding:10px 12px; font-size:14px; font-weight:600;" />
        </div>

        <div class="form-group-clean">
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Password *</label>
          <input type="password" v-model="inviteForm.password" placeholder="Enter Password" required class="clean-input-sm" style="width:100%; padding:10px 12px; font-size:14px; font-weight:600;" />
        </div>

        <div class="form-group-clean">
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Account Role *</label>
          <select v-model="inviteForm.role" class="clean-select" style="width:100%; padding:10px 12px; font-size:14px; font-weight:700; border-radius:8px;">
            <option value="Admin">👑 Admin (Full Control)</option>
          </select>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:12px; border-top:1px solid #E2E8F0; padding-top:16px;">
          <button type="button" class="btn-text-danger" @click="isInviteUserModalOpen = false" style="padding:8px 16px;">Cancel</button>
          <button type="submit" class="btn-primary-blue" :disabled="isSubmittingInvite" style="padding:10px 20px; font-weight:800;">
            {{ isSubmittingInvite ? 'Creating Account...' : '+ Create Account' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  products: Array
});

const emit = defineEmits(['exit-admin', 'add-product', 'delete-product']);

const isMobileMenuOpen = ref(false);
const isSidebarCollapsed = ref(false);
const isProfileDropdownOpen = ref(false);
const currentTab = ref('dashboard');
const selectedReportMonth = ref('2026-08');
const productType = ref('simple');
const adminToast = ref('');
const isSubmitting = ref(false);
const isLoading = ref(false);

// USERS LIST MANAGEMENT STATE
const userSearchQuery = ref('');
const userStatusFilter = ref('all');
const selectedUserProfileModal = ref(null);

const selectedUserOrders = computed(() => {
  if (!selectedUserProfileModal.value) return [];
  const u = selectedUserProfileModal.value;
  const uEmail = (u.email || '').toLowerCase().trim();
  const uPhone = (u.phone || '').trim();
  const uId = u.id;

  return ordersList.value.filter(o => {
    const matchId = uId && o.user_id == uId;
    const matchEmail = uEmail && (o.customer_email || '').toLowerCase().trim() === uEmail;
    const matchPhone = uPhone && uPhone !== 'N/A' && (o.customer_phone || '').includes(uPhone);
    return matchId || matchEmail || matchPhone;
  });
});

const totalUserSpend = computed(() => {
  return selectedUserOrders.value.reduce((sum, o) => sum + Number(o.total || 0), 0);
});

function openUserProfile(u) {
  selectedUserProfileModal.value = u;
  if (ordersList.value.length === 0) {
    fetchOrdersFromBackend();
  }
}

// INVITE / ADD NEW ADMIN OR USER STATE & HANDLER
const isInviteUserModalOpen = ref(false);
const isSubmittingInvite = ref(false);
const inviteUserError = ref('');
const inviteForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'Admin'
});

async function handleInviteUser() {
  if (!inviteForm.value.name || !inviteForm.value.email || !inviteForm.value.password) {
    inviteUserError.value = 'Name, email and password are required.';
    return;
  }

  isSubmittingInvite.value = true;
  inviteUserError.value = '';

  try {
    const res = await axios.post('/api/register', {
      name: inviteForm.value.name,
      email: inviteForm.value.email,
      phone: inviteForm.value.phone,
      password: inviteForm.value.password,
      role: inviteForm.value.role
    });

    isSubmittingInvite.value = false;
    if (res.data && res.data.success) {
      showToast(`Account for ${inviteForm.value.name} (${inviteForm.value.role}) created successfully! 🎉⚡`);
      isInviteUserModalOpen.value = false;
      inviteForm.value = { name: '', email: '', phone: '', password: '', role: 'Admin' };
      fetchBackendUsers();
    } else {
      inviteUserError.value = res.data.message || 'Failed to create user account.';
    }
  } catch (err) {
    isSubmittingInvite.value = false;
    inviteUserError.value = err.response?.data?.message || 'Error creating user account in database.';
  }
}

const usersList = ref([]);

async function fetchBackendUsers() {
  try {
    const res = await axios.get('/api/users');
    if (res.data && res.data.success && Array.isArray(res.data.users)) {
      usersList.value = res.data.users;
    }
  } catch (err) {
    console.error('Error fetching users from database:', err);
  }
}

const filteredUsersList = computed(() => {
  let list = usersList.value;
  if (userStatusFilter.value !== 'all') {
    list = list.filter(u => u.status === userStatusFilter.value);
  }
  if (userSearchQuery.value.trim()) {
    const q = userSearchQuery.value.toLowerCase().trim();
    list = list.filter(u => 
      u.name.toLowerCase().includes(q) || 
      u.email.toLowerCase().includes(q) || 
      u.phone.includes(q)
    );
  }
  return list;
});

function toggleUserStatus(u) {
  u.status = u.status === 'active' ? 'inactive' : 'active';
  showToast(`Account status for ${u.name} updated to ${u.status.toUpperCase()}! 👤✨`);
}

// Dynamic Monthly & Overall Sales Analytics Data from MySQL DB Orders
const monthlyReportData = computed(() => {
  const allOrders = ordersList.value || [];
  
  let filtered = allOrders;
  let monthLabel = 'All-Time Analytics';

  if (selectedReportMonth.value !== 'all') {
    const parts = selectedReportMonth.value.split('-');
    const targetYear = parts[0];
    const targetMonth = parts[1];
    const dateObj = new Date(Number(targetYear), Number(targetMonth) - 1, 1);
    monthLabel = dateObj.toLocaleString('en-US', { month: 'long', year: 'numeric' });

    filtered = allOrders.filter(o => {
      if (!o.created_at) return false;
      const d = new Date(o.created_at);
      if (isNaN(d.getTime())) return false;
      const yr = d.getFullYear().toString();
      const mo = (d.getMonth() + 1).toString().padStart(2, '0');
      return yr === targetYear && mo === targetMonth;
    });
  }

  const totalRevenue = filtered.reduce((sum, o) => sum + Number(o.total || 0), 0);
  const totalOrders = filtered.length;
  const avgOrderValue = totalOrders > 0 ? Math.round(totalRevenue / totalOrders) : 0;

  // Calculate Category-wise revenue breakdown from order items
  const catTotals = {};
  filtered.forEach(ord => {
    let items = ord.items;
    if (typeof items === 'string') {
      try { items = JSON.parse(items); } catch(e) { items = []; }
    }
    if (Array.isArray(items) && items.length > 0) {
      items.forEach(item => {
        const catName = item.category || item.name || 'General Sports';
        const price = Number(item.price || 0);
        const qty = Number(item.qty || item.quantity || 1);
        const lineTotal = price * qty;
        catTotals[catName] = (catTotals[catName] || 0) + (lineTotal > 0 ? lineTotal : Number(ord.total || 0));
      });
    } else {
      catTotals['General Sports'] = (catTotals['General Sports'] || 0) + Number(ord.total || 0);
    }
  });

  const catColors = ['#4B49AC', '#7DA0FA', '#7978E9', '#F3797E', '#10B981', '#F59E0B', '#6366F1', '#EC4899'];
  let catKeys = Object.keys(catTotals);

  // Fallback category breakdown if no orders in selected month yet
  if (catKeys.length === 0) {
    const fallbackCats = categories.value.length > 0 
      ? categories.value.map(c => c.name) 
      : ['Badminton & Rackets', 'Cricket Equipment', 'Fitness & Gym', 'Sports Shoes & Wear'];
    
    catKeys = fallbackCats;
    fallbackCats.forEach(c => { catTotals[c] = 0; });
  }

  const grandCategorySum = Object.values(catTotals).reduce((a, b) => a + b, 0) || 1;
  const categoryBreakdown = catKeys.map((name, i) => {
    const amount = catTotals[name] || 0;
    const percent = Math.round((amount / grandCategorySum) * 100) || 0;
    return {
      name,
      amount,
      percent,
      color: catColors[i % catColors.length]
    };
  }).sort((a, b) => b.amount - a.amount);

  const topCategory = categoryBreakdown[0]?.name || 'N/A';

  return {
    monthLabel,
    totalRevenue,
    totalOrders,
    avgOrderValue,
    topCategory,
    categoryBreakdown
  };
});

// Full Year Month-by-Month Dynamic Sales Graph (Jan - Dec 2026)
const yearlyMonthlySales = computed(() => {
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const allOrders = ordersList.value || [];
  const targetYear = 2026;

  const monthlyStats = monthNames.map((month, monthIdx) => {
    const monthOrders = allOrders.filter(o => {
      if (!o.created_at) return false;
      const d = new Date(o.created_at);
      if (isNaN(d.getTime())) return false;
      return d.getMonth() === monthIdx && d.getFullYear() === targetYear;
    });

    const revenue = monthOrders.reduce((sum, o) => sum + Number(o.total || 0), 0);
    const orders = monthOrders.length;

    return {
      month,
      revenue,
      orders,
      heightPct: 0
    };
  });

  const maxRevenue = Math.max(...monthlyStats.map(s => s.revenue), 1);
  return monthlyStats.map(s => ({
    ...s,
    heightPct: s.revenue > 0 ? Math.max(Math.round((s.revenue / maxRevenue) * 100), 12) : 0
  }));
});

const isAdminAuthenticated = ref(false);
const adminLoginEmail = ref('');
const adminLoginPassword = ref('');
const adminAuthError = ref('');

function checkAdminAuth() {
  const auth = sessionStorage.getItem('chhabra_admin_auth') || localStorage.getItem('chhabra_admin_auth');
  if (auth === 'true') {
    isAdminAuthenticated.value = true;
  } else {
    isAdminAuthenticated.value = false;
  }
}

async function handleAdminLogin() {
  if (!adminLoginEmail.value || !adminLoginPassword.value) {
    adminAuthError.value = 'Please enter both email/phone and password.';
    return;
  }

  adminAuthError.value = '';
  try {
    const res = await axios.post('/api/login', {
      email: adminLoginEmail.value,
      password: adminLoginPassword.value
    });

    if (res.data && res.data.success && res.data.user) {
      const uRole = String(res.data.user.role || '').toLowerCase();
      const uEmail = String(res.data.user.email || '').toLowerCase();
      if (uRole === 'admin' || uEmail === 'admin@gmail.com') {
        isAdminAuthenticated.value = true;
        sessionStorage.setItem('chhabra_admin_auth', 'true');
        localStorage.setItem('chhabra_admin_auth', 'true');
        adminAuthError.value = '';
        showToast('Admin Authenticated Successfully! 🔐⚡');
      } else {
        adminAuthError.value = 'Access Denied: Account is not an Admin.';
      }
    } else {
      adminAuthError.value = res.data.message || 'Invalid Admin credentials.';
    }
  } catch (err) {
    adminAuthError.value = err.response?.data?.message || 'Invalid Admin credentials or password.';
  }
}

function handleAdminLogout() {
  isAdminAuthenticated.value = false;
  isProfileDropdownOpen.value = false;
  sessionStorage.setItem('chhabra_admin_auth', 'false');
  localStorage.setItem('chhabra_admin_auth', 'false');
  showToast('Admin session logged out successfully! 🔒');
}

const backendProducts = ref([]);
const totalCount = ref(0);

const filters = ref({
  search: '',
  category: 'all',
  brand: 'all',
  tag: 'all',
  stock: 'all',
  type: 'all',
  sort: 'newest'
});

const menuItems = [
  { id: 'all-products', label: 'All Products', icon: '📦' },
  { id: 'orders', label: 'Order Management', icon: '🛒' },
  { id: 'attributes', label: 'Global Attributes', icon: '⚙️' },
  { id: 'brands', label: 'Brands', icon: '🏷️' },
  { id: 'categories', label: 'Categories', icon: '📁' },
  { id: 'tags', label: 'Tags', icon: '🏷️' }
];

const activeMenuItem = computed(() => menuItems.find(m => m.id === currentTab.value) || menuItems[0]);

const displayProducts = computed(() => {
  if (backendProducts.value.length > 0) {
    return backendProducts.value;
  }
  return props.products || [];
});

const newProd = ref({
  name: '',
  code_id: '',
  category: '',
  brand: '',
  price: 5000,
  salePrice: 4500,
  stockStatus: 'In stock',
  specs: '',
  tag: '',
  img: 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80',
  galleryImg: ''
});

const globalAttributes = ref([]);

const showNewAttrBox = ref(false);
const newAttrForm = ref({ name: '', values: '' });
const selectedGlobalAttr = ref('');

const variationsList = ref([]);

const brands = ref([]);

const showBrandForm = ref(false);
const newBrandForm = ref({ name: '', desc: '' });

const categories = ref([]);

const showCatForm = ref(false);
const newCatForm = ref({ name: '', icon: '📦', slug: '' });

const tags = ref([]);

const productCurrentPage = ref(1);
const productItemsPerPage = ref(10);
const productTotalItems = ref(0);
const productTotalPages = ref(1);

function getAttrValuesList(attr) {
  if (!attr.values) return [];
  return attr.values.map(v => typeof v === 'object' ? v.value : v);
}

async function fetchFilteredProducts() {
  isLoading.value = true;
  try {
    const params = {
      search: filters.value.search,
      category: filters.value.category,
      brand: filters.value.brand,
      tag: filters.value.tag,
      stock: filters.value.stock,
      type: filters.value.type,
      sort: filters.value.sort,
      page: productCurrentPage.value,
      per_page: productItemsPerPage.value
    };

    const res = await axios.get('/api/products', { params });
    if (res.data && res.data.success) {
      backendProducts.value = res.data.data;
      totalCount.value = res.data.total ?? res.data.count ?? 0;
      productTotalItems.value = res.data.total ?? 0;
      productTotalPages.value = res.data.last_page ?? 1;
      if (res.data.current_page) {
        productCurrentPage.value = res.data.current_page;
      }
    }
  } catch (e) {
    console.warn('Filter API request error', e);
  } finally {
    isLoading.value = false;
  }
}

function applyFilters() {
  productCurrentPage.value = 1;
  fetchFilteredProducts();
}

function resetFilters() {
  filters.value = {
    search: '',
    category: 'all',
    brand: 'all',
    tag: 'all',
    stock: 'all',
    type: 'all',
    sort: 'newest'
  };
  productCurrentPage.value = 1;
  fetchFilteredProducts();
}

function changeProductPage(p) {
  const target = Number(p);
  if (target >= 1 && target <= productTotalPages.value) {
    productCurrentPage.value = target;
    fetchFilteredProducts();
  }
}

async function fetchDatabaseAttributes() {
  try {
    const res = await axios.get('/api/attributes');
    if (res.data && res.data.success && res.data.data.length > 0) {
      globalAttributes.value = res.data.data.map(attr => ({
        id: attr.id,
        name: attr.name,
        values: attr.values ? attr.values.map(v => v.value) : [],
        inputValue: ''
      }));
    }
  } catch (e) {
    console.warn('Could not load database attributes', e);
  }
}

async function loadAdminPersistedData() {
  try {
    const resC = await axios.get('/api/categories');
    if (resC.data && resC.data.success && Array.isArray(resC.data.data)) {
      categories.value = resC.data.data;
    }
  } catch (e) {}

  try {
    const resB = await axios.get('/api/brands');
    if (resB.data && resB.data.success && Array.isArray(resB.data.data)) {
      brands.value = resB.data.data;
    }
  } catch (e) {}

  try {
    const resT = await axios.get('/api/tags');
    if (resT.data && resT.data.success && Array.isArray(resT.data.data)) {
      tags.value = resT.data.data.map(t => typeof t === 'object' ? t.name : t);
    }
  } catch (e) {}

  try {
    const savedInactiveCat = localStorage.getItem('chhabra_inactive_categories');
    if (savedInactiveCat) {
      inactiveCategories.value = new Set(JSON.parse(savedInactiveCat));
    }
  } catch (e) {}

  try {
    const savedInactiveBrand = localStorage.getItem('chhabra_inactive_brands');
    if (savedInactiveBrand) {
      inactiveBrands.value = new Set(JSON.parse(savedInactiveBrand));
    }
  } catch (e) {}
}

onMounted(() => {
  checkAdminAuth();
  loadAdminPersistedData();
  fetchDatabaseAttributes();
  fetchFilteredProducts();
  fetchOrdersFromBackend();
  fetchBackendUsers();
});

watch(currentTab, (newTab) => {
  if (newTab === 'users') {
    fetchBackendUsers();
  } else if (newTab === 'orders') {
    fetchOrdersFromBackend();
  }
});

function showToast(msg) {
  adminToast.value = msg;
  setTimeout(() => { adminToast.value = ''; }, 3500);
}

async function saveNewProduct() {
  isSubmitting.value = true;
  const isVar = productType.value === 'variable';
  const isInStock = newProd.value.stockStatus !== 'Out of stock';

  const prodToAdd = {
    id: newProd.value.id || ('p_custom_' + Date.now()),
    code_id: newProd.value.code_id || ('SKU-' + Date.now()),
    name: newProd.value.name,
    category: newProd.value.category,
    brand: newProd.value.brand,
    price: Number(newProd.value.price) || 5000,
    old_price: Number(newProd.value.salePrice) || (Number(newProd.value.price) + 1500),
    rating: 5.0,
    reviews: 1,
    tag: newProd.value.tag || 'NEW',
    specs: newProd.value.specs || 'Custom Product Specs',
    img: newProd.value.img || 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80',
    in_stock: isInStock,
    stockStatus: newProd.value.stockStatus || (isInStock ? 'In stock' : 'Out of stock'),
    isVariable: isVar,
    variations: isVar ? variationsList.value : []
  };

  try {
    await axios.post('/api/products', prodToAdd);
  } catch (e) {}

  emit('add-product', prodToAdd);
  showToast(`${isVar ? 'Variable' : 'Simple'} Product "${newProd.value.name}" Saved (${isInStock ? 'In Stock 🟢' : 'Out of Stock 🔴'})! 🎉`);

  newProd.value = {
    name: '',
    code_id: '',
    category: '',
    brand: '',
    price: 5000,
    salePrice: 4500,
    stockStatus: 'In stock',
    specs: '',
    tag: '',
    img: 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80',
    galleryImg: ''
  };

  isSubmitting.value = false;
  fetchFilteredProducts();
  currentTab.value = 'all-products';
}

async function handleDeleteProduct(id) {
  if (confirm('Are you sure you want to delete this product from catalog?')) {
    try {
      await axios.delete(`/api/products/${id}`);
    } catch (e) {}
    emit('delete-product', id);
    fetchFilteredProducts();
    showToast('Product deleted from store catalog.');
  }
}

function editProduct(p) {
  newProd.value = { ...p };
  const stockBool = (p.in_stock !== false && p.in_stock !== 0 && p.in_stock !== 'false');
  newProd.value.stockStatus = (p.stockStatus === 'Out of stock' || !stockBool) ? 'Out of stock' : 'In stock';
  productType.value = (p.isVariable || p.is_variable) ? 'variable' : 'simple';
  if (p.variations && Array.isArray(p.variations) && p.variations.length > 0) {
    variationsList.value = p.variations;
  }
  currentTab.value = 'add-product';
}

async function saveNewAttributeToDb() {
  if (!newAttrForm.value.name.trim()) {
    showToast('Please enter Variable/Attribute Name!');
    return;
  }

  isSubmitting.value = true;
  const initialVals = newAttrForm.value.values
    ? newAttrForm.value.values.split(',').map(v => v.trim()).filter(Boolean)
    : ['Standard'];

  try {
    const res = await axios.post('/api/attributes', {
      name: newAttrForm.value.name.trim(),
      values: initialVals
    });

    if (res.data && res.data.success) {
      const savedAttr = res.data.data;
      globalAttributes.value.push({
        id: savedAttr.id,
        name: savedAttr.name,
        values: savedAttr.values ? savedAttr.values.map(v => v.value) : initialVals,
        inputValue: ''
      });
      showToast(`Variable '${savedAttr.name}' SAVED TO DATABASE! 💾🎉`);
    } else {
      globalAttributes.value.push({
        name: newAttrForm.value.name.trim(),
        values: initialVals,
        inputValue: ''
      });
      showToast(`Variable '${newAttrForm.value.name}' saved!`);
    }
  } catch (e) {
    globalAttributes.value.push({
      name: newAttrForm.value.name.trim(),
      values: initialVals,
      inputValue: ''
    });
    showToast(`Variable '${newAttrForm.value.name}' saved to catalog!`);
  }

  newAttrForm.value = { name: '', values: '' };
  showNewAttrBox.value = false;
  isSubmitting.value = false;
}

async function addPredefinedAttr() {
  if (selectedGlobalAttr.value) {
    newAttrForm.value.name = selectedGlobalAttr.value;
    newAttrForm.value.values = 'Default Value 1, Default Value 2';
    await saveNewAttributeToDb();
    selectedGlobalAttr.value = '';
  }
}

async function deleteAttributeFromDb(attr, idx) {
  const name = attr.name;
  if (attr.id) {
    try {
      await axios.delete(`/api/attributes/${attr.id}`);
    } catch (e) {}
  }
  globalAttributes.value.splice(idx, 1);
  showToast(`Attribute '${name}' deleted from database.`);
}

async function commitNewValueToDb(attr) {
  if (!attr.inputValue || !attr.inputValue.trim()) return;
  const val = attr.inputValue.trim();

  if (attr.id) {
    try {
      await axios.post(`/api/attributes/${attr.id}/values`, { value: val });
    } catch (e) {}
  }

  if (!attr.values.includes(val)) {
    attr.values.push(val);
    showToast(`Added '${val}' to '${attr.name}' in database! 💾🎉`);
  }
  attr.inputValue = '';
}

function deleteValueFromDb(attr, valObj, vIdx) {
  const valText = typeof valObj === 'object' ? valObj.value : valObj;
  attr.values.splice(vIdx, 1);
  showToast(`Removed '${valText}' from ${attr.name}`);
}

function addManualVariation() {
  const newId = 31080 + variationsList.value.length;
  const initialAttrs = {};
  
  globalAttributes.value.forEach(attr => {
    const vals = getAttrValuesList(attr);
    initialAttrs[attr.name] = vals.length > 0 ? vals[0] : '';
  });

  variationsList.value.push({
    id: newId,
    attributes: initialAttrs,
    price: 5000,
    salePrice: 4500,
    sku: `SKU-VAR-${newId}`,
    enabled: true,
    stockStatus: 'In stock',
    expanded: true,
    img: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80'
  });
  showToast(`Added new variation #${newId}! 🎉`);
}

function generateAllVariations() {
  if (globalAttributes.value.length === 0) return;

  let combinations = [{}];
  globalAttributes.value.forEach(attr => {
    const vals = getAttrValuesList(attr);
    if (vals.length === 0) return;

    const temp = [];
    combinations.forEach(combo => {
      vals.forEach(val => {
        temp.push({ ...combo, [attr.name]: val });
      });
    });
    combinations = temp;
  });

  variationsList.value = combinations.map((combo, idx) => ({
    id: 32000 + idx,
    attributes: combo,
    price: 5000 + (idx * 500),
    salePrice: 4500 + (idx * 500),
    sku: `SKU-MATRIX-${32000 + idx}`,
    enabled: true,
    stockStatus: 'In stock',
    expanded: false,
    img: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80'
  }));

  showToast(`Generated ${variationsList.value.length} variation matrix combinations! ⚡🎉`);
}

function removeVariation(id) {
  variationsList.value = variationsList.value.filter(v => v.id !== id);
  showToast(`Variation #${id} removed.`);
}

function onMainImageSelected(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    newProd.value.img = e.target.result;
    showToast(`Image "${file.name}" selected from device! 🖼️🎉`);
  };
  reader.readAsDataURL(file);
}

function onGalleryImageSelected(event) {
  const files = Array.from(event.target.files);
  if (!files.length) return;
  let loaded = 0;
  const newUrls = [];
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      newUrls.push(e.target.result);
      loaded++;
      if (loaded === files.length) {
        const existing = newProd.value.galleryImg ? newProd.value.galleryImg.split(',').map(s => s.trim()).filter(Boolean) : [];
        newProd.value.galleryImg = [...existing, ...newUrls].join(', ');
        showToast(`${files.length} gallery image(s) uploaded! 🖼️🎉`);
      }
    };
    reader.readAsDataURL(file);
  });
}

function onVarFileSelected(event, v) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    v.img = e.target.result;
    showToast(`Variation image updated from device! 🖼️🎉`);
  };
  reader.readAsDataURL(file);
}

function changeVarImage(v) {
  const newImg = prompt('Enter Image URL for variation:', v.img);
  if (newImg) {
    v.img = newImg;
    showToast('Variation image updated!');
  }
}

function addBrand() {
  showBrandForm.value = !showBrandForm.value;
}

const editingBrandKey = ref(null);
const editingBrandForm = ref({ name: '', desc: '', status: 'active' });
const inactiveBrands = ref(new Set());

function isBrandActive(brandName) {
  return !inactiveBrands.value.has(brandName);
}

function toggleBrandStatus(b) {
  const identifier = b.name;
  const newSet = new Set(inactiveBrands.value);
  if (newSet.has(identifier)) {
    newSet.delete(identifier);
    showToast(`Brand '${b.name}' is now Active 🟢`);
  } else {
    newSet.add(identifier);
    showToast(`Brand '${b.name}' is now Inactive 🔴`);
  }
  inactiveBrands.value = newSet;
  try {
    localStorage.setItem('chhabra_inactive_brands', JSON.stringify(Array.from(newSet)));
  } catch (e) {}
}

function startEditBrand(b) {
  const key = b.id || b.name;
  editingBrandKey.value = key;
  editingBrandForm.value = {
    name: b.name,
    desc: b.desc || b.description || '',
    status: isBrandActive(b.name) ? 'active' : 'inactive'
  };
}

function cancelEditBrand() {
  editingBrandKey.value = null;
  editingBrandForm.value = { name: '', desc: '', status: 'active' };
}

async function saveEditBrand(originalBrand) {
  if (!editingBrandForm.value.name.trim()) {
    showToast('Brand name cannot be empty!');
    return;
  }

  const updatedName = editingBrandForm.value.name.trim();
  const updatedDesc = editingBrandForm.value.desc.trim() || 'Official Sports Equipment Brand';
  const updatedStatus = editingBrandForm.value.status;

  const oldIdentifier = originalBrand.name;
  const idx = brands.value.findIndex(b => (b.id && b.id === originalBrand.id) || b.name === originalBrand.name);

  if (idx !== -1) {
    brands.value[idx] = {
      ...brands.value[idx],
      name: updatedName,
      desc: updatedDesc,
      description: updatedDesc
    };

    const newIdentifier = updatedName;
    const newSet = new Set(inactiveBrands.value);
    if (newSet.has(oldIdentifier)) newSet.delete(oldIdentifier);
    if (updatedStatus === 'inactive') {
      newSet.add(newIdentifier);
    } else {
      newSet.delete(newIdentifier);
    }
    inactiveBrands.value = newSet;

    try {
      localStorage.setItem('chhabra_brands', JSON.stringify(brands.value));
      localStorage.setItem('chhabra_inactive_brands', JSON.stringify(Array.from(newSet)));
    } catch (e) {}

    try {
      await axios.put(`/api/brands/${originalBrand.id || encodeURIComponent(oldIdentifier)}`, {
        name: updatedName,
        desc: updatedDesc
      });
    } catch (e) {}

    const statusText = updatedStatus === 'active' ? 'Active 🟢' : 'Inactive 🔴';
    showToast(`Brand "${updatedName}" updated (${statusText})! ✏️🎉`);
  }

  editingBrandKey.value = null;
}

async function saveBrand() {
  if (!newBrandForm.value.name.trim()) {
    showToast('Please enter brand name!');
    return;
  }
  const name = newBrandForm.value.name.trim();
  const desc = newBrandForm.value.desc.trim() || 'Official Sports Equipment Brand';

  const newB = { name, desc, description: desc };
  brands.value.push(newB);

  try {
    localStorage.setItem('chhabra_brands', JSON.stringify(brands.value));
  } catch (e) {}

  try {
    await axios.post('/api/brands', newB);
    await loadAdminPersistedData();
  } catch (e) {}

  showToast(`Brand "${name}" added to Portfolio & Database! 🎉`);
  newBrandForm.value = { name: '', desc: '' };
  showBrandForm.value = false;
}

function addCategory() {
  showCatForm.value = !showCatForm.value;
}

const editingCategoryKey = ref(null);
const editingCategoryForm = ref({ name: '', icon: '📦', slug: '', status: 'active' });
const inactiveCategories = ref(new Set());

function isCategoryActive(catNameOrSlug) {
  return !inactiveCategories.value.has(catNameOrSlug);
}

function toggleCategoryStatus(c) {
  const identifier = c.slug || c.name;
  const newSet = new Set(inactiveCategories.value);
  if (newSet.has(identifier)) {
    newSet.delete(identifier);
    showToast(`Category '${c.name}' is now Active 🟢`);
  } else {
    newSet.add(identifier);
    showToast(`Category '${c.name}' is now Inactive 🔴`);
  }
  inactiveCategories.value = newSet;
  try {
    localStorage.setItem('chhabra_inactive_categories', JSON.stringify(Array.from(newSet)));
  } catch (e) {}
}

function startEditCategory(c) {
  const key = c.id || c.slug || c.name;
  editingCategoryKey.value = key;
  editingCategoryForm.value = {
    name: c.name,
    icon: c.icon || '📦',
    slug: c.slug || c.name.toLowerCase().replace(/\s+/g, '-'),
    status: isCategoryActive(c.slug || c.name) ? 'active' : 'inactive'
  };
}

function cancelEditCategory() {
  editingCategoryKey.value = null;
  editingCategoryForm.value = { name: '', icon: '📦', slug: '', status: 'active' };
}

async function saveEditCategory(originalCat) {
  if (!editingCategoryForm.value.name.trim()) {
    showToast('Category name cannot be empty!');
    return;
  }

  const updatedName = editingCategoryForm.value.name.trim();
  const updatedIcon = editingCategoryForm.value.icon.trim() || '📦';
  const updatedSlug = editingCategoryForm.value.slug.trim() || updatedName.toLowerCase().replace(/\s+/g, '-');
  const updatedStatus = editingCategoryForm.value.status;

  const oldIdentifier = originalCat.slug || originalCat.name;
  const idx = categories.value.findIndex(c => (c.id && c.id === originalCat.id) || c.slug === originalCat.slug || c.name === originalCat.name);

  if (idx !== -1) {
    categories.value[idx] = {
      ...categories.value[idx],
      name: updatedName,
      icon: updatedIcon,
      slug: updatedSlug
    };

    const newIdentifier = updatedSlug || updatedName;
    const newSet = new Set(inactiveCategories.value);
    if (newSet.has(oldIdentifier)) newSet.delete(oldIdentifier);
    if (updatedStatus === 'inactive') {
      newSet.add(newIdentifier);
    } else {
      newSet.delete(newIdentifier);
    }
    inactiveCategories.value = newSet;

    try {
      localStorage.setItem('chhabra_categories', JSON.stringify(categories.value));
      localStorage.setItem('chhabra_inactive_categories', JSON.stringify(Array.from(newSet)));
    } catch (e) {}

    try {
      await axios.put(`/api/categories/${originalCat.id || encodeURIComponent(oldIdentifier)}`, {
        name: updatedName,
        icon: updatedIcon,
        slug: updatedSlug
      });
    } catch (e) {}

    const statusText = updatedStatus === 'active' ? 'Active 🟢' : 'Inactive 🔴';
    showToast(`Category "${updatedName}" updated (${statusText})! ✏️🎉`);
  }

  editingCategoryKey.value = null;
}

async function saveCategory() {
  if (!newCatForm.value.name.trim()) {
    showToast('Please enter category name!');
    return;
  }
  const name = newCatForm.value.name.trim();
  const icon = newCatForm.value.icon.trim() || '📦';
  const slug = newCatForm.value.slug.trim() || name.toLowerCase().replace(/\s+/g, '-');

  const newCat = { name, icon, slug };
  categories.value.push(newCat);

  try {
    localStorage.setItem('chhabra_categories', JSON.stringify(categories.value));
  } catch (e) {}

  try {
    await axios.post('/api/categories', newCat);
    await loadAdminPersistedData();
  } catch (e) {}

  showToast(`Category "${name}" saved to Database & Catalog! 🎉`);
  newCatForm.value = { name: '', icon: '📦', slug: '' };
  showCatForm.value = false;
}

const attributeSearchQuery = ref('');
const brandSearchQuery = ref('');
const categorySearchQuery = ref('');
const tagSearchQuery = ref('');
const showTagForm = ref(false);
const newTagInput = ref('');
const editingTagOldName = ref(null);
const editingTagNewValue = ref('');
const editingTagStatus = ref('active');
const inactiveTags = ref(new Set());

function isTagActive(tagName) {
  return !inactiveTags.value.has(tagName);
}

function toggleTagStatus(tagName) {
  const newSet = new Set(inactiveTags.value);
  if (newSet.has(tagName)) {
    newSet.delete(tagName);
    showToast(`Tag '🏷️ ${tagName}' is now Active 🟢`);
  } else {
    newSet.add(tagName);
    showToast(`Tag '🏷️ ${tagName}' is now Inactive 🔴`);
  }
  inactiveTags.value = newSet;
  try {
    localStorage.setItem('chhabra_inactive_tags', JSON.stringify(Array.from(newSet)));
  } catch (e) {}
}

function startEditTag(tagName) {
  editingTagOldName.value = tagName;
  editingTagNewValue.value = tagName;
  editingTagStatus.value = isTagActive(tagName) ? 'active' : 'inactive';
}

function cancelEditTag() {
  editingTagOldName.value = null;
  editingTagNewValue.value = '';
  editingTagStatus.value = 'active';
}

async function saveEditTag(oldName) {
  if (!editingTagNewValue.value || !editingTagNewValue.value.trim()) {
    showToast('Tag name cannot be empty!');
    return;
  }
  const updatedTag = editingTagNewValue.value.trim().toUpperCase();
  const idx = tags.value.indexOf(oldName);
  if (idx !== -1) {
    tags.value[idx] = updatedTag;

    const newSet = new Set(inactiveTags.value);
    if (newSet.has(oldName)) newSet.delete(oldName);
    if (editingTagStatus.value === 'inactive') {
      newSet.add(updatedTag);
    } else {
      newSet.delete(updatedTag);
    }
    inactiveTags.value = newSet;

    try {
      localStorage.setItem('chhabra_tags', JSON.stringify(tags.value));
      localStorage.setItem('chhabra_inactive_tags', JSON.stringify(Array.from(newSet)));
    } catch (e) {}

    try {
      await axios.put(`/api/tags/${encodeURIComponent(oldName)}`, { name: updatedTag });
    } catch (e) {}

    const statusText = editingTagStatus.value === 'active' ? 'Active 🟢' : 'Inactive 🔴';
    showToast(`Tag updated to '🏷️ ${updatedTag}' (${statusText}) successfully! ✏️🎉`);
  }
  editingTagOldName.value = null;
  editingTagNewValue.value = '';
  editingTagStatus.value = 'active';
}

const filteredAttributesList = computed(() => {
  if (!attributeSearchQuery.value.trim()) return globalAttributes.value;
  const q = attributeSearchQuery.value.toLowerCase().trim();
  return globalAttributes.value.filter(attr => {
    const nameMatch = attr.name.toLowerCase().includes(q);
    const valsMatch = attr.values && attr.values.some(v => {
      const valStr = typeof v === 'object' ? v.value : v;
      return valStr.toLowerCase().includes(q);
    });
    return nameMatch || valsMatch;
  });
});

async function saveTagFromForm() {
  if (!newTagInput.value || !newTagInput.value.trim()) {
    showToast('Please enter a Tag name!');
    return;
  }
  const tagFormatted = newTagInput.value.trim().toUpperCase();
  if (!tags.value.includes(tagFormatted)) {
    tags.value.push(tagFormatted);
    try {
      localStorage.setItem('chhabra_tags', JSON.stringify(tags.value));
    } catch (e) {}

    try {
      await axios.post('/api/tags', { name: tagFormatted });
    } catch (e) {}

    showToast(`Tag '🏷️ ${tagFormatted}' added successfully! 🎉`);
  } else {
    showToast(`Tag '${tagFormatted}' already exists!`);
  }
  newTagInput.value = '';
  showTagForm.value = false;
}

const filteredBrands = computed(() => {
  if (!brandSearchQuery.value.trim()) return brands.value;
  const q = brandSearchQuery.value.toLowerCase().trim();
  return brands.value.filter(b => 
    b.name.toLowerCase().includes(q) || 
    (b.desc && b.desc.toLowerCase().includes(q))
  );
});

const filteredCategories = computed(() => {
  if (!categorySearchQuery.value.trim()) return categories.value;
  const q = categorySearchQuery.value.toLowerCase().trim();
  return categories.value.filter(c => 
    c.name.toLowerCase().includes(q) || 
    (c.slug && c.slug.toLowerCase().includes(q))
  );
});

const filteredTagsList = computed(() => {
  if (!tagSearchQuery.value.trim()) return tags.value;
  const q = tagSearchQuery.value.toLowerCase().trim();
  return tags.value.filter(t => t.toLowerCase().includes(q));
});

function deleteBrand(idx) {
  const name = brands.value[idx].name;
  brands.value.splice(idx, 1);
  showToast(`Brand "${name}" removed.`);
}

function deleteCategory(idx) {
  const name = categories.value[idx].name;
  categories.value.splice(idx, 1);
  showToast(`Category "${name}" removed.`);
}

function removeTagByName(tagName) {
  const idx = tags.value.indexOf(tagName);
  if (idx !== -1) {
    tags.value.splice(idx, 1);
    showToast(`Tag "${tagName}" removed.`);
  }
}

function removeTag(idx) {
  const removedName = tags.value[idx];
  tags.value.splice(idx, 1);
  showToast(`Tag '🏷️ ${removedName}' removed.`);
}

/* CUSTOMER ORDERS MANAGEMENT ENGINE */
const ordersList = ref([]);
const orderSearchQuery = ref('');
const orderStatusFilter = ref('all');
const orderDateFilter = ref('all');
const orderSortBy = ref('newest');
const orderCurrentPage = ref(1);
const orderItemsPerPage = ref(10);
const selectedOrderModal = ref(null);

watch([orderSearchQuery, orderStatusFilter, orderDateFilter, orderSortBy, orderItemsPerPage], () => {
  orderCurrentPage.value = 1;
});

async function fetchOrdersFromBackend() {
  try {
    const res = await axios.get('/api/orders');
    if (res.data && res.data.success) {
      ordersList.value = res.data.orders;
    }
  } catch (e) {
    console.error('Failed to fetch orders from database', e);
  }
}

const filteredOrdersList = computed(() => {
  let list = ordersList.value.filter(ord => {
    let matchSearch = true;
    if (orderSearchQuery.value.trim()) {
      const q = orderSearchQuery.value.toLowerCase().trim();
      matchSearch = (
        (ord.order_number && ord.order_number.toLowerCase().includes(q)) ||
        (ord.customer_name && ord.customer_name.toLowerCase().includes(q)) ||
        (ord.customer_phone && ord.customer_phone.toLowerCase().includes(q)) ||
        (ord.customer_email && ord.customer_email.toLowerCase().includes(q)) ||
        (ord.address && ord.address.toLowerCase().includes(q))
      );
    }

    let matchStatus = true;
    if (orderStatusFilter.value !== 'all') {
      matchStatus = ord.status === orderStatusFilter.value;
    }

    let matchDate = true;
    if (orderDateFilter.value !== 'all' && ord.created_at) {
      const orderDate = new Date(ord.created_at);
      const now = new Date();
      if (orderDateFilter.value === 'today') {
        matchDate = orderDate.toDateString() === now.toDateString();
      } else if (orderDateFilter.value === 'week') {
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(now.getDate() - 7);
        matchDate = orderDate >= sevenDaysAgo;
      } else if (orderDateFilter.value === 'month') {
        matchDate = orderDate.getMonth() === now.getMonth() && orderDate.getFullYear() === now.getFullYear();
      }
    }

    return matchSearch && matchStatus && matchDate;
  });

  // Sorting logic
  if (orderSortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
  } else if (orderSortBy.value === 'oldest') {
    list.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
  } else if (orderSortBy.value === 'amount-high') {
    list.sort((a, b) => (b.total || 0) - (a.total || 0));
  } else if (orderSortBy.value === 'amount-low') {
    list.sort((a, b) => (a.total || 0) - (b.total || 0));
  }

  return list;
});

const paginatedOrdersList = computed(() => {
  const start = (orderCurrentPage.value - 1) * orderItemsPerPage.value;
  return filteredOrdersList.value.slice(start, start + orderItemsPerPage.value);
});

const orderTotalPages = computed(() => {
  return Math.ceil(filteredOrdersList.value.length / orderItemsPerPage.value) || 1;
});

const totalOrderRevenue = computed(() => {
  return ordersList.value.reduce((sum, o) => sum + (o.total || 0), 0);
});

function countOrdersByStatus(status) {
  return ordersList.value.filter(o => o.status === status).length;
}

function formatOrderDate(dateStr) {
  if (!dateStr) return 'Just Now';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

function getStatusDropdownStyle(status) {
  switch (status) {
    case 'Confirmed': return 'background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
    case 'Processing': return 'background:#E0F2FE; color:#0369A1; border:1px solid #7DD3FC; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
    case 'Shipped': return 'background:#F3E8FF; color:#7E22CE; border:1px solid #C084FC; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
    case 'Delivered': return 'background:#DCFCE7; color:#15803D; border:1px solid #86EFAC; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
    case 'Cancelled': return 'background:#FEE2E2; color:#B91C1C; border:1px solid #FCA5A5; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
    default: return 'background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; font-weight:800; border-radius:6px; padding:4px 8px; font-size:12px;';
  }
}

async function updateOrderStatusOnBackend(ord) {
  try {
    const res = await axios.patch(`/api/orders/${ord.id}/status`, { status: ord.status });
    if (res.data && res.data.success) {
      showToast(`Order #${ord.order_number} status updated to ${ord.status}! 📦🎉`);
    }
  } catch (e) {
    showToast(`Order #${ord.order_number} status updated locally.`);
  }
}

function viewOrderDetailsModal(ord) {
  selectedOrderModal.value = ord;
}
</script>

<style scoped>
/* ---------------------------------------------------- */
/* USER PROFILE & ORDER HISTORY MODAL STYLES */
/* ---------------------------------------------------- */
.user-profile-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.75);
  backdrop-filter: blur(4px);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.user-profile-modal-card {
  background: #FFFFFF;
  border-radius: 16px;
  width: 100%;
  max-width: 840px;
  max-height: 88vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
  animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalFadeIn {
  from { opacity: 0; transform: scale(0.96) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.user-profile-modal-header {
  background: #0F172A;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #1E293B;
}

.close-profile-modal-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #FFFFFF;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.close-profile-modal-btn:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.05);
}

.user-profile-modal-body {
  padding: 24px;
  overflow-y: auto;
  background: #F8FAFC;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.user-metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
}

.user-metric-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  padding: 16px;
  border-radius: 12px;
  border-left-width: 4px;
  border-left-style: solid;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.user-metric-label {
  font-size: 11px;
  font-weight: 800;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: block;
}

.user-metric-val {
  display: block;
  font-size: 15px;
  color: #0F172A;
  margin-top: 4px;
  font-family: 'Outfit', sans-serif;
  word-break: break-all;
}

.user-orders-box {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

.empty-user-orders {
  text-align: center;
  padding: 40px 20px;
  background: #F8FAFC;
  border-radius: 12px;
  border: 2px dashed #E2E8F0;
}

.user-order-card {
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 16px;
  background: #FAFAFA;
  transition: all 0.2s ease;
}

.user-order-card:hover {
  border-color: #C7D2FE;
  box-shadow: 0 4px 12px rgba(75, 73, 172, 0.06);
}

.user-order-card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #E2E8F0;
  padding-bottom: 12px;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 10px;
}

.user-order-item-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #FFFFFF;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #F1F5F9;
}

.user-order-footer-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  color: #475569;
  flex-wrap: wrap;
  gap: 10px;
  background: #FFFFFF;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #F1F5F9;
}

/* ---------------------------------------------------- */
/* REPORT & GRAPH ANALYTICS STYLES */
/* ---------------------------------------------------- */
.report-month-filter-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #FFFFFF;
  padding: 8px 16px;
  border-radius: 12px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

.filter-lbl {
  font-size: 13px;
  font-weight: 700;
  color: #475569;
}

.month-select-dropdown {
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 13px;
  font-weight: 700;
  color: #4B49AC;
  background: #F8FAFC;
  outline: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.month-select-dropdown:hover {
  border-color: #4B49AC;
}

/* YEARLY BAR GRAPH */
.yearly-bar-graph-container {
  width: 100%;
  padding-top: 16px;
}

.bar-graph-bars {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 190px;
  border-bottom: 2px solid #E2E8F0;
  padding-bottom: 6px;
}

.bar-item-col {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  height: 100%;
  justify-content: flex-end;
  position: relative;
}

.bar-val-tooltip {
  font-size: 10px;
  font-weight: 800;
  color: #4B49AC;
  margin-bottom: 4px;
}

.bar-pillar {
  width: 22px;
  background: #7DA0FA;
  border-radius: 6px 6px 0 0;
  transition: all 0.3s ease;
}

.bar-item-col:hover .bar-pillar, .bar-item-col.highlight-month .bar-pillar {
  background: #4B49AC;
  box-shadow: 0 4px 12px rgba(75, 73, 172, 0.4);
}

.bar-month-lbl {
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
  margin-top: 8px;
}

/* CATEGORY SALES BREAKDOWN */
.category-sales-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-top: 10px;
}

.category-sale-row {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.cat-sale-header {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.cat-name {
  font-weight: 700;
  color: #1E293B;
}

.cat-revenue {
  color: #4B49AC;
  font-size: 13.5px;
}

.cat-pct {
  color: #64748B;
  font-size: 11.5px;
  font-weight: 600;
}

.progress-bar-bg {
  width: 100%;
  height: 10px;
  background: #F1F5F9;
  border-radius: 6px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  border-radius: 6px;
  transition: width 0.5s ease;
}

/* ---------------------------------------------------- */
/* admin-tem DASHBOARD MASTER THEME STYLES */
/* ---------------------------------------------------- */
.admin-tem-wrapper {
  min-height: 100vh;
  background: #F8FAFC;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  color: #1E293B;
}

.admin-tem-layout {
  display: flex;
  height: calc(100vh - 64px);
  overflow: hidden;
}

/* admin-tem TOPBAR */
.admin-tem-topbar {
  height: 64px;
  background: #FFFFFF;
  border-bottom: 1px solid #E2E8F0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.brand-box {
  display: flex;
  align-items: center;
  gap: 10px;
}

.admin-tem-logo-img {
  height: 34px;
  width: auto;
  border-radius: 6px;
}

.admin-tem-brand-text {
  font-family: 'Outfit', sans-serif;
  font-size: 20px;
  font-weight: 800;
  color: #4F46E5;
  letter-spacing: -0.5px;
}

.admin-tem-toggle-btn {
  background: none;
  border: none;
  color: #64748B;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.admin-tem-toggle-btn:hover {
  background: #EEF2FF;
  color: #4F46E5;
}

.admin-tem-search-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #F1F5F9;
  padding: 8px 16px;
  border-radius: 20px;
  width: 280px;
  border: 1px solid #E2E8F0;
}

.admin-tem-search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  color: #0F172A;
  width: 100%;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 18px;
}

.admin-tem-icon-btn {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #F1F5F9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.admin-tem-icon-btn:hover {
  background: #EEF2FF;
  color: #4F46E5;
}

.notification-badge-dot {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: #EF4444;
  border: 2px solid #FFFFFF;
  border-radius: 50%;
}

.admin-tem-user-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 30px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  transition: all 0.2s ease;
}

.admin-tem-user-profile:hover {
  background: #EEF2FF;
  border-color: #C7D2FE;
}

.user-avatar-img {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name-text {
  font-family: 'Outfit', sans-serif;
  font-weight: 700;
  font-size: 13.5px;
  color: #0F172A;
}

/* PROFILE DROPDOWN CARD */
.profile-dropdown-card {
  position: absolute;
  top: 54px;
  right: 0;
  width: 270px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.15);
  padding: 18px;
  z-index: 1000;
  animation: dropdownFadeIn 0.2s ease-out;
}

@keyframes dropdownFadeIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.dp-user-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dp-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
}

.dp-details {
  display: flex;
  flex-direction: column;
}

.dp-name {
  font-family: 'Outfit', sans-serif;
  font-size: 15px;
  font-weight: 800;
  color: #0F172A;
}

.dp-role {
  font-size: 11px;
  font-weight: 700;
  color: #4F46E5;
}

.dp-email {
  font-size: 11.5px;
  color: #64748B;
}

.dp-divider {
  height: 1px;
  background: #E2E8F0;
  margin: 12px 0;
}

.dp-menu-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.dp-menu-item {
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  transition: all 0.2s ease;
}

.dp-menu-item:hover {
  background: #EEF2FF;
  color: #4F46E5;
}

.dp-logout-item {
  color: #EF4444;
  font-weight: 700;
}

.dp-logout-item:hover {
  background: #FEF2F2;
  color: #DC2626;
}

/* FIXED NON-SCROLLABLE SIDEBAR WITH FULL COLLAPSE MODE */
.admin-tem-sidebar {
  width: 250px;
  height: calc(100vh - 64px);
  position: sticky;
  top: 64px;
  flex-shrink: 0;
  overflow-y: auto;
  background: #FFFFFF;
  border-right: 1px solid #E2E8F0;
  padding: 20px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 90;
}

/* FULL COLLAPSE MODE: HIDE TEXT, SHOW ONLY ICONS */
.admin-tem-sidebar.collapsed {
  width: 72px !important;
  padding: 20px 8px;
}

.admin-tem-sidebar.collapsed .menu-label,
.admin-tem-sidebar.collapsed .menu-arrow,
.admin-tem-sidebar.collapsed .pro-badge,
.admin-tem-sidebar.collapsed .btn-admin-tem-store span:not(.menu-icon) {
  display: none !important;
}

.admin-tem-sidebar.collapsed .admin-tem-menu-link {
  justify-content: center;
  padding: 12px 0;
}

.admin-tem-sidebar.collapsed .btn-admin-tem-store {
  justify-content: center;
  padding: 10px 0;
}

.admin-tem-menu {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.admin-tem-menu-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  color: #64748B;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  transition: all 0.25s ease;
}

.admin-tem-menu-link:hover {
  background: #EEF2FF;
  color: #4F46E5;
}

.admin-tem-menu-link.active {
  background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
  color: #FFFFFF;
  font-weight: 700;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}

.menu-icon {
  font-size: 16px;
}

.menu-label {
  flex: 1;
}

.menu-arrow {
  font-size: 11px;
  background: rgba(255,255,255,0.25);
  padding: 2px 8px;
  border-radius: 10px;
}

.pro-badge {
  font-size: 10px;
  font-weight: 800;
  background: #EFF6FF;
  color: #2563EB;
  border: 1px solid #BFDBFE;
  padding: 2px 6px;
  border-radius: 6px;
}

.btn-admin-tem-store {
  width: 100%;
  padding: 10px;
  background: #F5F7FF;
  color: #4B49AC;
  border: 1px solid #C7D2FE;
  border-radius: 10px;
  font-weight: 700;
  font-size: 12.5px;
  cursor: pointer;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-admin-tem-store:hover {
  background: #4B49AC;
  color: #FFFFFF;
}

/* MAIN CONTENT AREA (INDEPENDENTLY SCROLLABLE BODY) */
.admin-tem-main-content {
  flex: 1;
  height: calc(100vh - 64px);
  overflow-y: auto;
  padding: 30px;
  background: #F5F7FF;
}

/* MOBILE BACKDROP & RESPONSIVENESS */
.mobile-sidebar-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  z-index: 199;
}

@media (max-width: 768px) {
  .admin-tem-sidebar {
    position: fixed;
    top: 64px;
    left: -280px;
    height: calc(100vh - 64px);
    width: 260px;
    z-index: 200;
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
  }

  .admin-tem-sidebar.mobile-open {
    left: 0 !important;
  }

  .admin-tem-topbar {
    padding: 0 14px;
  }

  .admin-tem-search-box {
    display: none;
  }

  .admin-tem-main-content {
    padding: 16px;
  }

  .admin-tem-hero-grid, .admin-tem-charts-grid {
    grid-template-columns: 1fr;
  }
}

/* WELCOME BANNER */
.admin-tem-welcome-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.welcome-title {
  font-family: 'Outfit', sans-serif;
  font-size: 26px;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
}

.welcome-sub {
  font-size: 13.5px;
  color: #64748B;
  margin: 4px 0 0;
}

.alert-highlight {
  color: #4B49AC;
  font-weight: 700;
}

.welcome-date-picker {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 700;
  color: #475569;
  box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

/* HERO GRID (Illustration + 4 KPI Cards) */
.admin-tem-hero-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 28px;
}

.admin-tem-weather-card {
  background: linear-gradient(135deg, #E0ECFF 0%, #D4E4FF 100%);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 220px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(75, 73, 172, 0.06);
}

.weather-info-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 2;
}

.weather-icon {
  font-family: 'Outfit', sans-serif;
  font-size: 28px;
  font-weight: 800;
  color: #0F172A;
}

.weather-location {
  font-size: 13px;
  font-weight: 700;
  color: #475569;
}

.illustration-caption {
  z-index: 2;
  position: relative;
  margin-top: 40px;
}

.illustration-caption strong {
  display: block;
  font-size: 14.5px;
  color: #0F172A;
  font-weight: 800;
}

.illustration-caption p {
  font-size: 12px;
  color: #64748B;
  margin: 2px 0 0;
}

/* 4 KPI CARDS QUAD */
.admin-tem-kpi-quad {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.kpi-card {
  border-radius: 20px;
  padding: 20px;
  color: #FFFFFF;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
  transition: transform 0.25s ease;
}

.kpi-card:hover {
  transform: translateY(-3px);
}

.card-purple-light { background: #7978E9; }
.card-purple-dark  { background: #4B49AC; }
.card-blue-soft    { background: #7DA0FA; }
.card-pink-coral   { background: #F3797E; }

.kpi-title {
  font-size: 13px;
  font-weight: 600;
  opacity: 0.9;
}

.kpi-value {
  font-family: 'Outfit', sans-serif;
  font-size: 28px;
  font-weight: 800;
  margin: 10px 0 4px;
}

.kpi-trend {
  font-size: 11.5px;
  opacity: 0.85;
  font-weight: 500;
}

/* CHARTS SECTION */
.admin-tem-charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 28px;
}

.admin-tem-chart-card {
  background: #FFFFFF;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(75, 73, 172, 0.04);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.chart-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 16px;
}

.chart-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #0F172A;
  margin: 0;
}

.chart-desc {
  font-size: 12px;
  color: #64748B;
  margin: 4px 0 0;
}

.crown-badge {
  font-size: 14px;
}

.view-all-link {
  font-size: 12.5px;
  font-weight: 700;
  color: #4B49AC;
  text-decoration: none;
}

.chart-stats-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 18px;
  padding-bottom: 14px;
  border-bottom: 1px dashed #E2E8F0;
}

.stat-lbl {
  display: block;
  font-size: 11px;
  color: #94A3B8;
  font-weight: 600;
}

.stat-val {
  font-family: 'Outfit', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #4B49AC;
}

.chart-legend-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
  font-size: 12px;
  font-weight: 600;
  color: #64748B;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.leg-dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
}

.leg-dot.sky { background: #7DA0FA; }
.leg-dot.purple { background: #4B49AC; }

.svg-chart-container {
  width: 100%;
}

.svg-line-chart, .svg-bar-chart {
  width: 100%;
  height: 140px;
}

.chart-x-axis {
  display: flex;
  justify-content: space-around;
  font-size: 11px;
  color: #94A3B8;
  font-weight: 600;
  margin-top: 8px;
}

/* MAIN MENU PANEL */
.sidebar-panel-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #F8FAFC;
  padding: 18px 14px;
}

/* USER PROFILE CARD */
.sidebar-profile-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  padding: 10px 14px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.profile-avatar-wrapper {
  position: relative;
  width: 38px;
  height: 38px;
  flex-shrink: 0;
}

.profile-avatar-img {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  object-fit: cover;
}

.online-indicator {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 10px;
  height: 10px;
  background: #10B981;
  border: 2px solid #FFFFFF;
  border-radius: 50%;
}

.profile-info-text {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.profile-name {
  font-family: 'Outfit', sans-serif;
  font-size: 13.5px;
  font-weight: 800;
  color: #0F172A;
  line-height: 1.2;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.profile-role {
  font-size: 11px;
  color: #64748B;
  font-weight: 600;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* NAVIGATION SECTIONS */
.sidebar-nav-sections {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
  overflow-y: auto;
}

.nav-section-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-section-title {
  font-size: 10px;
  font-weight: 800;
  color: #94A3B8;
  letter-spacing: 1.2px;
  padding: 0 10px 6px;
}

.sidebar-nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9.5px 12px;
  border-radius: 10px;
  color: #475569;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.sidebar-nav-item:hover {
  background: #FFFFFF;
  color: #0F172A;
  transform: translateX(3px);
  box-shadow: 0 2px 5px rgba(0,0,0,0.04);
}

.sidebar-nav-item.active {
  background: #EFF6FF;
  color: #2563EB;
  font-weight: 800;
  box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.16);
}

.nav-item-icon {
  font-size: 15px;
}

.nav-item-label {
  flex: 1;
}

.nav-count-badge {
  background: #DBEAFE;
  color: #1E40AF;
  font-size: 11px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 10px;
}

.nav-count-badge.badge-orange {
  background: #FFEDD5;
  color: #C2410C;
}

/* STATUS LIST ITEMS */
.status-list-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  font-size: 12px;
  color: #64748B;
  font-weight: 600;
}

.status-color-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot-orange { background: #F97316; }
.dot-green { background: #10B981; }

.status-item-text {
  font-size: 12px;
}

/* FOOTER ACTIONS */
.sidebar-panel-footer {
  margin-top: 14px;
  padding-top: 12px;
  border-top: 1px solid #E2E8F0;
  display: flex;
  gap: 8px;
}

.btn-sidebar-storefront {
  flex: 1;
  padding: 8.5px 10px;
  background: #FFFFFF;
  color: #2563EB;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-sidebar-storefront:hover {
  background: #2563EB;
  color: #FFFFFF;
  border-color: #2563EB;
}

.btn-sidebar-logout {
  padding: 8.5px 12px;
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FCA5A5;
  border-radius: 8px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-sidebar-logout:hover {
  background: #DC2626;
  color: #FFFFFF;
  border-color: #DC2626;
}

/* MAIN CONTENT AREA */
.admin-main-clean { flex: 1; padding: 32px; background: #F8FAFC; }

.topbar-clean {
  display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;
  background: #FFFFFF; border: 1px solid #CBD5E1; padding: 18px 24px; border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.page-title-clean { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: #0F172A; margin: 0; }
.page-subtitle-clean { font-size: 12.5px; color: #64748B; margin: 4px 0 0; display: flex; align-items: center; gap: 6px; }
.live-status-dot { color: #059669; font-weight: 700; font-size: 11.5px; }

.header-right-actions { display: flex; align-items: center; gap: 16px; }
.btn-header-action {
  padding: 9px 16px; background: #F1F5F9; color: #0284C7; border: 1px solid #CBD5E1;
  border-radius: 6px; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.2s;
}
.btn-header-action:hover { background: #0284C7; color: #FFFFFF; border-color: #0284C7; }

.user-profile-clean { display: flex; align-items: center; gap: 12px; }
.user-details { text-align: right; }
.user-name { display: block; font-size: 13px; color: #0F172A; font-weight: 800; }
.user-role { font-size: 11px; color: #64748B; font-weight: 600; }
.user-avatar-circle {
  width: 40px; height: 40px; background: #0F172A; color: #38BDF8; border-radius: 50%;
  display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; border: 2px solid #38BDF8;
}

/* ENTERPRISE ADMIN FOOTER STYLES */
.admin-footer-clean {
  margin-top: 36px; padding: 20px 24px; background: #FFFFFF; border: 1px solid #CBD5E1;
  border-radius: 12px; display: flex; justify-content: space-between; align-items: center;
  font-size: 12px; color: #64748B; box-shadow: 0 1px 3px rgba(0,0,0,0.04); flex-wrap: wrap; gap: 14px;
}
.footer-brand { font-family: 'Outfit', sans-serif; color: #0F172A; font-weight: 800; margin-right: 8px; }
.system-badge-pill { background: #F1F5F9; color: #334155; padding: 4px 12px; border-radius: 12px; font-weight: 700; font-size: 11.5px; border: 1px solid #E2E8F0; }
.footer-link-btn { background: none; border: none; color: #0284C7; font-weight: 800; cursor: pointer; font-size: 12px; }
.footer-link-btn:hover { text-decoration: underline; }

.toast-banner-success {
  background: #10B981; color: #fff; padding: 14px 20px; border-radius: 8px;
  margin-bottom: 24px; font-weight: 700; font-size: 13.5px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

/* WHITE CARD CONTAINER */
.white-card {
  background: #FFFFFF; border-radius: 12px; padding: 28px;
  border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
}

.card-top-bar {
  display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
}
.card-heading { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: #0F172A; margin: 0; }
.card-sub-count { font-size: 12.5px; color: #64748B; }

/* DYNAMIC FILTER BAR */
.filter-toolbar {
  background: #F1F5F9; border: 1px solid #E2E8F0; padding: 18px;
  border-radius: 8px; margin-bottom: 24px;
}
.filter-grid {
  display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 14px; align-items: flex-end;
}
.filter-field label { display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 6px; }
.filter-input, .filter-select {
  width: 100%; padding: 10px 12px; background: #FFFFFF; border: 1px solid #CBD5E1;
  border-radius: 6px; font-size: 12.5px; color: #0F172A; outline: none; transition: 0.2s;
}
.filter-input:focus, .filter-select:focus { border-color: #0284C7; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15); }
.btn-reset-filters {
  padding: 10px 16px; background: #E2E8F0; color: #334155; border: 1px solid #CBD5E1;
  border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap; transition: 0.2s;
}
.btn-reset-filters:hover { background: #CBD5E1; color: #0F172A; }

/* ORDER STATUS TAB PILLS & PAGINATION NAV BUTTONS */
.tab-pill-btn {
  background: #F1F5F9;
  border: 1px solid #CBD5E1;
  color: #475569;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.tab-pill-btn:hover {
  background: #E2E8F0;
  color: #0F172A;
}
.tab-pill-btn.active {
  background: #0284C7;
  color: #FFFFFF;
  border-color: #0284C7;
  box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);
}

.btn-page-nav {
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  color: #334155;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-page-nav:hover:not(:disabled) {
  background: #F1F5F9;
  border-color: #94A3B8;
  color: #0F172A;
}
.btn-page-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* DATA TABLE (DISTINCT ENTERPRISE BOX GRID) */
.table-responsive {
  overflow-x: auto;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  background: #FFFFFF;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.white-data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0;
}

.white-data-table th {
  background: #F1F5F9;
  color: #0F172A;
  font-family: 'Outfit', sans-serif;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 14px 16px;
  border: 1px solid #CBD5E1;
  text-align: left;
}

.white-data-table td {
  padding: 14px 16px;
  border: 1px solid #E2E8F0;
  font-size: 13px;
  color: #334155;
  vertical-align: middle;
}

.table-row-item:nth-child(even) td {
  background: #FAFAFA;
}

.table-row-item:hover td {
  background: #F0F9FF;
}

.prod-cell { display: flex; align-items: center; gap: 14px; }
.table-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #E2E8F0; }
.prod-name-title { font-size: 14px; color: #0F172A; display: block; }
.prod-specs-sub { font-size: 11.5px; color: #64748B; margin-top: 2px; }

.badge-blue { background: #E0F2FE; color: #0369A1; padding: 4px 10px; border-radius: 14px; font-size: 11.5px; font-weight: 700; }
.brand-text { color: #0F172A; font-weight: 700; }

.price-main { font-weight: 800; color: #0F172A; font-size: 14px; }
.price-old { text-decoration: line-through; color: #94A3B8; font-size: 11.5px; margin-left: 6px; }

.badge-type { padding: 4px 10px; border-radius: 14px; font-size: 11px; font-weight: 800; display: inline-block; }
.badge-type.simple { background: #DCFCE7; color: #15803D; }
.badge-type.variable { background: #FEF3C7; color: #B45309; }

.action-btn-group { display: flex; gap: 8px; justify-content: flex-end; }
.btn-table-edit { padding: 6px 14px; background: #0284C7; color: #fff; border: none; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer; }
.btn-table-delete { padding: 6px 14px; background: #EF4444; color: #fff; border: none; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer; }

/* FORM STYLES */
.type-selector-card {
  display: flex; align-items: center; gap: 16px; padding: 18px 24px;
  background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; margin-bottom: 28px;
}
.type-label { font-size: 13.5px; font-weight: 800; color: #0F172A; }
.type-dropdown {
  padding: 10px 14px; background: #FFFFFF; border: 1px solid #0284C7;
  color: #0F172A; border-radius: 6px; font-size: 13px; font-weight: 700; outline: none;
}
.type-badge-info { font-size: 12px; color: #0284C7; background: #E0F2FE; padding: 4px 12px; border-radius: 12px; font-weight: 600; }

.form-section-card { background: #F8FAFC; padding: 24px; border-radius: 8px; border: 1px solid #E2E8F0; }
.section-card-title { font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800; color: #0F172A; margin: 0 0 18px; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px; }

.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }

.field-box label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; }
.clean-input, .clean-select, .clean-textarea {
  width: 100%; padding: 11px 14px; background: #FFFFFF; border: 1px solid #CBD5E1;
  border-radius: 6px; font-size: 13px; color: #0F172A; outline: none; font-family: inherit;
}
.clean-input.highlight { border-color: #0284C7; background: #F0F9FF; font-weight: 700; }

.btn-primary-blue { background: #0284C7; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; }
.btn-primary-blue:hover { background: #0369A1; }
.btn-secondary { background: #FFFFFF; color: #334155; border: 1px solid #CBD5E1; padding: 10px 16px; border-radius: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
.btn-submit-main { width: 100%; padding: 16px; background: #0F172A; color: #F8FAFC; border: none; border-radius: 8px; font-weight: 800; font-size: 14px; cursor: pointer; margin-top: 24px; }
.btn-submit-main:hover { background: #0284C7; }

/* INLINE BOXES & ATTRIBUTES */
.inline-box-white { background: #FFFFFF; padding: 20px; border-radius: 8px; border: 1px solid #0284C7; }
.attributes-stack { display: flex; flex-direction: column; gap: 14px; margin-top: 18px; }
.attr-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
.attr-card-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; background: #F1F5F9; }
.attr-name { font-size: 15px; color: #0F172A; font-weight: 800; }
.db-pill { font-size: 11px; background: #E0F2FE; color: #0369A1; padding: 2px 8px; border-radius: 10px; margin-left: 8px; font-weight: 700; }
.count-sub { font-size: 12px; color: #64748B; margin-left: 8px; }

.attr-card-body { padding: 18px 20px; }
.tag-chips-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
.chip-removable { background: #E2E8F0; color: #1E293B; padding: 6px 12px; border-radius: 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
.chip-removable:hover { background: #EF4444; color: #fff; }

.inline-add-row { display: flex; gap: 12px; align-items: center; margin-top: 12px; }
.clean-input-sm { flex: 1; padding: 8px 12px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 12px; }

/* VARIATIONS MATRIX STYLES */
.action-btn-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
.var-count-sub { font-size: 12.5px; color: #64748B; font-weight: 600; }
.variations-stack { display: flex; flex-direction: column; gap: 12px; }
.var-card { background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
.var-card.open { border-color: #0284C7; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.1); }
.var-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; }
.var-title { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.var-id-hash { font-weight: 800; color: #0284C7; }
.var-select-inline { display: flex; align-items: center; gap: 6px; background: #F1F5F9; padding: 4px 10px; border-radius: 6px; border: 1px solid #E2E8F0; }
.var-label { font-size: 11.5px; font-weight: 700; color: #475569; }
.var-select { padding: 4px 8px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 4px; font-size: 12px; }

.var-actions { display: flex; align-items: center; gap: 14px; }
.var-price-display { font-weight: 800; color: #16A34A; font-size: 14px; }
.btn-text-danger { background: none; border: none; color: #EF4444; font-size: 12px; font-weight: 700; cursor: pointer; }
.btn-text-blue { background: none; border: none; color: #0284C7; font-size: 12px; font-weight: 800; cursor: pointer; }

.var-drawer { padding: 20px; border-top: 1px solid #E2E8F0; background: #F8FAFC; }
.drawer-grid { display: grid; grid-template-columns: 140px 1fr; gap: 24px; }
.img-picker-box { width: 130px; height: 130px; background: #FFFFFF; border: 2px dashed #0284C7; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; text-align: center; }
.uploaded-thumb { width: 100%; height: 100%; object-fit: cover; }
.img-placeholder { font-size: 11.5px; color: #0284C7; font-weight: 700; }

.checkbox-row { display: flex; gap: 18px; font-size: 13px; color: #334155; margin-bottom: 16px; font-weight: 600; }

/* BRANDS & TAGS */
.brand-grid-clean { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px; margin-top: 18px; }
.brand-item { background: #F8FAFC; border: 1px solid #E2E8F0; padding: 20px; border-radius: 8px; }
.b-title { font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800; color: #0F172A; }
.b-sub { font-size: 12px; color: #64748B; margin: 6px 0 14px; }

.tags-container-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
.tag-card-chip-white {
  display: flex; align-items: center; gap: 10px; padding: 8px 16px;
  background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 20px;
  color: #0F172A; font-size: 13px; font-weight: 700;
}
.tag-remove-btn {
  background: none; border: none; color: #EF4444; font-size: 16px;
  font-weight: bold; cursor: pointer; padding: 0; line-height: 1; transition: 0.2s;
}
.tag-remove-btn:hover { color: #DC2626; transform: scale(1.2); }

/* ---------------------------------------------------- */
/* RESPONSIVE MOBILE VIEW STYLES (≤ 768px & ≤ 1024px)   */
/* ---------------------------------------------------- */
.admin-mobile-header {
  display: none;
}

@media (max-width: 1024px) {
  .admin-main-clean {
    padding: 20px;
  }
  .grid-3 {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 768px) {
  .admin-layout-white {
    flex-direction: column;
  }

  /* MOBILE TOPBAR HEADER */
  .admin-mobile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #0F172A;
    border-bottom: 1px solid #1E293B;
    position: sticky;
    top: 0;
    z-index: 1000;
  }
  .mobile-brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .mobile-logo {
    height: 32px;
    border-radius: 4px;
  }
  .mobile-brand-title {
    color: #F8FAFC;
    font-family: 'Outfit', sans-serif;
    font-size: 15px;
    font-weight: 800;
  }
  .mobile-menu-toggle {
    background: #0284C7;
    color: #FFFFFF;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
  }

  /* MOBILE SIDEBAR SLIDE-OVER / STACK */
  .admin-sidebar-finalui {
    width: 100%;
    display: none;
    border-right: none;
    border-bottom: 1px solid #E2E8F0;
  }
  .admin-sidebar-finalui.mobile-open {
    display: flex;
  }

  .sidebar-brand-box {
    display: none;
  }

  .sidebar-menu-link {
    padding: 12px 18px;
    font-size: 14px;
  }

  /* MAIN CONTENT REDUCED PADDING FOR MOBILE SMARTPHONES */
  .admin-main-clean {
    padding: 12px;
  }

  .topbar-clean {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 18px;
  }

  .page-title-clean {
    font-size: 22px;
  }

  .user-details {
    text-align: left;
  }

  .card-top-bar {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
  }

  .card-heading {
    font-size: 18px;
  }

  .white-card {
    padding: 14px;
    border-radius: 8px;
  }

  /* GRIDS RESPONSIVE COLLAPSE TO SINGLE COLUMN */
  .metrics-grid {
    grid-template-columns: 1fr 1fr !important;
    gap: 10px !important;
  }

  .filter-grid {
    grid-template-columns: 1fr;
  }

  .grid-2, .grid-3, .grid-4 {
    grid-template-columns: 1fr !important;
    gap: 12px;
  }

  /* FORM SELECTORS & BUTTONS */
  .type-selector-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    padding: 14px;
  }

  .type-dropdown {
    width: 100%;
  }

  .action-btn-row {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .var-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 14px;
  }

  .var-actions {
    width: 100%;
    justify-content: space-between;
  }

  .drawer-grid {
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .brand-grid-clean {
    grid-template-columns: 1fr;
    gap: 12px;
  }
}

/* MODAL POPUP DIALOG STYLES */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 20px;
  box-sizing: border-box;
  animation: modalFadeIn 0.2s ease-out forwards;
}

.modal-card {
  width: 100%;
  max-width: 650px;
  max-height: 88vh;
  overflow-y: auto;
  background: #ffffff;
  padding: 24px;
  border-radius: 16px;
  border: 1px solid #CBD5E1;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.05);
  margin: auto;
  box-sizing: border-box;
  animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* MODAL POPUP DIALOG ANIMATIONS */
@keyframes modalFadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes modalSlideUp {
  from { opacity: 0; transform: translateY(18px) scale(0.97); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

@media (max-width: 480px) {
  .metrics-grid {
    grid-template-columns: 1fr !important;
  }
}

/* ADMIN AUTHENTICATION GUARD OVERLAY */
.admin-auth-overlay {
  min-height: 100vh;
  background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.admin-auth-card {
  background: #FFFFFF;
  border-radius: 16px;
  max-width: 440px;
  width: 100%;
  padding: 36px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  border: 1px solid rgba(212, 175, 55, 0.3);
  text-align: center;
}

.auth-lock-badge {
  display: inline-block;
  font-family: monospace;
  font-size: 11px;
  font-weight: 800;
  color: #D4AF37;
  background: #0F172A;
  padding: 6px 14px;
  border-radius: 20px;
  letter-spacing: 1px;
  margin-bottom: 16px;
}

.auth-header-text h2 {
  font-family: 'Outfit', sans-serif;
  font-size: 24px;
  font-weight: 900;
  color: #0F172A;
  margin-bottom: 6px;
}

.auth-header-text p {
  font-size: 13px;
  color: #64748B;
  margin-bottom: 24px;
}

.admin-auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  text-align: left;
}

.auth-error-alert {
  background: #FEE2E2;
  border: 1px solid #EF4444;
  color: #991B1B;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
}

.form-group-clean {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label-clean {
  font-size: 12px;
  font-weight: 700;
  color: #334155;
}

.auth-input-field {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: all 0.2s ease;
}

.auth-input-field:focus {
  border-color: #0F172A;
  box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
}

.btn-auth-submit {
  background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
  color: #F8FAFC;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-family: 'Outfit', sans-serif;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  margin-top: 8px;
  transition: all 0.2s ease;
}

.btn-auth-submit:hover {
  background: #D4AF37;
  color: #0F172A;
}

.btn-auth-cancel {
  background: transparent;
  border: none;
  color: #64748B;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  padding: 6px;
  text-align: center;
}

.btn-auth-cancel:hover {
  color: #0F172A;
}

.btn-admin-logout {
  background: #FEE2E2;
  border: 1px solid #FCA5A5;
  color: #991B1B;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-admin-logout:hover {
  background: #EF4444;
  color: #FFFFFF;
}

.btn-admin-logout-sidebar {
  width: 100%;
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid #EF4444;
  color: #FCA5A5;
  padding: 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  margin-bottom: 8px;
  transition: all 0.2s ease;
}

.btn-admin-logout-sidebar:hover {
  background: #EF4444;
  color: #FFFFFF;
}

.status-toggle-btn {
  border: none;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-status-active {
  background: #DCFCE7;
  color: #166534;
  border: 1px solid #86EFAC;
}
.btn-status-active:hover {
  background: #BBF7D0;
}
.btn-status-inactive {
  background: #FEE2E2;
  color: #991B1B;
  border: 1px solid #FCA5A5;
}
.btn-status-inactive:hover {
  background: #FCA5A5;
  color: #7F1D1D;
}
</style>
