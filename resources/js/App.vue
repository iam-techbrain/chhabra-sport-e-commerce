<template>
  <div id="main-app">
    <!-- Navigation Bar -->
    <Navbar
      v-if="currentTab !== 'admin'"
      :cart-count="cartTotalCount"
      :wishlist-count="wishlist.size"
      :current-tab="currentTab"
      :user="user"
      @open-cart="cartOpen = true"
      @open-search="searchOpen = true"
      @filter-cat="handleFilterCategory"
      @filter-brand="handleFilterBrand"
      @filter-wishlist="handleFilterWishlist"
      @navigate="handleNavigate"
    />

    <!-- PAGE VIEW ROUTER BY TAB -->
    <main class="page-content-area">
      <!-- HOME VIEW -->
      <div v-if="currentTab === 'home'">
        <HeroBanner
          @filter-cat="handleFilterCategory"
          @open-racquet-finder="racquetFinderOpen = true"
        />

        <TrustStrip />

        <CategoryGrid
          @select-category="handleFilterCategory"
        />

        <ProductCatalog
          :products="filteredProducts"
          :current-category="currentCategory"
          :wishlist="wishlist"
          @change-category="handleFilterCategory"
          @add-to-cart="addToCart"
          @open-qv="openQuickView"
          @toggle-wishlist="toggleWishlist"
        />

        <ProServicesSection />

        <BrandStrip
          :brands="availableBrands"
          @filter-brand="handleFilterBrand"
        />
      </div>

      <!-- SHOP VIEW -->
      <div v-else-if="currentTab === 'shop'">
        <ProductCatalog
          :products="filteredProducts"
          :current-category="currentCategory"
          :wishlist="wishlist"
          @change-category="handleFilterCategory"
          @add-to-cart="addToCart"
          @open-qv="openQuickView"
          @toggle-wishlist="toggleWishlist"
        />
      </div>

      <!-- ABOUT US VIEW -->
      <div v-else-if="currentTab === 'about'">
        <AboutUs @nav-shop="handleNavigate('shop')" />
      </div>

      <!-- CONTACT US VIEW -->
      <div v-else-if="currentTab === 'contact'">
        <ContactUs @show-toast="showToast" />
      </div>

      <!-- LOGIN / REGISTER VIEW -->
      <div v-else-if="currentTab === 'auth'">
        <AuthPage 
          @show-toast="showToast" 
          @login-success="handleLoginSuccess" 
          @open-admin="handleNavigate('admin')"
        />
      </div>

      <!-- CUSTOMER ACCOUNT & ORDER TRACKING VIEW -->
      <div v-else-if="currentTab === 'account'">
        <CustomerAccount 
          :user="user" 
          @nav-shop="handleNavigate('shop')" 
          @logout="handleLogout" 
          @show-toast="showToast" 
        />
      </div>

      <!-- ADMIN DASHBOARD VIEW WITH FULL REACTIVE CRUD -->
      <div v-else-if="currentTab === 'admin'">
        <AdminDashboard 
          :products="allProducts" 
          @exit-admin="handleNavigate('home')" 
          @add-product="handleAdminAddProduct"
          @delete-product="handleAdminDeleteProduct"
        />
      </div>
    </main>

    <!-- ADMIN TRIGGER STRIP IN FOOTER -->
    <div v-if="currentTab !== 'admin'" style="background:#0A1B15; text-align:center; padding:10px 0; border-top:1px solid rgba(227,193,116,0.2);">
      <a href="#" @click.prevent="handleNavigate('admin')" style="color:#E3C174; font-family:'Space Mono',monospace; font-size:12px; font-weight:700; text-decoration:none;">
        ⚙️ Open Admin Management Console (All Products, Brands, Categories, Tags, Attributes...)
      </a>
    </div>

    <!-- Footer -->
    <FooterSection
      v-if="currentTab !== 'admin'"
      @filter-cat="handleFilterCategory"
    />

    <!-- Drawers & Modals -->
    <CartDrawer
      :is-open="cartOpen"
      :cart="cart"
      :discount="discount"
      :user="user"
      @close="cartOpen = false"
      @change-qty="changeQty"
      @remove-item="removeFromCart"
      @apply-coupon="applyCoupon"
      @checkout="openCheckout"
    />

    <AuthModal
      :is-open="authModalOpen"
      @close="authModalOpen = false"
      @login-success="handleLoginSuccess"
    />

    <QuickViewModal
      :is-open="quickViewOpen"
      :product="activeProduct"
      @close="quickViewOpen = false"
      @add-to-cart="addToCart"
    />

    <SearchDrawer
      :is-open="searchOpen"
      :products="allProducts"
      @close="searchOpen = false"
      @open-qv="openQuickView"
    />

    <RacquetFinderModal
      :is-open="racquetFinderOpen"
      @close="racquetFinderOpen = false"
      @recommend-cat="handleFilterCategory"
    />

    <CheckoutModal
      :is-open="checkoutOpen"
      :subtotal="cartSubtotal"
      :discount="discount"
      :cart="cart"
      :user="user"
      @close="checkoutOpen = false"
      @order-placed="handleOrderPlaced"
    />

    <OrderSuccessModal
      :is-open="orderSuccessOpen"
      :order="currentOrder"
      @close="orderSuccessOpen = false"
    />

    <ToastNotification :toasts="toasts" />
  </div>
