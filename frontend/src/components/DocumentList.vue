<script setup>
import { FileText, Loader2, CheckCircle2, AlertCircle } from "lucide-vue-next";
import { documentStore } from "../stores/documentStore";

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
  <div class="document-list">
    <div class="list-header">
      <h4>Recent Documents</h4>
    </div>

    <div v-if="documentStore.documents.length === 0" class="empty-state">
      No documents yet
    </div>

    <div v-else class="items">
      <div
        v-for="doc in documentStore.documents"
        :key="doc.id"
        class="doc-item"
        :class="doc.status"
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

  .items {
    max-height: 400px;
    overflow-y: auto;

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

      .filename {
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
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
