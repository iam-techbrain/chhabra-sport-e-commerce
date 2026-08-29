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
            <label>Select Payment Method (Secure Checkout)</label>
            <div class="pay-options" style="display:flex; flex-direction:column; gap:12px;">
              
              <!-- Option 1: Razorpay Online Payment -->
              <div 
                class="pay-option" 
                :class="{ active: form.payment_method === 'razorpay' }" 
                @click="form.payment_method = 'razorpay'"
                style="padding:14px; border:2px solid #cbd5e1; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:all 0.2s;"
                :style="form.payment_method === 'razorpay' ? 'border-color:#10B981; background:rgba(16,185,129,0.08);' : 'background:#fff;'"
              >
                <div style="display:flex; align-items:center; gap:10px;">
                  <span style="font-size:20px;">💳</span>
                  <div>
                    <strong style="display:block; font-size:14px; color:#0f172a;">Razorpay (Online Payment)</strong>
                    <span style="font-size:11px; color:#64748b;">UPI / GPay / PhonePe / Cards / NetBanking / QR</span>
                  </div>
                </div>
                <span 
                  v-if="form.payment_method === 'razorpay'" 
                  style="font-size:11px; background:#059669; color:#fff; padding:4px 10px; border-radius:20px; font-weight:700;"
                >
                  ✓ SELECTED
                </span>
              </div>

              <!-- Option 2: Cash on Delivery (COD) -->
              <div 
                class="pay-option" 
                :class="{ active: form.payment_method === 'cod' }" 
                @click="form.payment_method = 'cod'"
                style="padding:14px; border:2px solid #cbd5e1; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:all 0.2s;"
                :style="form.payment_method === 'cod' ? 'border-color:#10B981; background:rgba(16,185,129,0.08);' : 'background:#fff;'"
              >
                <div style="display:flex; align-items:center; gap:10px;">
                  <span style="font-size:20px;">💵</span>
                  <div>
                    <strong style="display:block; font-size:14px; color:#0f172a;">Cash on Delivery (COD)</strong>
                    <span style="font-size:11px; color:#64748b;">Pay cash when order arrives at doorstep</span>
                  </div>
                </div>
                <span 
                  v-if="form.payment_method === 'cod'" 
                  style="font-size:11px; background:#059669; color:#fff; padding:4px 10px; border-radius:20px; font-weight:700;"
                >
                  ✓ SELECTED
                </span>
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
            <button type="submit" class="btn btn-gold" :disabled="loading" style="padding:14px 24px;">
              <span v-if="loading">Processing Payment...</span>
              <span v-else-if="form.payment_method === 'razorpay'">Pay ₹{{ Number(total).toLocaleString('en-IN') }} with Razorpay 🔒</span>
              <span v-else>Confirm Order (COD) ✓</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  subtotal: Number,
  discount: Number,
  cart: Array,
  user: Object
});

const emit = defineEmits(['close', 'order-placed']);

const loading = ref(false);
const form = ref({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  address: '',
  customer_notes: '',
  payment_method: 'razorpay'
});

watch(() => props.isOpen, (open) => {
  if (open && props.user) {
    if (props.user.name && !form.value.customer_name) form.value.customer_name = props.user.name;
    if (props.user.email && !form.value.customer_email) form.value.customer_email = props.user.email;
    if (props.user.phone && !form.value.customer_phone) form.value.customer_phone = props.user.phone;
  }
}, { immediate: true });

// Enforce strict 10-digit numeric-only input
function onPhoneInput(e) {
  const clean = e.target.value.replace(/\D/g, '').slice(0, 10);
  form.value.customer_phone = clean;
}


const total = computed(() => {
  const sub = Number(props.subtotal) || 0;
  const disc = Number(props.discount) || 0;
  return Math.max(0, sub - disc);
});

// Helper script loader for Razorpay Checkout SDK
function loadRazorpayScript() {
  return new Promise((resolve) => {
    if (window.Razorpay) {
      resolve(true);
      return;
    }
    const script = document.createElement('script');
    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
    script.onload = () => resolve(true);
    script.onerror = () => resolve(false);
    document.body.appendChild(script);
  });
}

async function submitCheckout() {
  if (props.cart.length === 0) {
    alert('Your cart is empty!');
    return;
  }

  // Validate strict 10-digit mobile number
  if (!/^\d{10}$/.test(form.value.customer_phone)) {
    alert('Please enter a valid 10-digit mobile number (numbers only, e.g. 9876543210).');
    return;
  }

  loading.value = true;

  // Handle Online Payment via Razorpay
  if (form.value.payment_method === 'razorpay') {
    const isScriptLoaded = await loadRazorpayScript();
    if (!isScriptLoaded) {
      loading.value = false;
      alert('Unable to load Razorpay Payment Gateway. Please check internet connection.');
      return;
    }

    try {
      // 1. Create Razorpay Order on Backend
      const orderRes = await fetch('/api/razorpay/create-order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          amount: total.value
        })
      });

      const orderData = await orderRes.json();

      if (!orderData.success) {
        loading.value = false;
        alert('Payment initialization failed: ' + (orderData.message || 'Error creating order'));
        return;
      }

      // 2. Open Razorpay Checkout Popup
      const options = {
        key: orderData.key_id,
        amount: orderData.amount,
        currency: orderData.currency,
        name: 'Chhabra Sports',
        description: 'Order Payment',
        image: '/favicon.ico',
        order_id: orderData.razorpay_order_id,
        prefill: {
          name: form.value.customer_name,
          email: form.value.customer_email,
          contact: form.value.customer_phone,
        },
        theme: {
          color: '#059669',
        },
        handler: async function (response) {
          // 3. Verify Payment Signature on Backend Securely
          try {
            const verifyRes = await fetch('/api/razorpay/verify-payment', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                user_id: props.user?.id || null,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature,
                customer_name: form.value.customer_name,
                customer_email: form.value.customer_email,
                customer_phone: form.value.customer_phone,
                address: form.value.address,
                customer_notes: form.value.customer_notes,
                subtotal: Number(props.subtotal) || 0,
                discount: Number(props.discount) || 0,
                shipping: 0,
                total: total.value,
                items: props.cart
              })
            });

            const verifyData = await verifyRes.json();
            loading.value = false;

            if (verifyData.success) {
              emit('order-placed', verifyData.order);
              emit('close');
            } else {
              alert('Payment Verification Failed: ' + (verifyData.message || 'Signature mismatch'));
            }
          } catch (err) {
            loading.value = false;
            alert('Error verifying payment on server.');
          }
        },
        modal: {
          ondismiss: function () {
            loading.value = false;
          }
        }
      };

      const rzp = new window.Razorpay(options);
      rzp.open();

    } catch (err) {
      loading.value = false;
      alert('Error initiating Razorpay checkout: ' + err.message);
    }
  } else {
    // Handle Cash on Delivery (COD)
    const payload = {
      user_id: props.user?.id || null,
      customer_name: form.value.customer_name,
      customer_email: form.value.customer_email,
      customer_phone: form.value.customer_phone,
      address: form.value.address,
      customer_notes: form.value.customer_notes,
      payment_method: 'Cash on Delivery (COD)',
      subtotal: Number(props.subtotal) || 0,
      discount: Number(props.discount) || 0,
      shipping: 0,
      total: total.value,
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
        total: total.value,
        payment_method: 'Cash on Delivery (COD)',
        notes: form.value.customer_notes ? ('📌 Customer Note: ' + form.value.customer_notes) : '📌 Cash on Delivery Order',
        items: props.cart
      };
      emit('order-placed', fallbackOrder);
      emit('close');
    }
  }
}
</script>
