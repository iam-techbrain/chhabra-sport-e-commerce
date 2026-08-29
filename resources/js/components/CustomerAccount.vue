<template>
  <div class="customer-account-wrap">
    <section class="section" style="padding: 40px 0 80px;">
      <div class="wrap">
        <!-- USER PROFILE WELCOME HEADER -->
        <div class="account-header-card">
          <div class="user-avatar-badge">
            <span>{{ userInitial }}</span>
          </div>

          <div class="user-info-text">
            <span class="welcome-tag">WELCOME BACK TO CHHABRA SPORTS</span>
            <h1 class="user-fullname">{{ user?.name || 'Valued Customer' }}</h1>
            <p class="user-email">📧 {{ user?.email || 'customer@example.com' }} <span v-if="user?.phone"> &nbsp;|&nbsp; 📱 {{ user.phone }}</span></p>
          </div>

          <div class="account-header-actions">
            <button class="btn btn-outline-gold" @click="$emit('nav-shop')">
              🛍️ Continue Shopping
            </button>
            <button class="btn btn-logout" @click="$emit('logout')">
              🚪 Logout
            </button>
          </div>
        </div>

        <!-- MAIN DASHBOARD CONTENT (MY ORDERS & TRACKING) -->
        <div class="orders-section-card" style="margin-top: 30px;">
          <div class="orders-toolbar">
            <div>
              <h2 class="orders-heading">📦 My Orders & Live Order Tracking</h2>
              <p class="orders-subtext">Track your shipment status, warehouse dispatch location, and courier notes in real time.</p>
            </div>

            <div style="display:flex; gap:12px; align-items:center;">
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="🔍 Search by Order # (e.g. CHS-)..." 
                class="search-order-input"
              />
              <button class="btn-refresh" @click="fetchUserOrders">
                🔄 Refresh
              </button>
            </div>
          </div>

          <!-- LOADING STATE -->
          <div v-if="isLoading" class="loading-state-box">
            <div class="spinner"></div>
            <p>Fetching your orders from server database...</p>
          </div>

          <!-- EMPTY STATE -->
          <div v-else-if="filteredOrders.length === 0" class="empty-orders-box">
            <div class="empty-icon">🛒</div>
            <h3>No Orders Found</h3>
            <p v-if="searchQuery">No orders matching "{{ searchQuery }}". Try another order ID.</p>
            <p v-else>You haven't placed any orders yet! Explore our pro sports catalog and place your first order.</p>
            <button class="btn btn-gold" style="margin-top: 16px;" @click="$emit('nav-shop')">
              EXPLORE PRO CATALOG NOW →
            </button>
          </div>

          <!-- ORDERS LIST -->
          <div v-else class="orders-list">
            <div v-for="order in filteredOrders" :key="order.id" class="order-card">
              <!-- ORDER CARD TOP BAR -->
              <div class="order-card-header">
                <div>
                  <span class="order-num-badge">ORDER #{{ order.order_number }}</span>
                  <span class="order-date-text">Placed on {{ formatDate(order.created_at) }}</span>
                </div>

                <div class="status-badge-wrap">
                  <span class="status-badge" :class="getStatusClass(order.status)">
                    {{ getStatusIcon(order.status) }} {{ order.status.toUpperCase() }}
                  </span>
                </div>
              </div>

              <!-- VISUAL ORDER TRACKING TIMELINE / STEPPER -->
              <div class="tracking-stepper-box">
                <div class="stepper-track">
                  <div 
                    class="stepper-progress-bar" 
                    :style="{ width: getProgressPercent(order.status) + '%' }"
                  ></div>

                  <!-- STEP 1: ORDER PLACED -->
                  <div class="step-item" :class="{ completed: isStepPassed(order.status, 1), active: isStepActive(order.status, 1) }">
                    <div class="step-circle">📝</div>
                    <span class="step-title">Order Placed</span>
                    <span class="step-sub">Confirmed</span>
                  </div>

                  <!-- STEP 2: PROCESSING -->
                  <div class="step-item" :class="{ completed: isStepPassed(order.status, 2), active: isStepActive(order.status, 2) }">
                    <div class="step-circle">📦</div>
                    <span class="step-title">Processing</span>
                    <span class="step-sub">Packed at Factory</span>
                  </div>

                  <!-- STEP 3: DISPATCHED / SHIPPED -->
                  <div class="step-item" :class="{ completed: isStepPassed(order.status, 3), active: isStepActive(order.status, 3) }">
                    <div class="step-circle">🚚</div>
                    <span class="step-title">Dispatched</span>
                    <span class="step-sub">In Transit</span>
                  </div>

                  <!-- STEP 4: DELIVERED -->
                  <div class="step-item" :class="{ completed: isStepPassed(order.status, 4), active: isStepActive(order.status, 4) }">
                    <div class="step-circle">🏠</div>
                    <span class="step-title">Delivered</span>
                    <span class="step-sub">To Doorstep</span>
                  </div>
                </div>
              </div>

              <!-- DISPATCH WAREHOUSE & ADMIN NOTES BOX -->
              <div class="tracking-details-grid">
                <!-- WAREHOUSE & COURIER LOCATION -->
                <div class="track-detail-box">
                  <div class="detail-box-title">📍 DISPATCH & LOGISTICS INFO</div>
                  <div class="detail-line">
                    <strong>Origin Warehouse:</strong> {{ order.dispatch_warehouse || 'Chhabra Sports Central Facility, Main Market, New Delhi' }}
                  </div>
                  <div class="detail-line">
                    <strong>Delivery Address:</strong> {{ order.address }}
                  </div>
                  <div class="detail-line">
                    <strong>Payment Mode:</strong> {{ order.payment_method ? order.payment_method.toUpperCase() : 'CASH ON DELIVERY' }}
                  </div>
                </div>

                <!-- COURIER TRACKING NOTES & ADMIN UPDATES -->
                <div class="track-detail-box notes-box">
                  <div class="detail-box-title">📝 ORDER NOTES & SHIPMENT UPDATES</div>
                  <div v-if="order.notes && getNoteLines(order.notes).length > 0" class="admin-notes-content" style="display:flex; flex-direction:column; gap:6px;">
                    <div v-for="(noteLine, nIdx) in getNoteLines(order.notes)" :key="nIdx" style="display:flex; align-items:flex-start; gap:6px; line-height:1.4;">
                      <span>{{ noteLine }}</span>
                    </div>
                  </div>
                  <div v-else class="admin-notes-content empty-notes">
                    <em>No custom notes added yet. Your order is being processed according to standard dispatch schedule.</em>
                  </div>
                </div>

              </div>

              <!-- ORDER ITEMS BREAKDOWN -->
              <div class="order-items-breakdown">
                <h4 class="items-table-heading">ITEMS ORDERED ({{ getItemsCount(order) }})</h4>
                <div class="items-grid">
                  <div v-for="(item, idx) in getItemList(order)" :key="idx" class="item-row">
                    <img :src="item.img || 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&q=80'" :alt="item.name" class="item-img" />
                    <div class="item-meta">
                      <strong class="item-name">{{ item.name }}</strong>
                      <span class="item-specs" v-if="item.specs">{{ item.specs }}</span>
                      <span class="item-sku" v-if="item.code_id">SKU: {{ item.code_id }}</span>
                    </div>
                    <div class="item-price-qty">
                      <span class="item-qty">Qty: {{ item.quantity || item.qty || 1 }}</span>
                      <span class="item-price">₹{{ formatMoney((item.price || 0) * (item.quantity || item.qty || 1)) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ORDER CARD FOOTER TOTALS -->
              <div class="order-card-footer">
                <div class="total-summary-left">
                  <span>Subtotal: ₹{{ formatMoney(order.subtotal || order.total) }}</span>
                  <span v-if="order.discount > 0">Discount: -₹{{ formatMoney(order.discount) }}</span>
                  <span>Shipping: {{ order.shipping > 0 ? '₹' + formatMoney(order.shipping) : 'FREE' }}</span>
                </div>
                <div class="total-amount-grand">
                  GRAND TOTAL: <strong>₹{{ formatMoney(order.total) }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  user: Object
});

const emit = defineEmits(['nav-shop', 'logout', 'show-toast']);

const orders = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');

const userInitial = computed(() => {
  if (!props.user || !props.user.name) return 'C';
  return props.user.name.charAt(0).toUpperCase();
});

const filteredOrders = computed(() => {
  if (!searchQuery.value.trim()) return orders.value;
  const q = searchQuery.value.toLowerCase().trim();
  return orders.value.filter(o => {
    const numMatch = String(o.order_number || '').toLowerCase().includes(q);
    const nameMatch = String(o.customer_name || '').toLowerCase().includes(q);
    return numMatch || nameMatch;
  });
});

async function fetchUserOrders() {
  isLoading.value = true;
  try {
    const params = {};
    if (props.user?.email) {
      params.email = props.user.email;
    } else if (props.user?.phone) {
      params.phone = props.user.phone;
    }
    const res = await axios.get('/api/orders', { params });
    if (res.data && res.data.success && Array.isArray(res.data.orders)) {
      let list = res.data.orders;
      // Filter list specifically for logged in customer
      if (props.user?.email || props.user?.phone || props.user?.name) {
        const uEmail = (props.user?.email || '').toLowerCase().trim();
        const uPhone = (props.user?.phone || '').trim();
        const uName = (props.user?.name || '').toLowerCase().trim();

        list = list.filter(o => {
          const matchEmail = uEmail && String(o.customer_email || '').toLowerCase().trim() === uEmail;
          const matchPhone = uPhone && String(o.customer_phone || '').includes(uPhone);
          const matchName = uName && String(o.customer_name || '').toLowerCase().trim() === uName;
          return matchEmail || matchPhone || matchName;
        });
      }
      orders.value = list;
    } else {
      loadFromLocalStorage();
    }
  } catch (e) {
    loadFromLocalStorage();
  } finally {
    isLoading.value = false;
  }
}

function loadFromLocalStorage() {
  try {
    const saved = localStorage.getItem('chhabra_orders');
    if (saved) {
      const allSaved = JSON.parse(saved);
      if (props.user?.email || props.user?.phone || props.user?.name) {
        const uEmail = (props.user?.email || '').toLowerCase().trim();
        const uPhone = (props.user?.phone || '').trim();
        const uName = (props.user?.name || '').toLowerCase().trim();

        orders.value = allSaved.filter(o => {
          const matchEmail = uEmail && String(o.customer_email || '').toLowerCase().trim() === uEmail;
          const matchPhone = uPhone && String(o.customer_phone || '').includes(uPhone);
          const matchName = uName && String(o.customer_name || '').toLowerCase().trim() === uName;
          return matchEmail || matchPhone || matchName;
        });
      } else {
        orders.value = allSaved;
      }
    }
  } catch (e) {
    orders.value = [];
  }
}


function getStatusClass(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('confirm')) return 'badge-confirmed';
  if (s.includes('process')) return 'badge-processing';
  if (s.includes('dispatch') || s.includes('ship')) return 'badge-dispatched';
  if (s.includes('deliver')) return 'badge-delivered';
  if (s.includes('cancel')) return 'badge-cancelled';
  return 'badge-confirmed';
}

