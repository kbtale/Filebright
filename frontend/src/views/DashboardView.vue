<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import {
  FileText,
  MessageSquare,
  ChevronLeft,
  ChevronRight,
  Menu,
  X,
} from "lucide-vue-next";
import UploadZone from "../components/UploadZone.vue";
import DocumentList from "../components/DocumentList.vue";
import ChatInterface from "../components/chat/ChatInterface.vue";
import UserProfileMenu from "../components/UserProfileMenu.vue";
import EmptyState from "../components/EmptyState.vue";
import { authStore } from "../stores/authStore";
import { documentStore } from "../stores/documentStore";
import { notificationStore } from "../stores/notificationStore";
import { useRouter } from "vue-router";

const router = useRouter();
const activeTab = ref("documents");
const isSidebarCollapsed = ref(false);
const isMobileMenuOpen = ref(false);
const isMobile = ref(false);

const hasDocuments = computed(() => documentStore.documents.length > 0);

const updateMobileStatus = () => {
  isMobile.value = window.innerWidth <= 768;
};

const handleLogout = () => {
  authStore.logout();
  router.push("/auth");
};

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

onMounted(() => {
  documentStore.fetchDocuments();
  documentStore.startPolling();
  updateMobileStatus();
  window.addEventListener("resize", updateMobileStatus);
});

onUnmounted(() => {
  window.removeEventListener("resize", updateMobileStatus);
});
</script>

<template>
  <div
    class="app-container"
    :class="{
      'sidebar-collapsed': isSidebarCollapsed,
      'mobile-menu-open': isMobileMenuOpen,
    }"
  >
    <!-- Mobile Backdrop -->
    <div
      v-if="isMobileMenuOpen"
      class="mobile-backdrop"
      @click="toggleMobileMenu"
    ></div>
    <aside class="sidebar glass" :class="{ collapsed: isSidebarCollapsed }">
      <div class="brand">
        <span class="label brand-title">Filebright</span>
        <button class="toggle-btn desktop-only" @click="toggleSidebar">
          <ChevronLeft v-if="!isSidebarCollapsed" :size="18" />
          <ChevronRight v-else :size="18" />
        </button>
      </div>

      <nav>
        <div
          class="nav-item"
          :class="{ active: activeTab === 'documents' }"
          @click="
            activeTab = 'documents';
            isMobileMenuOpen = false;
          "
          title="Documents"
        >
          <FileText :size="20" />
          <span class="label">Documents</span>
        </div>

        <div
          class="nav-item"
          :class="{ active: activeTab === 'chat' }"
          @click="
            activeTab = 'chat';
            isMobileMenuOpen = false;
          "
          title="Chat"
        >
          <MessageSquare :size="20" />
          <span class="label">Chat</span>
        </div>
      </nav>

      <div class="sidebar-footer">
        <DocumentList :collapsed="isSidebarCollapsed && !isMobile" />
      </div>
    </aside>

    <main class="content">
      <header class="top-bar">
        <div class="left-section">
          <button class="mobile-menu-btn mobile-only" @click="toggleMobileMenu">
            <Menu v-if="!isMobileMenuOpen" :size="24" />
            <X v-else :size="24" />
          </button>
          <div class="search-placeholder"></div>
          <transition name="slide-fade-left">
            <span class="top-bar-title" v-if="isSidebarCollapsed">Filebright</span>
          </transition>
        </div>
        <UserProfileMenu @logout="handleLogout" />
      </header>

      <section class="viewport">
        <template v-if="activeTab === 'documents'">
          <EmptyState v-if="!hasDocuments && !documentStore.isLoading" />
          <UploadZone v-else />
        </template>
        <ChatInterface v-else />
      </section>
    </main>
  </div>
</template>

<style lang="scss" scoped>
.app-container {
  display: flex;
  height: 100vh;
  width: 100vw;
  position: relative;
  z-index: 1;
  --sidebar-width: 320px;

  &.sidebar-collapsed {
    --sidebar-width: 80px;
  }
}

.mobile-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 99;
}

.sidebar {
  width: var(--sidebar-width);
  display: flex;
  flex-direction: column;
  padding: 2rem 1rem;
  border-right: 1px solid var(--glass-border);
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;

  // All label text - always in the DOM, animated via CSS
  .label {
    overflow: hidden;
    white-space: nowrap;
    width: 100%;
    opacity: 1;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: left center;
  }

  svg {
    flex-shrink: 0;
  }

  .brand {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 3rem;
    padding: 0 0.5rem;
    height: 32px;
    position: relative;

    .brand-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-main);
      overflow: hidden;
      white-space: nowrap;
      width: 100%;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform-origin: left center;
    }


    .toggle-btn {
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-sm);
      color: var(--text-muted);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.25rem;
      flex-shrink: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

      &:hover {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.1);
      }
    }
  }

  nav {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    .nav-item {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      padding: 0.85rem 1.5rem;
      border-radius: var(--radius-md);
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      min-width: 0;
      overflow: hidden;

      &:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
      }

      &.active {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--primary-color);
      }
    }
  }

  &.collapsed {
    padding: 2rem 0.5rem;

    .label,
    .brand-title {
      width: 0;
      opacity: 0;
      transform: scale(0);
    }

    nav .nav-item,
    .logout-btn,
    .brand {
      gap: 0;
    }
  }

  .sidebar-footer {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }
}


// Move global transitions to end of file
.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;

  .top-bar {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 2rem;
    border-bottom: 1px solid var(--glass-border);

    .left-section {
      display: flex;
      align-items: center;
      gap: 0.4rem;

      .top-bar-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        margin-left: 0.25rem;
      }
    }

  }

  .viewport {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
}



.mobile-only {
  display: none;
}

.mobile-menu-btn {
  background: transparent;
  border: none;
  color: var(--text-main);
  cursor: pointer;
  padding: 0.5rem;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
  .desktop-only {
    display: none;
  }

  .mobile-only {
    display: block;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 280px;
    z-index: 100;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .mobile-menu-open .sidebar {
    transform: translateX(0);
  }

  .app-container.sidebar-collapsed {
    --sidebar-width: 0px;
  }

  .content .top-bar {
    padding: 0 1rem;
  }

  .content .viewport {
    padding: 1rem;
  }
}
</style>

<style lang="css">
.slide-fade-left-enter-active,
.slide-fade-left-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-left-enter-from,
.slide-fade-left-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}
</style>
