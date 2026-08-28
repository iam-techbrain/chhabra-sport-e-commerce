<template>
  <div class="auth-page-wrap">
    <section class="section" style="padding: 50px 0;">
      <div class="wrap">
        <div class="auth-grid">
          <!-- LEFT COLUMN: LOGIN FORM -->
          <div class="auth-box">
            <h2 class="auth-heading">CUSTOMER LOGIN</h2>

            <form @submit.prevent="handleLogin" class="auth-form">
              <div class="form-group">
                <label>Username or email address <span class="required">*</span></label>
                <input 
                  type="text" 
                  v-model="loginForm.email" 
                  placeholder="abc@gmail.com" 
                  required 
                  maxlength="80"
                />
              </div>

              <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <div class="password-input-wrap">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    v-model="loginForm.password" 
                    placeholder="••••••••••••" 
                    value="123456"
                    required 
                    maxlength="50"
                  />
                  <button type="button" class="eye-toggle" @click="showPassword = !showPassword">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn btn-gold btn-full">LOG IN →</button>

              <div class="form-sub-links">
                <label class="remember-label">
                  <input type="checkbox" v-model="loginForm.remember" />
                  <span>Remember me</span>
                </label>

                <a href="#" @click.prevent="forgotPassword" class="lost-pass-link">Lost your password?</a>
              </div>
            </form>
          </div>

          <!-- RIGHT COLUMN: REGISTER INFORMATION -->
          <div class="auth-box auth-border-left">
            <h2 class="auth-heading">REGISTER</h2>

            <div v-if="!showRegisterForm">
              <p class="register-info-text">
                Registering for this site allows you to access your order status and history. Just fill in the fields below, and we'll get a new account set up for you in no time. We will only ask you for information necessary to make the purchase process faster and easier.
              </p>

              <button class="btn btn-oxblood" @click="showRegisterForm = true" style="margin-top:24px;">REGISTER NOW →</button>
            </div>

            <form v-else @submit.prevent="handleRegister" class="auth-form">
              <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" v-model="registerForm.name" placeholder="Enter your full name" required maxlength="50" />
              </div>

              <div class="form-group">
                <label>Email address <span class="required">*</span></label>
                <input type="email" v-model="registerForm.email" placeholder="Enter your email" required maxlength="80" />
              </div>

              <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <input type="password" v-model="registerForm.password" placeholder="Choose a password" required maxlength="50" />
              </div>

              <button type="submit" class="btn btn-gold btn-full">CREATE ACCOUNT →</button>
              
              <button type="button" class="btn-link-back" @click="showRegisterForm = false">← Back to Information</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['show-toast', 'login-success', 'open-admin']);

const authRole = ref('customer');
const showPassword = ref(false);
const showRegisterForm = ref(false);

const loginForm = ref({
  email: 'abc@gmail.com',
  password: '123456',
  remember: true
});

const registerForm = ref({
  name: '',
  email: '',
  password: ''
});

function handleLogin() {
  const username = loginForm.value.email.split('@')[0] || 'User';
  emit('login-success', { name: username, email: loginForm.value.email, role: 'customer' });
  emit('show-toast', `Welcome back, ${username}! You are logged in 🎉`);
}

function handleRegister() {
  emit('login-success', { name: registerForm.value.name, email: registerForm.value.email, role: 'customer' });
  emit('show-toast', `Account created successfully! Welcome ${registerForm.value.name} 🎉`);
  showRegisterForm.value = false;
}

function forgotPassword() {
  emit('show-toast', 'Password reset link sent to your registered email address!');
}
</script>

<style scoped>
.auth-page-wrap {
  background: var(--bg, #EFECE6);
  color: var(--ink, #111);
}

.auth-mode-tabs {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 36px;
}

.mode-tab-btn {
  padding: 12px 24px;
  font-family: 'Space Mono', monospace;
  font-size: 14px;
  font-weight: 700;
  border-radius: 6px;
  border: 1px solid var(--line);
  background: #ffffff;
  color: var(--ink);
  cursor: pointer;
  transition: 0.2s;
}

.mode-tab-btn.active {
  background: var(--pitch-dark, #0A241C);
  color: #ffffff;
  border-color: var(--pitch-dark, #0A241C);
}

.mode-tab-btn.admin-tab.active {
  background: #0A1B15;
  color: #E3C174;
  border-color: #E3C174;
}

.auth-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: flex-start;
}

.auth-box {
  padding: 24px;
  background: #ffffff;
  border-radius: 8px;
  border: 1px solid var(--line);
  box-shadow: var(--shadow-sm);
}

.admin-auth-container {
  max-width: 520px;
  margin: 0 auto;
}

.admin-login-box {
  background: #18221E;
  padding: 36px;
  border-radius: 10px;
  border: 1px solid #E3C174;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.admin-header-badge {
  text-align: center;
  margin-bottom: 12px;
}

.admin-header-badge span {
  font-size: 11px;
  font-family: 'Space Mono', monospace;
  color: #6BE395;
  background: rgba(107, 227, 149, 0.1);
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: 700;
}

.auth-border-left {
  border-left: 1px solid var(--line);
}

.auth-heading {
  font-family: 'Outfit', sans-serif;
  font-size: 26px;
  font-weight: 900;
  color: var(--pitch-dark, #0A241C);
  margin-bottom: 24px;
  letter-spacing: 0.5px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group label {
  display: block;
  font-size: 12px;
  font-family: 'Space Mono', monospace;
  font-weight: 700;
  color: var(--pitch-dark, #0A241C);
  margin-bottom: 8px;
}

.required {
  color: var(--oxblood, #6B1D2F);
}

.form-group input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid var(--line);
  border-radius: 4px;
  font-size: 13px;
  font-family: 'Space Mono', monospace;
  background: #FAFAFA;
  outline: none;
}

.form-group input:focus {
  border-color: var(--pitch-dark);
  background: #ffffff;
}

.password-input-wrap {
  position: relative;
}

.eye-toggle {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--ink-soft);
  cursor: pointer;
}

.btn-full {
  width: 100%;
  justify-content: center;
}

.form-sub-links {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  font-size: 12px;
  font-family: 'Space Mono', monospace;
}

.remember-label {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--ink-soft);
  cursor: pointer;
}

.lost-pass-link {
  color: var(--oxblood, #6B1D2F);
  text-decoration: none;
  font-weight: 700;
}

.register-info-text {
  font-size: 14px;
  line-height: 1.7;
  color: var(--ink-soft);
}

.btn-link-back {
  background: none;
  border: none;
  color: var(--ink-soft);
  font-size: 12px;
  font-family: 'Space Mono', monospace;
  cursor: pointer;
  margin-top: 8px;
  text-align: center;
}

@media (max-width: 900px) {
  .auth-grid {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .auth-border-left {
    border-left: none;
    border-top: 1px solid var(--line);
    padding-top: 20px;
  }
}
</style>
