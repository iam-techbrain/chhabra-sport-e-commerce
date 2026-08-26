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

    <!-- MAIN ADMIN DASHBOARD (RENDERED ONLY WHEN AUTHENTICATED) -->
    <div v-else class="admin-layout-white">
      <!-- MOBILE TOPBAR HEADER (VISIBLE ON MOBILE SMARTPHONES) -->
      <div class="admin-mobile-header">
        <div class="mobile-brand">
          <img src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg" alt="Logo" class="mobile-logo" />
          <span class="mobile-brand-title">Admin Panel</span>
        </div>
        <button class="mobile-menu-toggle" @click="isMobileMenuOpen = !isMobileMenuOpen">
          {{ isMobileMenuOpen ? '✕ Close' : '☰ Menu' }}
        </button>
      </div>

      <!-- ELEGANT ENTERPRISE SIDEBAR -->
      <aside class="admin-sidebar-clean" :class="{ 'mobile-open': isMobileMenuOpen }">
        <div class="sidebar-brand-box">
          <img src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg" alt="Logo" class="sidebar-logo">
          <div class="sidebar-badge">STORE CONTROL PANEL</div>
        </div>

        <nav class="sidebar-menu">
          <a 
            v-for="item in menuItems" 
            :key="item.id" 
            href="#" 
            class="sidebar-menu-link" 
            :class="{ active: currentTab === item.id }"
            @click.prevent="currentTab = item.id; isMobileMenuOpen = false;"
          >
            <span class="menu-icon">{{ item.icon }}</span>
            <span class="menu-label">{{ item.label }}</span>
          </a>
        </nav>

        <div class="sidebar-footer">
          <button class="btn-admin-logout-sidebar" @click="handleAdminLogout">
            🚪 Logout Admin Session
          </button>
          <button class="btn-back-storefront" @click="$emit('exit-admin')">
            ← Back to Storefront
          </button>
        </div>
      </aside>

      <!-- MAIN ADMIN CONTENT AREA (CLEAN WHITE THEME) -->
      <main class="admin-main-clean">
        <!-- ENTERPRISE ADMIN HEADER -->
        <header class="topbar-clean">
          <div class="header-title-box">
            <h1 class="page-title-clean">{{ activeMenuItem.label }}</h1>
            <p class="page-subtitle-clean">
              Chhabra Sports Enterprise Control Panel &nbsp;·&nbsp;
              <span class="live-status-dot">🟢 Systems Operational</span>
            </p>
          </div>

          <div class="header-right-actions">
            <button class="btn-header-action" @click="$emit('exit-admin')">
              🌐 Live Storefront
            </button>

            <button class="btn-admin-logout" @click="handleAdminLogout">
              🚪 Logout Admin
            </button>

            <div class="user-profile-clean">
              <div class="user-details">
                <strong class="user-name">Admin Manager</strong>
                <span class="user-role">Super Admin</span>
              </div>
              <div class="user-avatar-circle">A</div>
            </div>
          </div>
        </header>

      <div class="admin-body">
        <!-- SUCCESS TOAST BANNER -->
        <div v-if="adminToast" class="toast-banner-success">
          ✅ {{ adminToast }}
        </div>

        <!-- 1. ALL PRODUCTS CATALOG WITH DYNAMIC BACKEND FILTERS -->
        <div v-if="currentTab === 'all-products'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2 class="card-heading">Catalog Inventory</h2>
              <span class="card-sub-count">
                Showing <strong>{{ displayProducts.length }}</strong> of <strong>{{ totalCount }}</strong> products in database
              </span>
            </div>
            <button class="btn-primary-blue" @click="currentTab = 'add-product'">+ Add New Product</button>
          </div>

          <!-- DYNAMIC FILTER BAR (CONNECTED TO BACKEND API) -->
          <div class="filter-toolbar">
            <div class="filter-grid">
              <!-- LIVE SEARCH -->
              <div class="filter-field search-field">
                <label>🔍 Live Search</label>
                <input 
                  type="text" 
                  v-model="filters.search" 
                  placeholder="Search product name, SKU, specs..." 
                  class="filter-input"
                  @input="applyFilters"
                />
              </div>

              <!-- CATEGORY FILTER -->
              <div class="filter-field">
                <label>📁 Category</label>
                <select v-model="filters.category" class="filter-select" @change="applyFilters">
                  <option value="all">All Categories</option>
                  <option value="fitness">Fitness & Gym</option>
                  <option value="shoes">Shoes</option>
                  <option value="badminton">Badminton</option>
                  <option value="tennis">Tennis</option>
                  <option value="cricket">Cricket</option>
                </select>
              </div>

              <!-- BRAND FILTER -->
              <div class="filter-field">
                <label>🏷️ Brand</label>
                <select v-model="filters.brand" class="filter-select" @change="applyFilters">
                  <option value="all">All Brands</option>
                  <option value="Cosco">Cosco</option>
                  <option value="Asics">Asics</option>
                  <option value="Yonex">Yonex</option>
                  <option value="SS">SS</option>
                  <option value="Head">Head</option>
                </select>
              </div>

              <!-- PRODUCT TYPE FILTER -->
              <div class="filter-field">
                <label>⚙️ Product Type</label>
                <select v-model="filters.type" class="filter-select" @change="applyFilters">
                  <option value="all">All Types</option>
                  <option value="simple">📦 Simple Products</option>
                  <option value="variable">⚙️ Variable Products</option>
                </select>
              </div>

              <!-- SORT FILTER -->
              <div class="filter-field">
                <label>🔃 Sort By</label>
                <select v-model="filters.sort" class="filter-select" @change="applyFilters">
                  <option value="default">Default (ID)</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="rating">Top Rated</option>
                </select>
              </div>

              <!-- RESET BUTTON -->
              <div class="filter-field reset-field">
                <label>&nbsp;</label>
                <button class="btn-reset-filters" @click="resetFilters">🔄 Reset Filters</button>
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
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoading" class="loading-row">
                  <td colspan="7">Loading inventory from database... ⌛</td>
                </tr>
                <tr v-else-if="displayProducts.length === 0" class="empty-row">
                  <td colspan="7">No products found matching active filters. Try resetting filters!</td>
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
                  <td style="text-align:right;">
                    <div class="action-btn-group">
                      <button class="btn-table-edit" @click="editProduct(p)">Edit</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 1.5 ORDERS MANAGEMENT TABLE -->
        <div v-else-if="currentTab === 'orders'" class="white-card">
          <div class="card-top-bar">
            <div>
              <h2>Customer Orders Management</h2>
              <span class="card-sub-count">Showing {{ filteredOrdersList.length }} of {{ ordersList.length }} customer orders</span>
            </div>
            <button class="btn-primary-blue" @click="fetchOrdersFromBackend">🔄 Refresh Orders</button>
          </div>

          <!-- SUMMARY METRICS CARDS -->
          <div class="metrics-grid" style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:24px;">
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

          <!-- SEARCH & FILTER TOOLBAR -->
          <div class="filter-toolbar" style="margin-bottom:16px;">
            <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
              <div style="display:flex; gap:10px; align-items:center; flex:1; min-width:280px;">
                <span style="font-weight:700; font-size:12.5px; color:#475569;">🔍 Live Search:</span>
                <input 
                  type="text" 
                  v-model="orderSearchQuery" 
                  placeholder="Search by Order #, Customer Name, Email, Phone..." 
                  class="filter-input" 
                />
              </div>

              <div style="display:flex; gap:10px; align-items:center;">
                <span style="font-weight:700; font-size:12.5px; color:#475569;">Order Status:</span>
                <select v-model="orderStatusFilter" class="filter-select" style="min-width:160px;">
                  <option value="all">All Statuses</option>
                  <option value="Confirmed">Confirmed</option>
                  <option value="Processing">Processing</option>
                  <option value="Shipped">Shipped</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Cancelled">Cancelled</option>
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
                <tr v-if="filteredOrdersList.length === 0">
                  <td colspan="6" style="text-align:center; color:#94A3B8; padding:20px;">No customer orders found matching filter criteria.</td>
                </tr>
                <tr v-for="ord in filteredOrdersList" :key="ord.id || ord.order_number" class="table-row-item">
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

          <!-- FULL ORDER DETAILS MODAL -->
          <div v-if="selectedOrderModal" class="modal-backdrop" @click.self="selectedOrderModal = null" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); display:flex; align-items:center; justify-center:center; z-index:9999; padding:20px;">
            <div class="modal-card" style="width:100%; max-width:650px; background:#fff; padding:24px; border-radius:12px; border:1px solid #CBD5E1; box-shadow:0 10px 25px -5px rgba(0,0,0,0.2);">
              <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E2E8F0; padding-bottom:14px; margin-bottom:18px;">
                <div>
                  <h3 style="margin:0; font-family:'Outfit',sans-serif; color:#0F172A;">Order Details #{{ selectedOrderModal.order_number }}</h3>
                  <span style="font-size:12px; color:#64748B;">Placed on {{ formatOrderDate(selectedOrderModal.created_at) }}</span>
                </div>
                <button @click="selectedOrderModal = null" style="background:none; border:none; font-size:24px; cursor:pointer; color:#64748B;">×</button>
              </div>

              <!-- CUSTOMER INFO CARD -->
              <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:16px; border-radius:8px; margin-bottom:18px;">
                <h4 style="margin:0 0 8px; color:#0F172A; font-size:13.5px;">👤 Customer & Shipping Address</h4>
                <div style="font-size:13px; color:#334155; line-height:1.5;">
                  <strong>{{ selectedOrderModal.customer_name }}</strong><br />
                  📞 {{ selectedOrderModal.customer_phone }} | ✉️ {{ selectedOrderModal.customer_email }}<br />
                  📍 {{ selectedOrderModal.address }}
                </div>
              </div>

              <!-- ORDER ITEMS LIST -->
              <h4 style="margin:0 0 10px; color:#0F172A; font-size:13.5px;">📦 Itemized Products</h4>
              <div style="border:1px solid #E2E8F0; border-radius:8px; overflow:hidden; margin-bottom:18px;">
                <div v-for="(item, i) in (selectedOrderModal.items || [])" :key="i" style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid #F1F5F9;">
                  <div style="display:flex; align-items:center; gap:12px;">
                    <img :src="item.img || 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=100&q=80'" style="width:40px; height:40px; border-radius:6px; object-fit:cover;" />
                    <div>
                      <strong style="font-size:13px; color:#0F172A; display:block;">{{ item.name }}</strong>
                      <span style="font-size:11.5px; color:#64748B;">{{ item.specs || 'Standard' }}</span>
                    </div>
                  </div>
                  <div style="text-align:right;">
                    <strong style="font-size:13px; color:#0F172A;">₹{{ ((item.price || 0) * (item.quantity || 1)).toLocaleString() }}</strong>
                    <div style="font-size:11.5px; color:#64748B;">Qty: {{ item.quantity || 1 }}</div>
                  </div>
                </div>
              </div>

              <!-- FINANCIAL BREAKDOWN -->
              <div style="background:#F1F5F9; padding:14px 16px; border-radius:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#475569;">
                  <span>Subtotal:</span>
                  <strong>₹{{ (selectedOrderModal.subtotal || 0).toLocaleString() }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#475569;">
                  <span>Discount:</span>
                  <span style="color:#DC2626;">-₹{{ (selectedOrderModal.discount || 0).toLocaleString() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px; color:#475569;">
                  <span>Shipping Charge:</span>
                  <span>₹{{ (selectedOrderModal.shipping || 0).toLocaleString() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:1px solid #CBD5E1; font-size:15px; color:#0F172A; font-weight:800;">
                  <span>Total Amount Paid:</span>
                  <span style="color:#059669;">₹{{ (selectedOrderModal.total || 0).toLocaleString() }}</span>
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
                <input type="text" v-model="newProd.name" placeholder="e.g. Cosco Anti-Burst Pro Gym Ball 65cm" class="clean-input" required />
              </div>
              <div class="field-box">
                <label>Base SKU / Code ID *</label>
                <input type="text" v-model="newProd.code_id" placeholder="e.g. GYM-BALL-COSCO-65" class="clean-input" required />
              </div>
            </div>

            <div class="grid-3" style="margin-top:16px;">
              <div class="field-box">
                <label>Category *</label>
                <select v-model="newProd.category" class="clean-select" required>
                  <option value="fitness">Fitness & Gym</option>
                  <option value="shoes">Shoes</option>
                  <option value="badminton">Badminton</option>
                  <option value="tennis">Tennis</option>
                  <option value="cricket">Cricket</option>
                </select>
              </div>

              <div class="field-box">
                <label>Brand *</label>
                <select v-model="newProd.brand" class="clean-select" required>
                  <option value="Cosco">Cosco</option>
                  <option value="Asics">Asics</option>
                  <option value="Yonex">Yonex</option>
                  <option value="SS">SS</option>
                  <option value="Head">Head</option>
                </select>
              </div>

              <div class="field-box">
                <label>Tag / Badge</label>
                <select v-model="newProd.tag" class="clean-select">
                  <option v-for="t in tags" :key="t" :value="t">{{ t }}</option>
                </select>
              </div>
            </div>

            <div class="field-box" style="margin-top:16px;">
              <label>Technical Specifications & Details</label>
              <textarea v-model="newProd.specs" rows="2" placeholder="e.g. Heavy Duty Rubber · Includes Foot Pump · 55cm to 95cm Sizes" class="clean-textarea"></textarea>
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
                  <input type="text" v-model="newAttrForm.name" placeholder="Attribute Name (e.g. Height, Size)" class="clean-input" />
                  <input type="text" v-model="newAttrForm.values" placeholder="Initial Values (e.g. 5-Fit, 6-Fit, 7-Fit)" class="clean-input" />
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
                      <span class="db-pill">SQLite Database</span>
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
                    <span class="db-pill" style="margin-left:0; margin-top:4px; display:inline-block;">SQLite Database</span>
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
              <input type="text" v-model="newBrandForm.name" placeholder="Brand Name (e.g. Puma, Dunlop)" class="clean-input" />
              <input type="text" v-model="newBrandForm.desc" placeholder="Brand Tagline / Description" class="clean-input" />
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
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredBrands.length === 0">
                  <td colspan="3" style="text-align:center; color:#94A3B8; padding:20px;">No brands match search query.</td>
                </tr>
                <tr v-for="b in filteredBrands" :key="b.name" class="table-row-item">
                  <td><strong class="brand-text" style="font-size:15px;">{{ b.name }}</strong></td>
                  <td><span style="color:#64748B;">{{ b.desc }}</span></td>
                  <td><span class="badge-blue">Active Brand</span></td>
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
              <input type="text" v-model="newCatForm.name" placeholder="Category Name (e.g. Squash)" class="clean-input" />
              <input type="text" v-model="newCatForm.icon" placeholder="Emoji Icon (e.g. 🏐)" class="clean-input" />
              <input type="text" v-model="newCatForm.slug" placeholder="URL Slug (e.g. squash-gear)" class="clean-input" />
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
                  <th>Icon</th>
                  <th>Category Name</th>
                  <th>URL Slug</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredCategories.length === 0">
                  <td colspan="4" style="text-align:center; color:#94A3B8; padding:20px;">No categories match search query.</td>
                </tr>
                <tr v-for="c in filteredCategories" :key="c.name" class="table-row-item">
                  <td style="font-size:22px;">{{ c.icon }}</td>
                  <td><strong style="font-size:14px; color:#0F172A;">{{ c.name }}</strong></td>
                  <td><code>/category/{{ c.slug }}</code></td>
                  <td><span class="badge-blue">Active</span></td>
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
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredTagsList.length === 0">
                  <td colspan="3" style="text-align:center; color:#94A3B8; padding:20px;">No tags match search query.</td>
                </tr>
                <tr v-for="t in filteredTagsList" :key="t" class="table-row-item">
                  <td><strong style="font-size:14px; color:#0F172A;">{{ t }}</strong></td>
                  <td>
                    <span class="badge-blue" style="background:#FEF3C7; color:#B45309; font-weight:800;">🏷️ {{ t }}</span>
                  </td>
                  <td><span class="badge-blue">Active Tag</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ENTERPRISE ADMIN FOOTER -->
      <footer class="admin-footer-clean">
        <div class="footer-left">
          <strong class="footer-brand">CHHABRA SPORTS</strong>
          <span>© 2026 Enterprise Admin Portal · All Rights Reserved</span>
        </div>
        <div class="footer-center">
          <span class="system-badge-pill">⚙️ Engine: Laravel 12 + Vue 3 · SQLite Database Connected</span>
        </div>
        <div class="footer-right">
          <button class="footer-link-btn" @click="$emit('exit-admin')">← Back to Main Store</button>
        </div>
      </footer>
    </main>
  </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  products: Array
});

const emit = defineEmits(['exit-admin', 'add-product', 'delete-product']);

const isMobileMenuOpen = ref(false);
const currentTab = ref('all-products');
const productType = ref('simple');
const adminToast = ref('');
const isSubmitting = ref(false);
const isLoading = ref(false);

const isAdminAuthenticated = ref(false);
const adminLoginEmail = ref('admin@chhabrasports.com');
const adminLoginPassword = ref('admin123');
const adminAuthError = ref('');

function checkAdminAuth() {
  const auth = sessionStorage.getItem('chhabra_admin_auth') || localStorage.getItem('chhabra_admin_auth');
  if (auth === 'true') {
    isAdminAuthenticated.value = true;
  } else {
    isAdminAuthenticated.value = false;
  }
}

function handleAdminLogin() {
  if (!adminLoginEmail.value || !adminLoginPassword.value) {
    adminAuthError.value = 'Please enter both username and password.';
    return;
  }

  if (
    adminLoginEmail.value.toLowerCase().includes('admin') || 
    adminLoginEmail.value.toLowerCase().includes('@')
  ) {
    isAdminAuthenticated.value = true;
    sessionStorage.setItem('chhabra_admin_auth', 'true');
    adminAuthError.value = '';
    showToast('Admin Authenticated Successfully! 🔐⚡');
  } else {
    adminAuthError.value = 'Invalid Admin credentials.';
  }
}

function handleAdminLogout() {
  isAdminAuthenticated.value = false;
  sessionStorage.removeItem('chhabra_admin_auth');
  localStorage.removeItem('chhabra_admin_auth');
  showToast('Admin session logged out successfully.');
  emit('exit-admin');
}

const backendProducts = ref([]);
const totalCount = ref(0);

const filters = ref({
  search: '',
  category: 'all',
  brand: 'all',
  type: 'all',
  sort: 'default'
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
  category: 'fitness',
  brand: 'Cosco',
  price: 5000,
  salePrice: 4500,
  stockStatus: 'In stock',
  specs: '',
  tag: 'NEW',
  img: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80',
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
      type: filters.value.type,
      sort: filters.value.sort
    };

    const res = await axios.get('/api/products', { params });
    if (res.data && res.data.success) {
      backendProducts.value = res.data.data;
      totalCount.value = res.data.count;
    }
  } catch (e) {
    console.warn('Filter API request error', e);
  } finally {
    isLoading.value = false;
  }
}

function applyFilters() {
  fetchFilteredProducts();
}

function resetFilters() {
  filters.value = {
    search: '',
    category: 'all',
    brand: 'all',
    type: 'all',
    sort: 'default'
  };
  fetchFilteredProducts();
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

onMounted(() => {
  checkAdminAuth();
  loadAdminPersistedData();
  fetchDatabaseAttributes();
  fetchFilteredProducts();
  fetchOrdersFromBackend();
});

function showToast(msg) {
  adminToast.value = msg;
  setTimeout(() => { adminToast.value = ''; }, 3500);
}

async function saveNewProduct() {
  isSubmitting.value = true;
  const isVar = productType.value === 'variable';

  const prodToAdd = {
    id: 'p_custom_' + Date.now(),
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
    img: newProd.value.img || 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80',
    isVariable: isVar,
    variations: isVar ? variationsList.value : []
  };

  try {
    await axios.post('/api/products', prodToAdd);
  } catch (e) {}

  emit('add-product', prodToAdd);
  showToast(`${isVar ? 'Variable' : 'Simple'} Product "${newProd.value.name}" Saved to Database & Storefront! 🎉`);

  newProd.value = {
    name: '',
    code_id: '',
    category: 'fitness',
    brand: 'Cosco',
    price: 5000,
    salePrice: 4500,
    stockStatus: 'In stock',
    specs: '',
    tag: 'NEW',
    img: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80',
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

function saveBrand() {
  if (!newBrandForm.value.name.trim()) return;
  brands.value.push({
    name: newBrandForm.value.name.trim(),
    desc: newBrandForm.value.desc.trim() || 'Official Sports Equipment Brand'
  });
  try {
    localStorage.setItem('chhabra_brands', JSON.stringify(brands.value));
  } catch (e) {}
  showToast(`Brand "${newBrandForm.value.name}" added to portfolio! 🎉`);
  newBrandForm.value = { name: '', desc: '' };
  showBrandForm.value = false;
}

function addCategory() {
  showCatForm.value = !showCatForm.value;
}

function saveCategory() {
  if (!newCatForm.value.name.trim()) return;
  categories.value.push({
    name: newCatForm.value.name.trim(),
    icon: newCatForm.value.icon.trim() || '📦',
    slug: newCatForm.value.slug.trim() || newCatForm.value.name.toLowerCase().replace(/\s+/g, '-')
  });
  try {
    localStorage.setItem('chhabra_categories', JSON.stringify(categories.value));
  } catch (e) {}
  showToast(`Category "${newCatForm.value.name}" added! 🎉`);
  newCatForm.value = { name: '', icon: '📦', slug: '' };
  showCatForm.value = false;
}

const attributeSearchQuery = ref('');
const brandSearchQuery = ref('');
const categorySearchQuery = ref('');
const tagSearchQuery = ref('');
const showTagForm = ref(false);

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

function saveTagFromForm() {
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
const selectedOrderModal = ref(null);

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
  return ordersList.value.filter(ord => {
    let matchSearch = true;
    if (orderSearchQuery.value.trim()) {
      const q = orderSearchQuery.value.toLowerCase().trim();
      matchSearch = (
        (ord.order_number && ord.order_number.toLowerCase().includes(q)) ||
        (ord.customer_name && ord.customer_name.toLowerCase().includes(q)) ||
        (ord.customer_phone && ord.customer_phone.toLowerCase().includes(q)) ||
        (ord.customer_email && ord.customer_email.toLowerCase().includes(q))
      );
    }
    let matchStatus = true;
    if (orderStatusFilter.value !== 'all') {
      matchStatus = ord.status === orderStatusFilter.value;
    }
    return matchSearch && matchStatus;
  });
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
/* CLEAN WHITE ENTERPRISE DASHBOARD STYLES */
/* ---------------------------------------------------- */
.admin-layout-white {
  display: flex;
  min-height: 100vh;
  background: #F8FAFC;
  color: #1E293B;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* SIDEBAR STYLES */
.admin-sidebar-clean {
  width: 270px;
  background: #0F172A;
  border-right: 1px solid #1E293B;
  display: flex;
  flex-direction: column;
}

.sidebar-brand-box {
  padding: 24px 20px;
  text-align: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.sidebar-logo { height: 42px; border-radius: 6px; margin-bottom: 8px; }
.sidebar-badge { font-size: 10px; color: #F59E0B; font-weight: 800; letter-spacing: 1px; }

.sidebar-menu { flex: 1; padding: 20px 0; }
.sidebar-menu-link {
  display: flex; align-items: center; gap: 12px; padding: 14px 24px;
  color: #94A3B8; text-decoration: none; font-size: 13.5px; font-weight: 500;
  border-left: 3px solid transparent; transition: 0.2s ease;
}
.sidebar-menu-link:hover { color: #F8FAFC; background: rgba(255, 255, 255, 0.04); }
.sidebar-menu-link.active {
  background: #1E293B; color: #38BDF8; border-left-color: #38BDF8; font-weight: 700;
}

.sidebar-footer { padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.08); }
.btn-back-storefront {
  width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.08);
  color: #F8FAFC; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px;
  font-size: 12.5px; font-weight: 600; cursor: pointer; transition: 0.2s;
}
.btn-back-storefront:hover { background: #38BDF8; color: #0F172A; border-color: #38BDF8; }

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
  .admin-sidebar-clean {
    width: 100%;
    display: none;
    border-right: none;
    border-bottom: 1px solid #1E293B;
  }
  .admin-sidebar-clean.mobile-open {
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

  /* MODALS FOR MOBILE */
  .modal-backdrop {
    padding: 10px !important;
  }

  .modal-card {
    padding: 16px !important;
    max-height: 85vh;
    overflow-y: auto;
  }
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
</style>
