<script setup>
import { ref } from 'vue'
import { X, AlertCircle, CheckCircle, Info, ChevronDown, ChevronUp } from 'lucide-vue-next'
import { notificationStore } from '../../stores/notificationStore'

const props = defineProps({
  notification: {
    type: Object,
    required: true
  }
})

const showDetails = ref(false)

const getIcon = (type) => {
  switch (type) {
    case 'success': return CheckCircle
    case 'error': return AlertCircle
    default: return Info
  }
}
</script>

<template>
  <div class="toast-item" :class="notification.type">
    <div class="toast-main">
      <div class="toast-icon">
        <component :is="getIcon(notification.type)" :size="20" />
      </div>
      
      <div class="toast-content">
        <p class="toast-message">{{ notification.message }}</p>
        <button 
          v-if="notification.details" 
          @click="showDetails = !showDetails"
          class="details-toggle"
        >
          <span>{{ showDetails ? 'Hide Details' : 'View Details' }}</span>
          <component :is="showDetails ? ChevronUp : ChevronDown" :size="14" />
        </button>
      </div>

      <button @click="notificationStore.remove(notification.id)" class="close-btn">
        <X :size="16" />
      </button>
    </div>

    <transition name="expand">
      <div v-if="showDetails" class="toast-details">
        <pre>{{ notification.details }}</pre>
      </div>
    </transition>
  </div>
</template>

<style lang="scss" scoped>
.toast-item {
  width: 320px;
  border-radius: var(--radius-md);
  padding: 1rem;
  margin-bottom: 0.75rem;
  background: #1a1b1e;
  background: linear-gradient(135deg, rgba(30, 31, 35, 0.95), rgba(20, 21, 25, 0.95));
  backdrop-filter: blur(12px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  overflow: hidden;
  pointer-events: auto;
  z-index: 9999;

  &.error {
    border-left: 4px solid #f87171;
    .toast-icon { color: #f87171; }
  }
  &.success {
    border-left: 4px solid #34d399;
    .toast-icon { color: #34d399; }
  }
  &.info {
    border-left: 4px solid #60a5fa;
    .toast-icon { color: #60a5fa; }
  }
}

.toast-main {
  display: flex;
  gap: 0.85rem;
  align-items: flex-start;
}

.toast-icon {
  flex-shrink: 0;
  margin-top: 1px;
}

.toast-content {
  flex: 1;
  min-width: 0;
}

.toast-message {
  font-size: 0.9rem;
  font-weight: 500;
  color: #fff;
  line-height: 1.4;
  margin: 0;
}

.details-toggle {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0;
  margin-top: 0.4rem;
  cursor: pointer;
  
  &:hover {
    color: #fff;
  }
}

.close-btn {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.4);
  cursor: pointer;
  padding: 2px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: -2px;
  
  &:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
  }
}

.toast-details {
  margin-top: 0.75rem;
  padding: 0.75rem;
  background: rgba(0, 0, 0, 0.4);
  border-radius: var(--radius-sm);
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
  overflow-x: auto;
  border: 1px solid rgba(255, 255, 255, 0.05);
  
  pre {
    margin: 0;
    white-space: pre-wrap;
    word-break: break-all;
    font-family: monospace;
  }
}

.expand-enter-active,
.expand-leave-active {
  transition: all 0.25s ease;
  max-height: 250px;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
  margin-top: 0;
}
</style>