</template>


<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import confetti from 'canvas-confetti';

import Navbar from './components/Navbar.vue';
import HeroBanner from './components/HeroBanner.vue';
import TrustStrip from './components/TrustStrip.vue';
import CategoryGrid from './components/CategoryGrid.vue';
import ProductCatalog from './components/ProductCatalog.vue';
import ProServicesSection from './components/ProServicesSection.vue';
import AuthModal from './components/AuthModal.vue';
import CartDrawer from './components/CartDrawer.vue';
import QuickViewModal from './components/QuickViewModal.vue';
import SearchDrawer from './components/SearchDrawer.vue';
import RacquetFinderModal from './components/RacquetFinderModal.vue';
import CheckoutModal from './components/CheckoutModal.vue';
import OrderSuccessModal from './components/OrderSuccessModal.vue';
import FooterSection from './components/FooterSection.vue';
import ToastNotification from './components/ToastNotification.vue';

import AboutUs from './components/AboutUs.vue';
import ContactUs from './components/ContactUs.vue';
import AuthPage from './components/AuthPage.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import CustomerAccount from './components/CustomerAccount.vue';

const currentTab = ref('home');
const user = ref(null);

const allProducts = ref([]);
const currentCategory = ref('all');
const currentBrand = ref('');
const showOnlyWishlist = ref(false);

const cart = ref([]);
const wishlist = ref(new Set());
const discount = ref(0);

const cartOpen = ref(false);
const authModalOpen = ref(false);
const searchOpen = ref(false);
const quickViewOpen = ref(false);
const racquetFinderOpen = ref(false);
const checkoutOpen = ref(false);
const orderSuccessOpen = ref(false);


const activeProduct = ref(null);
const currentOrder = ref(null);
const toasts = ref([]);

function getItemId(item) {
  if (!item) return '';
  return String(item.code_id || item.id);
}

const initialProducts = [];

function loadPersistedState() {
  try {
    const savedCart = localStorage.getItem('chhabra_cart');
    if (savedCart) cart.value = JSON.parse(savedCart);
  } catch (e) {}

  try {
    const savedWishlist = localStorage.getItem('chhabra_wishlist');
    if (savedWishlist) {
      const parsed = JSON.parse(savedWishlist);
      wishlist.value = new Set(parsed);
    }
  } catch (e) {}

  try {
    const savedProducts = localStorage.getItem('chhabra_products');
    if (savedProducts) {
      allProducts.value = JSON.parse(savedProducts);
    } else {
      allProducts.value = [];
    }
  } catch (e) {
    allProducts.value = [];
  }

  try {
    const savedUser = localStorage.getItem('chhabra_user');
    if (savedUser) user.value = JSON.parse(savedUser);
  } catch (e) {}
}

