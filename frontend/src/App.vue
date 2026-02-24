<script setup>
import { FileText, MessageSquare, Menu as MenuIcon } from "lucide-vue-next";
import UploadZone from "./components/UploadZone.vue";
import DocumentList from "./components/DocumentList.vue";
</script>

<template>
  <div class="app-container">
    <aside class="sidebar glass">
      <div class="brand">
        <h1>Filebright</h1>
      </div>
      <nav>
        <div class="nav-item active">
          <FileText :size="20" />
          <span>Documents</span>
        </div>
        <div class="nav-item">
          <MessageSquare :size="20" />
          <span>Chat</span>
        </div>
      </nav>
      <div class="sidebar-footer">
        <DocumentList />
      </div>
    </aside>

    <main class="content">
      <header class="top-bar">
        <div class="search-placeholder"></div>
        <div class="user-profile">
          <div class="avatar">JD</div>
        </div>
      </header>

      <section class="viewport">
        <UploadZone />
      </section>
    </main>
  </div>
</template>

<style lang="scss">
.app-container {
  display: flex;
  height: 100vh;
  width: 100vw;
}

.sidebar {
  width: 280px;
  display: flex;
  flex-direction: column;
  padding: 2rem 1.5rem;
  z-index: 10;

  .brand h1 {
    font-size: 1.5rem;
    background: linear-gradient(
      to right,
      var(--text-main),
      var(--primary-color)
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 3rem;
  }

  nav {
    flex: 1;
    .nav-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      color: var(--text-muted);
      cursor: pointer;
      margin-bottom: 0.5rem;
      transition: var(--transition-smooth);

      &.active,
      &:hover {
        color: var(--text-main);
        background: hsla(260, 80%, 70%, 0.1);
      }
    }
  }
}

.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 2rem 3rem;

  .top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4rem;

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--surface-border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      border: 1px solid var(--glass-border);
    }
  }

  .viewport {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }
}
</style>
