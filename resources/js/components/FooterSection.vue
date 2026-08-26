<template>
  <div>
    <!-- NEWSLETTER -->
    <section class="newsletter">
      <div class="wrap">
        <span class="eyebrow" style="color:var(--gold-light);">Racquet & Sports Club</span>
        <h2>Join the Players Club</h2>
        <p>Restock alerts, stringing tips & exclusive discount codes straight to your inbox.</p>
        <form class="nl-form" @submit.prevent="handleSubscribe">
          <input type="email" placeholder="Enter your email address" v-model="email" required>
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </section>

    <!-- FOOTER -->
    <footer>
      <div class="wrap">
        <div class="foot-top">
          <div class="foot-brand">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
              <img
                src="https://chhabrasports.com/wp-content/uploads/2025/09/csa-acrylic-letter-cutting-scaled-e1756718460651.jpg"
                style="height:38px; border-radius:4px;" alt="Chhabra Sports Logo">
            </div>
            <p>India's leading online racquet and sports gear store since 1998 — supplying genuine equipment to athletes
              nationwide.</p>
            <div class="foot-social">
              <a href="#" aria-label="Instagram">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                  <rect x="2" y="2" width="20" height="20" rx="5" />
                  <circle cx="12" cy="12" r="4" />
                </svg>
              </a>
              <a href="#" aria-label="Facebook">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                </svg>
              </a>
              <a href="#" aria-label="YouTube">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                  <rect x="2" y="5" width="20" height="14" rx="3" />
                  <polygon points="10 9 15 12 10 15" />
                </svg>
              </a>
            </div>
          </div>
          <div class="foot-col">
            <h5>Sports Store</h5>
            <a href="#products" @click.prevent="$emit('filter-cat', 'badminton')">Badminton Racquets</a>
            <a href="#products" @click.prevent="$emit('filter-cat', 'tennis')">Tennis Racquets</a>
            <a href="#products" @click.prevent="$emit('filter-cat', 'cricket')">Cricket Equipment</a>
            <a href="#products" @click.prevent="$emit('filter-cat', 'shoes')">Non-Marking Shoes</a>
            <a href="#products" @click.prevent="$emit('filter-cat', 'football')">Football Boots</a>
          </div>
          <div class="foot-col">
            <h5>Customer Service</h5>
            <a href="tel:+917277252440">Phone: +91-72772-52440</a>
            <a href="mailto:chhabrasportspatna@outlook.com">Email: chhabrasportspatna@outlook.com</a>
            <a href="#services">Stringing Service</a>
            <a href="#products" @click.prevent="$emit('filter-cat', 'all')">Track Order</a>
          </div>
          <div class="foot-col">
            <h5>Store Address</h5>
            <a href="#">L. B. Shop No. 10, Boring Road,</a>
            <a href="#">Patna, Bihar – 800001</a>
            <a href="#">Mon - Sat: 10:00 AM - 8:30 PM</a>
          </div>
          <div class="foot-col">
            <h5>Legal & Trust</h5>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Return Policy</a>
            <a href="#">100% Genuine Warranty</a>
          </div>
        </div>

        <div class="foot-bottom">
          <div>© 2026 Chhabra Sports (Racquets4u). All Rights Reserved. Designed with Precision.</div>
          <div class="pay-icons">
            <span>UPI</span>
            <span>VISA</span>
            <span>MASTERCARD</span>
            <span>NETBANKING</span>
            <span>COD</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineEmits(['filter-cat']);

const email = ref('');

function handleSubscribe() {
  if (email.value) {
    alert('Thank you for subscribing to Chhabra Sports Club!');
    email.value = '';
  }
}
</script>
