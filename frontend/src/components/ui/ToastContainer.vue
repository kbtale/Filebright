<script setup>
import { notificationState } from '../../stores/notificationStore'
import ToastNotification from './ToastNotification.vue'
</script>

<template>
  <div class="toast-container" id="global-toast-container">
    <transition-group name="list">
      <ToastNotification 
        v-for="notification in notificationState.notifications" 
        :key="notification.id"
        :notification="notification"
      />
    </transition-group>
  </div>
</template>

<style lang="scss" scoped>
.toast-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 10000;
  display: flex;
  flex-direction: column-reverse;
  gap: 12px;
  pointer-events: none;
}

/* Animations */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.list-enter-from {
  opacity: 0;
  transform: translateX(50px) scale(0.9);
}

.list-leave-to {
  opacity: 0;
  transform: scale(0.95);
  filter: blur(4px);
}

.list-move {
  transition: transform 0.4s ease;
}
</style>
