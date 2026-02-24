<script setup>
import { ref } from "vue";
import { UploadCloud, File, X } from "lucide-vue-next";
import { documentStore } from "../stores/documentStore";

const isDragging = ref(false);
const fileInput = ref(null);

const handleDrop = (e) => {
  isDragging.value = false;
  const files = e.dataTransfer.files;
  uploadFiles(files);
};

const handleFileSelect = (e) => {
  const files = e.target.files;
  uploadFiles(files);
};

const uploadFiles = (files) => {
  Array.from(files).forEach((file) => {
    documentStore.uploadFile(file);
  });
};
</script>

<template>
  <div
    class="upload-zone glass"
    :class="{ 'is-dragging': isDragging }"
    @dragover.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @drop.prevent="handleDrop"
    @click="fileInput.click()"
  >
    <input
      type="file"
      ref="fileInput"
      multiple
      hidden
      @change="handleFileSelect"
    />

    <div class="upload-content">
      <div class="icon-wrapper">
        <UploadCloud :size="48" class="upload-icon" />
      </div>
      <h3>Upload Documents</h3>
      <p>Drag and drop your PDF or TXT files here, or click to browse</p>
      <div class="limits">Max file size: 100MB</div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.upload-zone {
  width: 100%;
  max-width: 600px;
  height: 400px;
  border: 2px dashed var(--surface-border);
  border-radius: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: var(--transition-smooth);

  &:hover,
  &.is-dragging {
    border-color: var(--primary-color);
    background: hsla(260, 80%, 70%, 0.05);
    transform: translateY(-4px);

    .upload-icon {
      color: var(--primary-color);
      transform: scale(1.1);
    }
  }

  .upload-content {
    text-align: center;
    padding: 2rem;

    .icon-wrapper {
      margin-bottom: 1.5rem;
      .upload-icon {
        color: var(--text-muted);
        transition: var(--transition-smooth);
      }
    }

    h3 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    p {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .limits {
      font-size: 0.8rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
  }
}
</style>
