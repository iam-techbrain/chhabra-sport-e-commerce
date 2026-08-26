<template>
  <div class="search-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="search-box-wrap">
      <input 
        type="text" 
        class="search-input" 
        v-model="query" 
        placeholder="Search Yonex Astrox, Head Speed, SS Willow..." 
        ref="searchInput"
      >
      <button class="close-search" @click="$emit('close')">✕</button>
    </div>

    <div class="search-results">
      <div 
        v-for="p in filteredProducts" 
        :key="p.id" 
        class="search-result-item" 
        @click="$emit('open-qv', p.code_id || p.id); $emit('close')"
      >
        <img :src="p.img" :alt="p.name">
        <div>
          <div style="font-weight:700; font-size:14px; color:var(--ink);">{{ p.name }}</div>
          <div style="font-family:'Space Mono',monospace; font-size:12px; color:var(--gold-dark); font-weight:700;">
            ₹{{ Number(p.price).toLocaleString('en-IN') }} · {{ p.brand }}
          </div>
        </div>
      </div>

      <div v-if="query && filteredProducts.length === 0" style="color:#fff; padding:16px; text-align:center;">
        No sports equipment matching "{{ query }}"
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  products: Array
});

defineEmits(['close', 'open-qv']);

const query = ref('');

const filteredProducts = computed(() => {
  if (!query.value.trim()) return [];
  const q = query.value.toLowerCase();
  return props.products.filter(p => 
    p.name.toLowerCase().includes(q) || 
    p.brand.toLowerCase().includes(q) || 
    p.category.toLowerCase().includes(q)
  );
});
</script>
