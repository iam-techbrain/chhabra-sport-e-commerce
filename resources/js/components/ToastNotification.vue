<template>
  <div class="toast-container">
    <div v-for="t in toasts" :key="t.id" class="toast show">
      <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <span>{{ t.message }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  toasts: Array
});
</script>
