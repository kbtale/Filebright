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

<style lang="scss">
.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  margin-top: 1rem;
  background: transparent;
  border: 1px solid var(--glass-border);
  border-radius: 12px;
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-smooth);

  &:hover {
    background: hsla(0, 80%, 60%, 0.1);
    color: #ef4444;
    border-color: hsla(0, 80%, 60%, 0.2);
  }
}
</style>
