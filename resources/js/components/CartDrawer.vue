<template>
  <div class="drawer-backdrop" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="cart-drawer">
      <div class="cart-header">
        <h3>YOUR SHOPPING CART ({{ totalCount }})</h3>
        <button class="close-drawer" @click="$emit('close')">✕</button>
      </div>

      <!-- Free Shipping Progress -->
      <div class="free-ship-bar">
        <span v-if="rawSubtotal >= 2999">🎉 You unlocked <strong>FREE EXPRESS PAN-INDIA SHIPPING!</strong></span>
        <span v-else>Add <strong>₹{{ formatMoney(2999 - rawSubtotal) }}</strong> more for <strong>FREE PAN-INDIA SHIPPING!</strong></span>
      </div>

      <!-- Cart Items -->
      <div class="cart-items">
        <div v-if="cart.length === 0" style="text-align:center; padding:50px 10px;">
          <svg style="width:48px; height:48px; stroke:var(--line); fill:none; margin:0 auto 12px;" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          <p style="font-family:'Space Mono',monospace; font-size:13px; color:var(--ink-soft);">Your cart is currently empty.</p>
        </div>

        <div v-else class="cart-item" v-for="item in cart" :key="getItemKey(item)">
          <img :src="item.img" :alt="item.name">
          <div class="cart-item-info">
            <div class="cart-item-title">{{ item.name }}</div>
            <div class="cart-item-meta">{{ item.brand }} · {{ item.category }}</div>
            <div class="cart-item-price">₹{{ formatMoney(item.price * item.qty) }}</div>
            <div class="qty-ctrl">
              <button class="qty-btn" @click="$emit('change-qty', { id: getItemKey(item), delta: -1 })">-</button>
              <span class="qty-val">{{ item.qty }}</span>
              <button class="qty-btn" @click="$emit('change-qty', { id: getItemKey(item), delta: 1 })">+</button>
              <button class="remove-item" @click="$emit('remove-item', getItemKey(item))">Remove</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Cart Footer -->
      <div class="cart-footer" v-if="cart.length > 0">
        <div class="cart-coupon">
          <input type="text" v-model="couponCode" maxlength="20" placeholder="Enter Promo Code (CHHABRA10)">
          <button @click="applyPromo">APPLY</button>
        </div>

        <div style="font-size:12px; color:var(--oxblood); font-weight:700; margin-bottom:8px;" v-if="discount > 0">
          Discount Applied: -₹{{ formatMoney(discount) }}
        </div>

        <div class="cart-subtotal">
          <span>Subtotal</span>
          <span>₹{{ formatMoney(finalSubtotal) }}</span>
        </div>

        <button class="btn btn-gold" style="width:100%; justify-content:center; padding:16px;" @click="$emit('checkout')">
          PROCEED TO CHECKOUT →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  cart: Array,
  discount: Number
});

const emit = defineEmits(['close', 'change-qty', 'remove-item', 'apply-coupon', 'checkout']);

const couponCode = ref('');

const totalCount = computed(() => props.cart.reduce((sum, item) => sum + item.qty, 0));
const rawSubtotal = computed(() => props.cart.reduce((sum, item) => sum + (item.price * item.qty), 0));
const finalSubtotal = computed(() => Math.max(0, rawSubtotal.value - props.discount));

function getItemKey(item) {
  return String(item.code_id || item.id);
}

function formatMoney(val) {
  return Number(val).toLocaleString('en-IN');
}

function applyPromo() {
  emit('apply-coupon', couponCode.value);
}
</script>