function getStatusIcon(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('confirm')) return '📝';
  if (s.includes('process')) return '⚙️';
  if (s.includes('dispatch') || s.includes('ship')) return '🚚';
  if (s.includes('deliver')) return '✅';
  if (s.includes('cancel')) return '❌';
  return '📦';
}

function getProgressPercent(status) {
  const s = String(status || '').toLowerCase();
  if (s.includes('cancel')) return 0;
  if (s.includes('deliver')) return 100;
  if (s.includes('dispatch') || s.includes('ship')) return 70;
  if (s.includes('process')) return 40;
  return 15; // Confirmed / Order Placed
}

function isStepPassed(status, stepNum) {
  const percent = getProgressPercent(status);
  if (stepNum === 1) return percent >= 15;
  if (stepNum === 2) return percent >= 40;
  if (stepNum === 3) return percent >= 70;
  if (stepNum === 4) return percent >= 100;
  return false;
}

function isStepActive(status, stepNum) {
  const percent = getProgressPercent(status);
  if (stepNum === 1) return percent === 15;
  if (stepNum === 2) return percent === 40;
  if (stepNum === 3) return percent === 70;
  if (stepNum === 4) return percent === 100;
  return false;
}

function getItemList(order) {
  if (!order || !order.items) return [];
  if (Array.isArray(order.items)) return order.items;
  if (typeof order.items === 'string') {
    try { return JSON.parse(order.items); } catch (e) { return []; }
  }
  return [];
}

