<template>
  <section class="section tight category-section-pro" id="categories">
    <div class="wrap">
      <!-- PROFESSIONAL COMPACT HEADER -->
      <div class="sec-head" style="margin-bottom: 22px;">
        <div>
          <span class="eyebrow" style="letter-spacing:1.5px; color:#E3C174; font-weight:700; font-size:11px;">SHOP BY SPORT & CATEGORY</span>
          <h2 style="font-size: 26px; font-weight:800; margin-top:3px; color:#0F172A; font-family:'Outfit',sans-serif;">Explore Categories</h2>
        </div>
        <a href="#products" class="sec-link" @click.prevent="$emit('select-category', 'all')" style="font-size:13px; font-weight:700; color:#0F172A;">View Complete Catalog →</a>
      </div>

      <!-- COMPACT HIGH-END CATEGORY GRID -->
      <div v-if="categories.length > 0" class="pro-cat-grid">
        <div 
          v-for="cat in categories" 
          :key="cat.id || cat.slug" 
          class="pro-cat-card" 
          @click="$emit('select-category', cat.slug)"
        >
          <img :src="getCategoryImg(cat.slug)" :alt="cat.name" class="pro-cat-bg" />
          <div class="pro-cat-overlay">
            <div class="pro-cat-top">
              <span class="cat-icon-badge">{{ cat.icon || '📦' }}</span>
              <div class="arrow-pill">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </div>
            </div>
            <div class="pro-cat-bottom">
              <h3 class="pro-cat-name">{{ cat.name }}</h3>
              <span class="pro-cat-sub">Shop Collection</span>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-else class="empty-categories">
        <div style="font-size:28px; margin-bottom:6px;">📁</div>
        <h3 style="font-family:'Outfit',sans-serif; color:#0F172A; font-weight:800; font-size:16px;">No Categories Found</h3>
        <p style="font-size:12.5px; color:#64748B; margin-top:2px;">Add new categories from Admin Dashboard.</p>
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

<style scoped>
.category-section-pro {
  padding: 35px 0 25px;
}

.pro-cat-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}

@media (min-width: 640px) {
  .pro-cat-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1024px) {
  .pro-cat-grid {
    grid-template-columns: repeat(6, 1fr);
  }
}

.pro-cat-card {
  position: relative;
  height: 140px;
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  background: #0F172A;
  border: 1px solid rgba(227, 193, 116, 0.18);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.pro-cat-card:hover {
  transform: translateY(-4px);
  border-color: #E3C174;
  box-shadow: 0 12px 25px rgba(227, 193, 116, 0.25);
}

.pro-cat-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  filter: brightness(0.65) contrast(1.08);
}

.pro-cat-card:hover .pro-cat-bg {
  transform: scale(1.1);
  filter: brightness(0.75) contrast(1.15);
}

.pro-cat-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.88) 100%);
  padding: 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.pro-cat-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cat-icon-badge {
  font-size: 16px;
  background: rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(8px);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.arrow-pill {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFFFFF;
  transition: all 0.3s ease;
}

.pro-cat-card:hover .arrow-pill {
  background: #E3C174;
  border-color: #E3C174;
  color: #0A1B15;
  transform: translateX(3px);
}

.pro-cat-bottom {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.pro-cat-name {
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  font-size: 15px;
  color: #FFFFFF;
  margin: 0;
  letter-spacing: -0.2px;
  line-height: 1.2;
}

.pro-cat-sub {
  font-size: 11px;
  font-weight: 700;
  color: #E3C174;
  opacity: 0.85;
  transition: opacity 0.2s;
  letter-spacing: 0.2px;
}

.pro-cat-card:hover .pro-cat-sub {
  opacity: 1;
}

.empty-categories {
  text-align: center;
  padding: 35px 20px;
  background: #F8FAFC;
  border-radius: 12px;
  border: 2px dashed #E2E8F0;
  margin-top: 15px;
}
</style>
