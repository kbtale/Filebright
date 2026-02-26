<script setup>
import { FileText, Loader2, CheckCircle2, AlertCircle } from "lucide-vue-next";
import { documentStore } from "../stores/documentStore";

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false,
  },
});

const getStatusIcon = (status) => {
  switch (status) {
    case "processing":
      return Loader2;
    case "completed":
      return CheckCircle2;
    case "failed":
      return AlertCircle;
    default:
      return FileText;
  }
};
</script>

<template>
  <div class="document-list" :class="{ 'is-collapsed': collapsed }">
    <div class="list-header">
      <h4>Recent Documents</h4>
    </div>

    <div v-if="documentStore.documents.length === 0" class="empty-state">
      No documents yet
    </div>

    <div v-if="documentStore.documents.length > 0" class="items">
      <div
        v-for="doc in documentStore.documents"
        :key="doc.id"
        class="doc-item"
        :class="doc.status"
        :title="collapsed ? doc.filename : ''"
      >
        <div class="doc-icon">
          <component
            :is="getStatusIcon(doc.status)"
            :size="16"
            :class="{ spin: doc.status === 'processing' }"
          />
        </div>
        <div class="doc-info">
          <div class="filename">{{ doc.filename }}</div>
          <div class="status-text">{{ doc.status }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.document-list {
  margin-top: 2rem;
  overflow: hidden;
  max-height: 500px;
  transition:
    max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
    margin 0.3s ease;

  .list-header h4 {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-muted);
    letter-spacing: 0.1em;
    margin-bottom: 1rem;
    padding: 0 1rem;
  }

  .empty-state {
    padding: 1rem;
    font-size: 0.85rem;
    color: var(--text-muted);
    font-style: italic;
  }

  &.is-collapsed {
    max-height: 0;
    opacity: 0;
    margin-top: 0;
    transition:
      max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
      opacity 0.15s ease 0.25s,
      margin 0.3s ease;
  }

  .items {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;

    .doc-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      border-radius: var(--radius-sm);
      transition: var(--transition-smooth);
      margin-bottom: 0.25rem;

      &:hover {
        background: rgba(255, 255, 255, 0.05);
      }

      .doc-icon {
        color: var(--text-muted);
        &.spin {
          animation: spin 2s linear infinite;
        }
      }

      .doc-info {
        display: flex;
        flex-direction: column;
        transition: opacity 0.2s ease, max-width 0.3s ease;
        opacity: 1;
        max-width: 200px;
        overflow: hidden;
        white-space: nowrap;
      }

      .filename {
        font-size: 0.9rem;
        color: var(--text-main);
      }

      .status-text {
        font-size: 0.7rem;
        text-transform: capitalize;
        color: var(--text-muted);
      }

      &.completed .doc-icon {
        color: #10b981;
      }
      &.failed .doc-icon {
        color: #ef4444;
      }
      &.processing .doc-icon {
        color: var(--primary-color);
      }
    }
  }

  &.is-collapsed {
    max-height: 0;
    opacity: 0;
    margin-top: 0;

    .doc-info {
      opacity: 0;
      max-width: 0;
    }
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
