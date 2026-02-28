<script setup>
import { ref, computed } from "vue";
import {
  FileText,
  Loader2,
  CheckCircle2,
  AlertCircle,
  Trash2,
  Pencil,
  Search,
  X,
  Check,
} from "lucide-vue-next";
import { documentStore } from "../stores/documentStore";

const props = defineProps({
  collapsed: {
    type: Boolean,
    default: false,
  },
});

/* State */
const showDeleteConfirm = ref(null); // doc id being confirmed
const editingId = ref(null);
const editingName = ref("");
const isDeleting = ref(false);
const searchFocused = ref(false);

/* Computed */
const displayedDocs = computed(() => documentStore.filteredDocuments);

/* Helpers */
const getStatusIcon = (status) => {
  switch (status) {
    case "processing":
    case "parsing":
    case "vectorizing":
    case "indexing":
      return Loader2;
    case "completed":
      return CheckCircle2;
    case "failed":
      return AlertCircle;
    default:
      return FileText;
  }
};

/* Delete */
const confirmDelete = (id) => {
  showDeleteConfirm.value = id;
};

const cancelDelete = () => {
  showDeleteConfirm.value = null;
};

const executeDelete = async (id) => {
  isDeleting.value = true;
  await documentStore.deleteDocument(id);
  showDeleteConfirm.value = null;
  isDeleting.value = false;
};

/* Rename */
const startRename = (doc) => {
  editingId.value = doc.id;
  editingName.value = doc.filename;
};

const cancelRename = () => {
  editingId.value = null;
  editingName.value = "";
};

const submitRename = async () => {
  if (!editingName.value.trim() || !editingId.value) return;
  await documentStore.renameDocument(editingId.value, editingName.value.trim());
  editingId.value = null;
  editingName.value = "";
};
</script>

<template>
  <div class="document-list" :class="{ 'is-collapsed': collapsed }">
    <!-- Search Bar -->
    <div class="search-wrapper" :class="{ focused: searchFocused }">
      <Search :size="14" class="search-icon" />
      <input
        v-model="documentStore.searchQuery"
        type="text"
        placeholder="Filter documents…"
        class="search-input"
        @focus="searchFocused = true"
        @blur="searchFocused = false"
        id="document-search-input"
      />
      <button
        v-if="documentStore.searchQuery"
        class="clear-btn"
        @click="documentStore.searchQuery = ''"
      >
        <X :size="12" />
      </button>
    </div>

    <div class="list-header">
      <h4>Recent Documents</h4>
      <span v-if="displayedDocs.length" class="doc-count">{{
        displayedDocs.length
      }}</span>
    </div>

    <!-- Empty search results -->
    <div
      v-if="
        documentStore.documents.length > 0 && displayedDocs.length === 0
      "
      class="empty-state"
    >
      No matches found
    </div>

    <!-- No documents at all -->
    <div
      v-if="documentStore.documents.length === 0"
      class="empty-state"
    >
      No documents yet
    </div>

    <!-- Document Items -->
    <div v-if="displayedDocs.length > 0" class="items">
      <div
        v-for="doc in displayedDocs"
        :key="doc.id"
        class="doc-item"
        :class="[doc.status, { 'is-confirming': showDeleteConfirm === doc.id }]"
        :title="collapsed ? doc.filename : ''"
      >
        <!-- Delete confirmation overlay -->
        <transition name="confirm-fade">
          <div
            v-if="showDeleteConfirm === doc.id"
            class="delete-confirm-overlay"
          >
            <span class="confirm-text">Delete this file?</span>
            <div class="confirm-actions">
              <button
                class="confirm-yes"
                @click.stop="executeDelete(doc.id)"
                :disabled="isDeleting"
                id="confirm-delete-btn"
              >
                <Check :size="14" />
              </button>
              <button
                class="confirm-no"
                @click.stop="cancelDelete"
                id="cancel-delete-btn"
              >
                <X :size="14" />
              </button>
            </div>
          </div>
        </transition>

        <!-- Normal item content -->
        <div class="doc-icon">
          <component
            :is="getStatusIcon(doc.status)"
            :size="16"
            :class="{ spin: ['processing', 'parsing', 'vectorizing', 'indexing'].includes(doc.status) }"
          />
        </div>

        <div class="doc-info">
          <!-- Inline rename mode -->
          <template v-if="editingId === doc.id">
            <div class="rename-row">
              <input
                v-model="editingName"
                class="rename-input"
                @keyup.enter="submitRename"
                @keyup.escape="cancelRename"
                ref="renameInput"
                autofocus
              />
              <button class="rename-ok" @click="submitRename">
                <Check :size="12" />
              </button>
              <button class="rename-cancel" @click="cancelRename">
                <X :size="12" />
              </button>
            </div>
          </template>

          <!-- Normal display mode -->
          <template v-else>
            <div class="filename">{{ doc.filename }}</div>
            <div class="status-row">
              <span class="status-badge" :class="doc.status">
                {{ doc.status }}
              </span>
            </div>
          </template>
        </div>

        <!-- Action buttons (only when not in confirm mode) -->
        <div
          v-if="showDeleteConfirm !== doc.id && editingId !== doc.id"
          class="doc-actions"
        >
          <button
            class="action-btn rename"
            @click.stop="startRename(doc)"
            title="Rename"
          >
            <Pencil :size="13" />
          </button>
          <button
            class="action-btn delete"
            @click.stop="confirmDelete(doc.id)"
            title="Delete"
          >
            <Trash2 :size="13" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.document-list {
  margin-top: 1.5rem;
  overflow: hidden;
  max-height: 600px;
  transition:
    max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
    margin 0.3s ease;

  &.is-collapsed {
    max-height: 0;
    opacity: 0;
    margin-top: 0;
    transition:
      max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
      opacity 0.15s ease 0.25s,
      margin 0.3s ease;

    .doc-info {
      opacity: 0;
      max-width: 0;
    }

    .doc-actions {
      opacity: 0;
      width: 0;
    }

    .search-wrapper {
      opacity: 0;
      max-height: 0;
      margin-bottom: 0;
      padding: 0;
    }
  }
}

