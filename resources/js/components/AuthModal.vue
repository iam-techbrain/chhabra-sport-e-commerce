<template>
  <div class="auth-modal-backdrop" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="auth-modal-card">
      <div class="auth-modal-head">
        <div style="display:flex; align-items:center; gap:10px;">
          <span style="font-size:24px;">🔐</span>
          <div>
            <h3 style="font-family:'Outfit',sans-serif; font-weight:800; font-size:18px; color:#fff; margin:0;">
              {{ isRegister ? 'CREATE AN ACCOUNT' : 'LOGIN TO CHECKOUT' }}
            </h3>
            <span style="font-size:11px; color:#A7F3D0; font-family:'Space Mono',monospace;">
              {{ isRegister ? '1-step registration to complete your order' : 'Log in to your Chhabra Sports account' }}
            </span>
          </div>
        </div>
        <button class="close-auth-btn" @click="$emit('close')">✕</button>
      </div>

      <div class="auth-modal-body">
        <!-- LOGIN FORM -->
        <form v-if="!isRegister" @submit.prevent="handleLogin" class="auth-form-sm">
          <div class="form-group-sm">
            <label>Email Address / Mobile Number</label>
            <input type="text" v-model="loginEmail" placeholder="Enter your email or phone number" required />
          </div>

          <div class="form-group-sm">
            <label>Password</label>
            <input type="password" v-model="loginPassword" placeholder="••••••••" required />
          </div>

          <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:14px; font-weight:800; margin-top:8px;">
            LOG IN & CONTINUE TO CHECKOUT →
          </button>

          <div style="text-align:center; margin-top:16px; font-size:13px; color:#475569;">
            Don't have an account? 
            <a href="#" @click.prevent="isRegister = true" style="color:#059669; font-weight:800; text-decoration:none;">Create Account Now</a>
          </div>
        </form>

        <!-- REGISTER FORM -->
        <form v-else @submit.prevent="handleRegister" class="auth-form-sm">
          <div class="form-group-sm">
            <label>Full Name</label>
            <input type="text" v-model="regName" placeholder="Enter your full name" required />
          </div>

          <div class="form-row-sm">
            <div class="form-group-sm">
              <label>Email Address</label>
              <input type="email" v-model="regEmail" placeholder="e.g. name@example.com" required />
            </div>
            <div class="form-group-sm">
              <label>Mobile Number (10 Digits)</label>
              <input type="tel" v-model="regPhone" @input="onPhoneInput" maxlength="10" placeholder="10-digit mobile number" required />
            </div>
          </div>

          <div class="form-group-sm">
            <label>Choose Password</label>
            <input type="password" v-model="regPassword" placeholder="Minimum 6 characters" required />
          </div>

          <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:14px; font-weight:800; margin-top:8px;">
            REGISTER & CONTINUE TO CHECKOUT →
          </button>

          <div style="text-align:center; margin-top:16px; font-size:13px; color:#475569;">
            Already have an account? 
            <a href="#" @click.prevent="isRegister = false" style="color:#059669; font-weight:800; text-decoration:none;">Log In Here</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'login-success']);

const isRegister = ref(false);
const loading = ref(false);

const loginEmail = ref('');
const loginPassword = ref('');

const regName = ref('');
const regEmail = ref('');
const regPhone = ref('');
const regPassword = ref('');

function onPhoneInput(e) {
  regPhone.value = e.target.value.replace(/\D/g, '').slice(0, 10);
}

async function handleLogin() {
  loading.value = true;
  try {
    const res = await axios.post('/api/login', {
      email: loginEmail.value,
      password: loginPassword.value
    });
    loading.value = false;
    if (res.data && res.data.success) {
      emit('login-success', res.data.user);
      emit('close');
    } else {
      alert(res.data.message || 'Login failed!');
    }
  } catch (err) {
    loading.value = false;
    const msg = err.response?.data?.message || 'Login failed. Please check your credentials.';
    alert(msg);
  }
}

async function handleRegister() {
  if (regPhone.value.length !== 10) {
    alert('Please enter a valid 10-digit mobile number!');
    return;
  }
  loading.value = true;
  try {
    const res = await axios.post('/api/register', {
      name: regName.value,
      email: regEmail.value,
      phone: regPhone.value,
      password: regPassword.value
    });
    loading.value = false;
    if (res.data && res.data.success) {
      emit('login-success', res.data.user);
      emit('close');
    } else {
      alert(res.data.message || 'Registration failed!');
    }
  } catch (err) {
    loading.value = false;
    const msg = err.response?.data?.message || 'Registration failed. Check email or mobile number.';
    alert(msg);
  }
}
</script>


<style scoped>
.auth-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(10, 27, 21, 0.75);
  z-index: 600;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.auth-modal-backdrop.open {
  opacity: 1;
  visibility: visible;
}

.auth-modal-card {
  background: #ffffff;
  width: 100%;
  max-width: 480px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.auth-modal-head {
  background: #0A241C;
  padding: 20px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.close-auth-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
}

.auth-modal-body {
  padding: 24px;
}

.auth-form-sm {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form-row-sm {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-group-sm label {
  display: block;
  font-family: 'Space Mono', monospace;
  font-size: 11px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 4px;
}

.form-group-sm input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #CBD5E1;
  border-radius: 6px;
  font-size: 13px;
  outline: none;
}

.form-group-sm input:focus {
  border-color: #059669;
}
</style>
