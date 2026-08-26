<template>
  <section class="section tight" id="categories">
    <div class="wrap">
      <div class="sec-head">
        <div>
          <span class="eyebrow">Shop by Sport & Category</span>
          <h2>Explore Sports Categories</h2>
        </div>
        <a href="#products" class="sec-link" @click.prevent="$emit('select-category', 'all')">View Complete Catalog →</a>
      </div>

      <div v-if="categories.length > 0" class="cat-grid">
        <div 
          v-for="cat in categories" 
          :key="cat.id || cat.slug" 
          class="cat-card" 
          @click="$emit('select-category', cat.slug)"
        >
          <img :src="getCategoryImg(cat.slug)" :alt="cat.name">
          <div class="cat-overlay">
            <div class="cat-label">
              <span>{{ cat.icon || '📦' }} {{ cat.name }}</span>
              <div class="arrow">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else style="text-align:center; padding:50px 20px; background:#F8FAFC; border-radius:12px; border:2px dashed #E2E8F0; margin-top:20px;">
        <div style="font-size:32px; margin-bottom:8px;">📁</div>
        <h3 style="font-family:'Outfit',sans-serif; color:#0F172A; font-weight:800; font-size:18px;">No Categories Found in Database</h3>
        <p style="font-size:13px; color:#64748B; margin-top:4px;">Project database is clean. Add new categories from Admin Dashboard.</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

defineEmits(['select-category']);

const categories = ref([]);

const catImages = {
  badminton: 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=500&q=80&auto=format&fit=crop',
  tennis: 'https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?w=500&q=80&auto=format&fit=crop',
  cricket: 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=500&q=80&auto=format&fit=crop',
  shoes: 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=500&q=80&auto=format&fit=crop',
  football: 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=500&q=80&auto=format&fit=crop',
  fitness: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500&q=80&auto=format&fit=crop'
};

function getCategoryImg(slug) {
  return catImages[slug] || 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500&q=80&auto=format&fit=crop';
}

onMounted(async () => {
  try {
    const res = await axios.get('/api/categories');
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      categories.value = res.data.data;
    }
  } catch (e) {
    categories.value = [];
  }
});
</script>