/* Search */
.search-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.75rem;
  margin: 0 0.5rem 0.75rem;
  border-radius: var(--radius-sm);
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  transition: all 0.25s ease;
  overflow: hidden;
  max-height: 50px;

  &.focused {
    border-color: hsla(175, 75%, 48%, 0.25);
    background: rgba(255, 255, 255, 0.05);
  }

  .search-icon {
    color: var(--text-muted);
    flex-shrink: 0;
    opacity: 0.6;
  }

  .search-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: var(--text-main);
    font-size: 0.8rem;
    font-family: inherit;
    min-width: 0;

    &::placeholder {
      color: var(--text-muted);
      opacity: 0.5;
    }
  }

  .clear-btn {
    background: rgba(255, 255, 255, 0.08);
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;

    &:hover {
      background: rgba(255, 255, 255, 0.15);
      color: var(--text-main);
    }
  }
}

/* Header */
.list-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
  margin-bottom: 0.75rem;

  h4 {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: var(--text-muted);
    letter-spacing: 0.1em;
  }

  .doc-count {
    font-size: 0.65rem;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.06);
    padding: 0.1rem 0.45rem;
    border-radius: 10px;
    font-weight: 500;
  }
}

.empty-state {
  padding: 1rem;
  font-size: 0.8rem;
  color: var(--text-muted);
  font-style: italic;
  text-align: center;
}

/* Items List */
.items {
  max-height: 340px;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0 0.25rem;
}

