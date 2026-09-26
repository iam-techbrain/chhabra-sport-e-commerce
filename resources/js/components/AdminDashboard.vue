<template>
  <div class="kaiadmin-app-root">
    
    <!-- INITIAL CLEAN FULLSCREEN PRELOADER (Only Chhabra Logo and Brand Name) -->
    <transition name="fade-loader">
      <div v-if="isInitialLoading" class="admin-initial-loader">
        <div class="loader-content text-center">
          <div class="loader-logo-card mb-3 mx-auto">
            <img src="/favicon.png" alt="Chhabra Sports" class="loader-logo-img" />
          </div>
          <h4 class="fw-bold text-white mb-1" style="letter-spacing: 2px;">CHHABRA SPORTS</h4>
          <p class="text-white-50 small mb-3">Admin Management Console</p>
          <div class="loader-spinner mx-auto"></div>
        </div>
      </div>
    </transition>

    <!-- ========================================================= -->
    <!-- 1. ADMIN AUTHENTICATION GUARD (RESTRICTED ACCESS LOGIN)    -->
    <!-- ========================================================= -->
    <div v-if="!isAdminAuthenticated" class="kaiadmin-login-screen">
      <div class="login-card-container">
        <div class="card card-round shadow-lg border-0 login-card">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <div class="login-logo-card mb-3 mx-auto">
                <img 
                  src="/favicon.png" 
                  alt="Chhabra Sports Logo" 
                  class="brand-login-logo"
                />
              </div>
              <h3 class="fw-bold mb-1 title-navy">CHHABRA SPORTS ADMIN</h3>
              <p class="text-muted small">Restricted Store Administration Console · Kaiadmin Lite</p>
            </div>

            <form @submit.prevent="handleAdminLogin">
              <div v-if="adminAuthError" class="alert alert-danger py-2 small mb-3 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>{{ adminAuthError }}</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Admin Email or Username</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                  <input 
                    type="text" 
                    v-model="adminLoginEmail" 
                    class="form-control border-start-0" 
                    placeholder="admin@chhabrasports.com" 
                    required 
                    maxlength="80"
                  />
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold small text-muted">Security Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                  <input 
                    type="password" 
                    v-model="adminLoginPassword" 
                    class="form-control border-start-0" 
                    placeholder="••••••••" 
                    required 
                    maxlength="50"
                  />
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-round w-100 fw-bold py-2 mb-3 shadow-sm">
                <i class="fas fa-unlock-alt me-2"></i> Unlock Dashboard
              </button>

              <button type="button" class="btn btn-outline-secondary btn-round w-100 btn-sm" @click="handleExitAdmin">
                <i class="fas fa-arrow-left me-1"></i> Return to Public Storefront
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- 2. MAIN KAIADMIN DASHBOARD (WHEN AUTHENTICATED)           -->
    <div v-else class="wrapper kaiadmin-wrapper" :class="{ 'sidebar_minimize': isSidebarCollapsed, 'nav_open': isMobileMenuOpen, 'topbar_open': isMobileTopbarOpen }">
      
      <!-- MOBILE SIDEBAR OVERLAY BACKDROP -->
      <div v-if="isMobileMenuOpen" class="kaiadmin-mobile-backdrop" @click="isMobileMenuOpen = false"></div>

      <!-- KAIADMIN SIDEBAR NAVIGATION -->
      <div class="sidebar sidebar-style-2" data-background-color="dark">
        <!-- SIDEBAR LOGO HEADER (PURE KAIADMIN TEMPLATE) -->
        <div class="sidebar-logo">
          <div class="logo-header" data-background-color="dark">
            <a href="#" @click.prevent="currentTab = 'dashboard'" class="logo text-decoration-none">
              <div class="sidebar-logo-card">
                <img
                  src="/favicon.png"
                  alt="Chhabra Sports Logo"
                  class="sidebar-logo-img"
                />
              </div>
              <div class="sidebar-brand-text-wrap">
                <span class="brand-title">CHHABRA SPORTS</span>
                <span class="sidebar-sub-badge">ADMIN CONSOLE</span>
              </div>
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar" @click="isSidebarCollapsed = !isSidebarCollapsed" title="Toggle Desktop Sidebar">
                <i class="fas fa-bars text-white"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler" :class="{ 'toggled': isMobileMenuOpen }" @click="isMobileMenuOpen = !isMobileMenuOpen" title="Toggle Navigation">
                <i class="fas fa-bars text-white"></i>
              </button>
            </div>
            <button class="topbar-toggler more" :class="{ 'toggled': isMobileTopbarOpen }" @click="isMobileTopbarOpen = !isMobileTopbarOpen">
              <i class="fas fa-ellipsis-v text-white"></i>
            </button>
          </div>
        </div>

        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              
              <!-- MAIN OVERVIEW SECTION -->
              <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">ANALYTICS & METRICS</h4>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'dashboard' }">
                <a href="#" @click.prevent="currentTab = 'dashboard'; isMobileMenuOpen = false;">
                  <i class="fas fa-chart-line"></i>
                  <p>Dashboard</p>
                  <span class="sidebar-badge badge-live">Live</span>
                </a>
              </li>

              <!-- CATALOG MANAGEMENT SECTION -->
              <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">CATALOG & INVENTORY</h4>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'all-products' }">
                <a href="#" @click.prevent="currentTab = 'all-products'; isMobileMenuOpen = false;">
                  <i class="fas fa-boxes"></i>
                  <p>All Products</p>
                  <span class="sidebar-badge badge-num-primary">{{ productTotalItems || displayProducts.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'add-product' }">
                <a href="#" @click.prevent="currentTab = 'add-product'; isMobileMenuOpen = false;">
                  <i class="fas fa-plus-circle"></i>
                  <p>Add Product</p>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'bulk-upload' }">
                <a href="#" @click.prevent="currentTab = 'bulk-upload'; isMobileMenuOpen = false;">
                  <i class="fas fa-file-excel text-success"></i>
                  <p>Bulk Upload (Excel)</p>
                  <span class="sidebar-badge badge-num-success">CSV</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'categories' }">
                <a href="#" @click.prevent="currentTab = 'categories'; isMobileMenuOpen = false;">
                  <i class="fas fa-folder"></i>
                  <p>Categories</p>
                  <span class="sidebar-badge badge-num-info">{{ categories.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'brands' }">
                <a href="#" @click.prevent="currentTab = 'brands'; isMobileMenuOpen = false;">
                  <i class="fas fa-tags"></i>
                  <p>Brands</p>
                  <span class="sidebar-badge badge-num-secondary">{{ brands.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'tags' }">
                <a href="#" @click.prevent="currentTab = 'tags'; isMobileMenuOpen = false;">
                  <i class="fas fa-bookmark"></i>
                  <p>Tags</p>
                  <span class="sidebar-badge badge-num-light">{{ tags.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'attributes' }">
                <a href="#" @click.prevent="currentTab = 'attributes'; isMobileMenuOpen = false;">
                  <i class="fas fa-sliders-h"></i>
                  <p>Attributes</p>
                  <span class="sidebar-badge badge-num-secondary">{{ globalAttributes.length }}</span>
                </a>
              </li>

              <!-- ORDERS & CUSTOMERS SECTION -->
              <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">ORDERS & CUSTOMERS</h4>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'orders' }">
                <a href="#" @click.prevent="currentTab = 'orders'; isMobileMenuOpen = false;">
                  <i class="fas fa-shopping-cart"></i>
                  <p>Orders</p>
                  <span class="sidebar-badge badge-num-success">{{ ordersList.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'users' }">
                <a href="#" @click.prevent="currentTab = 'users'; isMobileMenuOpen = false;">
                  <i class="fas fa-users"></i>
                  <p>Users List</p>
                  <span class="sidebar-badge badge-num-warning">{{ usersList.length }}</span>
                </a>
              </li>

              <li class="nav-item" :class="{ active: currentTab === 'contact-messages' }">
                <a href="#" @click.prevent="currentTab = 'contact-messages'; isMobileMenuOpen = false;">
                  <i class="fas fa-envelope"></i>
                  <p>Contact Us</p>
                  <span v-if="unreadContactCount > 0" class="sidebar-badge badge-num-danger">{{ unreadContactCount }} NEW</span>
                  <span v-else class="sidebar-badge badge-num-secondary">{{ contactMessagesList.length }}</span>
                </a>
              </li>

              <!-- QUICK STORE ACTIONS -->
              <li class="nav-section">
                <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                <h4 class="text-section">SHORTCUTS</h4>
              </li>

              <li class="nav-item">
                <a href="#" @click.prevent="handleExitAdmin">
                  <i class="fas fa-external-link-alt text-info"></i>
                  <p class="text-info fw-semibold">Live Storefront</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="#" @click.prevent="handleAdminLogout">
                  <i class="fas fa-sign-out-alt text-danger"></i>
                  <p class="text-danger fw-semibold">Logout Session</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <!-- MAIN PANEL -->
      <div class="main-panel">
        
        <!-- MAIN TOPBAR HEADER (PURE KAIADMIN TEMPLATE) -->
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header (Left-Aligned Emblem & Brand Title) -->
            <div class="logo-header" data-background-color="dark">
              <button class="btn btn-toggle sidenav-toggler me-2 border-0 bg-transparent text-white" :class="{ 'toggled': isMobileMenuOpen }" @click="isMobileMenuOpen = !isMobileMenuOpen" title="Toggle Navigation">
                <i class="fas fa-bars fs-5"></i>
              </button>
              <a href="#" @click.prevent="currentTab = 'dashboard'" class="logo text-decoration-none d-flex align-items-center me-auto">
                <div class="sidebar-logo-card me-2">
                  <img
                    src="/favicon.png"
                    alt="Chhabra Sports Logo"
                    class="sidebar-logo-img"
                  />
                </div>
                <div class="sidebar-brand-text-wrap">
                  <span class="brand-title">CHHABRA SPORTS</span>
                  <span class="sidebar-sub-badge">ADMIN CONSOLE</span>
                </div>
              </a>
              <button class="topbar-toggler more border-0 bg-transparent text-white" :class="{ 'toggled': isMobileTopbarOpen }" @click="isMobileTopbarOpen = !isMobileTopbarOpen">
                <i class="fas fa-ellipsis-v fs-5"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>

          <!-- Navbar Header (Pure Kaiadmin Responsive Header) -->
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="button" class="btn btn-search pe-1" @click="applyFilters">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input
                    type="text"
                    placeholder="Search catalog, orders, users..."
                    v-model="filters.search"
                    @input="fetchFilteredProducts"
                    class="form-control"
                  />
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <!-- Mobile Search Dropdown Item -->
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                  <a class="nav-link dropdown-toggle" href="#" @click.prevent="isMobileSearchOpen = !isMobileSearchOpen">
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn" :class="{ show: isMobileSearchOpen }" style="display: block;" v-if="isMobileSearchOpen">
                    <form class="navbar-left navbar-form nav-search" @submit.prevent>
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search products, SKUs..."
                          v-model="filters.search"
                          @input="currentTab = 'all-products'; fetchFilteredProducts();"
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>

                <!-- Public Storefront -->
                <li class="nav-item me-3 d-none d-sm-block">
                  <button class="btn btn-outline-primary btn-round btn-sm" @click="handleExitAdmin">
                    <i class="fas fa-globe me-1"></i> Public Storefront
                  </button>
                </li>

                <!-- Inquiries Notification -->
                <li class="nav-item topbar-icon dropdown hidden-caret me-3">
                  <a class="nav-link" href="#" @click.prevent="currentTab = 'contact-messages'" title="Customer Inquiries">
                    <i class="fa fa-bell"></i>
                    <span v-if="unreadContactCount > 0" class="notification">{{ unreadContactCount }}</span>
                  </a>
                </li>

                <!-- Admin Profile Dropdown -->
                <li class="nav-item topbar-user dropdown hidden-caret position-relative">
                  <a class="dropdown-toggle profile-pic d-flex align-items-center text-decoration-none" href="#" @click.prevent="isProfileDropdownOpen = !isProfileDropdownOpen">
                    <div class="avatar-sm me-2">
                      <img src="https://ui-avatars.com/api/?name=Admin+Manager&background=1572E8&color=ffffff&bold=true" alt="Admin" class="avatar-img rounded-circle" />
                    </div>
                    <span class="profile-username d-none d-md-inline">
                      <span class="op-7">Hi, </span>
                      <span class="fw-bold">Admin Manager</span>
                      <i class="fas fa-angle-down ms-1 text-muted small"></i>
                    </span>
                  </a>

                  <!-- Profile Menu Card -->
                  <div v-if="isProfileDropdownOpen" class="dropdown-menu dropdown-user animated fadeIn show position-absolute end-0 shadow-lg border-0" style="display:block; min-width:290px; margin-top:8px; z-index:1050; border-radius:12px;">
                    <div class="dropdown-user-scroll scrollbar-outer p-3">
                      <div class="user-box d-flex align-items-center mb-3">
                        <div class="avatar-lg me-3 flex-shrink-0">
                          <img src="https://ui-avatars.com/api/?name=Admin+Manager&background=1572E8&color=ffffff&bold=true" alt="Admin" class="avatar-img rounded-circle" style="width:46px; height:46px;" />
                        </div>
                        <div class="u-text overflow-hidden">
                          <h5 class="fw-bold mb-0 text-dark text-truncate">Admin Manager</h5>
                          <p class="text-muted small mb-1 text-truncate">admin@chhabrasports.com</p>
                          <span class="badge bg-success text-white py-1 px-2 fw-semibold" style="margin-left:0 !important; font-size:10.5px; border-radius:6px;">Super Administrator</span>
                        </div>
                      </div>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item py-2" href="#" @click.prevent="currentTab = 'dashboard'; isProfileDropdownOpen = false;">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard Overview
                      </a>
                      <a class="dropdown-item py-2" href="#" @click.prevent="currentTab = 'users'; isProfileDropdownOpen = false;">
                        <i class="fas fa-user-cog me-2 text-info"></i> User Accounts
                      </a>
                      <a class="dropdown-item py-2" href="#" @click.prevent="currentTab = 'orders'; isProfileDropdownOpen = false;">
                        <i class="fas fa-shopping-bag me-2 text-success"></i> Customer Orders
                      </a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item py-2 text-danger fw-semibold" href="#" @click.prevent="handleAdminLogout">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout Session
                      </a>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </nav>
        </div>
        <!-- End Main Header -->

        <!-- CONTAINER CONTENT AREA -->
        <div class="container-fluid px-3 px-md-4 py-4 kaiadmin-content-area">
          
          <!-- TOAST BANNER NOTIFICATION -->
          <div v-if="adminToast" class="alert alert-success alert-dismissible fade show shadow-sm mb-4 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fs-5 me-2 text-success"></i>
            <div class="fw-semibold">{{ adminToast }}</div>
            <button type="button" class="btn-close ms-auto" @click="adminToast = ''"></button>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 1: STORE ANALYTICS & DASHBOARD OVERVIEW               -->
          <!-- ========================================================= -->
          <div v-if="currentTab === 'dashboard'">
            <!-- Page Title Bar -->
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Store Analytics & Sales Performance</h3>
                <h6 class="op-7 mb-2 text-muted">Viewing sales revenue, orders, and category distribution</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0 d-flex flex-wrap align-items-center gap-2 dashboard-header-actions">
                <div class="d-flex align-items-center gap-2 bg-white px-3 py-1 rounded shadow-sm border range-picker-group">
                  <label class="small text-muted fw-bold mb-0 text-nowrap"><i class="far fa-calendar-alt text-primary me-1"></i>Range:</label>
                  <select v-model="selectedReportMonth" class="form-select form-select-sm border-0 bg-transparent" style="min-width: 210px; cursor: pointer;">
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
                <button class="btn btn-primary btn-round btn-sm text-nowrap px-3 shadow-sm" @click="currentTab = 'add-product'">
                  <i class="fas fa-plus me-1"></i> Add Product
                </button>
              </div>
            </div>

            <!-- 4 KAIADMIN STATS KPI CARDS (2x2 Grid on Mobile) -->
            <div class="row g-2 g-md-3 mb-4">
              <!-- KPI 1: Revenue -->
              <div class="col-6 col-md-3">
                <div class="card card-stats card-round shadow-sm border-0 mb-0">
                  <div class="card-body">
                    <div class="row align-items-center g-0">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                          <i class="fas fa-rupee-sign"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-2 ms-sm-3">
                        <div class="numbers">
                          <p class="card-category text-truncate">Revenue</p>
                          <h4 class="card-title text-truncate">₹{{ monthlyReportData.totalRevenue.toLocaleString() }}</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- KPI 2: Total Orders -->
              <div class="col-6 col-md-3">
                <div class="card card-stats card-round shadow-sm border-0 mb-0">
                  <div class="card-body">
                    <div class="row align-items-center g-0">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                          <i class="fas fa-shopping-bag"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-2 ms-sm-3">
                        <div class="numbers">
                          <p class="card-category text-truncate">Orders</p>
                          <h4 class="card-title text-truncate">{{ monthlyReportData.totalOrders }} Placed</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- KPI 3: Avg Order Value -->
              <div class="col-6 col-md-3">
                <div class="card card-stats card-round shadow-sm border-0 mb-0">
                  <div class="card-body">
                    <div class="row align-items-center g-0">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                          <i class="fas fa-calculator"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-2 ms-sm-3">
                        <div class="numbers">
                          <p class="card-category text-truncate">Avg Order</p>
                          <h4 class="card-title text-truncate">₹{{ monthlyReportData.avgOrderValue.toLocaleString() }}</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- KPI 4: Top Category -->
              <div class="col-6 col-md-3">
                <div class="card card-stats card-round shadow-sm border-0 mb-0">
                  <div class="card-body">
                    <div class="row align-items-center g-0">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                          <i class="fas fa-crown"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-2 ms-sm-3">
                        <div class="numbers">
                          <p class="card-category text-truncate">Top Category</p>
                          <h4 class="card-title text-truncate" style="font-size:16px;">{{ monthlyReportData.topCategory }}</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CHARTS ROW: FULL YEAR GRAPH & CATEGORY BREAKDOWN -->
            <div class="row">
              <!-- Full Year Bar Chart -->
              <div class="col-md-8">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <div class="card-head-row d-flex align-items-center justify-content-between">
                      <div>
                        <h4 class="card-title fw-bold" style="color:#2A2F5B;">Full Year Month-by-Month Sales (2026)</h4>
                        <p class="card-category mb-0">Exact sales revenue (₹) across all months (Jan to Dec)</p>
                      </div>
                      <span class="annual-trend-pill">
                        <i class="fas fa-chart-line me-1"></i> Annual Trend
                      </span>
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- Interactive Bar Chart -->
                    <div class="kaiadmin-bar-chart-container py-3">
                      <div class="d-flex align-items-end justify-content-between chart-bars-track" style="height: 220px;">
                        <div 
                          v-for="m in yearlyMonthlySales" 
                          :key="m.month" 
                          class="bar-item text-center flex-fill px-1"
                        >
                          <div class="small fw-bold text-muted mb-1" style="font-size: 11px;">
                            ₹{{ (m.revenue / 1000).toFixed(1) }}k
                          </div>
                          <div 
                            class="bar-pillar rounded-top mx-auto"
                            :class="m.month === 'Aug' ? 'bg-primary' : 'bg-info'"
                            :style="{ height: (m.heightPct || 10) + '%', width: '22px', transition: 'height 0.3s ease' }"
                            :title="`${m.month}: ₹${m.revenue.toLocaleString()} (${m.orders} orders)`"
                          ></div>
                          <div class="small fw-semibold mt-2 text-dark">{{ m.month }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Category Sales Distribution -->
              <div class="col-md-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h4 class="card-title fw-bold" style="color:#2A2F5B;">Category Revenue Share</h4>
                    <p class="card-category mb-0">For {{ monthlyReportData.monthLabel }}</p>
                  </div>
                  <div class="card-body">
                    <div class="category-breakdown-list">
                      <div v-for="cat in monthlyReportData.categoryBreakdown" :key="cat.name" class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                          <span class="small fw-semibold text-dark">{{ cat.name }}</span>
                          <span class="small fw-bold text-primary">₹{{ cat.amount.toLocaleString() }} ({{ cat.percent }}%)</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                          <div class="progress-bar" role="progressbar" :style="{ width: cat.percent + '%', backgroundColor: cat.color }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- RECENT ORDERS QUICK PREVIEW -->
            <div class="row">
              <div class="col-12">
                <div class="card card-round shadow-sm border-0">
                  <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                    <h4 class="card-title fw-bold" style="color:#2A2F5B;">Recent Customer Orders</h4>
                    <button class="btn btn-outline-primary btn-round btn-sm" @click="currentTab = 'orders'">
                      View All Orders ({{ ordersList.length }}) →
                    </button>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                          <tr>
                            <th class="ps-4">Order #</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="pe-4 text-end">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-if="ordersList.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">No orders placed yet.</td>
                          </tr>
                          <tr v-for="ord in ordersList.slice(0, 5)" :key="ord.id">
                            <td class="ps-4 fw-bold text-primary">#{{ ord.order_number }}</td>
                            <td>
                              <div class="fw-semibold text-dark">{{ ord.customer_name }}</div>
                              <small class="text-muted">{{ ord.customer_phone || ord.customer_email }}</small>
                            </td>
                            <td class="fw-bold text-dark">₹{{ Number(ord.total || 0).toLocaleString() }}</td>
                            <td>
                              <span :style="getStatusDropdownStyle(ord.status)">
                                {{ ord.status }}
                              </span>
                            </td>
                            <td class="text-muted small">{{ formatOrderDate(ord.created_at) }}</td>
                            <td class="pe-4 text-end">
                              <button class="btn btn-link btn-primary btn-sm p-0" @click="viewOrderDetailsModal(ord)">
                                <i class="fas fa-eye me-1"></i> Details
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 2: ALL PRODUCTS CATALOG INVENTORY                     -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'all-products'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Product Catalog Inventory</h3>
                <h6 class="op-7 mb-2 text-muted">Showing {{ displayProducts.length }} of {{ totalCount }} products in store database</h6>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-round shadow-sm" @click="currentTab = 'bulk-upload'">
                  <i class="fas fa-file-excel me-1"></i> Bulk Excel Upload
                </button>
                <button class="btn btn-primary btn-round" @click="currentTab = 'add-product'">
                  <i class="fas fa-plus me-1"></i> Add New Product
                </button>
              </div>
            </div>

            <!-- SEARCH & FILTER TOOLBAR CARD -->
            <div class="card card-round shadow-sm border-0 mb-3 mb-md-4">
              <div class="card-body p-3 p-md-4">
                <div class="row g-2 g-md-3 align-items-center">
                  <div class="col-12 col-md-4">
                    <label class="small text-muted fw-bold d-none d-md-block">Search Products</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted small"></i></span>
                      <input 
                        type="text" 
                        v-model="filters.search" 
                        @input="fetchFilteredProducts"
                        placeholder="Search title, SKU or specs..." 
                        class="form-control border-start-0"
                      />
                      <button 
                        class="btn btn-outline-secondary d-md-none border-start-0" 
                        type="button" 
                        @click="isProductsMobileFilterOpen = !isProductsMobileFilterOpen"
                        :class="{ 'active text-primary fw-bold': isProductsMobileFilterOpen }"
                      >
                        <i class="fas fa-sliders-h me-1"></i> Filters
                      </button>
                    </div>
                  </div>
                  <div class="col-6 col-md-2" :class="{ 'd-none d-md-block': !isProductsMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Category</label>
                    <select v-model="filters.category" @change="applyFilters" class="form-select form-select-sm">
                      <option value="all">All Categories</option>
                      <option v-for="c in formattedCategoryOptions" :key="c.id || c.name" :value="c.slug || c.name.toLowerCase()">{{ c.displayName }}</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2" :class="{ 'd-none d-md-block': !isProductsMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Brand</label>
                    <select v-model="filters.brand" @change="applyFilters" class="form-select form-select-sm">
                      <option value="all">All Brands</option>
                      <option v-for="b in brands" :key="b.id || b.name" :value="b.name">{{ b.name }}</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2" :class="{ 'd-none d-md-block': !isProductsMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Stock Status</label>
                    <select v-model="filters.stock" @change="applyFilters" class="form-select form-select-sm">
                      <option value="all">All Stock</option>
                      <option value="in_stock">In Stock 🟢</option>
                      <option value="out_of_stock">Out of Stock 🔴</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2 d-flex align-items-end" :class="{ 'd-none d-md-block': !isProductsMobileFilterOpen }">
                    <button class="btn btn-outline-secondary btn-round w-100 btn-sm py-2" @click="resetFilters">
                      <i class="fas fa-undo me-1"></i> Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- PRODUCTS TABLE CARD -->
            <div class="card card-round shadow-sm border-0">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0" style="min-width: 1050px;">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-4" style="width: 260px;">Product & Image</th>
                        <th style="width: 160px;">Category</th>
                        <th style="width: 130px;">Brand</th>
                        <th style="width: 150px;">Price & MRP (₹)</th>
                        <th style="width: 150px;">Stock Status & Qty</th>
                        <th style="width: 120px;">Tag</th>
                        <th class="pe-4 text-end" style="width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="displayProducts.length === 0">
                        <td colspan="7" class="text-center text-muted py-5">
                          <i class="fas fa-boxes fs-1 d-block mb-2 text-muted"></i>
                          No products found matching current filters.
                        </td>
                      </tr>
                      <tr v-for="p in displayProducts" :key="p.id || p.code_id">
                        <!-- Product Image & Title -->
                        <td class="ps-4">
                          <div class="d-flex align-items-center gap-2">
                            <div class="position-relative flex-shrink-0" style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #cbd5e1;">
                              <img :src="p.img" :alt="p.name" style="width: 100%; height: 100%; object-fit: cover;" />
                              <label :for="'inline_file_' + (p.id || p.code_id)" class="position-absolute bottom-0 end-0 bg-dark bg-opacity-75 text-white d-flex align-items-center justify-content-center cursor-pointer" style="width: 20px; height: 20px; border-top-left-radius: 4px; cursor: pointer;" title="Upload/Change Image">
                                <i class="fas fa-camera" style="font-size: 10px;"></i>
                                <input type="file" accept="image/*" class="d-none" :id="'inline_file_' + (p.id || p.code_id)" @change="handleInlineImageUpload($event, p)" />
                              </label>
                            </div>

                            <div class="flex-grow-1">
                              <input type="text" v-model="p.name" @change="saveInlineProduct(p)" class="form-control form-control-sm fw-bold text-dark mb-1" placeholder="Product Name" />
                              <div class="d-flex align-items-center gap-1">
                                <small class="text-muted" style="font-size: 11px;">SKU:</small>
                                <input type="text" v-model="p.code_id" @change="saveInlineProduct(p)" class="form-control form-control-sm text-muted p-0 px-1" style="font-size: 11px; height: 20px; width: 110px;" />
                                <span v-if="p.is_variable || p.isVariable" class="badge badge-info ms-1" style="font-size: 9px;">Variable</span>
                              </div>
                            </div>
                          </div>
                        </td>

                        <!-- Category Dropdown -->
                        <td>
                          <select v-model="p.category" @change="saveInlineProduct(p)" class="form-select form-select-sm">
                            <option value="">-- Category --</option>
                            <option v-for="c in formattedCategoryOptions" :key="c.id || c.name" :value="c.slug || c.name.toLowerCase()">{{ c.displayName }}</option>
                          </select>
                        </td>

                        <!-- Brand Dropdown -->
                        <td>
                          <select v-model="p.brand" @change="saveInlineProduct(p)" class="form-select form-select-sm fw-semibold">
                            <option value="Generic">Generic</option>
                            <option v-for="b in brands" :key="b.id || b.name" :value="b.name">{{ b.name }}</option>
                          </select>
                        </td>

                        <!-- Price & Old MRP Price -->
                        <td>
                          <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text fw-bold">₹</span>
                            <input type="number" v-model.number="p.price" @change="saveInlineProduct(p)" class="form-control fw-bold" placeholder="Price" />
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text text-muted small" style="font-size: 9px;">MRP</span>
                            <input type="number" v-model.number="p.old_price" @change="saveInlineProduct(p)" class="form-control text-muted" placeholder="Old MRP" />
                          </div>
                        </td>

                        <!-- Stock Status & Quantity Number -->
                        <td>
                          <select 
                            v-model="p.stockStatus" 
                            @change="p.in_stock = (p.stockStatus === 'In stock'); saveInlineProduct(p)" 
                            class="form-select form-select-sm mb-1 fw-bold" 
                            :class="(p.stockStatus === 'Out of stock' || p.in_stock === false) ? 'text-danger border-danger' : 'text-success border-success'"
                          >
                            <option value="In stock">🟢 In Stock</option>
                            <option value="Out of stock">🔴 Out of Stock</option>
                          </select>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text" style="font-size: 9px;">Qty</span>
                            <input type="number" v-model.number="p.stock" @change="saveInlineProduct(p)" class="form-control form-control-sm" placeholder="Stock Qty" />
                          </div>
                        </td>

                        <!-- Promotional Tag -->
                        <td>
                          <select v-model="p.tag" @change="saveInlineProduct(p)" class="form-select form-select-sm fw-bold text-uppercase">
                            <option value="NEW">✨ NEW</option>
                            <option value="HOT">🔥 HOT</option>
                            <option value="BESTSELLER">⭐ BESTSELLER</option>
                            <option value="HOT DEAL">💥 HOT DEAL</option>
                            <option value="FEATURED">🎯 FEATURED</option>
                            <option value="SALE">🏷️ SALE</option>
                            <option value="PRO">⚡ PRO</option>
                          </select>
                        </td>

                        <!-- Action Buttons -->
                        <td class="pe-4 text-end">
                          <div class="d-inline-flex gap-1">
                            <button class="btn btn-success btn-sm btn-round px-2" @click="saveInlineProduct(p)" title="Save Inline Edits">
                              <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm btn-round px-2" @click="editProduct(p)" title="Full Edit Modal">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm btn-round px-2" @click="deleteProduct(p)" title="Delete Product">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- PAGINATION CONTROLS -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top" v-if="productTotalPages > 1">
                  <span class="small text-muted">Page {{ productCurrentPage }} of {{ productTotalPages }}</span>
                  <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" :disabled="productCurrentPage <= 1" @click="changeProductPage(productCurrentPage - 1)">
                      ← Previous
                    </button>
                    <button 
                      v-for="page in productTotalPages" 
                      :key="page" 
                      class="btn btn-sm" 
                      :class="page === productCurrentPage ? 'btn-primary' : 'btn-outline-secondary'"
                      @click="changeProductPage(page)"
                    >
                      {{ page }}
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" :disabled="productCurrentPage >= productTotalPages" @click="changeProductPage(productCurrentPage + 1)">
                      Next →
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB: BULK EXCEL / CSV PRODUCT UPLOAD & REVIEW             -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'bulk-upload'">
            <div class="d-flex align-items-center justify-content-between pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">
                  <i class="fas fa-file-excel text-success me-2"></i> Bulk Product Excel/CSV Import
                </h3>
                <h6 class="op-7 mb-0 text-muted">Upload product template, review parsed details, add/assign images, and bulk save to database</h6>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-success btn-round shadow-sm" @click="downloadCsvTemplate">
                  <i class="fas fa-download me-1"></i> Download Sample Excel Template
                </button>
                <button class="btn btn-outline-secondary btn-round" @click="currentTab = 'all-products'">
                  ← Back to Catalog
                </button>
              </div>
            </div>

            <!-- STEP 1: FILE UPLOAD DROPZONE (Visible when no parsed products) -->
            <div v-if="bulkParsedProducts.length === 0" class="card card-round shadow-sm border-0 mb-4">
              <div class="card-body p-5 text-center">
                <div class="mb-3">
                  <div class="mx-auto rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-file-csv fa-2x text-success"></i>
                  </div>
                </div>
                <h4 class="fw-bold text-dark mb-2">Upload Product CSV / Excel Sheet</h4>
                <p class="text-muted small max-w-lg mx-auto mb-4" style="max-width: 550px;">
                  Select or drag & drop your Excel exported CSV file below. The system will parse your products and let you <strong>review details & choose image options</strong> before saving!
                </p>

                <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                  <label class="btn btn-success btn-lg btn-round px-4 shadow-sm cursor-pointer mb-0" style="cursor: pointer;">
                    <i class="fas fa-upload me-2"></i> Browse & Choose CSV File
                    <input type="file" accept=".csv, .txt" class="d-none" @change="handleCsvFileSelect" />
                  </label>
                  <button class="btn btn-outline-primary btn-lg btn-round px-4" @click="downloadCsvTemplate">
                    <i class="fas fa-file-download me-2"></i> Get Sample Template (.csv)
                  </button>
                </div>

                <div class="mt-4 p-3 bg-light rounded border text-start mx-auto" style="max-width: 650px;">
                  <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle text-info me-2"></i> How Template Upload Works:</h6>
                  <ul class="small text-muted mb-0 ps-3">
                    <li>Download the <strong>Sample CSV Template</strong> to see required column headers (Code ID, Name, Category, Brand, Price, Stock, etc.).</li>
                    <li>Fill your product details in Excel or Google Sheets and save/export as <code>.csv</code>.</li>
                    <li>Upload the <code>.csv</code> file here. You will see a live <strong>Review Screen</strong> where you can attach images per product or choose image settings before importing!</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- STEP 2: PARSED PRODUCTS REVIEW & IMAGE SELECTION WORKSPACE -->
            <div v-else class="card card-round shadow-sm border-0 mb-4">
              <div class="card-header bg-transparent border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                      <span class="badge bg-success me-2 fs-6">{{ bulkParsedProducts.length }} Products Parsed</span>
                      Review Products & Choose Image Options
                    </h5>
                    <small class="text-muted">Review details, edit values, upload/link images, and select which items to import into DB</small>
                  </div>
                  <div class="d-flex gap-2 flex-wrap align-items-center">
                    <button class="btn btn-outline-secondary btn-sm btn-round" @click="bulkParsedProducts = []">
                      <i class="fas fa-trash me-1"></i> Clear & Upload New File
                    </button>
                    <button class="btn btn-success btn-round px-4 shadow-sm fw-bold" :disabled="isBulkImporting || selectedBulkCount === 0" @click="executeBulkImport">
                      <span v-if="isBulkImporting"><i class="fas fa-spinner fa-spin me-1"></i> Saving Products...</span>
                      <span v-else><i class="fas fa-check-circle me-1"></i> Confirm & Import ({{ selectedBulkCount }} Products)</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- BULK CONTROLS & FILTER BAR -->
              <div class="card-body bg-light border-bottom p-3">
                <div class="row g-2 align-items-center">
                  <div class="col-12 col-md-4">
                    <input type="text" v-model="bulkSearchFilter" placeholder="Filter parsed list by name, code or category..." class="form-control form-control-sm" />
                  </div>
                  <div class="col-6 col-md-3">
                    <button class="btn btn-outline-dark btn-sm me-2" @click="selectAllBulk(true)">Select All</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="selectAllBulk(false)">Deselect All</button>
                  </div>
                  <div class="col-6 col-md-5 text-end text-muted small">
                    Selected for import: <strong class="text-success">{{ selectedBulkCount }}</strong> of {{ bulkParsedProducts.length }} items
                  </div>
                </div>

                <!-- PROGRESS BAR IF IMPORTING -->
                <div v-if="isBulkImporting" class="mt-3">
                  <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Importing Products to Database...</span>
                    <span>{{ bulkImportProgress }}%</span>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" :style="{ width: bulkImportProgress + '%' }"></div>
                  </div>
                </div>
              </div>

              <!-- REVIEW TABLE WITH IMAGE OPTIONS -->
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 980px;">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 40px;" class="ps-3">
                        <input type="checkbox" :checked="isAllBulkSelected" @change="toggleAllBulkCheckbox($event)" class="form-check-input" />
                      </th>
                      <th style="width: 140px;">Code / SKU</th>
                      <th style="width: 220px;">Product Name</th>
                      <th style="width: 150px;">Category & Brand</th>
                      <th style="width: 120px;">Price & Stock</th>
                      <th style="width: 320px;">Image Option & Preview (Review Stage)</th>
                      <th style="width: 50px;" class="pe-3 text-end">Remove</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(prod, idx) in filteredBulkProducts" :key="prod.id || idx" :class="{ 'table-active': !prod.selected }">
                      <!-- Selection Checkbox -->
                      <td class="ps-3">
                        <input type="checkbox" v-model="prod.selected" class="form-check-input" />
                      </td>

                      <!-- Code / SKU -->
                      <td>
                        <input type="text" v-model="prod.code_id" class="form-control form-control-sm" placeholder="Code ID" />
                      </td>

                      <!-- Name -->
                      <td>
                        <input type="text" v-model="prod.name" class="form-control form-control-sm fw-bold text-dark" placeholder="Product Title" />
                        <small class="text-muted d-block text-truncate mt-1" style="max-width: 210px;">{{ prod.specs || 'No specs' }}</small>
                      </td>

                      <!-- Category & Brand -->
                      <td>
                        <select v-model="prod.category" class="form-select form-select-sm mb-1">
                          <option value="">Category...</option>
                          <option v-for="c in formattedCategoryOptions" :key="c.id || c.name" :value="c.slug || c.name.toLowerCase()">{{ c.displayName }}</option>
                        </select>
                        <select v-model="prod.brand" class="form-select form-select-sm">
                          <option value="">Brand...</option>
                          <option v-for="b in brands" :key="b.id || b.name" :value="b.name">{{ b.name }}</option>
                        </select>
                      </td>

                      <!-- Price & Stock -->
                      <td>
                        <div class="input-group input-group-sm mb-1">
                          <span class="input-group-text">₹</span>
                          <input type="number" v-model.number="prod.price" class="form-control fw-bold" placeholder="Price" />
                        </div>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">Qty</span>
                          <input type="number" v-model.number="prod.stock" class="form-control" placeholder="Stock" />
                        </div>
                      </td>

                      <!-- IMAGE SELECTION & OPTION (CORE USER FEATURE) -->
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <!-- Image Preview Box -->
                          <div class="position-relative flex-shrink-0" style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #f1f5f9; border: 1px solid #cbd5e1;">
                            <img :src="getBulkProductDisplayImage(prod)" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;" />
                          </div>

                          <!-- Image Settings Controls -->
                          <div class="flex-grow-1">
                            <select v-model="prod.image_option" class="form-select form-select-sm mb-1">
                              <option value="upload">🖼️ Upload Custom Image File</option>
                              <option value="url">🔗 Paste Image URL Link</option>
                              <option value="default">📦 Use Default Placeholder</option>
                              <option value="none">🚫 No Image</option>
                            </select>

                            <!-- File Upload Input if option === 'upload' -->
                            <div v-if="prod.image_option === 'upload'">
                              <input type="file" accept="image/*" class="form-control form-control-sm" @change="handleRowImageUpload($event, prod)" />
                            </div>

                            <!-- URL Input if option === 'url' -->
                            <div v-else-if="prod.image_option === 'url'">
                              <input type="url" v-model="prod.img_url" class="form-control form-control-sm" placeholder="https://images.unsplash.com/..." />
                            </div>

                            <div v-else class="small text-muted fst-italic">
                              <span v-if="prod.image_option === 'default'">Default sports product image</span>
                              <span v-else>Will import without custom image</span>
                            </div>
                          </div>
                        </div>
                      </td>

                      <!-- Remove Action -->
                      <td class="pe-3 text-end">
                        <button class="btn btn-link text-danger btn-sm p-0" @click="removeBulkRow(idx)" title="Remove product row">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- CARD FOOTER CONFIRM BUTTON -->
              <div class="card-footer bg-transparent py-3 text-end border-top">
                <button class="btn btn-success btn-round px-4 shadow-sm fw-bold" :disabled="isBulkImporting || selectedBulkCount === 0" @click="executeBulkImport">
                  <span v-if="isBulkImporting"><i class="fas fa-spinner fa-spin me-1"></i> Saving to Database...</span>
                  <span v-else><i class="fas fa-cloud-upload-alt me-1"></i> Import {{ selectedBulkCount }} Products to Database</span>
                </button>
              </div>
            </div>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 3: ADD / EDIT PRODUCT FORM                            -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'add-product'">
            <div class="d-flex align-items-center justify-content-between pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">
                  {{ newProd.id ? `Edit Product: ${newProd.name}` : 'Add New Catalog Product' }}
                </h3>
                <h6 class="op-7 mb-2 text-muted">Configure product specifications, variations, pricing & gallery images</h6>
              </div>
              <button class="btn btn-outline-secondary btn-round" @click="currentTab = 'all-products'">
                ← Back to Products
              </button>
            </div>

            <form @submit.prevent="saveNewProduct" class="row">
              <div class="col-lg-8">
                <!-- Basic Details Card -->
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">1. Basic Information</h5>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label fw-semibold small text-muted">Product Title / Name *</label>
                        <input type="text" v-model="newProd.name" placeholder="e.g. Yonex Astrox 99 Pro Badminton Racquet" required class="form-control" />
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Product Code / SKU</label>
                        <input type="text" v-model="newProd.code_id" placeholder="e.g. YNX-AX99-PRO" class="form-control" />
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Product Type</label>
                        <select v-model="productType" class="form-select">
                          <option value="simple">Simple Product</option>
                          <option value="variable">Variable Product (Attributes & Matrix)</option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Category *</label>
                        <div class="input-group">
                          <select v-model="newProd.category" required class="form-select">
                            <option value="">Select Category</option>
                            <option v-for="c in formattedCategoryOptions" :key="c.id || c.name" :value="c.slug || c.name.toLowerCase()">{{ c.displayName }}</option>
                          </select>
                          <button type="button" class="btn btn-outline-primary" @click="showInlineCatForm = !showInlineCatForm" title="Quick Add Category">+</button>
                        </div>
                        <div v-if="showInlineCatForm" class="mt-2 p-2 bg-light rounded border">
                          <div class="d-flex gap-2">
                            <input type="text" v-model="quickCatName" placeholder="New Category Name" class="form-control form-control-sm" />
                            <button type="button" class="btn btn-primary btn-sm" @click="quickCreateCategory">Add</button>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Brand *</label>
                        <div class="input-group">
                          <select v-model="newProd.brand" required class="form-select">
                            <option value="">Select Brand</option>
                            <option v-for="b in brands" :key="b.id || b.name" :value="b.name">{{ b.name }}</option>
                          </select>
                          <button type="button" class="btn btn-outline-primary" @click="showInlineBrandForm = !showInlineBrandForm" title="Quick Add Brand">+</button>
                        </div>
                        <div v-if="showInlineBrandForm" class="mt-2 p-2 bg-light rounded border">
                          <div class="d-flex gap-2">
                            <input type="text" v-model="quickBrandName" placeholder="New Brand Name" class="form-control form-control-sm" />
                            <button type="button" class="btn btn-primary btn-sm" @click="quickCreateBrand">Add</button>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Promotional Tag</label>
                        <input type="text" v-model="newProd.tag" placeholder="e.g. HOT DEAL, BESTSELLER, NEW" class="form-control" />
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Stock Status</label>
                        <select v-model="newProd.stockStatus" class="form-select">
                          <option value="In stock">In Stock 🟢</option>
                          <option value="Out of stock">Out of Stock 🔴</option>
                        </select>
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-semibold small text-muted">Product Specifications & Highlights</label>
                        <textarea v-model="newProd.specs" rows="3" placeholder="Weight, Grip Size, Balance Point, Material..." class="form-control"></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Pricing Card -->
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">2. Pricing Information (₹)</h5>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Regular Price / MRP (₹) *</label>
                        <input type="number" v-model="newProd.price" required min="0" class="form-control" />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Discounted Sale Price (₹)</label>
                        <input type="number" v-model="newProd.salePrice" min="0" placeholder="Optional discount price" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Variable Product Variations Section -->
                <div v-if="productType === 'variable'" class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0">3. Variations & Global Attributes</h5>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-primary btn-sm btn-round" @click="generateAllVariations">
                        <i class="fas fa-magic me-1"></i> Auto Generate Matrix
                      </button>
                      <button type="button" class="btn btn-primary btn-sm btn-round" @click="addManualVariation">
                        <i class="fas fa-plus me-1"></i> Add Variation
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div v-if="variationsList.length === 0" class="text-center text-muted py-4">
                      No variations generated yet. Click "Auto Generate Matrix" or "Add Variation".
                    </div>
                    <div v-for="(v, index) in variationsList" :key="v.id" class="p-3 mb-3 border rounded bg-light">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-dark">Variation #{{ v.id }} ({{ v.sku }})</strong>
                        <button type="button" class="btn btn-link text-danger btn-sm p-0" @click="removeVariation(v.id)">
                          <i class="fas fa-trash"></i> Remove
                        </button>
                      </div>
                      <div class="row g-2">
                        <div class="col-md-4">
                          <label class="small text-muted fw-bold">Regular Price (₹)</label>
                          <input type="number" v-model="v.price" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-4">
                          <label class="small text-muted fw-bold">Sale Price (₹)</label>
                          <input type="number" v-model="v.salePrice" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-4">
                          <label class="small text-muted fw-bold">Stock</label>
                          <select v-model="v.stockStatus" class="form-select form-select-sm">
                            <option value="In stock">In stock</option>
                            <option value="Out of stock">Out of stock</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                  <button type="submit" class="btn btn-primary btn-round px-4 py-2 fw-bold" :disabled="isSubmitting">
                    <i class="fas fa-save me-1"></i> {{ isSubmitting ? 'Saving...' : 'Save Product to Database' }}
                  </button>
                  <button type="button" class="btn btn-outline-secondary btn-round px-4 py-2" @click="currentTab = 'all-products'">
                    Cancel
                  </button>
                </div>
              </div>

              <!-- Product Media & Images (Right Column) -->
              <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">Main Showcase Image</h5>
                  </div>
                  <div class="card-body text-center">
                    <img :src="newProd.img" alt="Main Preview" class="rounded mb-3 border w-100" style="max-height: 220px; object-fit: cover;" />
                    <div class="mb-3 text-start">
                      <label class="form-label small fw-semibold text-muted">Image URL</label>
                      <input type="text" v-model="newProd.img" placeholder="https://..." class="form-control form-control-sm" />
                    </div>
                    <div class="text-start">
                      <label class="form-label small fw-semibold text-muted">Or Upload from Device</label>
                      <input type="file" @change="onMainImageSelected" accept="image/*" class="form-control form-control-sm" />
                    </div>
                  </div>
                </div>

                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">Gallery Images</h5>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label small fw-semibold text-muted">Comma-Separated URLs</label>
                      <input type="text" v-model="newProd.galleryImg" placeholder="url1, url2..." class="form-control form-control-sm" />
                    </div>
                    <div>
                      <label class="form-label small fw-semibold text-muted">Upload Additional Images</label>
                      <input type="file" multiple @change="onGalleryImageSelected" accept="image/*" class="form-control form-control-sm" />
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- ========================================================= -->
          <!-- TAB 4: ORDERS MANAGEMENT ENGINE                           -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'orders'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Customer Orders Management</h3>
                <h6 class="op-7 mb-2 text-muted">Total Orders: {{ ordersList.length }} · Revenue: ₹{{ totalOrderRevenue.toLocaleString() }}</h6>
              </div>
              <button class="btn btn-outline-primary btn-round btn-sm" @click="fetchOrdersFromBackend">
                <i class="fas fa-sync-alt me-1"></i> Refresh Orders
              </button>
            </div>

            <!-- Filters Toolbar -->
            <div class="card card-round shadow-sm border-0 mb-3 mb-md-4">
              <div class="card-body p-3 p-md-4">
                <div class="row g-2 g-md-3">
                  <div class="col-12 col-md-4">
                    <label class="small text-muted fw-bold d-none d-md-block">Search Orders</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted small"></i></span>
                      <input 
                        type="text" 
                        v-model="orderSearchQuery" 
                        placeholder="Search order #, customer, phone..." 
                        class="form-control border-start-0" 
                      />
                      <button 
                        class="btn btn-outline-secondary d-md-none border-start-0" 
                        type="button" 
                        @click="isOrdersMobileFilterOpen = !isOrdersMobileFilterOpen"
                        :class="{ 'active text-primary fw-bold': isOrdersMobileFilterOpen }"
                      >
                        <i class="fas fa-sliders-h me-1"></i> Filters
                      </button>
                    </div>
                  </div>
                  <div class="col-6 col-md-3" :class="{ 'd-none d-md-block': !isOrdersMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Order Status</label>
                    <select v-model="orderStatusFilter" class="form-select form-select-sm">
                      <option value="all">All Statuses ({{ ordersList.length }})</option>
                      <option value="Confirmed">Confirmed</option>
                      <option value="Processing">Processing</option>
                      <option value="Shipped">Shipped</option>
                      <option value="Delivered">Delivered</option>
                      <option value="Cancelled">Cancelled</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-3" :class="{ 'd-none d-md-block': !isOrdersMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Date Range</label>
                    <select v-model="orderDateFilter" class="form-select form-select-sm">
                      <option value="all">All Dates</option>
                      <option value="today">Today</option>
                      <option value="week">Past 7 Days</option>
                      <option value="month">This Month</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-2" :class="{ 'd-none d-md-block': !isOrdersMobileFilterOpen }">
                    <label class="small text-muted fw-bold">Sort By</label>
                    <select v-model="orderSortBy" class="form-select form-select-sm">
                      <option value="newest">Newest First</option>
                      <option value="oldest">Oldest First</option>
                      <option value="amount-high">Amount (High to Low)</option>
                      <option value="amount-low">Amount (Low to High)</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Orders Table -->
            <div class="card card-round shadow-sm border-0">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th class="pe-4 text-end">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="filteredOrdersList.length === 0">
                        <td colspan="7" class="text-center text-muted py-5">No orders found matching filters.</td>
                      </tr>
                      <tr v-for="ord in paginatedOrdersList" :key="ord.id">
                        <td class="ps-4 fw-bold text-primary">#{{ ord.order_number }}</td>
                        <td>
                          <div class="fw-semibold text-dark">{{ ord.customer_name }}</div>
                          <small class="text-muted">📞 {{ ord.customer_phone || 'N/A' }}</small>
                        </td>
                        <td class="fw-bold text-dark">₹{{ Number(ord.total || 0).toLocaleString() }}</td>
                        <td>
                          <select 
                            v-model="ord.status" 
                            @change="updateOrderStatusOnBackend(ord)" 
                            :style="getStatusDropdownStyle(ord.status)"
                            class="form-select form-select-sm d-inline-block w-auto"
                          >
                            <option value="Confirmed">Confirmed</option>
                            <option value="Processing">Processing</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                          </select>
                        </td>
                        <td>
                          <span class="badge badge-light border text-dark">{{ ord.payment_method || 'Online' }}</span>
                        </td>
                        <td class="text-muted small">{{ formatOrderDate(ord.created_at) }}</td>
                        <td class="pe-4 text-end">
                          <button class="btn btn-outline-primary btn-sm btn-round" @click="viewOrderDetailsModal(ord)">
                            <i class="fas fa-eye me-1"></i> Details
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top" v-if="orderTotalPages > 1">
                  <span class="small text-muted">Page {{ orderCurrentPage }} of {{ orderTotalPages }}</span>
                  <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" :disabled="orderCurrentPage <= 1" @click="orderCurrentPage--">
                      ← Previous
                    </button>
                    <button 
                      v-for="p in orderTotalPages" 
                      :key="p" 
                      class="btn btn-sm" 
                      :class="p === orderCurrentPage ? 'btn-primary' : 'btn-outline-secondary'"
                      @click="orderCurrentPage = p"
                    >
                      {{ p }}
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" :disabled="orderCurrentPage >= orderTotalPages" @click="orderCurrentPage++">
                      Next →
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 5: REGISTERED CUSTOMERS & USERS LIST                 -->
          <!-- ========================================================= -->
          <div v-if="currentTab === 'users'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Registered Customer Accounts</h3>
                <h6 class="op-7 mb-2 text-muted">Showing {{ filteredUsersList.length }} of {{ usersList.length }} registered accounts</h6>
              </div>
              <button class="btn btn-primary btn-round" @click="isInviteUserModalOpen = true">
                <i class="fas fa-user-plus me-1"></i> + Invite / Add New Admin
              </button>
            </div>

            <div class="card card-round shadow-sm border-0 mb-4">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-8">
                    <label class="small text-muted fw-bold">Search Users</label>
                    <input type="text" v-model="userSearchQuery" placeholder="Search by name, email or phone..." class="form-control" />
                  </div>
                  <div class="col-md-4">
                    <label class="small text-muted fw-bold">Account Status</label>
                    <select v-model="userStatusFilter" class="form-select">
                      <option value="all">All Accounts ({{ usersList.length }})</option>
                      <option value="active">Active Accounts 🟢</option>
                      <option value="inactive">Inactive Accounts 🔴</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-round shadow-sm border-0">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-4">User Details</th>
                        <th>Contact Info</th>
                        <th>Account Role</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                        <th class="pe-4 text-end">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="filteredUsersList.length === 0">
                        <td colspan="7" class="text-center text-muted py-5">No user accounts found.</td>
                      </tr>
                      <tr v-for="u in filteredUsersList" :key="u.id">
                        <td class="ps-4">
                          <div class="d-flex align-items-center">
                            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=1572E8&color=ffffff&bold=true`" class="rounded-circle me-3" style="width:38px; height:38px;" />
                            <div>
                              <div class="fw-bold text-dark">{{ u.name }}</div>
                              <small class="text-muted">ID: #USR-{{ u.id }}</small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="text-dark small">📧 {{ u.email }}</div>
                          <div class="text-muted small">📞 {{ u.phone || 'N/A' }}</div>
                        </td>
                        <td>
                          <span class="badge" :class="u.role.includes('Admin') ? 'badge-primary' : 'badge-light border text-dark'">
                            {{ u.role }}
                          </span>
                        </td>
                        <td>
                          <strong class="text-primary">{{ u.orders_count || 0 }} Orders</strong>
                        </td>
                        <td>
                          <button 
                            class="btn btn-sm btn-round" 
                            :class="u.status === 'active' ? 'btn-outline-success' : 'btn-outline-danger'"
                            @click="toggleUserStatus(u)"
                          >
                            {{ u.status === 'active' ? '🟢 Active' : '🔴 Inactive' }}
                          </button>
                        </td>
                        <td class="text-muted small">{{ u.registered_at || 'Recent' }}</td>
                        <td class="pe-4 text-end">
                          <button class="btn btn-outline-primary btn-sm btn-round" @click="openUserProfile(u)">
                            <i class="fas fa-id-card me-1"></i> Profile & Orders
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 6: CONTACT US INQUIRIES & MESSAGES                    -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'contact-messages'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Customer Contact Inquiries</h3>
                <h6 class="op-7 mb-2 text-muted">Showing {{ filteredContactMessages.length }} messages · Unread: {{ unreadContactCount }}</h6>
              </div>
              <button class="btn btn-outline-primary btn-round btn-sm" @click="fetchContactMessages">
                <i class="fas fa-sync-alt me-1"></i> Refresh Messages
              </button>
            </div>

            <div class="card card-round shadow-sm border-0 mb-4">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-8">
                    <label class="small text-muted fw-bold">Search Inquiries</label>
                    <input type="text" v-model="contactSearchQuery" placeholder="Search sender, email, company or message..." class="form-control" />
                  </div>
                  <div class="col-md-4">
                    <label class="small text-muted fw-bold">Status Filter</label>
                    <select v-model="contactStatusFilter" class="form-select">
                      <option value="all">All Messages ({{ contactMessagesList.length }})</option>
                      <option value="unread">Unread Only ({{ unreadContactCount }})</option>
                      <option value="read">Read Messages</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-round shadow-sm border-0">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-4">Sender Details</th>
                        <th>Contact</th>
                        <th>Message Preview</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="filteredContactMessages.length === 0">
                        <td colspan="6" class="text-center text-muted py-5">No contact inquiries found.</td>
                      </tr>
                      <tr v-for="m in filteredContactMessages" :key="m.id">
                        <td class="ps-4">
                          <div class="fw-bold text-dark">{{ m.name }}</div>
                          <small v-if="m.company" class="text-muted">🏢 {{ m.company }}</small>
                        </td>
                        <td>
                          <div class="small text-dark">📧 {{ m.email }}</div>
                          <div class="small text-muted">📞 {{ m.phone || 'N/A' }}</div>
                        </td>
                        <td style="max-width: 280px;">
                          <div class="text-truncate text-secondary">{{ m.message }}</div>
                        </td>
                        <td class="text-muted small">{{ new Date(m.created_at).toLocaleDateString() }}</td>
                        <td>
                          <span class="badge" :class="m.status === 'unread' ? 'badge-danger' : 'badge-light border text-dark'">
                            {{ m.status === 'unread' ? '🔵 Unread' : 'Read' }}
                          </span>
                        </td>
                        <td class="pe-4 text-end">
                          <button class="btn btn-outline-primary btn-sm btn-round me-2" @click="openContactMessageModal(m)">
                            <i class="fas fa-envelope-open me-1"></i> View
                          </button>
                          <button class="btn btn-outline-danger btn-sm btn-round" @click="deleteContactMessage(m.id)">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 7: CATEGORIES MANAGEMENT                              -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'categories'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Categories Management</h3>
                <h6 class="op-7 mb-2 text-muted">Total Categories: {{ categories.length }}</h6>
              </div>
            </div>

            <div class="row">
              <!-- Add / Edit Category Form -->
              <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">{{ editingCategoryKey ? 'Edit Category' : 'Add New Category' }}</h5>
                  </div>
                  <div class="card-body">
                    <form v-if="editingCategoryKey" @submit.prevent="saveEditCategory">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Category Name *</label>
                        <input 
                          type="text" 
                          v-model="editingCategoryForm.name" 
                          placeholder="e.g. Batting Gloves" 
                          required 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Parent Category (Subcategory of)</label>
                        <select v-model="editingCategoryForm.parent_id" class="form-select">
                          <option :value="null">-- None (Main Top-Level Category) --</option>
                          <option 
                            v-for="cat in categories.filter(c => !c.parent_id && (c.id !== editingCategoryKey && c.slug !== editingCategoryKey))" 
                            :key="cat.id || cat.name" 
                            :value="cat.id"
                          >
                            {{ cat.icon || '📁' }} {{ cat.name }}
                          </option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Icon / Emoji</label>
                        <input 
                          type="text" 
                          v-model="editingCategoryForm.icon" 
                          placeholder="🏸" 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Slug (URL)</label>
                        <input 
                          type="text" 
                          v-model="editingCategoryForm.slug" 
                          placeholder="batting-gloves" 
                          class="form-control" 
                        />
                      </div>
                      <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                          Update Category
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-round" @click="cancelEditCategory">
                          Cancel
                        </button>
                      </div>
                    </form>
                    <form v-else @submit.prevent="saveCategory">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Category Name *</label>
                        <input 
                          type="text" 
                          v-model="newCatForm.name" 
                          placeholder="e.g. Batting Gloves" 
                          required 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Parent Category (Subcategory of)</label>
                        <select v-model="newCatForm.parent_id" class="form-select">
                          <option :value="null">-- None (Main Top-Level Category) --</option>
                          <option 
                            v-for="cat in categories.filter(c => !c.parent_id)" 
                            :key="cat.id || cat.name" 
                            :value="cat.id"
                          >
                            {{ cat.icon || '📁' }} {{ cat.name }}
                          </option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Icon / Emoji</label>
                        <input 
                          type="text" 
                          v-model="newCatForm.icon" 
                          placeholder="🏸" 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Slug (URL)</label>
                        <input 
                          type="text" 
                          v-model="newCatForm.slug" 
                          placeholder="batting-gloves" 
                          class="form-control" 
                        />
                      </div>
                      <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                        + Add Category
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Categories Table -->
              <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0">
                  <div class="card-header bg-transparent border-bottom">
                    <input type="text" v-model="categorySearchQuery" placeholder="Search categories..." class="form-control form-control-sm" />
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                          <tr>
                            <th class="ps-4">Icon</th>
                            <th>Category Name</th>
                            <th>Parent / Type</th>
                            <th>Slug</th>
                            <th class="pe-4 text-end">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="c in filteredCategories" :key="c.id || c.name">
                            <td class="ps-4 fs-4">{{ c.icon || '📦' }}</td>
                            <td class="fw-bold text-dark">
                              <span v-if="c.parent_id" class="text-muted me-1">↳</span>
                              {{ c.name }}
                            </td>
                            <td>
                              <span v-if="c.parent_id" class="badge bg-info text-white">
                                ↳ {{ getParentCategoryName(c) }}
                              </span>
                              <span v-else class="badge bg-light text-primary border">
                                📁 Main Category
                              </span>
                            </td>
                            <td class="text-muted">{{ c.slug || c.name.toLowerCase() }}</td>
                            <td class="pe-4 text-end">
                              <button class="btn btn-outline-primary btn-sm btn-round me-2" @click="startEditCategory(c)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-outline-danger btn-sm btn-round" @click="deleteCategory(c)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 8: BRANDS MANAGEMENT                                  -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'brands'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Brands Management</h3>
                <h6 class="op-7 mb-2 text-muted">Total Brands: {{ brands.length }}</h6>
              </div>
            </div>

            <div class="row">
              <!-- Add / Edit Brand Form -->
              <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">{{ editingBrandKey ? 'Edit Brand' : 'Add New Brand' }}</h5>
                  </div>
                  <div class="card-body">
                    <form v-if="editingBrandKey" @submit.prevent="saveEditBrand(brands.find(b => (b.id && b.id === editingBrandKey) || b.name === editingBrandKey))">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Brand Name *</label>
                        <input 
                          type="text" 
                          v-model="editingBrandForm.name" 
                          placeholder="e.g. Yonex" 
                          required 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Description / Notes</label>
                        <textarea 
                          v-model="editingBrandForm.desc" 
                          rows="3" 
                          placeholder="Official Sports Brand" 
                          class="form-control"
                        ></textarea>
                      </div>
                      <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                          Update Brand
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-round" @click="cancelEditBrand">
                          Cancel
                        </button>
                      </div>
                    </form>
                    <form v-else @submit.prevent="saveBrand">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Brand Name *</label>
                        <input 
                          type="text" 
                          v-model="newBrandForm.name" 
                          placeholder="e.g. Yonex" 
                          required 
                          class="form-control" 
                        />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Description / Notes</label>
                        <textarea 
                          v-model="newBrandForm.desc" 
                          rows="3" 
                          placeholder="Official Sports Brand" 
                          class="form-control"
                        ></textarea>
                      </div>
                      <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                        + Add Brand
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Brands Table -->
              <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0">
                  <div class="card-header bg-transparent border-bottom">
                    <input type="text" v-model="brandSearchQuery" placeholder="Search brands..." class="form-control form-control-sm" />
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                          <tr>
                            <th class="ps-4">Brand Name</th>
                            <th>Description</th>
                            <th class="pe-4 text-end">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="b in filteredBrands" :key="b.id || b.name">
                            <td class="ps-4 fw-bold text-dark">{{ b.name }}</td>
                            <td class="text-muted small">{{ b.desc || b.description || 'Official Sports Brand' }}</td>
                            <td class="pe-4 text-end">
                              <button class="btn btn-outline-primary btn-sm btn-round me-2" @click="startEditBrand(b)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-outline-danger btn-sm btn-round" @click="deleteBrand(b)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 9: TAGS MANAGEMENT                                    -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'tags'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Product Tags</h3>
                <h6 class="op-7 mb-2 text-muted">Total Tags: {{ tags.length }}</h6>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">Add New Tag</h5>
                  </div>
                  <div class="card-body">
                    <form @submit.prevent="saveTagFromForm">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Tag Name *</label>
                        <input type="text" v-model="newTagInput" placeholder="e.g. BESTSELLER" required class="form-control" />
                      </div>
                      <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                        + Add Tag
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0">
                  <div class="card-header bg-transparent border-bottom">
                    <input type="text" v-model="tagSearchQuery" placeholder="Search tags..." class="form-control form-control-sm" />
                  </div>
                  <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                      <span v-for="(t, i) in filteredTagsList" :key="i" class="badge badge-light border text-dark p-2 fs-6 d-inline-flex align-items-center">
                        <i class="fas fa-tag me-1 text-primary"></i> {{ typeof t === 'object' ? t.name : t }}
                        <button type="button" class="btn-close ms-2" style="font-size:10px;" @click="deleteTag(t)"></button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- ========================================================= -->
          <!-- TAB 10: ATTRIBUTES MANAGEMENT                             -->
          <!-- ========================================================= -->
          <div v-else-if="currentTab === 'attributes'">
            <div class="d-flex align-items-md-center flex-column flex-md-row pt-2 pb-4 justify-content-between">
              <div>
                <h3 class="fw-bold mb-1" style="color:#2A2F5B;">Global Attributes & Variations</h3>
                <h6 class="op-7 mb-2 text-muted">Manage attributes (e.g. Size, Grip Size, String Tension) used in products</h6>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4">
                  <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title fw-bold mb-0">Add New Attribute</h5>
                  </div>
                  <div class="card-body">
                    <form @submit.prevent="saveNewAttributeToDb">
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Attribute Name *</label>
                        <input type="text" v-model="newAttrForm.name" placeholder="e.g. Grip Size" required class="form-control" />
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Initial Values (Comma Separated)</label>
                        <input type="text" v-model="newAttrForm.values" placeholder="G4, G5, G6" class="form-control" />
                      </div>
                      <button type="submit" class="btn btn-primary btn-round w-100 fw-bold">
                        + Save Attribute
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0">
                  <div class="card-header bg-transparent border-bottom">
                    <input type="text" v-model="attributeSearchQuery" placeholder="Search attributes..." class="form-control form-control-sm" />
                  </div>
                  <div class="card-body">
                    <div v-for="(attr, idx) in filteredAttributesList" :key="attr.id || attr.name" class="p-3 mb-3 border rounded bg-light">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-dark fs-6">{{ attr.name }}</strong>
                        <button class="btn btn-outline-danger btn-sm btn-round" @click="deleteAttributeFromDb(attr, idx)">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </div>
                      <div class="d-flex flex-wrap gap-1 mb-2">
                        <span v-for="(v, vIdx) in (attr.values || [])" :key="vIdx" class="badge badge-primary">
                          {{ typeof v === 'object' ? v.value : v }}
                          <span role="button" class="ms-1" @click="deleteValueFromDb(attr, v, vIdx)">×</span>
                        </span>
                      </div>
                      <div class="input-group input-group-sm mt-2" style="max-width: 280px;">
                        <input type="text" v-model="attr.inputValue" placeholder="Add value..." class="form-control" @keyup.enter="commitNewValueToDb(attr)" />
                        <button class="btn btn-primary" type="button" @click="commitNewValueToDb(attr)">Add</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <!-- KAIADMIN CLEAN FOOTER -->
        <footer class="footer py-3 px-4 border-top bg-white">
          <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between align-items-center">
            <div class="copyright text-muted small">
              © 2026, <strong>Chhabra Sports</strong> — Pro E-Commerce Store Control Panel
            </div>
            <div class="text-muted small mt-2 mt-sm-0">
              <span class="badge badge-light text-secondary border me-2">Kaiadmin Template</span>
              <a href="#" @click.prevent="handleExitAdmin" class="text-primary text-decoration-none">Return to Live Storefront ↗</a>
            </div>
          </div>
        </footer>

      </div>
      <!-- End Main Panel -->

    </div>

    <!-- ========================================================= -->
    <!-- 3. MODAL POPUPS (ORDER DETAILS, USER PROFILE, INVITATION)  -->
    <!-- ========================================================= -->
    
    <!-- 1. FULL ORDER DETAILS MODAL -->
    <div v-if="selectedOrderModal" class="kaiadmin-modal-backdrop" @click.self="selectedOrderModal = null">
      <div class="card card-round shadow-lg kaiadmin-modal-dialog">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title fw-bold mb-0">Order #{{ selectedOrderModal?.order_number }}</h4>
            <small class="text-muted">Placed on {{ formatOrderDate(selectedOrderModal?.created_at) }}</small>
          </div>
          <button type="button" class="btn-close" @click="selectedOrderModal = null"></button>
        </div>
        <div class="card-body p-4">
          <div class="row mb-3">
            <div class="col-md-6">
              <h6 class="fw-bold text-muted small">CUSTOMER DETAILS</h6>
              <div class="fw-bold text-dark">{{ selectedOrderModal?.customer_name }}</div>
              <div class="small text-muted">📞 {{ selectedOrderModal?.customer_phone || 'N/A' }}</div>
              <div class="small text-muted">✉️ {{ selectedOrderModal?.customer_email || 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <h6 class="fw-bold text-muted small">DELIVERY ADDRESS</h6>
              <div class="small text-dark">{{ selectedOrderModal?.address || 'N/A' }}</div>
              <div class="small text-muted mt-1">Payment Method: <strong>{{ selectedOrderModal?.payment_method || 'Online' }}</strong></div>
            </div>
          </div>

          <div class="border-top pt-3 mb-3">
            <h6 class="fw-bold text-muted small mb-2">ORDER ITEMS</h6>
            <div v-for="(item, i) in (selectedOrderModal?.items || [])" :key="i" class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <span class="fw-semibold text-dark">{{ item.name }}</span>
                <span class="text-muted small ms-2">x{{ item.qty || item.quantity || 1 }}</span>
              </div>
              <div class="fw-bold text-dark">₹{{ (Number(item.price || 0) * Number(item.qty || item.quantity || 1)).toLocaleString() }}</div>
            </div>
          </div>

          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Subtotal:</span>
            <span>₹{{ Number(selectedOrderModal?.subtotal || 0).toLocaleString() }}</span>
          </div>
          <div v-if="selectedOrderModal?.discount" class="d-flex justify-content-between small text-danger mb-1">
            <span>Discount:</span>
            <span>-₹{{ Number(selectedOrderModal?.discount || 0).toLocaleString() }}</span>
          </div>
          <div class="d-flex justify-content-between small text-muted mb-2">
            <span>Shipping:</span>
            <span>₹{{ Number(selectedOrderModal?.shipping || 0).toLocaleString() }}</span>
          </div>
          <div class="d-flex justify-content-between fw-bold fs-5 text-dark border-top pt-2">
            <span>Total:</span>
            <span class="text-primary">₹{{ Number(selectedOrderModal?.total || 0).toLocaleString() }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. CONTACT MESSAGE MODAL -->
    <div v-if="selectedContactModal" class="kaiadmin-modal-backdrop" @click.self="selectedContactModal = null">
      <div class="card card-round shadow-lg kaiadmin-modal-dialog">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title fw-bold mb-0">{{ selectedContactModal.name }}</h4>
            <small class="text-muted">{{ new Date(selectedContactModal.created_at).toLocaleString() }}</small>
          </div>
          <button type="button" class="btn-close" @click="selectedContactModal = null"></button>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <span class="badge me-2" :class="selectedContactModal.status === 'unread' ? 'badge-danger' : 'badge-success'">
              {{ selectedContactModal.status.toUpperCase() }}
            </span>
            <span class="text-muted small">📧 <a :href="'mailto:' + selectedContactModal.email">{{ selectedContactModal.email }}</a></span>
            <span v-if="selectedContactModal.phone" class="text-muted small ms-3">📞 {{ selectedContactModal.phone }}</span>
            <span v-if="selectedContactModal.company" class="text-muted small ms-3">🏢 {{ selectedContactModal.company }}</span>
          </div>
          <div class="p-3 bg-light rounded border mb-4">
            <p class="mb-0 text-dark">{{ selectedContactModal.message }}</p>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <a :href="'mailto:' + selectedContactModal.email + '?subject=Re: Inquiry with Chhabra Sports'" class="btn btn-primary btn-round btn-sm">
              <i class="fas fa-reply me-1"></i> Reply via Email
            </a>
            <div class="d-flex gap-2">
              <button 
                class="btn btn-outline-secondary btn-round btn-sm"
                @click="markContactStatus(selectedContactModal, selectedContactModal.status === 'read' ? 'unread' : 'read')"
              >
                Mark as {{ selectedContactModal.status === 'read' ? 'Unread' : 'Read' }}
              </button>
              <button class="btn btn-outline-danger btn-round btn-sm" @click="deleteContactMessage(selectedContactModal.id)">
                <i class="fas fa-trash me-1"></i> Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. USER PROFILE & ORDER HISTORY MODAL -->
    <div v-if="selectedUserProfileModal" class="kaiadmin-modal-backdrop" @click.self="selectedUserProfileModal = null">
      <div class="card card-round shadow-lg kaiadmin-modal-dialog" style="max-width: 680px;">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(selectedUserProfileModal.name)}&background=1572E8&color=ffffff&bold=true`" class="rounded-circle me-3" style="width:44px; height:44px;" />
            <div>
              <h5 class="fw-bold mb-0 text-dark">{{ selectedUserProfileModal.name }}</h5>
              <small class="text-muted">User ID: #USR-{{ selectedUserProfileModal.id }} · Registered: {{ selectedUserProfileModal.registered_at }}</small>
            </div>
          </div>
          <button type="button" class="btn-close" @click="selectedUserProfileModal = null"></button>
        </div>
        <div class="card-body p-4">
          <div class="row g-2 mb-3">
            <div class="col-sm-4 p-2 bg-light rounded text-center">
              <small class="text-muted d-block">Email</small>
              <strong class="text-dark small">{{ selectedUserProfileModal.email }}</strong>
            </div>
            <div class="col-sm-4 p-2 bg-light rounded text-center">
              <small class="text-muted d-block">Phone</small>
              <strong class="text-dark small">{{ selectedUserProfileModal.phone || 'N/A' }}</strong>
            </div>
            <div class="col-sm-4 p-2 bg-light rounded text-center">
              <small class="text-muted d-block">Total Spend</small>
              <strong class="text-primary small">₹{{ totalUserSpend.toLocaleString() }}</strong>
            </div>
          </div>

          <h6 class="fw-bold text-dark mb-2">Order History ({{ selectedUserOrders.length }})</h6>
          <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Order #</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="selectedUserOrders.length === 0">
                  <td colspan="4" class="text-center text-muted py-3">No orders placed by this user yet.</td>
                </tr>
                <tr v-for="o in selectedUserOrders" :key="o.id">
                  <td class="fw-bold text-primary">#{{ o.order_number }}</td>
                  <td>₹{{ Number(o.total || 0).toLocaleString() }}</td>
                  <td>
                    <span :style="getStatusDropdownStyle(o.status)">{{ o.status }}</span>
                  </td>
                  <td class="text-muted small">{{ formatOrderDate(o.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. INVITE / ADD NEW ADMIN MODAL -->
    <div v-if="isInviteUserModalOpen" class="kaiadmin-modal-backdrop" @click.self="isInviteUserModalOpen = false">
      <div class="card card-round shadow-lg kaiadmin-modal-dialog" style="max-width: 520px;">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title fw-bold mb-0">Invite / Create New Account</h5>
          <button type="button" class="btn-close" @click="isInviteUserModalOpen = false"></button>
        </div>
        <div class="card-body p-4">
          <form @submit.prevent="handleInviteUser">
            <div v-if="inviteUserError" class="alert alert-danger py-2 small mb-3">
              {{ inviteUserError }}
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-muted">Full Name *</label>
              <input type="text" v-model="inviteForm.name" placeholder="John Doe" required class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-muted">Email Address *</label>
              <input type="email" v-model="inviteForm.email" placeholder="john@example.com" required class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-muted">Phone Number</label>
              <input type="text" v-model="inviteForm.phone" placeholder="+91 9876543210" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold text-muted">Initial Password *</label>
              <input type="password" v-model="inviteForm.password" placeholder="••••••••" required class="form-control" />
            </div>
            <div class="mb-4">
              <label class="form-label small fw-semibold text-muted">Role</label>
              <select v-model="inviteForm.role" class="form-select">
                <option value="Admin">Administrator (Full Dashboard Access)</option>
                <option value="Customer">Standard Customer</option>
              </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-outline-secondary btn-round" @click="isInviteUserModalOpen = false">Cancel</button>
              <button type="submit" class="btn btn-primary btn-round" :disabled="isSubmittingInvite">
                {{ isSubmittingInvite ? 'Creating...' : 'Create Account' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ========================================================= -->
    <!-- 4. KAIADMIN NATIVE MOBILE BOTTOM NAVIGATION BAR (< 768px)  -->
    <!-- ========================================================= -->
    <nav v-if="isAdminAuthenticated" class="d-md-none kaiadmin-bottom-nav">
      <a href="#" class="bottom-nav-item" :class="{ active: currentTab === 'dashboard' }" @click.prevent="currentTab = 'dashboard'; isMobileMenuOpen = false;">
        <i class="fas fa-chart-line"></i>
        <span>Overview</span>
      </a>
      <a href="#" class="bottom-nav-item" :class="{ active: currentTab === 'all-products' }" @click.prevent="currentTab = 'all-products'; isMobileMenuOpen = false;">
        <i class="fas fa-boxes"></i>
        <span>Catalog</span>
      </a>
      <a href="#" class="bottom-nav-item" :class="{ active: currentTab === 'orders' }" @click.prevent="currentTab = 'orders'; isMobileMenuOpen = false;">
        <i class="fas fa-shopping-cart"></i>
        <span>Orders</span>
      </a>
      <a href="#" class="bottom-nav-item position-relative" :class="{ active: currentTab === 'contact-messages' }" @click.prevent="currentTab = 'contact-messages'; isMobileMenuOpen = false;">
        <i class="fas fa-envelope"></i>
        <span v-if="unreadContactCount > 0" class="bottom-nav-badge">{{ unreadContactCount }}</span>
        <span>Inquiries</span>
      </a>
      <a href="#" class="bottom-nav-item" :class="{ active: isMobileMenuOpen }" @click.prevent="isMobileMenuOpen = !isMobileMenuOpen">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
      </a>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  products: Array
});

const emit = defineEmits(['exit-admin', 'add-product', 'delete-product']);

// Sidebar & Layout State
const isMobileMenuOpen = ref(false);
const isMobileTopbarOpen = ref(false);
const isMobileSearchOpen = ref(false);
const isProductsMobileFilterOpen = ref(false);
const isOrdersMobileFilterOpen = ref(false);
const isSidebarCollapsed = ref(false);
const isProfileDropdownOpen = ref(false);
const currentTab = ref('dashboard');
const selectedReportMonth = ref('2026-08');
const productType = ref('simple');
const adminToast = ref('');
const isSubmitting = ref(false);
const isLoading = ref(false);
const isInitialLoading = ref(true);

// Mobile Sidebar & Topbar Toggle Watcher (Pure KaiAdmin pattern)
watch([isMobileMenuOpen, isMobileTopbarOpen], ([menuOpen, topbarOpen]) => {
  if (typeof document !== 'undefined') {
    document.documentElement.classList.toggle('nav_open', menuOpen);
    document.documentElement.classList.toggle('topbar_open', topbarOpen);
    document.body.style.overflow = menuOpen ? 'hidden' : '';
  }
});

// Dynamic Stylesheet Injector for Kaiadmin
function injectAdminCss() {
  const cssFiles = [
    { id: 'kaiadmin-bs', href: '/admin-assets/css/bootstrap.min.css' },
    { id: 'kaiadmin-plugins', href: '/admin-assets/css/plugins.min.css' },
    { id: 'kaiadmin-core', href: '/admin-assets/css/kaiadmin.min.css' }
  ];

  cssFiles.forEach(file => {
    if (!document.getElementById(file.id)) {
      const link = document.createElement('link');
      link.id = file.id;
      link.rel = 'stylesheet';
      link.href = file.href;
      document.head.appendChild(link);
    }
  });
}

function removeAdminCss() {
  ['kaiadmin-bs', 'kaiadmin-plugins', 'kaiadmin-core'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.remove();
  });
}

function handleExitAdmin() {
  removeAdminCss();
  emit('exit-admin');
}

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

// CONTACT MESSAGES MANAGEMENT STATE
const contactMessagesList = ref([]);
const contactSearchQuery = ref('');
const contactStatusFilter = ref('all');
const selectedContactModal = ref(null);

const unreadContactCount = computed(() => {
  return contactMessagesList.value.filter(m => m.status === 'unread').length;
});

async function fetchContactMessages() {
  try {
    const res = await axios.get('/api/contact-messages');
    if (res.data && res.data.success && Array.isArray(res.data.messages)) {
      contactMessagesList.value = res.data.messages;
    }
  } catch (err) {
    console.error('Error fetching contact messages from database:', err);
  }
}

const filteredContactMessages = computed(() => {
  let list = contactMessagesList.value;
  if (contactStatusFilter.value !== 'all') {
    list = list.filter(m => m.status === contactStatusFilter.value);
  }
  if (contactSearchQuery.value.trim()) {
    const q = contactSearchQuery.value.toLowerCase().trim();
    list = list.filter(m => 
      (m.name && m.name.toLowerCase().includes(q)) || 
      (m.email && m.email.toLowerCase().includes(q)) || 
      (m.phone && m.phone.includes(q)) ||
      (m.company && m.company.toLowerCase().includes(q)) ||
      (m.message && m.message.toLowerCase().includes(q))
    );
  }
  return list;
});

async function openContactMessageModal(msg) {
  selectedContactModal.value = msg;
  if (msg.status === 'unread') {
    try {
      await axios.put(`/api/contact-messages/${msg.id}/status`, { status: 'read' });
      msg.status = 'read';
      fetchContactMessages();
    } catch (e) {}
  }
}

async function markContactStatus(msg, status) {
  try {
    const res = await axios.put(`/api/contact-messages/${msg.id}/status`, { status });
    if (res.data && res.data.success) {
      msg.status = status;
      if (selectedContactModal.value && selectedContactModal.value.id === msg.id) {
        selectedContactModal.value.status = status;
      }
      showToast(`Contact message marked as ${status.toUpperCase()}! 📩`);
      fetchContactMessages();
    }
  } catch (e) {}
}

async function deleteContactMessage(id) {
  if (confirm('Are you sure you want to delete this contact message?')) {
    try {
      const res = await axios.delete(`/api/contact-messages/${id}`);
      if (res.data && res.data.success) {
        showToast('Contact message deleted successfully. 🗑️');
        selectedContactModal.value = null;
        fetchContactMessages();
      }
    } catch (e) {}
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

// Dynamic Monthly & Overall Sales Analytics Data from SQLite/MySQL DB Orders
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

  const catColors = ['#1572E8', '#48ABF7', '#31CE36', '#FFAD46', '#F25961', '#6861CE', '#10B981', '#EC4899'];
  let catKeys = Object.keys(catTotals);

  // Fallback category breakdown if no orders in selected month yet
  if (catKeys.length === 0) {
    const fallbackCats = categories.value.length > 0 
      ? categories.value.map(c => c.name) 
      : ['Badminton & Racquets', 'Cricket Equipment', 'Fitness & Gym', 'Sports Shoes'];
    
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
    heightPct: s.revenue > 0 ? Math.max(Math.round((s.revenue / maxRevenue) * 100), 12) : 8
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
        if (res.data.token) {
          localStorage.setItem('chhabra_token', res.data.token);
          sessionStorage.setItem('chhabra_token', res.data.token);
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + res.data.token;
        }
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
  localStorage.removeItem('chhabra_token');
  sessionStorage.removeItem('chhabra_token');
  delete axios.defaults.headers.common['Authorization'];
  removeAdminCss();
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

const showInlineBrandForm = ref(false);
const quickBrandName = ref('');
const isQuickBrandSubmitting = ref(false);

async function quickCreateBrand() {
  const bName = quickBrandName.value.trim();
  if (!bName) {
    showToast('Please enter a Brand Name!');
    return;
  }

  isQuickBrandSubmitting.value = true;
  try {
    const res = await axios.post('/api/brands', { name: bName });
    if (res.data && res.data.success) {
      const newBrandObj = res.data.data;
      if (!brands.value.some(b => b.name.toLowerCase() === bName.toLowerCase())) {
        brands.value.push(newBrandObj || { name: bName, slug: bName.toLowerCase() });
      }
      newProd.value.brand = bName;
      quickBrandName.value = '';
      showInlineBrandForm.value = false;
      showToast(`New brand "${bName}" added to database and selected! 🎉`);
    } else {
      showToast('Could not save brand!');
    }
  } catch (e) {
    if (!brands.value.some(b => b.name.toLowerCase() === bName.toLowerCase())) {
      brands.value.push({ name: bName, slug: bName.toLowerCase() });
    }
    newProd.value.brand = bName;
    quickBrandName.value = '';
    showInlineBrandForm.value = false;
    showToast(`Brand "${bName}" selected! 🎉`);
  } finally {
    isQuickBrandSubmitting.value = false;
  }
}

const categories = ref([]);
const showInlineCatForm = ref(false);
const quickCatName = ref('');
const isQuickCatSubmitting = ref(false);

async function quickCreateCategory() {
  const cName = quickCatName.value.trim();
  if (!cName) {
    showToast('Please enter a Category Name!');
    return;
  }

  isQuickCatSubmitting.value = true;
  try {
    const res = await axios.post('/api/categories', { name: cName, icon: '📦' });
    if (res.data && res.data.success) {
      const newCatObj = res.data.data;
      if (!categories.value.some(c => c.name.toLowerCase() === cName.toLowerCase())) {
        categories.value.push(newCatObj || { name: cName, slug: cName.toLowerCase(), icon: '📦' });
      }
      newProd.value.category = newCatObj?.slug || cName.toLowerCase();
      quickCatName.value = '';
      showInlineCatForm.value = false;
      showToast(`New category "${cName}" added to database and selected! 🎉`);
    }
  } catch (e) {
    const slug = cName.toLowerCase().replace(/\s+/g, '-');
    if (!categories.value.some(c => c.name.toLowerCase() === cName.toLowerCase())) {
      categories.value.push({ name: cName, slug, icon: '📦' });
    }
    newProd.value.category = slug;
    quickCatName.value = '';
    showInlineCatForm.value = false;
    showToast(`Category "${cName}" selected! 🎉`);
  } finally {
    isQuickCatSubmitting.value = false;
  }
}

// --- BULK EXCEL / CSV UPLOAD STATE & FUNCTIONS ---
const bulkParsedProducts = ref([]);
const bulkSearchFilter = ref('');
const isBulkImporting = ref(false);
const bulkImportProgress = ref(0);

const selectedBulkCount = computed(() => {
  return bulkParsedProducts.value.filter(p => p.selected).length;
});

const isAllBulkSelected = computed(() => {
  return bulkParsedProducts.value.length > 0 && bulkParsedProducts.value.every(p => p.selected);
});

const filteredBulkProducts = computed(() => {
  if (!bulkSearchFilter.value.trim()) {
    return bulkParsedProducts.value;
  }
  const q = bulkSearchFilter.value.toLowerCase().trim();
  return bulkParsedProducts.value.filter(p => 
    (p.name && p.name.toLowerCase().includes(q)) ||
    (p.code_id && p.code_id.toLowerCase().includes(q)) ||
    (p.category && p.category.toLowerCase().includes(q)) ||
    (p.brand && p.brand.toLowerCase().includes(q))
  );
});

function selectAllBulk(val) {
  bulkParsedProducts.value.forEach(p => p.selected = val);
}

function toggleAllBulkCheckbox(e) {
  selectAllBulk(e.target.checked);
}

function removeBulkRow(index) {
  bulkParsedProducts.value.splice(index, 1);
}

// Download Sample CSV Template
function downloadCsvTemplate() {
  const csvHeaders = "code_id,name,category,brand,price,old_price,stock,tag,specs\n";
  const sampleRow1 = 'CHS-RAC-101,"Yonex Astrox 99 Play Badminton Racquet",badminton,Yonex,5990,4490,25,HOT,"Weight: 4U / G5 | Tension: 28 lbs"\n';
  const sampleRow2 = 'CHS-RAC-102,"Victor Thruster K 15 Badminton Racquet",badminton,Victor,7500,5990,15,NEW,"Weight: 5U / G5 | Tension: 30 lbs"\n';
  const sampleRow3 = 'CHS-SH-103,"Li-Ning Ranger IV Badminton Shoes",indoor-shoes,Li-Ning,6990,5290,10,BESTSELLER,"Size: UK 8 | Non-Marking Sole"\n';

  const csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(csvHeaders + sampleRow1 + sampleRow2 + sampleRow3);
  const link = document.createElement("a");
  link.setAttribute("href", csvContent);
  link.setAttribute("download", "chhabra_sports_products_template.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  showToast("Sample Excel/CSV Template downloaded successfully! 📥");
}

// Handle CSV File Selection
function handleCsvFileSelect(e) {
  const file = e.target.files[0];
  if (!file) return;
  parseCsvFile(file);
}

function safeNum(val, fallback = 0) {
  if (val === null || val === undefined || val === '') return fallback;
  if (typeof val === 'number') return isNaN(val) ? fallback : val;
  const str = String(val).replace(/[^0-9.]/g, '');
  const num = parseFloat(str);
  return isNaN(num) ? fallback : num;
}

function parseCsvFile(file) {
  const reader = new FileReader();
  reader.onload = (evt) => {
    const text = evt.target.result;
    const parsedRows = parseCSV(text);

    if (parsedRows.length === 0) {
      showToast("No product rows found in uploaded CSV file!");
      return;
    }

    bulkParsedProducts.value = parsedRows.map((row, idx) => {
      // 1. Code / SKU / ID
      const codeId = row.sku || row.code_id || row.code || row.id || ('CHS-IMP-' + (1000 + idx));
      
      // 2. Name
      const name = row.name || row.title || row.product_name || `Imported Product #${idx + 1}`;
      
      // 3. Category & Subcategory (e.g. "Cricket > Batting Gloves, Cricket")
      let category = row.categories || row.category || 'badminton';
      if (category.includes(',')) {
        category = category.split(',')[0].trim();
      }
      
      // 4. Brand
      let brand = row.brands || row.brand || 'Generic';
      if (!brand || brand === '1') brand = 'Generic';

      // 5. Price & Old Price
      const regPrice = safeNum(row.regular_price || row.price, 2000);
      const salePrice = row.sale_price ? safeNum(row.sale_price, 0) : 0;
      const finalPrice = salePrice > 0 && salePrice < regPrice ? salePrice : regPrice;
      const finalOldPrice = salePrice > 0 && salePrice < regPrice ? regPrice : null;

      // 6. Stock
      const stock = safeNum(row.stock || row.qty || row.quantity, 25);

      // 7. Image Extraction (WooCommerce images column)
      let extractedImg = row.images || row.image || row.img_url || '';
      if (extractedImg.includes(',')) {
        extractedImg = extractedImg.split(',')[0].trim();
      }

      // 8. Specs & Tag
      const rawSpecs = row.short_description || row.specs || row.description || '';
      const cleanSpecs = rawSpecs.replace(/<[^>]*>?/gm, '').trim().slice(0, 160);
      const tag = row.type ? row.type.toUpperCase() : 'NEW';

      const imageOption = extractedImg ? 'url' : 'upload';

      return {
        id: 'tmp_' + Date.now() + '_' + idx,
        selected: true,
        code_id: codeId,
        name: name,
        category: category,
        brand: brand,
        price: finalPrice || 1000,
        old_price: finalOldPrice,
        stock: stock,
        tag: tag,
        specs: cleanSpecs,
        image_option: imageOption,
        img_url: extractedImg,
        img_file_preview: '',
        status: 'pending'
      };
    });

    showToast(`${bulkParsedProducts.value.length} Products & Categories parsed from CSV! Review details & image options before saving. 📊`);
  };

  reader.readAsText(file);
}

// Custom CSV Parser
function parseCSV(text) {
  const lines = text.split(/\r\n|\n/);
  if (lines.length < 2) return [];

  const headers = parseCSVLine(lines[0]).map(h => h.trim().toLowerCase().replace(/[^a-z0-9_]/g, '_'));
  const results = [];

  for (let i = 1; i < lines.length; i++) {
    const rawLine = lines[i].trim();
    if (!rawLine) continue;

    const values = parseCSVLine(rawLine);
    const row = {};
    headers.forEach((h, idx) => {
      row[h] = values[idx] !== undefined ? values[idx].trim() : '';
    });
    results.push(row);
  }
  return results;
}

function parseCSVLine(line) {
  const result = [];
  let current = '';
  let inQuotes = false;
  for (let i = 0; i < line.length; i++) {
    const char = line[i];
    if (char === '"' || char === "'") {
      inQuotes = !inQuotes;
    } else if (char === ',' && !inQuotes) {
      result.push(current);
      current = '';
    } else {
      current += char;
    }
  }
  result.push(current);
  return result;
}

// Handle Image File Upload for a Single Review Row
function handleRowImageUpload(e, prod) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (evt) => {
    prod.img_file_preview = evt.target.result;
    prod.img_url = evt.target.result;
  };
  reader.readAsDataURL(file);
}

// Get Image Source for Thumbnail Preview
function getBulkProductDisplayImage(prod) {
  if (prod.image_option === 'upload' && prod.img_file_preview) {
    return prod.img_file_preview;
  }
  if (prod.image_option === 'url' && prod.img_url) {
    return prod.img_url;
  }
  if (prod.image_option === 'none') {
    return 'https://ui-avatars.com/api/?name=No+Image&background=cbd5e1&color=64748b';
  }
  return prod.img_url || 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80';
}

// Execute Bulk Import to Database
async function executeBulkImport() {
  const toImport = bulkParsedProducts.value.filter(p => p.selected);
  if (toImport.length === 0) {
    showToast("Please select at least one product to import!");
    return;
  }

  isBulkImporting.value = true;
  bulkImportProgress.value = 0;

  let successCount = 0;
  let failCount = 0;
  let isAuthError = false;

  for (let i = 0; i < toImport.length; i++) {
    const prod = toImport[i];
    const finalImg = getBulkProductDisplayImage(prod);

    const payload = {
      code_id: prod.code_id,
      name: prod.name,
      category: prod.category || 'badminton',
      brand: prod.brand || 'Yonex',
      price: safeNum(prod.price, 1000),
      old_price: prod.old_price ? safeNum(prod.old_price, null) : null,
      stock: safeNum(prod.stock, 10),
      in_stock: safeNum(prod.stock, 10) > 0,
      tag: prod.tag || 'NEW',
      specs: prod.specs || '',
      img: finalImg
    };

    try {
      const res = await axios.post('/api/products', payload);
      if (res.data && res.data.success) {
        successCount++;
        prod.status = 'success';
      } else {
        failCount++;
        prod.status = 'error';
        console.error('Bulk import item failed:', prod.name, res.data);
      }
    } catch (err) {
      failCount++;
      prod.status = 'error';
      const status = err.response?.status;
      const errMsg = err.response?.data?.message || err.message;
      console.error('Bulk import item exception:', prod.name, status, errMsg);
      if (status === 401) {
        isAuthError = true;
        showToast("Session expired or Unauthorized! Please re-login to Admin Panel.");
        break;
      }
    }

    bulkImportProgress.value = Math.round(((i + 1) / toImport.length) * 100);
  }

  isBulkImporting.value = false;
  if (!isAuthError) {
    showToast(`Bulk Import Completed! ${successCount} Products Saved to Database (${failCount} Failed). 🎉`);
    await fetchFilteredProducts();
    await loadAdminPersistedData();
  }
}

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
}

onMounted(async () => {
  injectAdminCss();
  checkAdminAuth();
  loadAdminPersistedData();
  
  try {
    await Promise.allSettled([
      fetchDatabaseAttributes(),
      fetchFilteredProducts(),
      fetchOrdersFromBackend(),
      fetchBackendUsers(),
      fetchContactMessages()
    ]);
  } catch (e) {
    console.error('Admin initial load error:', e);
  } finally {
    setTimeout(() => {
      isInitialLoading.value = false;
    }, 350);
  }
});

onUnmounted(() => {
  removeAdminCss();
});

watch(currentTab, (newTab) => {
  if (newTab === 'users') {
    fetchBackendUsers();
  } else if (newTab === 'orders') {
    fetchOrdersFromBackend();
  } else if (newTab === 'contact-messages') {
    fetchContactMessages();
  }
});

function showToast(msg) {
  adminToast.value = msg;
  setTimeout(() => { adminToast.value = ''; }, 3500);
}

async function saveNewProduct() {
  if (!newProd.value.name || !newProd.value.name.trim()) {
    showToast('Please enter Product Name!');
    return;
  }

  isSubmitting.value = true;
  const isVar = productType.value === 'variable';
  const isInStock = newProd.value.stockStatus !== 'Out of stock';

  const regPrice = Number(newProd.value.price) || 0;
  const salePrice = Number(newProd.value.salePrice) || 0;

  let effectivePrice = regPrice;
  let oldPrice = null;

  if (salePrice > 0 && salePrice < regPrice) {
    effectivePrice = salePrice;
    oldPrice = regPrice;
  } else if (salePrice > 0) {
    effectivePrice = salePrice;
  } else {
    effectivePrice = regPrice;
  }

  const prodToAdd = {
    code_id: newProd.value.code_id ? newProd.value.code_id.trim() : ('SKU-' + Date.now()),
    name: newProd.value.name.trim(),
    category: newProd.value.category || 'badminton',
    brand: newProd.value.brand || 'Generic',
    price: effectivePrice,
    old_price: oldPrice,
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

  if (newProd.value.id) {
    prodToAdd.id = newProd.value.id;
  }

  let savedRecord = { ...prodToAdd };

  try {
    const res = await axios.post('/api/products', prodToAdd);
    if (res.data && res.data.success && res.data.data) {
      savedRecord = { ...savedRecord, ...res.data.data };
      emit('add-product', savedRecord);
      await fetchFilteredProducts();
      await loadAdminPersistedData();
      showToast(`${isVar ? 'Variable' : 'Simple'} Product "${newProd.value.name}" Saved to Database! 🎉`);

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
      currentTab.value = 'all-products';
    } else {
      showToast(`Failed to save product: ${res.data?.message || 'Server error'}`);
    }
  } catch (e) {
    console.error('Error saving product:', e);
    const msg = e.response?.data?.message || 'Failed to save product to Database!';
    showToast(`Error: ${msg}`);
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteProduct(p) {
  if (!p) return;
  const targetId = typeof p === 'object' ? (p.id || p.code_id) : p;
  const name = typeof p === 'object' ? p.name : 'Product';

  if (confirm(`Are you sure you want to delete "${name}" from store database?`)) {
    try {
      const res = await axios.delete(`/api/products/${targetId}`);
      if (res.data && res.data.success) {
        showToast(`Product "${name}" deleted from store database! 🗑️`);
        emit('delete-product', targetId);
        fetchFilteredProducts();
      } else {
        showToast(`⚠️ ${res.data?.message || 'Cannot delete product!'}`);
      }
    } catch (e) {
      showToast(`⚠️ Cannot delete product "${name}"!`);
    }
  }
}

function handleInlineImageUpload(e, product) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async (evt) => {
    product.img = evt.target.result;
    showToast(`Uploading new image for "${product.name}"... ⏳`);
    await saveInlineProduct(product);
  };
  reader.readAsDataURL(file);
}

async function saveInlineProduct(p) {
  if (!p || !p.name) return;

  const isInStock = p.stockStatus !== 'Out of stock' && p.in_stock !== false;
  const payload = {
    id: p.id,
    code_id: p.code_id,
    name: p.name,
    category: p.category || 'badminton',
    brand: p.brand || 'Generic',
    price: Number(p.price) || 0,
    old_price: p.old_price ? Number(p.old_price) : null,
    stock: Number(p.stock) || 0,
    in_stock: isInStock,
    stockStatus: isInStock ? 'In stock' : 'Out of stock',
    tag: p.tag || 'NEW',
    specs: p.specs || '',
    img: p.img,
    isVariable: p.is_variable || p.isVariable || false,
    variations: p.variations || []
  };

  try {
    const res = await axios.post('/api/products', payload);
    if (res.data && res.data.success && res.data.data) {
      if (res.data.data.img) {
        p.img = res.data.data.img;
      }
      showToast(`Product "${p.name}" updated inline! ⚡🎉`);
    } else {
      showToast(`Failed to update product inline: ${res.data?.message || 'Error'}`);
    }
  } catch (e) {
    console.error('Error saving inline product:', e);
    const msg = e.response?.data?.message || 'Error saving changes to database.';
    showToast(`Error: ${msg}`);
  }
}

function editProduct(p) {
  newProd.value = { ...p };
  const stockBool = (p.in_stock !== false && p.in_stock !== 0 && p.in_stock !== 'false');
  newProd.value.stockStatus = (p.stockStatus === 'Out of stock' || !stockBool) ? 'Out of stock' : 'In stock';
  productType.value = (p.isVariable || p.is_variable) ? 'variable' : 'simple';

  if (p.old_price && Number(p.old_price) > Number(p.price)) {
    newProd.value.price = p.old_price;
    newProd.value.salePrice = p.price;
  } else {
    newProd.value.price = p.price;
    newProd.value.salePrice = '';
  }

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
      showToast(`Attribute '${savedAttr.name}' saved! 💾🎉`);
    } else {
      globalAttributes.value.push({
        name: newAttrForm.value.name.trim(),
        values: initialVals,
        inputValue: ''
      });
      showToast(`Attribute '${newAttrForm.value.name}' saved!`);
    }
  } catch (e) {
    globalAttributes.value.push({
      name: newAttrForm.value.name.trim(),
      values: initialVals,
      inputValue: ''
    });
    showToast(`Attribute '${newAttrForm.value.name}' saved to catalog!`);
  }

  newAttrForm.value = { name: '', values: '' };
  showNewAttrBox.value = false;
  isSubmitting.value = false;
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
    showToast(`Added '${val}' to '${attr.name}'! 💾🎉`);
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
    img: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80'
  }));

  showToast(`Generated ${variationsList.value.length} variation combinations! ⚡🎉`);
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
    showToast(`Image "${file.name}" loaded! 🖼️🎉`);
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
        showToast(`${files.length} gallery image(s) added! 🖼️🎉`);
      }
    };
    reader.readAsDataURL(file);
  });
}

const editingBrandKey = ref(null);
const editingBrandForm = ref({ name: '', desc: '' });
const newBrandForm = ref({ name: '', desc: '' });

function startEditBrand(b) {
  const key = b.id || b.name;
  editingBrandKey.value = key;
  editingBrandForm.value = {
    name: b.name,
    desc: b.desc || b.description || ''
  };
}

function cancelEditBrand() {
  editingBrandKey.value = null;
  editingBrandForm.value = { name: '', desc: '' };
}

async function saveEditBrand(originalBrand) {
  if (!editingBrandForm.value.name.trim()) {
    showToast('Brand name cannot be empty!');
    return;
  }

  const updatedName = editingBrandForm.value.name.trim();
  const updatedDesc = editingBrandForm.value.desc.trim() || 'Official Sports Equipment Brand';
  const oldIdentifier = originalBrand.name;
  const idx = brands.value.findIndex(b => (b.id && b.id === originalBrand.id) || b.name === originalBrand.name);

  if (idx !== -1) {
    brands.value[idx] = {
      ...brands.value[idx],
      name: updatedName,
      desc: updatedDesc,
      description: updatedDesc
    };

    try {
      await axios.put(`/api/brands/${originalBrand.id || encodeURIComponent(oldIdentifier)}`, {
        name: updatedName,
        desc: updatedDesc
      });
    } catch (e) {}

    showToast(`Brand "${updatedName}" updated successfully! ✏️🎉`);
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
    await axios.post('/api/brands', newB);
    await loadAdminPersistedData();
  } catch (e) {}

  showToast(`Brand "${name}" added to Database! 🎉`);
  newBrandForm.value = { name: '', desc: '' };
}

const editingCategoryKey = ref(null);
const editingCategoryItem = ref(null);
const editingCategoryForm = ref({ name: '', icon: '📦', slug: '', parent_id: null });
const newCatForm = ref({ name: '', icon: '📦', slug: '', parent_id: null });

function getParentCategoryName(c) {
  if (!c) return '';
  if (c.parent && c.parent.name) return c.parent.name;
  if (c.parent_id) {
    const p = categories.value.find(cat => cat.id == c.parent_id);
    if (p) return p.name;
  }
  return '';
}

function startEditCategory(c) {
  editingCategoryItem.value = c;
  const key = c.id || c.slug || c.name;
  editingCategoryKey.value = key;
  editingCategoryForm.value = {
    name: c.name,
    icon: c.icon || '📦',
    slug: c.slug || c.name.toLowerCase().replace(/\s+/g, '-'),
    parent_id: c.parent_id || null
  };
}

function cancelEditCategory() {
  editingCategoryKey.value = null;
  editingCategoryItem.value = null;
  editingCategoryForm.value = { name: '', icon: '📦', slug: '', parent_id: null };
}

async function saveEditCategory() {
  if (!editingCategoryForm.value.name.trim()) {
    showToast('Category name cannot be empty!');
    return;
  }

  const targetCat = editingCategoryItem.value || categories.value.find(c => (c.id && c.id == editingCategoryKey.value) || c.slug === editingCategoryKey.value || c.name === editingCategoryKey.value);

  const updatedName = editingCategoryForm.value.name.trim();
  const updatedIcon = editingCategoryForm.value.icon.trim() || '📦';
  const updatedSlug = editingCategoryForm.value.slug.trim() || updatedName.toLowerCase().replace(/\s+/g, '-');
  const parent_id = editingCategoryForm.value.parent_id || null;

  const targetId = targetCat ? (targetCat.id || targetCat.slug || targetCat.name) : editingCategoryKey.value;

  try {
    const res = await axios.put(`/api/categories/${encodeURIComponent(targetId)}`, {
      name: updatedName,
      icon: updatedIcon,
      slug: updatedSlug,
      parent_id: parent_id
    });

    showToast(`Category "${updatedName}" updated successfully! ✏️🎉`);
    await loadAdminPersistedData();
  } catch (e) {
    console.error('Error updating category:', e);
    showToast('Failed to update category. Please try again.');
  } finally {
    editingCategoryKey.value = null;
    editingCategoryItem.value = null;
    editingCategoryForm.value = { name: '', icon: '📦', slug: '', parent_id: null };
  }
}

async function saveCategory() {
  if (!newCatForm.value.name.trim()) {
    showToast('Please enter category name!');
    return;
  }
  const name = newCatForm.value.name.trim();
  const icon = newCatForm.value.icon.trim() || '📦';
  const slug = newCatForm.value.slug.trim() || name.toLowerCase().replace(/\s+/g, '-');
  const parent_id = newCatForm.value.parent_id || null;

  const newCat = { name, icon, slug, parent_id };

  try {
    await axios.post('/api/categories', newCat);
    await loadAdminPersistedData();
  } catch (e) {
    categories.value.push(newCat);
  }

  showToast(`Category "${name}" saved to Database! 🎉`);
  newCatForm.value = { name: '', icon: '📦', slug: '', parent_id: null };
}

const attributeSearchQuery = ref('');
const brandSearchQuery = ref('');
const categorySearchQuery = ref('');
const tagSearchQuery = ref('');
const newTagInput = ref('');

async function saveTagFromForm() {
  if (!newTagInput.value || !newTagInput.value.trim()) {
    showToast('Please enter a Tag name!');
    return;
  }
  const tagFormatted = newTagInput.value.trim().toUpperCase();
  if (!tags.value.includes(tagFormatted)) {
    tags.value.push(tagFormatted);
    try {
      await axios.post('/api/tags', { name: tagFormatted });
    } catch (e) {}
    showToast(`Tag '🏷️ ${tagFormatted}' added! 🎉`);
  } else {
    showToast(`Tag '${tagFormatted}' already exists!`);
  }
  newTagInput.value = '';
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

const formattedCategoryOptions = computed(() => {
  const result = [];
  const mainCats = categories.value.filter(c => !c.parent_id);

  mainCats.forEach(main => {
    result.push({
      id: main.id,
      name: main.name,
      slug: main.slug || main.name.toLowerCase(),
      isParent: true,
      displayName: `📁 ${main.name}`
    });

    const children = categories.value.filter(c => c.parent_id && (c.parent_id === main.id || (c.parent && c.parent.id === main.id)));
    children.forEach(sub => {
      result.push({
        id: sub.id,
        name: sub.name,
        slug: sub.slug || sub.name.toLowerCase(),
        isParent: false,
        displayName: `\u00A0\u00A0\u00A0\u00A0↳ ${sub.name}`
      });
    });
  });

  categories.value.forEach(c => {
    if (!result.some(r => (c.id && r.id === c.id) || r.name === c.name)) {
      result.push({
        id: c.id,
        name: c.name,
        slug: c.slug || c.name.toLowerCase(),
        isParent: false,
        displayName: c.parent_id ? `\u00A0\u00A0\u00A0\u00A0↳ ${c.name}` : c.name
      });
    }
  });

  return result;
});

const filteredTagsList = computed(() => {
  if (!tagSearchQuery.value.trim()) return tags.value;
  const q = tagSearchQuery.value.toLowerCase().trim();
  return tags.value.filter(t => (typeof t === 'object' ? t.name : t).toLowerCase().includes(q));
});

async function deleteBrand(b) {
  if (!b) return;
  const name = typeof b === 'object' ? b.name : b;
  const targetId = typeof b === 'object' ? (b.id || b.name || b.slug) : b;

  if (confirm(`Are you sure you want to delete brand "${name}"?`)) {
    brands.value = brands.value.filter(brand => (brand.id && brand.id === b.id) ? false : brand.name === b.name ? false : true);
    try {
      await axios.delete(`/api/brands/${encodeURIComponent(targetId)}`);
    } catch (e) {}
    showToast(`Brand "${name}" deleted successfully! 🗑️`);
    await loadAdminPersistedData();
  }
}

async function deleteCategory(c) {
  if (!c) return;
  const name = typeof c === 'object' ? c.name : c;
  const targetId = typeof c === 'object' ? (c.id || c.slug || c.name) : c;

  if (confirm(`Are you sure you want to delete category "${name}"?`)) {
    try {
      const res = await axios.delete(`/api/categories/${encodeURIComponent(targetId)}`);
      if (res.data && res.data.success) {
        showToast(`Category "${name}" deleted! 🗑️`);
        await loadAdminPersistedData();
      }
    } catch (e) {}
  }
}

async function deleteTag(t) {
  if (!t) return;
  const tagName = typeof t === 'object' ? t.name : t;

  if (confirm(`Are you sure you want to delete tag "${tagName}"?`)) {
    tags.value = tags.value.filter(tag => (typeof tag === 'object' ? tag.name : tag) !== tagName);
    try {
      await axios.delete(`/api/tags/${encodeURIComponent(tagName)}`);
    } catch (e) {}
    showToast(`Tag "${tagName}" deleted! 🗑️`);
    await loadAdminPersistedData();
  }
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

function formatOrderDate(dateStr) {
  if (!dateStr) return 'Just Now';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

function getStatusDropdownStyle(status) {
  switch (status) {
    case 'Confirmed': return 'background:#FEF3C7; color:#B45309; border:1px solid #FCD34D; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
    case 'Processing': return 'background:#E0F2FE; color:#0369A1; border:1px solid #7DD3FC; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
    case 'Shipped': return 'background:#F3E8FF; color:#7E22CE; border:1px solid #C084FC; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
    case 'Delivered': return 'background:#DCFCE7; color:#15803D; border:1px solid #86EFAC; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
    case 'Cancelled': return 'background:#FEE2E2; color:#B91C1C; border:1px solid #FCA5A5; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
    default: return 'background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; font-weight:700; border-radius:6px; padding:3px 8px; font-size:12px;';
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
/* KAIADMIN PROFESSIONAL STYLING ENHANCEMENTS          */
/* ---------------------------------------------------- */
.kaiadmin-app-root {
  font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #2A2F5B;
  background-color: #f5f7fd;
  min-height: 100vh;
}

/* Authentication Screen */
.kaiadmin-login-screen {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0F172A 0%, #1A2035 100%);
  padding: 20px;
}

.login-card-container {
  width: 100%;
  max-width: 440px;
}

.login-logo-card {
  width: 68px;
  height: 68px;
  background: #ffffff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25), 0 0 15px rgba(21, 114, 232, 0.2);
}

.brand-login-logo {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.title-navy {
  color: #2A2F5B;
  letter-spacing: 0.5px;
}

.kaiadmin-app-root {
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

/* Kaiadmin Wrapper Layout */
.kaiadmin-wrapper {
  display: flex;
  min-height: 100vh;
  position: relative;
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

.kaiadmin-wrapper .sidebar {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  width: 265px;
  display: block;
  z-index: 1001;
  transition: all 0.3s;
}

.kaiadmin-wrapper .main-header {
  position: fixed;
  top: 0;
  left: 265px;
  width: calc(100% - 265px);
  min-height: 65px;
  background: #ffffff;
  z-index: 1000;
  transition: all 0.3s;
}

.kaiadmin-wrapper .main-panel {
  position: relative;
  margin-left: 265px;
  width: calc(100% - 265px);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: all 0.3s;
}

/* Sidebar Minimized state */
.kaiadmin-wrapper.sidebar_minimize .sidebar {
  width: 75px;
}

.kaiadmin-wrapper.sidebar_minimize .main-header {
  left: 75px;
  width: calc(100% - 75px);
}

.kaiadmin-wrapper.sidebar_minimize .main-panel {
  margin-left: 75px;
  width: calc(100% - 75px);
}

.kaiadmin-mobile-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.45);
  z-index: 1000;
}

.kaiadmin-content-area {
  background: #f5f7fd;
  flex: 1;
  min-height: calc(100vh - 125px);
  margin-top: 65px;
}

/* Custom Yearly Bar Chart */
.kaiadmin-bar-chart-container {
  overflow-x: auto;
}

.chart-bars-track {
  min-width: 500px;
}

.bar-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  height: 100%;
}

.bar-pillar {
  cursor: pointer;
}

.bar-pillar:hover {
  opacity: 0.85;
  filter: brightness(1.1);
}

/* Modal Dialogs */
.kaiadmin-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(4px);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  animation: fadeIn 0.2s ease-out;
}

.kaiadmin-modal-dialog {
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

/* Responsiveness & Mobile Styling (KaiAdmin Simple Template Style) */
.kaiadmin-mobile-bar {
  height: 60px;
  background: #0F172A;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
  position: relative;
  z-index: 1000;
  width: 100%;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.mobile-hamburger-btn {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: 8px;
  transition: background 0.2s ease;
}

.mobile-hamburger-btn:active {
  background: rgba(255, 255, 255, 0.1);
}

.mobile-action-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-color: rgba(255, 255, 255, 0.2) !important;
  transition: all 0.2s ease;
}

.mobile-action-btn.active,
.mobile-action-btn:active {
  background: rgba(21, 114, 232, 0.3) !important;
  border-color: #1572E8 !important;
}

.sidebar-mobile-header {
  background: #0F172A;
}

.btn-close-mobile-nav {
  text-decoration: none !important;
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.btn-close-mobile-nav:active {
  background: rgba(255, 255, 255, 0.15);
}

/* Mobile Search Bar Drawer */
.mobile-search-bar-wrap {
  background: #1e293b !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.slide-down-search-enter-active,
.slide-down-search-leave-active {
  transition: all 0.22s ease-out;
}

.slide-down-search-enter-from,
.slide-down-search-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

/* Native Mobile Bottom Navigation Bar (KaiAdmin App Feel) */
.kaiadmin-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #0F172A;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 -4px 18px rgba(0, 0, 0, 0.35);
  display: flex;
  align-items: center;
  justify-content: space-around;
  z-index: 1040;
  padding-bottom: env(safe-area-inset-bottom, 0px);
}

.bottom-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  height: 100%;
  color: #94a3b8;
  text-decoration: none !important;
  font-size: 10px;
  font-weight: 500;
  transition: all 0.2s ease;
  position: relative;
  gap: 3px;
}

.bottom-nav-item i {
  font-size: 16px;
  transition: transform 0.2s ease;
}

.bottom-nav-item.active {
  color: #38bdf8 !important;
  font-weight: 700 !important;
}

.bottom-nav-item.active i {
  transform: translateY(-2px);
  color: #38bdf8 !important;
}

.bottom-nav-badge {
  position: absolute;
  top: 5px;
  right: calc(50% - 15px);
  background: #ef4444;
  color: #ffffff;
  font-size: 9px;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 10px;
  line-height: 1.2;
}

@media (max-width: 991.98px) {
  .kaiadmin-wrapper .sidebar {
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    bottom: 0 !important;
    width: 270px !important;
    max-width: 85vw !important;
    height: 100vh !important;
    z-index: 1050 !important;
    transform: translate3d(-270px, 0, 0) !important;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: none !important;
  }

  .kaiadmin-wrapper.nav_open .sidebar {
    transform: translate3d(0, 0, 0) !important;
    box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5) !important;
  }

  .kaiadmin-wrapper .main-panel {
    margin-left: 0 !important;
    width: 100% !important;
    min-width: 100% !important;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
  }

  .kaiadmin-wrapper.nav_open .main-panel {
    transform: translate3d(270px, 0, 0) !important;
  }

  .kaiadmin-wrapper .main-header {
    left: 0 !important;
    width: 100% !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 1020 !important;
    min-height: 60px !important;
  }

  .main-header-logo {
    display: block !important;
    width: 100% !important;
    background: #0F172A !important;
  }

  .main-header-logo .logo-header {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    height: 65px !important;
    padding: 0 12px !important;
    background: #0F172A !important;
    position: relative !important;
    gap: 8px !important;
  }

  .main-header-logo .logo-header .sidenav-toggler,
  .logo-header .sidenav-toggler,
  .logo-header .btn-toggle {
    position: static !important;
    transform: none !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding: 4px 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    order: 1 !important;
    flex-shrink: 0 !important;
  }

  .main-header-logo .logo-header .logo,
  .logo-header .logo {
    position: static !important;
    transform: none !important;
    left: auto !important;
    top: auto !important;
    margin-left: 0 !important;
    margin-right: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    order: 2 !important;
    flex-shrink: 0 !important;
  }

  .main-header-logo .logo-header .topbar-toggler,
  .logo-header .topbar-toggler {
    position: static !important;
    transform: none !important;
    margin-left: auto !important;
    margin-right: 0 !important;
    padding: 4px 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    order: 3 !important;
    flex-shrink: 0 !important;
  }

  .kaiadmin-content-area {
    margin-top: 0 !important;
    padding: 14px 12px 84px 12px !important; /* Bottom padding so bottom nav doesn't cover content */
  }

  .dashboard-header-actions {
    width: 100% !important;
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 8px !important;
  }

  .range-picker-group {
    width: 100% !important;
    justify-content: space-between !important;
  }

  .range-picker-group select {
    min-width: unset !important;
    width: 100% !important;
  }

  .dashboard-header-actions button {
    width: 100% !important;
    justify-content: center !important;
  }

  .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    border-radius: 8px !important;
  }

  .kaiadmin-bar-chart-container {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    padding-bottom: 10px !important;
  }
}

@media (max-width: 767.98px) {
  /* KPI 2x2 Grid compact mobile styles */
  .card-stats .card-body {
    padding: 10px 8px !important;
  }

  .card-stats .icon-big {
    width: 36px !important;
    height: 36px !important;
    font-size: 15px !important;
    line-height: 36px !important;
    border-radius: 8px !important;
  }

  .card-stats .numbers .card-category {
    font-size: 9.5px !important;
    letter-spacing: 0.2px !important;
    margin-bottom: 2px !important;
  }

  .card-stats .numbers .card-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    margin-bottom: 0 !important;
    line-height: 1.2 !important;
  }

  /* Table styling on mobile phones */
  .table-responsive > .table {
    min-width: 580px !important;
  }

  .table-responsive > .table td,
  .table-responsive > .table th {
    padding: 0.6rem 0.5rem !important;
    font-size: 12px !important;
  }

  /* Mobile Bottom Sheet Modal feel */
  .kaiadmin-modal-backdrop {
    padding: 0 !important;
    align-items: flex-end !important;
  }

  .kaiadmin-modal-dialog {
    max-width: 100% !important;
    max-height: 90vh !important;
    border-radius: 20px 20px 0 0 !important;
    margin-bottom: 0 !important;
    border-bottom: 0 !important;
  }

  .kaiadmin-modal-dialog .card-body {
    padding: 16px !important;
  }
}

/* ========================================================= */
/* FIX OVERFLOW, LOGO STYLING & CLEAN PRELOADER              */
/* ========================================================= */

/* 1. Fullscreen Initial Splash Loader */
.admin-initial-loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: #0F172A;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999999;
}

.loader-logo-card {
  width: 76px;
  height: 76px;
  background: #ffffff;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 25px rgba(21, 114, 232, 0.35);
  animation: pulseLogo 1.8s ease-in-out infinite;
}

.loader-logo-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.loader-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid rgba(255, 255, 255, 0.15);
  border-top-color: #1572E8;
  border-radius: 50%;
  animation: spinLoader 0.75s linear infinite;
}

@keyframes spinLoader {
  to { transform: rotate(360deg); }
}

@keyframes pulseLogo {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.06); }
}

.fade-loader-enter-active,
.fade-loader-leave-active {
  transition: opacity 0.35s ease;
}
.fade-loader-enter-from,
.fade-loader-leave-to {
  opacity: 0;
}

/* 1.5 Custom Ultra-Thin & Sleek Scrollbar for Admin Sidebar */
.sidebar-wrapper {
  scrollbar-width: thin !important;
  scrollbar-color: rgba(255, 255, 255, 0.25) transparent !important;
}

.sidebar-wrapper::-webkit-scrollbar,
.sidebar-wrapper *::-webkit-scrollbar,
.sidebar::-webkit-scrollbar,
.sidebar *::-webkit-scrollbar {
  width: 4px !important;
  height: 4px !important;
}

.sidebar-wrapper::-webkit-scrollbar-track,
.sidebar-wrapper *::-webkit-scrollbar-track,
.sidebar::-webkit-scrollbar-track,
.sidebar *::-webkit-scrollbar-track {
  background: transparent !important;
}

.sidebar-wrapper::-webkit-scrollbar-thumb,
.sidebar-wrapper *::-webkit-scrollbar-thumb,
.sidebar::-webkit-scrollbar-thumb,
.sidebar *::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.22) !important;
  border-radius: 10px !important;
}

.sidebar-wrapper::-webkit-scrollbar-thumb:hover,
.sidebar-wrapper *::-webkit-scrollbar-thumb:hover,
.sidebar::-webkit-scrollbar-thumb:hover,
.sidebar *::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.45) !important;
}

