<template>
  <section class="section" id="products">
    <div class="wrap">
      <div class="sec-head">
        <div>
          <span class="eyebrow">Browse Online Store</span>
          <h2>Featured Racquets & Sports Gear</h2>
        </div>
        <div class="sec-link" style="cursor:default;">
          Showing {{ products.length > 0 ? (startIndex + 1) : 0 }}–{{ Math.min(endIndex, products.length) }} of {{ products.length }} products
        </div>
      </div>

      <!-- Category Filter Pills -->
      <div class="filter-pills">
        <button 
          v-for="cat in filterTabs" 
          :key="cat.id" 
          class="pill-btn" 
          :class="{ active: currentCategory === cat.id }"
          @click="onSelectCategory(cat.id)"
        >
          {{ cat.label }}
        </button>
      </div>

      <!-- Dynamic Products Grid -->
      <div v-if="paginatedProducts.length > 0" class="prod-grid">
        <div v-for="p in paginatedProducts" :key="p.id" class="prod-card">
          <div class="prod-media" @click="$emit('open-qv', p.code_id || p.id)">
            <div v-if="p.tag" class="prod-tag" :class="p.tag.toLowerCase()">{{ p.tag }}</div>
            
            <button 
              class="prod-wish" 
              :class="{ active: isWishlisted(p.code_id || p.id) }" 
              @click.stop="$emit('toggle-wishlist', p.code_id || p.id)"
              title="Add to Wishlist"
            >
              <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/>
              </svg>
            </button>

            <img class="primary" :src="p.img" :alt="p.name">
            <img class="secondary" :src="p.img2 || p.img" :alt="p.name">

            <button class="quick-add-btn" @click.stop="$emit('add-to-cart', p)">+ Add to Cart</button>
          </div>

          <div class="prod-info">
            <div>
              <div class="prod-brand">{{ p.brand }}</div>
              <div class="prod-name" @click="$emit('open-qv', p.code_id || p.id)">{{ p.name }}</div>
              
              <div class="prod-rating">
                <span class="stars">{{ getStars(p.rating) }}</span>
                <span class="rev-count">({{ p.reviews }})</span>
              </div>
            </div>

            <div class="prod-price">
              <span class="price-now">₹{{ formatMoney(p.price) }}</span>
              <span v-if="p.old_price" class="price-old">₹{{ formatMoney(p.old_price) }}</span>
              <span v-if="p.old_price" class="price-off">-{{ Math.round((1 - p.price / p.old_price) * 100) }}% OFF</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else style="text-align:center; padding:80px 20px;">
        <h3 style="font-family:'Outfit',sans-serif; font-size:24px; color:var(--ink-soft); font-weight:800;">No products found in this filter</h3>
        <p style="margin-top:8px; font-size:14px;">Select another sport category or click "All Products".</p>
      </div>

      <!-- PAGINATION CONTROLS CONTAINER -->
      <div v-if="products.length > 0 && totalPages > 1" class="catalog-pagination">
        <div class="pagination-info">
          Page <strong>{{ currentPage }}</strong> of <strong>{{ totalPages }}</strong> &nbsp;·&nbsp; Showing {{ startIndex + 1 }}–{{ Math.min(endIndex, products.length) }} of {{ products.length }} items
        </div>

        <div class="pagination-controls">
          <button 
            class="pg-btn pg-nav" 
            :disabled="currentPage === 1" 
            @click="changePage(currentPage - 1)"
          >
            ← Prev
          </button>

          <button 
            v-for="page in totalPages" 
            :key="page" 
            class="pg-btn pg-num" 
            :class="{ active: currentPage === page }"
            @click="changePage(page)"
          >
            {{ page }}
          </button>

          <button 
            class="pg-btn pg-nav" 
            :disabled="currentPage === totalPages" 
            @click="changePage(currentPage + 1)"
          >
            Next →
          </button>
        </div>

        <div class="per-page-box">
          <label class="per-page-label">Show:</label>
          <select v-model="itemsPerPage" class="per-page-select" @change="currentPage = 1">
            <option :value="8">8 per page</option>
            <option :value="12">12 per page</option>
            <option :value="16">16 per page</option>
            <option :value="24">24 per page</option>
          </select>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  products: Array,
  currentCategory: String,
  wishlist: Object,
  categories: Array
});

const emit = defineEmits(['change-category', 'add-to-cart', 'open-qv', 'toggle-wishlist']);

const itemsPerPage = ref(8);
const currentPage = ref(1);

const categoriesList = ref([]);

async function fetchCategories() {
  try {
    const res = await axios.get('/api/categories');
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      categoriesList.value = res.data.data;
    }
  } catch (e) {
    console.warn('Could not load categories in ProductCatalog', e);
  }
}

onMounted(() => {
  fetchCategories();
});

watch(() => props.categories, (newVal) => {
  if (newVal && Array.isArray(newVal) && newVal.length > 0) {
    categoriesList.value = newVal;
  }
}, { immediate: true });

const filterTabs = computed(() => {
  const tabs = [{ id: 'all', label: 'All Products' }];
  
  if (categoriesList.value && categoriesList.value.length > 0) {
    categoriesList.value.forEach(c => {
      tabs.push({
        id: c.slug || c.name.toLowerCase(),
        label: c.name
      });
    });
  } else {
    tabs.push(
      { id: 'badminton', label: 'Badminton' },
      { id: 'cricket', label: 'Cricket' },
      { id: 'shoes', label: 'Shoes & Spikes' },
      { id: 'football', label: 'Football' },
      { id: 'fitness', label: 'Fitness' }
    );
  }
  
  return tabs;
});

watch(() => props.currentCategory, () => {
  currentPage.value = 1;
});

watch(() => props.products?.length, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1;
  }
});

const totalPages = computed(() => {
  return Math.ceil((props.products?.length || 0) / itemsPerPage.value) || 1;
});

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => startIndex.value + itemsPerPage.value);

const paginatedProducts = computed(() => {
  if (!props.products || props.products.length === 0) return [];
  return props.products.slice(startIndex.value, endIndex.value);
});

function onSelectCategory(catId) {
  currentPage.value = 1;
  emit('change-category', catId);
}

function changePage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    const el = document.getElementById('products');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
  }
}

function isWishlisted(id) {
  return props.wishlist && props.wishlist.has(id);
}

function formatMoney(val) {
  return Number(val || 0).toLocaleString('en-IN');
}

function getStars(r) {
  const full = Math.floor(r || 5);
  const half = (r || 5) % 1 !== 0;
  return '★'.repeat(full) + (half ? '☆' : '') + '☆'.repeat(Math.max(0, 5 - full - (half ? 1 : 0)));
}
</script>

<style scoped>
.catalog-pagination {
  margin-top: 40px;
  padding-top: 24px;
  border-top: 1px solid var(--gold-glass, #E2E8F0);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.pagination-info {
  font-size: 13px;
  color: #64748B;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 6px;
}

.pg-btn {
  padding: 8px 14px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pg-btn:hover:not(:disabled) {
  background: #0284C7;
  color: #FFFFFF;
  border-color: #0284C7;
}

.pg-btn.active {
  background: #0F172A;
  color: #38BDF8;
  border-color: #0F172A;
}

.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.per-page-box {
  display: flex;
  align-items: center;
  gap: 8px;
}

.per-page-label {
  font-size: 12.5px;
  color: #64748B;
  font-weight: 600;
}

.per-page-select {
  padding: 6px 12px;
  border: 1px solid #CBD5E1;
  border-radius: 6px;
  background: #FFFFFF;
  color: #0F172A;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
}

@media (max-width: 640px) {
  .catalog-pagination {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}
</style>
