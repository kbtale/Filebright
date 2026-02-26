<script setup>
import { ref } from "vue";
import { FileText, UploadCloud } from "lucide-vue-next";
import { documentStore } from "../stores/documentStore";

const fileInput = ref(null);

const openFilePicker = () => {
  fileInput.value.click();
};

const handleFileSelect = (e) => {
  const files = e.target.files;
  Array.from(files).forEach((file) => {
    documentStore.uploadFile(file);
  });
  e.target.value = "";
};
</script>

<template>
  <div class="empty-state-container">
    <input
      type="file"
      ref="fileInput"
      multiple
      hidden
      @change="handleFileSelect"
    />

    <div class="empty-state-visual">
      <div class="icon-stage">
        <div class="glow-ring"></div>
        <div class="icon-circle glass">
          <FileText :size="40" class="main-icon" />
        </div>
      </div>
    </div>

    <div class="empty-state-content">
      <h2 class="empty-title">No documents yet</h2>
      <p class="empty-description">
        Upload your first PDF or TXT file to get started.<br />
        You can drag & drop or browse your files.
      </p>

      <button class="upload-cta glass" @click="openFilePicker">
        <UploadCloud :size="18" />
        <span>Upload your first document</span>
      </button>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.empty-state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  max-width: 520px;
  padding: 3rem 2rem;
  position: relative;
  animation: fadeInUp 0.6s ease-out;
}

/* Visual Stage */
.empty-state-visual {
  position: relative;
  width: 160px;
  height: 160px;
  margin-bottom: 2.5rem;
}

.icon-stage {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.glow-ring {
  position: absolute;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: radial-gradient(
    circle,
    hsla(175, 75%, 48%, 0.15) 0%,
    transparent 70%
  );
  animation: pulse-glow 3s ease-in-out infinite;
}

.icon-circle {
  width: 88px;
  height: 88px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);

  .main-icon {
    color: var(--primary-color);
    opacity: 0.85;
  }
}

/* Content */
.empty-state-content {
  text-align: center;
  position: relative;
  z-index: 1;
}

.empty-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 0.75rem;
  letter-spacing: -0.02em;
}

.empty-description {
  color: var(--text-muted);
  font-size: 0.95rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.upload-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.85rem 1.75rem;
  border-radius: var(--radius-md);
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid var(--glass-border);
  color: var(--primary-color);
  font-family: var(--font-body);
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    border-color: hsla(175, 75%, 48%, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.06);
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse-glow {
  0%,
  100% {
    transform: scale(1);
    opacity: 0.6;
  }
  50% {
    transform: scale(1.15);
    opacity: 1;
  }
}
</style>