function getItemsCount(order) {
  const list = getItemList(order);
  return list.reduce((sum, item) => sum + (item.quantity || item.qty || 1), 0);
}

function formatDate(dateStr) {
  if (!dateStr) return new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
  return new Date(dateStr).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatMoney(amount) {
  return Number(amount || 0).toLocaleString('en-IN');
}

function getNoteLines(notes) {
  if (!notes) return [];
  return String(notes)
    .split(/\r?\n|;/)
    .map(line => line.trim())
    .filter(line => line.length > 0)
    .map(line => line.startsWith('📌') ? line : '📌 ' + line);
}


onMounted(() => {
  fetchUserOrders();
});
</script>

<style scoped>
.customer-account-wrap {
  background: #F8FAFC;
  min-height: 80vh;
}

.account-header-card {
  background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
  border: 1px solid #334155;
  border-radius: 16px;
  padding: 32px;
  display: flex;
  align-items: center;
  gap: 24px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
}

.user-avatar-badge {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: linear-gradient(135deg, #D4AF37 0%, #AA7C11 100%);
  color: #0F172A;
  font-family: 'Outfit', sans-serif;
  font-size: 32px;
  font-weight: 900;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
}

.user-info-text {
  flex: 1;
}

.welcome-tag {
  font-family: monospace;
  font-size: 11px;
  color: #D4AF37;
  letter-spacing: 1.5px;
  text-transform: uppercase;
}

.user-fullname {
  color: #F8FAFC;
  font-family: 'Outfit', sans-serif;
  font-size: 26px;
  font-weight: 800;
  margin: 4px 0;
}

.user-email {
  color: #94A3B8;
  font-size: 14px;
}

.account-header-actions {
  display: flex;
  gap: 12px;
}

.btn-outline-gold {
  border: 1px solid #D4AF37;
  color: #D4AF37;
  background: transparent;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-outline-gold:hover {
  background: #D4AF37;
  color: #0F172A;
}

.btn-logout {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid #EF4444;
  color: #FCA5A5;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-logout:hover {
  background: #EF4444;
  color: #FFFFFF;
}

.orders-section-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.orders-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #E2E8F0;
  padding-bottom: 20px;
  margin-bottom: 24px;
}

.orders-heading {
  font-family: 'Outfit', sans-serif;
  font-size: 22px;
  font-weight: 800;
  color: #0F172A;
}

.orders-subtext {
  font-size: 13px;
  color: #64748B;
  margin-top: 4px;
}

.search-order-input {
  border: 1px solid #CBD5E1;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  width: 260px;
}

.btn-refresh {
  background: #F1F5F9;
  border: 1px solid #CBD5E1;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  color: #334155;
  cursor: pointer;
}

.loading-state-box, .empty-orders-box {
  text-align: center;
  padding: 50px 20px;
}

.empty-icon {
  font-size: 54px;
  margin-bottom: 12px;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #E2E8F0;
  border-top-color: #D4AF37;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.order-card {
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  background: #FFFFFF;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.order-card-header {
  background: #F8FAFC;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #E2E8F0;
}

.order-num-badge {
  font-family: monospace;
  font-weight: 800;
  font-size: 15px;
  color: #0F172A;
  margin-right: 12px;
}

.order-date-text {
  font-size: 12px;
  color: #64748B;
}

.status-badge {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.badge-confirmed { background: #FEF3C7; color: #92400E; }
.badge-processing { background: #E0F2FE; color: #075985; }
.badge-dispatched { background: #F3E8FF; color: #6B21A8; }
.badge-delivered { background: #DCFCE7; color: #166534; }
.badge-cancelled { background: #FEE2E2; color: #991B1B; }

/* STEPPER TIMELINE STYLES */
.tracking-stepper-box {
  padding: 24px 30px;
  background: #FAFAFA;
  border-bottom: 1px solid #E2E8F0;
}

.stepper-track {
  display: flex;
  justify-content: space-between;
  position: relative;
}

.stepper-track::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 10%;
  right: 10%;
  height: 4px;
  background: #E2E8F0;
  z-index: 1;
}

.stepper-progress-bar {
  position: absolute;
  top: 20px;
  left: 10%;
  height: 4px;
  background: linear-gradient(90deg, #D4AF37 0%, #166534 100%);
  z-index: 2;
  transition: width 0.4s ease;
}

.step-item {
  position: relative;
  z-index: 3;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  width: 100px;
}

.step-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 2px solid #CBD5E1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}

.step-item.completed .step-circle {
  border-color: #166534;
  background: #DCFCE7;
}

.step-item.active .step-circle {
  border-color: #D4AF37;
  background: #FEF3C7;
  box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
}

.step-title {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
}

.step-item.completed .step-title { color: #166534; }
.step-item.active .step-title { color: #0F172A; }

.step-sub {
  font-size: 10px;
  color: #94A3B8;
}

/* DISPATCH & NOTES DETAILS */
.tracking-details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  padding: 20px;
  background: #FFFFFF;
  border-bottom: 1px solid #E2E8F0;
}

.track-detail-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 14px;
}

.detail-box-title {
  font-family: monospace;
  font-size: 11px;
  font-weight: 800;
  color: #64748B;
  letter-spacing: 1px;
  margin-bottom: 10px;
}

.detail-line {
  font-size: 12.5px;
  color: #334155;
  margin-bottom: 6px;
}

.notes-box {
  background: #FFFBEB;
  border-color: #FDE68A;
}

.admin-notes-content {
  font-size: 13px;
  color: #92400E;
  font-weight: 600;
  line-height: 1.4;
}

.empty-notes {
  color: #B45309;
  font-weight: 400;
}

/* ITEMS BREAKDOWN TABLE */
.order-items-breakdown {
  padding: 20px;
}

.items-table-heading {
  font-family: monospace;
  font-size: 11px;
  color: #64748B;
  letter-spacing: 1px;
  margin-bottom: 12px;
}

.items-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.item-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding-bottom: 12px;
  border-bottom: 1px dashed #E2E8F0;
}

.item-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.item-img {
  width: 50px;
  height: 50px;
  border-radius: 6px;
  object-fit: cover;
  border: 1px solid #E2E8F0;
}

.item-meta {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-name {
  font-size: 14px;
  color: #0F172A;
}

.item-specs {
  font-size: 12px;
  color: #64748B;
}

.item-sku {
  font-family: monospace;
  font-size: 11px;
  color: #94A3B8;
}

.item-price-qty {
  text-align: right;
  display: flex;
  flex-direction: column;
}

.item-qty {
  font-size: 12px;
  color: #64748B;
}

.item-price {
  font-weight: 800;
  color: #0F172A;
  font-size: 14px;
}

.order-card-footer {
  background: #F8FAFC;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #E2E8F0;
}

.total-summary-left {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #64748B;
}

.total-amount-grand {
  font-family: 'Outfit', sans-serif;
  font-size: 16px;
  color: #0F172A;
}

.total-amount-grand strong {
  font-size: 20px;
  color: #D4AF37;
}

@media (max-width: 768px) {
  .account-header-card {
    flex-direction: column;
    text-align: center;
  }
  .account-header-actions {
    width: 100%;
    flex-direction: column;
  }
  .tracking-details-grid {
    grid-template-columns: 1fr;
  }
  .orders-toolbar {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .search-order-input {
    width: 100%;
  }
}
</style>
