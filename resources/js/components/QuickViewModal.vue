<template>
  <div class="qv-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="qv-card" v-if="product">
      <button class="close-qv" @click="$emit('close')">✕</button>
      
      <div class="qv-media">
        <img :src="activeImage" :alt="product.name">
      </div>

      <div class="qv-details">
        <div>
          <div class="brand-badge-wrap">
            <span class="eyebrow">{{ product.brand }} Official</span>
            <span class="sku-badge">SKU: {{ activeVariantSKU }}</span>
          </div>

          <h2 style="font-family:'Outfit',sans-serif; font-weight:800; font-size:24px; margin:8px 0 12px;">{{ product.name }}</h2>
          <p style="font-size:13px; color:var(--ink-soft); line-height:1.5; margin-bottom:16px;">{{ product.specs }}</p>
          
          <!-- PRICE DISPLAY -->
          <div class="price-box-dynamic">
            <span class="current-price">₹{{ formatMoney(computedPrice) }}</span>
            <span v-if="computedOldPrice && computedOldPrice > computedPrice" class="old-price">₹{{ formatMoney(computedOldPrice) }}</span>
            <span v-if="savePercent > 0" class="save-tag">Save {{ savePercent }}%</span>
          </div>

          <div class="stock-status-line">
            <span v-if="product.in_stock !== false && product.in_stock !== 0 && product.stockStatus !== 'Out of stock'" style="color:#166534;"><span class="dot-green">●</span> In Stock (Available for Instant Dispatch)</span>
            <span v-else style="color:#991B1B; font-weight:700;"><span style="color:#EF4444;">●</span> Out of Stock (Currently Unavailable)</span>
          </div>

          <!-- VARIATION ATTRIBUTES DISPLAY (ONLY RENDERED FOR VARIABLE PRODUCTS) -->
          <div v-if="isProductVariable" style="margin-top: 16px;">
            <!-- 1. DYNAMIC ADMIN VARIATION ATTRIBUTE GROUPS -->
            <div v-if="isCustomAdminVariable && dynamicAttributeGroups.length > 0">
              <div v-for="group in dynamicAttributeGroups" :key="group.name" class="variant-group" style="margin-bottom: 14px;">
                <label class="variant-label">SELECT {{ group.name.toUpperCase() }}: <strong>{{ selectedAttributes[group.name] }}</strong></label>
                <div class="variant-options colors-opt">
                  <button 
                    v-for="val in group.values" 
                    :key="val" 
                    class="size-btn"
                    :class="{ active: selectedAttributes[group.name] === val }"
                    @click="selectedAttributes[group.name] = val"
                    style="min-width: 70px;"
                  >
                    <span>{{ val }}</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- 2. DEMO VARIABLE PRODUCT COLORS & SIZES -->
            <template v-else-if="isDemoVariable">
              <!-- COLOR SELECTION -->
              <div class="variant-group">
                <label class="variant-label">SELECT COLOR: <strong>{{ selectedColor }}</strong></label>
                <div class="variant-options colors-opt">
                  <button 
                    v-for="c in colorList" 
                    :key="c.name" 
                    class="color-btn"
                    :class="{ active: selectedColor === c.name }"
                    @click="selectedColor = c.name"
                  >
                    <span class="color-swatch" :style="{ background: c.hex }"></span>
                    <span>{{ c.name }}</span>
                  </button>
                </div>
              </div>

              <!-- SIZE SELECTION -->
              <div class="variant-group" style="margin-top: 14px;">
                <div class="size-header">
                  <label class="variant-label">SELECT SIZE: <strong>Size {{ selectedSize }}</strong></label>
                  <span class="size-guide-link">📏 Official Size Chart</span>
                </div>
                <div class="variant-options sizes-opt">
                  <button 
                    v-for="s in sizeList" 
                    :key="s" 
                    class="size-btn"
                    :class="{ active: String(selectedSize) === String(s) }"
                    @click="selectedSize = s"
                  >
                    <span>Size {{ s }}</span>
                    <span class="size-price-tag">₹{{ formatMoney(getExactVariantPrice(selectedColor, s)) }}</span>
                  </button>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div style="margin-top: 20px;">
          <button 
            class="btn btn-gold" 
            :disabled="product.in_stock === false || product.in_stock === 0 || product.stockStatus === 'Out of stock'"
            :style="(product.in_stock === false || product.in_stock === 0 || product.stockStatus === 'Out of stock') ? 'width:100%; justify-content:center; padding:16px; margin-bottom:10px; font-weight:800; font-size:15px; opacity:0.6; cursor:not-allowed; background:#CBD5E1; color:#475569; border-color:#CBD5E1;' : 'width:100%; justify-content:center; padding:16px; margin-bottom:10px; font-weight:800; font-size:15px;'" 
            @click="handleAddToCart"
          >
            {{ (product.in_stock === false || product.in_stock === 0 || product.stockStatus === 'Out of stock') ? 'OUT OF STOCK' : `ADD TO CART — ₹${formatMoney(computedPrice)} →` }}
          </button>
          <button class="btn btn-outline" style="width:100%; justify-content:center; padding:12px; color:var(--ink);" @click="$emit('close')">
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  product: Object
});