/* Kaiadmin jquery-scrollbar plugin element support */
.sidebar-wrapper .scroll-element.scroll-y,
.sidebar .scroll-element.scroll-y {
  width: 4px !important;
  right: 2px !important;
}

.sidebar-wrapper .scroll-element .scroll-bar,
.sidebar .scroll-element .scroll-bar {
  background-color: rgba(255, 255, 255, 0.22) !important;
  border-radius: 10px !important;
}

.sidebar-wrapper .scroll-element:hover .scroll-bar,
.sidebar .scroll-element:hover .scroll-bar {
  background-color: rgba(255, 255, 255, 0.45) !important;
}

/* 2. Official Emblem Logo Cards in Sidebar & Header */
.sidebar .logo-header,
.main-header .logo-header {
  height: 70px !important;
  line-height: normal !important;
  display: flex !important;
  align-items: center !important;
  padding: 0 16px !important;
  position: relative !important;
  overflow: hidden !important;
}

.sidebar .logo-header .logo,
.main-header .logo-header .logo {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  justify-content: flex-start !important;
  height: auto !important;
  line-height: 1 !important;
  text-decoration: none !important;
  padding: 0 !important;
  margin: 0 !important;
  max-width: calc(100% - 40px) !important;
}

.sidebar-logo-card {
  width: 36px !important;
  height: 36px !important;
  min-width: 36px !important;
  background: #ffffff !important;
  border-radius: 9px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 4px !important;
  flex-shrink: 0 !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
}

