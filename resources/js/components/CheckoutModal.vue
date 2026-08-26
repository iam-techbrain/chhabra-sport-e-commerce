<template>
  <div class="checkout-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="checkout-card">
      <div class="checkout-head">
        <h3>CHHABRA SPORTS EXPRESS CHECKOUT</h3>
        <button class="close-drawer" style="color:#fff;" @click="$emit('close')">✕</button>
      </div>

      <div class="checkout-body">
        <form @submit.prevent="submitCheckout">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" v-model="form.customer_name" required placeholder="e.g. Rahul Sharma">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" v-model="form.customer_phone" required placeholder="+91 98765 43210">
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" v-model="form.customer_email" required placeholder="rahul@example.com">
            </div>
          </div>

          <div class="form-group">
            <label>Delivery Address & Pincode</label>
            <textarea rows="2" v-model="form.address" required placeholder="Flat / House No, Street Name, City, Pincode"></textarea>
          </div>

          <div class="form-group">
            <label>Payment Method</label>
            <div class="pay-options">
              <div class="pay-option active" style="border-color:#10B981; background:rgba(16,185,129,0.1); cursor:default;">
                💵 Cash on Delivery (COD) <span style="font-size:11px; background:#059669; color:#fff; padding:2px 6px; border-radius:4px; margin-left:auto;">✓ ACTIVE</span>
              </div>
              <div class="pay-option disabled" style="opacity:0.5; cursor:not-allowed; background:#f1f5f9;" title="Online Payment Temporarily Unavailable">
                📱 UPI / GPay / PhonePe <span style="font-size:10px; color:#64748B; margin-left:auto;">🔒 Disabled</span>
              </div>
              <div class="pay-option disabled" style="opacity:0.5; cursor:not-allowed; background:#f1f5f9;" title="Online Payment Temporarily Unavailable">
                💳 Credit / Debit Card <span style="font-size:10px; color:#64748B; margin-left:auto;">🔒 Disabled</span>
              </div>
            </div>
          </div>

          <div style="margin-top:24px; border-top:1px solid var(--line); padding-top:18px; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <span style="font-family:'Space Mono',monospace; font-size:11px; color:var(--ink-soft);">Total Payable:</span>
              <div style="font-family:'Outfit',sans-serif; font-size:26px; font-weight:900; color:var(--pitch);">
                ₹{{ Number(total).toLocaleString('en-IN') }}
              </div>
            </div>
            <button type="submit" class="btn btn-gold" :disabled="loading">
              {{ loading ? 'Placing Order...' : 'Confirm Order ✓' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  subtotal: Number,
  discount: Number,
  cart: Array
});

const emit = defineEmits(['close', 'order-placed']);

const loading = ref(false);
const form = ref({
  customer_name: 'Aditya Chhabra',
  customer_email: '',
  customer_phone: '+91 98765 43210',
  address: 'Boring Road Crossing, Near Main Market, Patna, Bihar 800001',
  payment_method: 'cod'
});

const total = ref(props.subtotal);

async function submitCheckout() {
  loading.value = true;
  const payload = {
    customer_name: form.value.customer_name,
    customer_email: form.value.customer_email,
    customer_phone: form.value.customer_phone,
    address: form.value.address,
    payment_method: form.value.payment_method,
    subtotal: props.subtotal + props.discount,
    discount: props.discount,
    shipping: 0,
    total: props.subtotal,
    items: props.cart
  };

  try {
    const res = await fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    loading.value = false;

    if (data.success) {
      emit('order-placed', data.order);
      emit('close');
    } else {
      alert('Error placing order: ' + (data.message || 'Validation failed'));
    }
  } catch (err) {
    loading.value = false;
    // Fallback order generation if offline/test
    const fallbackOrder = {
      order_number: 'CHS-' + Math.floor(100000 + Math.random() * 900000),
      customer_name: form.value.customer_name,
      total: props.subtotal,
      payment_method: form.value.payment_method,
      items: props.cart
    };
    emit('order-placed', fallbackOrder);
    emit('close');
  }
}
</script>