const emit = defineEmits(['close', 'add-to-cart']);

const selectedColor = ref('Blue');
const selectedSize = ref('7');
const selectedAttributes = ref({});

const isCustomAdminVariable = computed(() => {
  if (!props.product) return false;
  return !!(props.product.isVariable || props.product.is_variable || (props.product.variations && props.product.variations.length > 0));
});

const isDemoVariable = computed(() => {
  if (!props.product) return false;
  return !!(props.product.custom_variations);
});

const isProductVariable = computed(() => {
  return isCustomAdminVariable.value || isDemoVariable.value;
});

const adminVariations = computed(() => {
  if (!props.product?.variations) return [];
  return Array.isArray(props.product.variations) ? props.product.variations : [];
});

const dynamicAttributeGroups = computed(() => {
  if (!isCustomAdminVariable.value || adminVariations.value.length === 0) return [];
  
  const groups = {};
  adminVariations.value.forEach(v => {
    if (v.attributes && typeof v.attributes === 'object') {
      Object.keys(v.attributes).forEach(attrName => {
        if (!groups[attrName]) {
          groups[attrName] = new Set();
        }
        if (v.attributes[attrName]) {
          groups[attrName].add(v.attributes[attrName]);
        }
      });
    }
  });

  return Object.keys(groups).map(name => ({
    name,
    values: Array.from(groups[name])
  }));
});

const activeAdminVariation = computed(() => {
  if (!isCustomAdminVariable.value || adminVariations.value.length === 0) return null;

  return adminVariations.value.find(v => {
    if (!v.attributes) return false;
    return Object.keys(selectedAttributes.value).every(key => {
      return String(v.attributes[key]) === String(selectedAttributes.value[key]);
    });
  }) || adminVariations.value[0];
});

const colorList = computed(() => {
  if (!isDemoVariable.value) return [];
  if (props.product?.category === 'shoes' || props.product?.category === 'football') {
    return [
      { name: 'Blue', hex: '#1976D2', img: props.product?.img },
      { name: 'Green', hex: '#388E3C', img: props.product?.img2 || props.product?.img },
      { name: 'Red', hex: '#D32F2F', img: props.product?.img }
    ];
  }
  return [
    { name: 'Matte Black', hex: '#212121', img: props.product?.img },
    { name: 'Cyan Blue', hex: '#00BCD4', img: props.product?.img2 || props.product?.img },
    { name: 'Edition Red', hex: '#E53935', img: props.product?.img }
  ];
});

const sizeList = computed(() => {
  if (!isDemoVariable.value) return [];
  if (props.product?.category === 'shoes' || props.product?.category === 'football') {
    return ['7', '8', '9', '10'];
  }
  return ['4U / G5', '3U / G4', 'Strung (BG66)'];
});

watch(() => props.product, (newVal) => {
  if (newVal) {
    if (colorList.value.length > 0) {
      selectedColor.value = colorList.value[0]?.name || 'Blue';
    }
    if (sizeList.value.length > 0) {
      selectedSize.value = sizeList.value[0] || '7';
    }

    const initial = {};
    if (dynamicAttributeGroups.value.length > 0) {
      dynamicAttributeGroups.value.forEach(group => {
        initial[group.name] = group.values[0] || '';
      });
    }
    selectedAttributes.value = initial;
  }
}, { immediate: true });

function getExactVariantPrice(color, size) {
  if (!props.product) return 5000;
  const base = props.product.price || 5000;
  const c = String(color).toLowerCase();
  const s = String(size);
  let delta = 0;
  if (c.includes('red')) delta += 500;
  if (c.includes('green')) delta += 300;
  if (s === '8') delta += 200;
  if (s === '9') delta += 500;
  return base + delta;
}

const computedPrice = computed(() => {
  if (isCustomAdminVariable.value && activeAdminVariation.value) {
    return Number(activeAdminVariation.value.salePrice || activeAdminVariation.value.price) || props.product?.price || 5000;
  }
  if (isDemoVariable.value) {
    return getExactVariantPrice(selectedColor.value, selectedSize.value);
  }
  return Number(props.product?.price) || 5000;
});

const computedOldPrice = computed(() => {
  if (props.product?.old_price && Number(props.product.old_price) > Number(props.product.price)) {
    return Number(props.product.old_price);
  }
  if (isCustomAdminVariable.value && activeAdminVariation.value) {
    const orig = Number(activeAdminVariation.value.price);
    const sale = Number(activeAdminVariation.value.salePrice);
    if (orig && sale && orig > sale) return orig;
  }
  if (isDemoVariable.value) {
    return computedPrice.value + 1500;
  }
  return props.product?.old_price ? Number(props.product.old_price) : (computedPrice.value + 1500);
});

const savePercent = computed(() => {
  if (!computedOldPrice.value || computedOldPrice.value <= computedPrice.value) return 0;
  return Math.round(((computedOldPrice.value - computedPrice.value) / computedOldPrice.value) * 100);
});

