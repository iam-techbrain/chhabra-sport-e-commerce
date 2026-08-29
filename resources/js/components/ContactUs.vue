<template>
  <div class="contact-page-wrap">
    <section class="section" style="padding: 60px 0;">
      <div class="wrap">
        <div class="contact-grid">
          <!-- LEFT: GOOGLE MAP EMBED -->
          <div class="map-container">
            <div class="map-header">
              <a href="https://maps.google.com/?q=Chhabra+Sports+Boring+Road+Patna" target="_blank" class="open-maps-link">
                Open in Maps ↗
              </a>
            </div>
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3597.514480036647!2d85.12351227618953!3d25.61276067744383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ed5845c479dd69%3A0x4bb57c79e67ef1bb!2sChhabra%20Sports!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
              width="100%" 
              height="450" 
              style="border:0; border-radius:8px;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>

          <!-- RIGHT: CONTACT FORM -->
          <div class="form-container">
            <span class="eyebrow" style="color:var(--gold-dark, #8A6818);">INFORMATION ABOUT US</span>
            <h2 class="form-title">CONTACT US FOR ANY QUESTIONS</h2>

            <form @submit.prevent="handleSubmit" class="contact-form">
              <div class="form-row-2">
                <div class="field-wrap">
                  <input type="text" v-model="form.name" placeholder="Your Name *" required maxlength="50" />
                </div>
                <div class="field-wrap">
                  <input type="email" v-model="form.email" placeholder="Your Email *" required maxlength="80" />
                </div>
              </div>

              <div class="form-row-2">
                <div class="field-wrap">
                  <input type="text" v-model="form.phone" placeholder="Phone Number" maxlength="15" />
                </div>
                <div class="field-wrap">
                  <input type="text" v-model="form.company" placeholder="Company" maxlength="60" />
                </div>
              </div>

              <div class="field-wrap">
                <textarea v-model="form.message" rows="5" placeholder="Your Message *" required maxlength="500"></textarea>
              </div>

              <button type="submit" class="btn btn-gold" :disabled="isSubmitting">
                {{ isSubmitting ? 'SENDING MESSAGE... ⌛' : 'ASK A QUESTION →' }}
              </button>
            </form>
          </div>
        </div>

        <!-- ADDRESS & FAQS BOTTOM SECTION -->
        <div class="contact-bottom-grid">
          <div class="address-box">
            <h3 class="box-title">OUR ADDRESS</h3>
            <div class="location-name">PATNA, BIHAR</div>
            <p class="address-text">
              L. B. Shop No. 10, Boring Road,<br>
              Patna – 800001
            </p>
            <div class="contact-info-list">
              <p><strong>Email:</strong> chhabrasportspatna@outlook.com</p>
              <p><strong>Phone:</strong> +91-72772-52440</p>
            </div>
          </div>

          <div class="faq-box">
            <h3 class="box-title">FREQUENTLY ASKED QUESTIONS</h3>
            <p class="faq-desc">
              Have questions about orders, shipping, returns, or product recommendations?
            </p>
            <button class="btn btn-outline" @click="showFaqToast">VISIT OUR FAQS →</button>

            <div class="social-share">
              <span>Share:</span>
              <div class="social-icons">
                <a href="https://www.facebook.com/chhabrasports/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--pitch-dark)"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="https://www.instagram.com/chhabrasportsagencies/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--pitch-dark)"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['show-toast']);

const form = ref({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: ''
});

const isSubmitting = ref(false);

async function handleSubmit() {
  if (!form.value.name || !form.value.email || !form.value.message) return;
  isSubmitting.value = true;

  try {
    const res = await axios.post('/api/contact', form.value);
    if (res.data && res.data.success) {
      emit('show-toast', res.data.message || `Thank you ${form.value.name}! Your message has been sent.`);
      form.value = { name: '', email: '', phone: '', company: '', message: '' };
    } else {
      emit('show-toast', 'Error submitting message. Please try again.');
    }
  } catch (err) {
    emit('show-toast', err.response?.data?.message || 'Failed to submit contact message.');
  } finally {
    isSubmitting.value = false;
  }
}

function showFaqToast() {
  emit('show-toast', 'FAQs: Free Pan-India Shipping over ₹2,999. 100% Original Equipment Guaranteed!');
}
</script>

<style scoped>
.contact-page-wrap {
  background: var(--bg, #EFECE6);
  color: var(--ink, #111);
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-bottom: 60px;
}

.map-container {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--line);
  box-shadow: var(--shadow-sm);
}

.map-header {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 10;
}

.open-maps-link {
  background: var(--pitch-dark, #0A241C);
  color: var(--gold-light, #E3C174);
  padding: 8px 16px;
  border-radius: 4px;
  font-weight: 700;
  font-size: 12px;
  font-family: 'Space Mono', monospace;
  text-decoration: none;
}

.form-title {
  font-family: 'Outfit', sans-serif;
  font-size: 28px;
  font-weight: 900;
  color: var(--pitch-dark, #0A241C);
  margin: 6px 0 24px;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.field-wrap input, .field-wrap textarea {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid var(--line);
  border-radius: 4px;
  font-size: 13px;
  font-family: 'Space Mono', monospace;
  outline: none;
  background: #ffffff;
}

.field-wrap input:focus, .field-wrap textarea:focus {
  border-color: var(--pitch-dark);
}

.contact-bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  border-top: 1px solid var(--line);
  padding-top: 40px;
}

.box-title {
  font-family: 'Outfit', sans-serif;
  font-size: 22px;
  font-weight: 900;
  color: var(--pitch-dark, #0A241C);
  margin-bottom: 12px;
}

.location-name {
  font-weight: 800;
  font-family: 'Space Mono', monospace;
  color: var(--oxblood, #6B1D2F);
  font-size: 13px;
  margin-bottom: 6px;
}

.address-text {
  font-size: 14px;
  color: var(--ink-soft);
  line-height: 1.6;
  margin-bottom: 16px;
}

.contact-info-list p {
  font-size: 13px;
  font-family: 'Space Mono', monospace;
  color: var(--ink);
  margin-bottom: 6px;
}

.faq-desc {
  font-size: 14px;
  color: var(--ink-soft);
  margin-bottom: 20px;
  line-height: 1.5;
}

.social-share {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
  font-size: 13px;
  font-family: 'Space Mono', monospace;
  font-weight: 700;
}

.social-icons {
  display: flex;
  gap: 10px;
}

@media (max-width: 900px) {
  .contact-grid, .contact-bottom-grid {
    grid-template-columns: 1fr;
  }
}
</style>
