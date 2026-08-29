<template>
  <div class="order-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="checkout-card" style="max-width:540px; text-align:center; padding:36px; margin:auto;" v-if="order">
      <div style="font-size:54px; margin-bottom:12px;">🎉</div>
      <span class="eyebrow" style="color:var(--gold-dark); font-weight:800; letter-spacing:1.5px;">ORDER CONFIRMED</span>
      <h2 style="font-family:'Outfit',sans-serif; font-weight:900; font-size:28px; color:var(--pitch); margin:8px 0 14px;">
        THANK YOU FOR YOUR ORDER!
      </h2>
      
      <p style="font-size:14px; color:var(--ink-soft); margin-bottom:24px; line-height:1.5;">
        Your order <strong style="color:var(--pitch);">#{{ order.order_number }}</strong> has been verified. We are preparing your sports gear for express dispatch!
      </p>

      <div style="background:var(--parchment-dim); border:1px solid var(--line); border-radius:var(--radius-sm); padding:18px; margin-bottom:28px; text-align:left;">
        <div style="font-family:'Space Mono',monospace; font-size:12px; display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:var(--ink-soft);">Customer:</span>
          <strong style="color:var(--pitch);">{{ order.customer_name }}</strong>
        </div>
        <div style="font-family:'Space Mono',monospace; font-size:12px; display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:var(--ink-soft);">Payment:</span>
          <strong style="text-transform:uppercase; color:var(--pitch);">{{ order.payment_method }}</strong>
        </div>
        <div style="font-family:'Space Mono',monospace; font-size:13px; display:flex; justify-content:space-between; color:var(--pitch); font-weight:800; border-top:1px dashed var(--line); padding-top:8px;">
          <span>Amount Paid:</span>
          <span>₹{{ Number(order.total).toLocaleString('en-IN') }}</span>
        </div>
      </div>

      <button class="btn btn-gold" style="width:100%; justify-content:center; padding:16px; font-weight:800; font-size:13px;" @click="$emit('close')">
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

<style scoped>
.order-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(10, 27, 21, 0.75);
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.order-modal.open {
  opacity: 1;
  visibility: visible;
}
</style>
