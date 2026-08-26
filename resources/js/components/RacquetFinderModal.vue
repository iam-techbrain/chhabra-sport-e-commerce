<template>
  <div class="rf-modal" :class="{ open: isOpen }" @click.self="$emit('close')">
    <div class="rf-card">
      <button class="close-qv" @click="$emit('close')">✕</button>
      
      <span class="eyebrow">RACQUETS4U WIZARD</span>
      <h3>FIND YOUR PERFECT RACQUET</h3>
      <p style="font-size:13.5px; color:var(--ink-soft);">Answer 2 simple questions and our algorithm selects your ideal Yonex, Head or Babolat frame.</p>

      <div class="rf-step">
        <label style="font-family:'Space Mono',monospace; font-size:11px; text-transform:uppercase; font-weight:700; color:var(--gold-dark);">1. What is your playing level?</label>
        <div class="rf-options-grid">
          <div class="rf-opt-btn" :class="{ active: level === 'beginner' }" @click="level = 'beginner'">
            <h6>Beginner / Casual</h6>
            <p>Focus on easy power & lightweight forgiveness</p>
          </div>
          <div class="rf-opt-btn" :class="{ active: level === 'pro' }" @click="level = 'pro'">
            <h6>Intermediate / Pro</h6>
            <p>Stiff frame, high tension & pinpoint smash control</p>
          </div>
        </div>
      </div>

      <div class="rf-step">
        <label style="font-family:'Space Mono',monospace; font-size:11px; text-transform:uppercase; font-weight:700; color:var(--gold-dark);">2. What is your primary priority?</label>
        <div class="rf-options-grid">
          <div class="rf-opt-btn" :class="{ active: preference === 'power' }" @click="preference = 'power'">
            <h6>Max Power & Smash</h6>
            <p>Head heavy swing for explosive backcourt hits</p>
          </div>
          <div class="rf-opt-btn" :class="{ active: preference === 'control' }" @click="preference = 'control'">
            <h6>Speed & Speed Control</h6>
            <p>Even balance & fast maneuverability at net</p>
          </div>
        </div>
      </div>

      <div style="margin-top:24px; display:flex; justify-content:flex-end;">
        <button class="btn btn-gold" @click="submitFinder">SHOW MATCHED RACQUETS →</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  isOpen: Boolean
});

const emit = defineEmits(['close', 'recommend-cat']);

const level = ref('pro');
const preference = ref('power');

function submitFinder() {
  emit('recommend-cat', 'badminton');
  emit('close');
}
</script>
