<template>
  <section v-if="brands && brands.length > 0" class="section tight brand-section">
    <div class="wrap">
      <div class="sec-head">
        <div>
          <span class="eyebrow">DIRECT FACTORY SOURCED</span>
          <h2>AUTHORISED OFFICIAL BRANDS</h2>
        </div>
      </div>

      <!-- INFINITE SMOOTH SCROLLING BRAND MARQUEE -->
      <div class="brand-marquee-container">
        <div class="brand-marquee-track">
          <!-- First track loop -->
          <div 
            v-for="(b, idx) in extendedBrands" 
            :key="'b1-' + idx" 
            class="brand-cell" 
            @click="$emit('filter-brand', typeof b === 'object' ? b.name : b)"
          >
            <span class="brand-icon">🏷️</span>
            <span>{{ typeof b === 'object' ? b.name : b }}</span>
          </div>

          <!-- Second track loop for seamless infinite marquee -->
          <div 
            v-for="(b, idx) in extendedBrands" 
            :key="'b2-' + idx" 
            class="brand-cell" 
            @click="$emit('filter-brand', typeof b === 'object' ? b.name : b)"
          >
            <span class="brand-icon">🏷️</span>
            <span>{{ typeof b === 'object' ? b.name : b }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  brands: {
    type: Array,
    default: () => []
  }
});

defineEmits(['filter-brand']);

// If brand list is short, duplicate items to ensure full width marquee coverage
const extendedBrands = computed(() => {
  if (!props.brands || props.brands.length === 0) return [];
  let list = [...props.brands];
  while (list.length < 8) {
    list = list.concat(props.brands);
  }
  return list;
});
</script>

<style scoped>
.brand-section {
  overflow: hidden;
  padding-top: 30px;
  padding-bottom: 30px;
}

.brand-marquee-container {
  overflow: hidden;
  width: 100%;
  position: relative;
  background: #FFFFFF;
  border: 1px solid var(--gold-glass, #E2E8F0);
  border-radius: 12px;
  padding: 16px 0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}

/* Gradient edge masks for smooth fade effect */
.brand-marquee-container::before,
.brand-marquee-container::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  width: 60px;
  z-index: 2;
  pointer-events: none;
}

.brand-marquee-container::before {
  left: 0;
  background: linear-gradient(to right, #FFFFFF, transparent);
}

.brand-marquee-container::after {
  right: 0;
  background: linear-gradient(to left, #FFFFFF, transparent);
}

.brand-marquee-track {
  display: flex;
  width: max-content;
  gap: 16px;
  animation: marqueeScroll 30s linear infinite;
}

.brand-marquee-container:hover .brand-marquee-track {
  animation-play-state: paused;
}

.brand-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 28px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  font-size: 14px;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: #0F172A;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.25s ease;
  user-select: none;
}

.brand-cell:hover {
  background: #0F172A;
  color: #38BDF8;
  border-color: #0F172A;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.brand-icon {
  font-size: 14px;
}

@keyframes marqueeScroll {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}
</style>
