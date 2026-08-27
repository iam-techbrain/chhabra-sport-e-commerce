<template>
  <div>
    <!-- ANNOUNCEMENT TOPBAR (ORIGINAL TEMPLATE DESIGN) -->
    <div class="announce">
      FREE PAN-INDIA EXPRESS DELIVERY ON ORDERS OVER <strong>₹2,999</strong> <span>/</span> 100% ORIGINAL WARRANTY
      <span>/</span> HELPLINE: <strong>+91-72772-52440</strong>
    </div>

    <!-- SITE HEADER (ORIGINAL PITCH-DARK TEMPLATE DESIGN) -->
    <header class="site">
      <div class="header-inner">
        <button class="hamburger icon-btn hide-desktop" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
          <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
          </svg>
        </button>

        <!-- OFFICIAL LOGO IMAGE -->
        <a href="#" class="logo-link" @click.prevent="$emit('navigate', 'home')">
          <img
            src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg"
            alt="Chhabra Sports Official Logo" class="logo-img">
        </a>

        <!-- PRIMARY NAVIGATION -->
        <nav class="primary">
          <div class="nav-item">
            <a href="#" :class="{ active: currentTab === 'home' }" @click.prevent="$emit('navigate', 'home')">Home</a>
          </div>

          <div class="nav-item">
            <a href="#products" :class="{ active: currentTab === 'shop' }" @click.prevent="$emit('navigate', 'shop')">Shop</a>
          </div>

          <div class="nav-item">
            <a href="#" :class="{ active: currentTab === 'about' }" @click.prevent="$emit('navigate', 'about')">About Us</a>
          </div>

          <div class="nav-item">
            <a href="#" :class="{ active: currentTab === 'contact' }" @click.prevent="$emit('navigate', 'contact')">Contact Us</a>
          </div>
        </nav>

        <!-- HEADER ACTIONS WITH ADMIN PANEL, MY ORDERS & LOGIN LINKS -->
        <div class="header-actions">

          <a href="#" class="cart-chip hide-mobile" @click.prevent="$emit('navigate', user ? 'account' : 'auth')" style="text-decoration: none;">
            <span v-if="user">👤 {{ user.name }}</span>
            <span v-else>👤 LOGIN / MY ORDERS</span>
          </a>

          <button class="icon-btn" aria-label="Search" @click="$emit('open-search')">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
              <circle cx="11" cy="11" r="7" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
          </button>

          <button class="icon-btn hide-mobile" aria-label="Wishlist" @click="$emit('filter-wishlist')">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
              <path
                d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
            </svg>
            <span class="badge" v-if="wishlistCount > 0">{{ wishlistCount }}</span>
          </button>

          <button class="cart-chip" @click="$emit('open-cart')">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="2">
              <circle cx="9" cy="21" r="1" />
              <circle cx="20" cy="21" r="1" />
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
            </svg>
            <span><!-- Cart --></span> (<span>{{ cartCount }}</span>)
          </button>
        </div>
      </div>
    </header>

    <!-- MOBILE NAVIGATION DRAWER -->
    <div class="mobile-drawer" :class="{ open: mobileMenuOpen }">
      <div class="mobile-scrim" @click="mobileMenuOpen = false"></div>
      <div class="mobile-panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <img
              src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg"
              style="height:36px; border-radius:4px;">
          </div>
          <button class="icon-btn" @click="mobileMenuOpen = false">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="black" fill="none" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
        <a class="m-link" href="#" @click="$emit('navigate', 'home'); mobileMenuOpen = false">Home <span>›</span></a>
        <a class="m-link" href="#products" @click="$emit('navigate', 'shop'); mobileMenuOpen = false">Shop Catalog <span>›</span></a>
        <a class="m-link" href="#" @click="$emit('navigate', 'about'); mobileMenuOpen = false">About Us <span>›</span></a>
        <a class="m-link" href="#" @click="$emit('navigate', 'contact'); mobileMenuOpen = false">Contact Us <span>›</span></a>
        <a class="m-link" href="#" @click="$emit('navigate', 'admin'); mobileMenuOpen = false">⚙️ Admin Control Panel <span>›</span></a>
        <a class="m-link" href="#" v-if="user" @click="$emit('navigate', 'account'); mobileMenuOpen = false">👤 {{ user.name }} (My Account) <span>›</span></a>
        <a class="m-link" href="#" v-else @click="$emit('navigate', 'auth'); mobileMenuOpen = false">👤 Login / Register <span>›</span></a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  cartCount: { type: Number, default: 0 },
  wishlistCount: { type: Number, default: 0 },
  currentTab: { type: String, default: 'home' },
  user: { type: Object, default: null }
});

defineEmits(['open-cart', 'open-search', 'filter-cat', 'filter-brand', 'filter-wishlist', 'navigate']);

const mobileMenuOpen = ref(false);
</script>

<style scoped>
.btn-header-admin {
  font-family: 'Outfit', sans-serif;
  font-size: 12px;
  font-weight: 800;
  color: #38BDF8;
  text-decoration: none;
  letter-spacing: 0.5px;
  padding: 8px 14px;
  border-radius: 8px;
  background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
  border: 1px solid #38BDF8;
  box-shadow: 0 4px 10px rgba(56, 189, 248, 0.15);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-header-admin:hover {
  background: #38BDF8;
  color: #0F172A;
  box-shadow: 0 6px 16px rgba(56, 189, 248, 0.4);
  transform: translateY(-1px);
}

.btn-header-orders {
  font-family: 'Outfit', sans-serif;
  font-size: 12px;
  font-weight: 800;
  color: #FFFFFF;
  text-decoration: none;
  letter-spacing: 0.5px;
  padding: 8px 14px;
  border-radius: 8px;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border: 1px solid #10B981;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-header-orders:hover {
  background: #10B981;
  color: #FFFFFF;
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.45);
  transform: translateY(-1px);
}

.btn-header-auth {
  font-family: 'Outfit', sans-serif;
  font-size: 12px;
  font-weight: 800;
  color: #0F172A;
  text-decoration: none;
  letter-spacing: 0.5px;
  padding: 8px 16px;
  border-radius: 8px;
  background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
  border: 1px solid #F59E0B;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.35);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-header-auth:hover {
  background: #F59E0B;
  color: #0F172A;
  box-shadow: 0 6px 18px rgba(245, 158, 11, 0.5);
  transform: translateY(-1px);
}
</style>