.sidebar-logo-img {
  width: 24px !important;
  height: 24px !important;
  object-fit: contain !important;
  display: block !important;
}

.sidebar-brand-text-wrap {
  display: flex !important;
  flex-direction: column !important;
  justify-content: center !important;
  line-height: 1 !important;
  margin-left: 10px !important;
  overflow: hidden !important;
}

.brand-title {
  color: #ffffff !important;
  font-size: 13.5px !important;
  font-weight: 800 !important;
  letter-spacing: 0.6px !important;
  line-height: 1.2 !important;
  margin: 0 !important;
  padding: 0 !important;
  white-space: nowrap !important;
  display: block !important;
}

.sidebar-sub-badge {
  color: #8d9498 !important;
  font-size: 8.5px !important;
  font-weight: 700 !important;
  letter-spacing: 1.2px !important;
  line-height: 1.2 !important;
  margin-top: 3px !important;
  margin-bottom: 0 !important;
  padding: 0 !important;
  white-space: nowrap !important;
  display: block !important;
}

.sidebar .logo-header .nav-toggle,
.main-header .logo-header .nav-toggle {
  position: absolute !important;
  right: 14px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  height: auto !important;
  line-height: 1 !important;
  z-index: 10 !important;
}

/* 3. Non-Overflowing Sidebar Nav Items & Badges */
.sidebar.sidebar-style-2 .nav .nav-item {
  padding: 0 14px !important;
}