async function fetchProductsFromBackend() {
  try {
    const res = await axios.get('/api/products?all=true');
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      allProducts.value = res.data.data;
      if (res.data.data.length === 0) {
        localStorage.removeItem('chhabra_products');
      }
    } else {
      allProducts.value = [];
      localStorage.removeItem('chhabra_products');
    }
  } catch (e) {
    allProducts.value = [];
  }
}

watch(allProducts, (newProds) => {
  try { localStorage.setItem('chhabra_products', JSON.stringify(newProds)); } catch (e) {}
}, { deep: true });

watch(cart, (newCart) => {
  try { localStorage.setItem('chhabra_cart', JSON.stringify(newCart)); } catch (e) {}
}, { deep: true });

watch(wishlist, (newWishlist) => {
  try { localStorage.setItem('chhabra_wishlist', JSON.stringify(Array.from(newWishlist))); } catch (e) {}
}, { deep: true });

function syncTabFromUrl() {
  const path = window.location.pathname.replace(/^\/+|\/+$/g, '');
  const validTabs = ['home', 'shop', 'about', 'contact', 'auth', 'admin'];
  if (validTabs.includes(path)) {
    currentTab.value = path;
  } else {
    currentTab.value = 'home';
  }
}

function syncUrlFromTab(tab) {
  const targetPath = tab === 'home' ? '/' : '/' + tab;
  if (window.location.pathname !== targetPath) {
    window.history.pushState({ tab }, '', targetPath);
  }
}

watch(currentTab, (newTab) => {
  syncUrlFromTab(newTab);
});

onMounted(() => {
  loadPersistedState();
  fetchProductsFromBackend();
  syncTabFromUrl();
  window.addEventListener('popstate', () => {
    syncTabFromUrl();
  });
});

const filteredProducts = computed(() => {
  let list = allProducts.value;

  if (showOnlyWishlist.value) {
    return list.filter(p => wishlist.value.has(getItemId(p)));
  }

  if (currentCategory.value !== 'all') {
    list = list.filter(p => p.category === currentCategory.value);
  }

  if (currentBrand.value) {
    list = list.filter(p => p.brand.toLowerCase().includes(currentBrand.value.toLowerCase()));
  }

  return list;
});

const availableBrands = computed(() => {
  if (!allProducts.value || allProducts.value.length === 0) return [];
  const set = new Set();
  allProducts.value.forEach(p => {
    if (p.brand) set.add(p.brand);
  });
  return Array.from(set);
});

const cartTotalCount = computed(() => cart.value.reduce((sum, item) => sum + item.qty, 0));
const cartSubtotal = computed(() => cart.value.reduce((sum, item) => sum + (item.price * item.qty), 0));

