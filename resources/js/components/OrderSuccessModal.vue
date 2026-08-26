<template>
  <div class="order-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="checkout-card" style="max-width:540px; text-align:center; padding:36px;" v-if="order">
      <div style="font-size:48px; margin-bottom:12px;">🎉</div>
      <span class="eyebrow" style="color:var(--gold-dark);">ORDER CONFIRMED</span>
      <h2 style="font-family:'Outfit',sans-serif; font-weight:900; font-size:28px; color:var(--pitch); margin:6px 0 12px;">
        THANK YOU FOR YOUR ORDER!
      </h2>
      
      <p style="font-size:14px; color:var(--ink-soft); margin-bottom:20px;">
        Your order <strong>#{{ order.order_number }}</strong> has been verified. We are preparing your sports gear for express dispatch!
      </p>

      <div style="background:var(--parchment-dim); border:1px solid var(--line); border-radius:var(--radius-sm); padding:16px; margin-bottom:24px; text-align:left;">
        <div style="font-family:'Space Mono',monospace; font-size:12px; display:flex; justify-space-between; margin-bottom:6px;">
          <span>Customer:</span>
          <strong>{{ order.customer_name }}</strong>
        </div>
        <div style="font-family:'Space Mono',monospace; font-size:12px; display:flex; justify-space-between; margin-bottom:6px;">
          <span>Payment:</span>
          <strong style="text-transform:uppercase;">{{ order.payment_method }}</strong>
        </div>
        <div style="font-family:'Space Mono',monospace; font-size:12px; display:flex; justify-space-between; color:var(--pitch); font-weight:700;">
          <span>Amount Paid:</span>
          <span>₹{{ Number(order.total).toLocaleString('en-IN') }}</span>
        </div>
      </div>

      <button class="btn btn-gold" style="width:100%; justify-content:center;" @click="$emit('close')">
        CONTINUE SHOPPING →
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isOpen: Boolean,
  order: Object
});

defineEmits(['close']);
</script>
