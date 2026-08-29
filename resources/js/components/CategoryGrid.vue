<template>
  <section class="section category-section-pro" id="categories">
    <div class="wrap">
      <!-- PROFESSIONAL HEADER -->
      <div class="sec-head" style="margin-bottom: 28px;">
        <div>
          <span class="eyebrow" style="letter-spacing:1.5px; color:#D97706; font-weight:700; font-size:11px;">SHOP BY SPORT & CATEGORY</span>
          <h2 style="font-size: 28px; font-weight:800; margin-top:4px; color:#0F172A; font-family:'Outfit',sans-serif; letter-spacing:-0.5px;">Explore Categories</h2>
        </div>
        <a href="#products" class="sec-link" @click.prevent="$emit('select-category', 'all')" style="font-size:13.5px; font-weight:700; color:#0F172A; display:inline-flex; align-items:center; gap:6px;">
          View Complete Catalog 
          <span style="font-size:15px; transition:transform 0.2s;">→</span>
        </a>
      </div>

      <!-- PREMIUM FLEXIBLE CATEGORY GRID -->
      <div v-if="categories.length > 0" class="pro-cat-grid">
        <div 
          v-for="cat in categories" 
          :key="cat.id || cat.slug || cat.name" 
          class="pro-cat-card" 
          @click="$emit('select-category', cat.slug || cat.name.toLowerCase())"
        >
          <img :src="getCategoryImg(cat.slug || cat.name)" :alt="cat.name" class="pro-cat-bg" />
          <div class="pro-cat-overlay">
            <div class="pro-cat-top">
              <span class="cat-icon-badge">{{ cat.icon || getFallbackIcon(cat.name) }}</span>
              <div class="arrow-pill">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2.5">
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </div>
            </div>
            <div class="pro-cat-bottom">
              <h3 class="pro-cat-name">{{ cat.name }}</h3>
              <span class="pro-cat-sub">Shop Collection →</span>
            </div>
          </div>
        </div>
      </div>

      <!-- EMPTY STATE -->
      <div v-else class="empty-categories">
        <div style="font-size:32px; margin-bottom:8px;">📁</div>
        <h3 style="font-family:'Outfit',sans-serif; color:#0F172A; font-weight:800; font-size:17px;">No Categories Found</h3>
        <p style="font-size:13px; color:#64748B; margin-top:2px;">Add new categories from Admin Dashboard.</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

defineEmits(['select-category']);

const categories = ref([]);

function getCategoryImg(str) {
  if (!str) return 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80&auto=format&fit=crop';
  const s = str.toLowerCase();
  if (s.includes('badminton')) return 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=600&q=80&auto=format&fit=crop';
  if (s.includes('cricket')) return 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=600&q=80&auto=format&fit=crop';
  if (s.includes('shoe') || s.includes('footwear') || s.includes('spike')) return 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80&auto=format&fit=crop';
  if (s.includes('foot') || s.includes('soccer')) return 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600&q=80&auto=format&fit=crop';
  if (s.includes('fit') || s.includes('gym')) return 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80&auto=format&fit=crop';
  if (s.includes('tennis') || s.includes('racquet')) return 'https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?w=600&q=80&auto=format&fit=crop';
  return 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600&q=80&auto=format&fit=crop';
}

function getFallbackIcon(str) {
  if (!str) return '📦';
  const s = str.toLowerCase();
  if (s.includes('badminton')) return '🏸';
  if (s.includes('cricket')) return '🏏';
  if (s.includes('shoe') || s.includes('spike')) return '👟';
  if (s.includes('foot') || s.includes('soccer')) return '⚽';
  if (s.includes('fit') || s.includes('gym')) return '🏋️';
  if (s.includes('tennis')) return '🎾';
  return '🏆';
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
  padding: 40px 0 30px;
}

.pro-cat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
}

.pro-cat-card {
  position: relative;
  height: 185px;
  border-radius: 18px;
  overflow: hidden;
  cursor: pointer;
  background: #0F172A;
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.pro-cat-card:hover {
  transform: translateY(-6px) scale(1.01);
  border-color: #E3C174;
  box-shadow: 0 20px 35px -10px rgba(227, 193, 116, 0.3);
}

.pro-cat-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
  filter: brightness(0.65) contrast(1.1);
}

.pro-cat-card:hover .pro-cat-bg {
  transform: scale(1.12);
  filter: brightness(0.75) contrast(1.15);
}

.pro-cat-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.15) 0%, rgba(15, 23, 42, 0.82) 60%, rgba(15, 23, 42, 0.95) 100%);
  padding: 18px;
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
  font-size: 18px;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.arrow-pill {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFFFFF;
  transition: all 0.35s ease;
}

.pro-cat-card:hover .arrow-pill {
  background: #E3C174;
  border-color: #E3C174;
  color: #0A1B15;
  transform: translateX(4px);
}

.pro-cat-bottom {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.pro-cat-name {
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  font-size: 18px;
  color: #FFFFFF;
  margin: 0;
  letter-spacing: -0.3px;
  line-height: 1.25;
}

.pro-cat-sub {
  font-size: 12px;
  font-weight: 700;
  color: #E3C174;
  opacity: 0.9;
  transition: all 0.3s ease;
  letter-spacing: 0.3px;
}

.pro-cat-card:hover .pro-cat-sub {
  opacity: 1;
  color: #FFFFFF;
  transform: translateX(2px);
}

.empty-categories {
  text-align: center;
  padding: 40px 20px;
  background: #F8FAFC;
  border-radius: 16px;
  border: 2px dashed #E2E8F0;
  margin-top: 15px;
}
</style>
