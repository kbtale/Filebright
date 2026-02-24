<script setup>
import { onMounted, ref } from "vue";
import { FileText, MessageSquare, LogOut } from "lucide-vue-next";
import UploadZone from "../components/UploadZone.vue";
import DocumentList from "../components/DocumentList.vue";
import ChatInterface from "../components/chat/ChatInterface.vue";
import { authStore } from "../stores/authStore";
import { documentStore } from "../stores/documentStore";
import { useRouter } from "vue-router";

const router = useRouter();
const activeTab = ref("documents");

const handleLogout = () => {
  authStore.logout();
  router.push("/auth");
};

onMounted(() => {
  documentStore.fetchDocuments();
  documentStore.startPolling();
});
</script>

<template>
  <div class="app-container">
    <aside class="sidebar glass">
      <div class="brand">
        <h1>Filebright</h1>
      </div>
      <nav>
        <div
          class="nav-item"
          :class="{ active: activeTab === 'documents' }"
          @click="activeTab = 'documents'"
        >
          <FileText :size="20" />
          <span>Documents</span>
        </div>
        <div
          class="nav-item"
          :class="{ active: activeTab === 'chat' }"
          @click="activeTab = 'chat'"
        >
          <MessageSquare :size="20" />
          <span>Chat</span>
        </div>
      </nav>
      <div class="sidebar-footer">
        <DocumentList />
        <button class="logout-btn" @click="handleLogout">
          <LogOut :size="18" />
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <main class="content">
      <header class="top-bar">
        <div class="search-placeholder"></div>
        <div class="user-profile">
          <div class="avatar">
            {{ authStore.user?.name?.substring(0, 2).toUpperCase() || "JD" }}
          </div>
        </div>
      </header>

      <section class="viewport">
        <UploadZone v-if="activeTab === 'documents'" />
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
}

.sidebar {
  width: 320px;
  display: flex;
  flex-direction: column;
  padding: 2rem 1.5rem;
  border-right: 1px solid var(--glass-border);

  .brand h1 {
    font-size: 1.5rem;
    margin-bottom: 3rem;
    color: var(--text-main);
  }

  nav {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    .nav-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.85rem 1.25rem;
      border-radius: var(--radius-md);
      color: var(--text-muted);
      cursor: pointer;
      transition: var(--transition-smooth);

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

  .sidebar-footer {
    margin-top: auto;
  }
}

.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;

  .top-bar {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 3rem;
    border-bottom: 1px solid var(--glass-border);

    .user-profile {
      .avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
      }
    }
  }

  .viewport {
    flex: 1;
    padding: 3rem;
    overflow-y: auto;
    display: flex;
    justify-content: center;
  }
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1.25rem;
  margin-top: 2rem;
  background: transparent;
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-md);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-smooth);

  &:hover {
    background: rgba(248, 113, 113, 0.1);
    color: #f87171;
    border-color: rgba(248, 113, 113, 0.2);
  }
}
</style>