const activeImage = computed(() => {
  if (isCustomAdminVariable.value && activeAdminVariation.value?.img) {
    return activeAdminVariation.value.img;
  }
  if (isDemoVariable.value) {
    const found = colorList.value.find(c => c.name === selectedColor.value);
    return found?.img || props.product?.img;
  }
  return props.product?.img || 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80';
});

const activeVariantSKU = computed(() => {
  if (isCustomAdminVariable.value && activeAdminVariation.value?.sku) {
    return activeAdminVariation.value.sku;
  }
  if (isDemoVariable.value) {
    const baseCode = (props.product?.code_id || props.product?.id || 'PROD').toUpperCase();
    const cCode = selectedColor.value.substring(0, 3).toUpperCase();
    return `${baseCode}-${cCode}-SZ${selectedSize.value}`;
  }
  return String(props.product?.code_id || props.product?.id || 'SKU-PROD').toUpperCase();
});

function handleAddToCart() {
  if (!props.product) return;
  let itemTitle = props.product.name;
  let specsSummary = props.product.specs || '';

  if (isCustomAdminVariable.value && activeAdminVariation.value) {
    const attrPairs = Object.entries(selectedAttributes.value).map(([k, v]) => `${k}: ${v}`).join(' · ');
    specsSummary = attrPairs || activeAdminVariation.value.description || specsSummary;
    itemTitle = `${props.product.name} (${attrPairs})`;
  } else if (isDemoVariable.value) {
    itemTitle = `${props.product.name} (${selectedColor.value} / Size ${selectedSize.value})`;
  }

  const variantItem = {
    ...props.product,
    code_id: activeVariantSKU.value,
    name: itemTitle,
    price: computedPrice.value,
    old_price: computedOldPrice.value,
    specs: specsSummary,
    img: activeImage.value
  };

  emit('add-to-cart', variantItem);
  emit('close');
}

function formatMoney(amount) {
  return Number(amount || 0).toLocaleString('en-IN');
}
</script>

<style scoped>
.qv-modal {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(8px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: all 0.3s ease;
}

.qv-modal.open {
  opacity: 1;
  pointer-events: auto;
}

.qv-card {
  background: #FDFBF7;
  border: 1px solid var(--gold-glass);
  border-radius: 16px;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  display: grid;
  grid-template-columns: 1fr 1.1fr;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
  position: relative;
}

.close-qv {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid #E2E8F0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 16px;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.close-qv:hover {
  background: #0F172A;
  color: #FFFFFF;
}

.qv-media {
  background: #F5EFE6;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px;
  border-top-left-radius: 16px;
  border-bottom-left-radius: 16px;
}

.qv-media img {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
  filter: drop-shadow(0 10px 15px rgba(0,0,0,0.15));
}

.qv-details {
  padding: 32px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.brand-badge-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.eyebrow {
  font-family: 'Outfit', sans-serif;
  font-weight: 800;
  font-size: 11px;
  letter-spacing: 1.5px;
  color: var(--gold);
  text-transform: uppercase;
}

.sku-badge {
  font-family: monospace;
  font-size: 11px;
  background: #F1F5F9;
  color: #475569;
  padding: 4px 8px;
  border-radius: 4px;
}

.price-box-dynamic {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin: 12px 0 8px;
}

.current-price {
  font-family: 'Outfit', sans-serif;
  font-weight: 900;
  font-size: 28px;
  color: #0F172A;
}

.old-price {
  font-size: 16px;
  color: #94A3B8;
  text-decoration: line-through;
}

.save-tag {
  background: #EF4444;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 800;
  padding: 3px 8px;
  border-radius: 4px;
}

.stock-status-line {
  font-family: monospace;
  font-size: 12px;
  color: #166534;
  margin-bottom: 16px;
}

.dot-green {
  color: #22C55E;
}

.variant-label {
  display: block;
  font-family: monospace;
  font-size: 11px;
  color: #64748B;
  margin-bottom: 8px;
  text-transform: uppercase;
}

.variant-options {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.color-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  background: #FFFFFF;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.color-btn.active {
  border-color: #0F172A;
  background: #F8FAFC;
  box-shadow: 0 0 0 1px #0F172A;
}

.color-swatch {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 1px solid rgba(0,0,0,0.1);
}

.size-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.size-guide-link {
  font-size: 11px;
  color: var(--gold);
  font-weight: 700;
  cursor: pointer;
}

.size-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px 14px;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  background: #FFFFFF;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.size-btn.active {
  border-color: #0F172A;
  background: #F8FAFC;
  box-shadow: 0 0 0 1px #0F172A;
}

.size-price-tag {
  font-size: 10px;
  color: #64748B;
  font-weight: 500;
}

@media (max-width: 768px) {
  .qv-card {
    grid-template-columns: 1fr;
  }
  .qv-media {
    border-radius: 16px 16px 0 0;
    padding: 20px;
  }
  .qv-details {
    padding: 20px;
  }
}
</style>
