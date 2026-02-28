<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { LogOut, User, ChevronDown, Settings } from "lucide-vue-next";
import { authStore } from "../stores/authStore";

const emit = defineEmits(["logout"]);

const isOpen = ref(false);
const menuRef = ref(null);

const toggleMenu = () => {
  isOpen.value = !isOpen.value;
};

const handleLogout = () => {
  isOpen.value = false;
  emit("logout");
};

const handleClickOutside = (e) => {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div class="profile-menu" ref="menuRef">
    <button class="profile-trigger" @click="toggleMenu" id="user-profile-btn">
      <div class="avatar">
        {{ authStore.user?.name?.substring(0, 2).toUpperCase() || (authStore.isInitializing ? '' : '?') }}
      </div>
      <div class="user-brief">
        <span class="user-name">{{
          authStore.user?.name || (authStore.isInitializing ? 'Loading...' : 'User')
        }}</span>
        <span class="user-email">{{
          authStore.user?.email || ''
        }}</span>
      </div>
      <ChevronDown
        :size="14"
        class="chevron"
        :class="{ rotated: isOpen }"
      />
    </button>

    <transition name="dropdown">
      <div v-if="isOpen" class="dropdown-panel glass">
        <div class="dropdown-header">
          <div class="avatar-lg">
            {{ authStore.user?.name?.substring(0, 2).toUpperCase() || (authStore.isInitializing ? '' : '?') }}
          </div>
          <div class="user-details">
            <span class="name">{{ authStore.user?.name || 'User' }}</span>
            <span class="email">{{
              authStore.user?.email || ''
            }}</span>
          </div>
        </div>

        <div class="dropdown-divider"></div>

        <div class="dropdown-items">
          <button class="dropdown-item" id="profile-settings-btn" disabled>
            <Settings :size="16" />
            <span>Settings</span>
            <span class="coming-soon">Soon</span>
          </button>

          <button
            class="dropdown-item danger"
            @click="handleLogout"
            id="profile-logout-btn"
          >
            <LogOut :size="16" />
            <span>Logout</span>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<style lang="scss" scoped>
.profile-menu {
  position: relative;
}

.profile-trigger {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.4rem 0.6rem 0.4rem 0.4rem;
  border-radius: var(--radius-md);
  background: transparent;
  border: 1px solid transparent;
  color: var(--text-main);
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: inherit;

  &:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: var(--glass-border);
  }

  .avatar {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-sm);
    background: linear-gradient(
      135deg,
      hsla(175, 75%, 48%, 0.2),
      hsla(272, 55%, 50%, 0.2)
    );
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary-color);
    flex-shrink: 0;
  }

  .user-brief {
    display: flex;
    flex-direction: column;
    text-align: left;
    min-width: 0;

    .user-name {
      font-size: 0.85rem;
      font-weight: 600;
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .user-email {
      font-size: 0.7rem;
      color: var(--text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }

  .chevron {
    color: var(--text-muted);
    transition: transform 0.25s ease;
    flex-shrink: 0;

    &.rotated {
      transform: rotate(180deg);
    }
  }
}

/* Dropdown */
.dropdown-panel {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 260px;
  border-radius: var(--radius-md);
  padding: 0.75rem;
  z-index: 200;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.06);
}

.dropdown-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;

  .avatar-lg {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-sm);
    background: linear-gradient(
      135deg,
      hsla(175, 75%, 48%, 0.25),
      hsla(272, 55%, 50%, 0.25)
    );
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--primary-color);
    flex-shrink: 0;
  }

  .user-details {
    display: flex;
    flex-direction: column;
    min-width: 0;

    .name {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--text-main);
    }

    .email {
      font-size: 0.75rem;
      color: var(--text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
}

.dropdown-divider {
  height: 1px;
  background: var(--glass-border);
  margin: 0.5rem 0;
}

.dropdown-items {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.6rem 0.75rem;
  border-radius: var(--radius-sm);
  background: transparent;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  font-family: inherit;
  font-size: 0.85rem;
  transition: all 0.2s ease;
  width: 100%;
  text-align: left;

  &:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.06);
    color: var(--text-main);
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  &.danger:hover {
    background: rgba(var(--color-danger-rgb), 0.1);
    color: var(--color-danger);
  }

  .coming-soon {
    margin-left: auto;
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.06);
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    opacity: 0.7;
  }
}

/* Transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  transform-origin: top right;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-4px);
}

/* Mobile */
@media (max-width: 768px) {
  .user-brief {
    display: none;
  }

  .chevron {
    display: none;
  }

  .profile-trigger {
    padding: 0.3rem;
  }
}
</style>