.doc-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.6rem 0.75rem;
  border-radius: var(--radius-sm);
  transition: all 0.2s ease;
  margin-bottom: 0.15rem;
  position: relative;

  &:hover {
    background: rgba(255, 255, 255, 0.04);

    .doc-actions {
      opacity: 1;
      transform: translateX(0);
    }
  }

  &.is-confirming {
    background: rgba(var(--color-danger-rgb), 0.06);
    border: 1px solid rgba(var(--color-danger-rgb), 0.12);
  }

  .doc-icon {
    color: var(--text-muted);
    flex-shrink: 0;

    .spin {
      animation: spin 2s linear infinite;
    }
  }

  .doc-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    transition: opacity 0.2s ease, max-width 0.3s ease;
    opacity: 1;
    overflow: hidden;
  }

  .filename {
    font-size: 0.85rem;
    color: var(--text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .status-badge {
    font-size: 0.65rem;
    text-transform: capitalize;
    color: var(--text-muted);
    font-weight: 600;

    &.parsing, &.vectorizing, &.indexing {
      color: var(--primary-color);
    }
    &.completed {
      color: var(--color-success);
    }
    &.failed {
      color: var(--color-danger);
    }
  }

  &.completed .doc-icon {
    color: var(--color-success);
  }
  &.failed .doc-icon {
    color: var(--color-danger);
  }
  &.processing .doc-icon,
  &.parsing .doc-icon,
  &.vectorizing .doc-icon,
  &.indexing .doc-icon {
    color: var(--primary-color);
  }
}

/* Action Buttons */
.doc-actions {
  display: flex;
  gap: 0.25rem;
  opacity: 0;
  transform: translateX(4px);
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.action-btn {
  width: 26px;
  height: 26px;
  border-radius: var(--radius-sm);
  background: transparent;
  border: 1px solid transparent;
  color: var(--text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;

  &.rename:hover {
    background: rgba(255, 255, 255, 0.08);
    color: var(--primary-color);
    border-color: hsla(175, 75%, 48%, 0.2);
  }

  &.delete:hover {
    background: rgba(var(--color-danger-rgb), 0.1);
    color: var(--color-danger);
    border-color: rgba(var(--color-danger-rgb), 0.2);
  }
}

/* Delete Confirm Overlay */
.delete-confirm-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.92);
  backdrop-filter: blur(4px);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 0.75rem;
  z-index: 5;

  .confirm-text {
    font-size: 0.8rem;
    color: var(--color-danger);
    font-weight: 500;
  }

  .confirm-actions {
    display: flex;
    gap: 0.35rem;
  }

  .confirm-yes,
  .confirm-no {
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .confirm-yes {
    background: rgba(var(--color-danger-rgb), 0.15);
    color: var(--color-danger);
    border-color: rgba(var(--color-danger-rgb), 0.25);

    &:hover {
      background: rgba(var(--color-danger-rgb), 0.3);
    }

    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
  }

  .confirm-no {
    background: rgba(255, 255, 255, 0.06);
    color: var(--text-muted);

    &:hover {
      background: rgba(255, 255, 255, 0.12);
      color: var(--text-main);
    }
  }
}

/* Inline Rename */
.rename-row {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  width: 100%;
}

.rename-input {
  flex: 1;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid hsla(175, 75%, 48%, 0.25);
  border-radius: 4px;
  padding: 0.25rem 0.45rem;
  color: var(--text-main);
  font-size: 0.82rem;
  font-family: inherit;
  outline: none;
  min-width: 0;

  &:focus {
    border-color: hsla(175, 75%, 48%, 0.5);
    background: rgba(255, 255, 255, 0.08);
  }
}

.rename-ok,
.rename-cancel {
  width: 22px;
  height: 22px;
  border-radius: 4px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.rename-ok {
  background: hsla(175, 75%, 48%, 0.15);
  color: var(--primary-color);

  &:hover {
    background: hsla(175, 75%, 48%, 0.3);
  }
}

.rename-cancel {
  background: rgba(255, 255, 255, 0.06);
  color: var(--text-muted);

  &:hover {
    background: rgba(255, 255, 255, 0.12);
    color: var(--text-main);
  }
}

/* Transitions */
.confirm-fade-enter-active,
.confirm-fade-leave-active {
  transition: all 0.2s ease;
}

.confirm-fade-enter-from,
.confirm-fade-leave-to {
  opacity: 0;
  transform: scale(0.96);
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