function handleNavigate(tab) {
  currentTab.value = tab;
  syncUrlFromTab(tab);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleAdminAddProduct(newProd) {
  allProducts.value.unshift(newProd);
  showToast(`Product "${newProd.name}" is now live in store! 🎉`);
}

function handleAdminDeleteProduct(prodId) {
  const targetId = String(prodId);
  allProducts.value = allProducts.value.filter(p => getItemId(p) !== targetId);
  showToast('Product deleted successfully.');
}

function handleFilterCategory(cat) {
  showOnlyWishlist.value = false;
  currentBrand.value = '';
  currentCategory.value = cat;
  if (currentTab.value !== 'home' && currentTab.value !== 'shop') {
    currentTab.value = 'shop';
  }
  scrollToProducts();
}

function handleFilterBrand(brand) {
  showOnlyWishlist.value = false;
  currentCategory.value = 'all';
  currentBrand.value = brand;
  if (currentTab.value !== 'home' && currentTab.value !== 'shop') {
    currentTab.value = 'shop';
  }
  scrollToProducts();
  showToast(`Filtering equipment by brand: ${brand}`);
}

function handleFilterWishlist() {
  showOnlyWishlist.value = true;
  if (currentTab.value !== 'home' && currentTab.value !== 'shop') {
    currentTab.value = 'shop';
  }
  scrollToProducts();
  showToast('Showing your saved Wishlist items ❤️');
}

function scrollToProducts() {
  setTimeout(() => {
    const el = document.getElementById('products');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  }, 100);
}

function addToCart(p) {
  const targetId = getItemId(p);
  const existing = cart.value.find(c => getItemId(c) === targetId);
  if (existing) {
    existing.qty++;
  } else {
    cart.value.push({ ...p, qty: 1 });
  }
  cartOpen.value = true;
  showToast(`Added ${p.name} to your cart!`);
}

function removeFromCart(pId) {
  const targetId = String(pId);
  cart.value = cart.value.filter(c => getItemId(c) !== targetId);
  showToast('Item removed from cart.');
}

function changeQty({ id, delta }) {
  const targetId = String(id);
  const item = cart.value.find(c => getItemId(c) === targetId);
  if (item) {
    item.qty += delta;
    if (item.qty <= 0) removeFromCart(targetId);
  }
}

function applyCoupon(code) {
  if (code.trim().toUpperCase() === 'CHHABRA10') {
    discount.value = 500;
    showToast('Promo Code CHHABRA10 applied! ₹500 Discount 🎉');
  } else {
    showToast('Invalid Coupon! Try "CHHABRA10"');
  }
}

function toggleWishlist(id) {
  const targetId = String(id);
  const newSet = new Set(wishlist.value);
  if (newSet.has(targetId)) {
    newSet.delete(targetId);
    showToast('Removed from your wishlist.');
  } else {
    newSet.add(targetId);
    showToast('Saved to your wishlist! ❤️');
  }
  wishlist.value = newSet;
}

function openQuickView(id) {
  const targetId = String(id);
  const found = allProducts.value.find(p => getItemId(p) === targetId);
  if (found) {
    activeProduct.value = found;
    quickViewOpen.value = true;
  }
}

function openCheckout() {
  if (cart.value.length === 0) {
    showToast('Please add items to cart before checkout!');
    return;
  }
  if (!user.value) {
    cartOpen.value = false;
    authModalOpen.value = true;
    showToast('Please log in or register to complete your checkout 🔐');
    return;
  }
  cartOpen.value = false;
  checkoutOpen.value = true;
}

function handleLoginSuccess(userData) {
  user.value = userData;
  localStorage.setItem('chhabra_user', JSON.stringify(userData));
  showToast(`Welcome back, ${userData.name}! 🚀`);
  authModalOpen.value = false;

  // If user had items in cart, proceed straight to Checkout Modal!
  if (cart.value.length > 0) {
    checkoutOpen.value = true;
  } else {
    handleNavigate('account');
  }
}


function handleLogout() {
  user.value = null;
  localStorage.removeItem('chhabra_user');
  showToast('Logged out successfully.');
  handleNavigate('home');
}

function handleOrderPlaced(order) {
  currentOrder.value = order;
  cart.value = [];
  discount.value = 0;
  orderSuccessOpen.value = true;

  try {
    const existing = JSON.parse(localStorage.getItem('chhabra_orders') || '[]');
    existing.unshift(order);
    localStorage.setItem('chhabra_orders', JSON.stringify(existing));
  } catch (e) {}

  showToast(`Order Placed! Order ID: ${order.order_number}`);
  confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
}


function showToast(message) {
  const id = Date.now() + Math.random();
  toasts.value.push({ id, message });
  setTimeout(() => {
    toasts.value = toasts.value.filter(t => t.id !== id);
  }, 3200);
}
</script>