.sidebar.sidebar-style-2 .nav .nav-item a {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  padding: 8px 12px !important;
  border-radius: 10px !important;
  position: relative !important;
  overflow: visible !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.sidebar.sidebar-style-2 .nav .nav-item a p {
  margin: 0 !important;
  font-size: 13.5px !important;
  font-weight: 500 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
}

.sidebar.sidebar-style-2 .nav .nav-item a .sidebar-badge {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  line-height: 1 !important;
  padding: 3px 8px !important;
  border-radius: 12px !important;
  margin-left: auto !important;
  margin-right: 0 !important;
  flex-shrink: 0 !important;
  white-space: nowrap !important;
  box-sizing: border-box !important;
  position: static !important;
}

.sidebar-badge.badge-live {
  background: #22c55e !important;
  color: #ffffff !important;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.45) !important;
  padding: 3px 9px !important;
  letter-spacing: 0.5px !important;
}

.sidebar-badge.badge-num-primary {
  background: #1572E8 !important;
  color: #ffffff !important;
}

.sidebar-badge.badge-num-info {
  background: #48ABF7 !important;
  color: #ffffff !important;
}

.sidebar-badge.badge-num-secondary {
  background: #6861CE !important;
  color: #ffffff !important;
}

.sidebar-badge.badge-num-light {
  background: #f1f5f9 !important;
  color: #1e293b !important;
}

.sidebar-badge.badge-num-success {
  background: #31CE36 !important;
  color: #ffffff !important;
}

.sidebar-badge.badge-num-warning {
  background: #FFAD46 !important;
  color: #ffffff !important;
}

.sidebar-badge.badge-num-danger {
  background: #F25961 !important;
  color: #ffffff !important;
}

/* 4. Chart Badge & Header Controls Overflow Fix */
.annual-trend-pill {
  display: inline-flex !important;
  align-items: center !important;
  padding: 4px 12px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #ffffff !important;
  background: #1572E8 !important;
  border-radius: 20px !important;
  white-space: nowrap !important;
  box-shadow: 0 2px 6px rgba(21, 114, 232, 0.3) !important;
  width: auto !important;
  overflow: visible !important;
  flex-shrink: 0 !important;
}

.range-picker-group {
  transition: all 0.2s ease;
}

.range-picker-group:hover {
  border-color: #1572E8 !important;
  box-shadow: 0 2px 8px rgba(21, 114, 232, 0.15) !important;
}

/* 5. General Table & Card Badges Normalization */
.table .badge,
.card .table .badge,
.dropdown-menu .badge {
  margin-left: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  white-space: nowrap !important;
  vertical-align: middle !important;
}
</style>
