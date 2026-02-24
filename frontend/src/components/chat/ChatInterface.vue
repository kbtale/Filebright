<script setup>
import { ref } from "vue";
import { Send, Bot, User, Loader2 } from "lucide-vue-next";
import { chatStore } from "../../stores/chatStore";

const query = ref("");

const handleSend = () => {
  if (!query.value.trim() || chatStore.isLoading) return;
  chatStore.sendMessage(query.value);
  query.value = "";
};
</script>

<template>
  <div class="chat-container">
    <div class="message-feed">
      <div v-if="chatStore.messages.length === 0" class="empty-chat">
        <Bot :size="48" />
        <h2>How can I help you today?</h2>
        <p>Ask anything about your uploaded documents.</p>
      </div>

      <div
        v-for="(msg, idx) in chatStore.messages"
        :key="idx"
        class="message-wrapper"
        :class="msg.role"
      >
        <div class="avatar">
          <Bot
            v-if="msg.role === 'assistant' || msg.role === 'system'"
            :size="18"
          />
          <User v-else :size="18" />
        </div>
        <div class="message-bubble glass">
          <div class="bubble-content">{{ msg.content }}</div>
        </div>
      </div>

      <div v-if="chatStore.isLoading" class="message-wrapper assistant">
        <div class="avatar">
          <Bot :size="18" />
        </div>
        <div class="message-bubble glass loading">
          <Loader2 class="spin" :size="18" />
        </div>
      </div>
    </div>

    <div class="input-area">
      <div class="input-glass glass">
        <input
          v-model="query"
          placeholder="Ask a question..."
          @keyup.enter="handleSend"
        />
        <button
          class="send-btn"
          @click="handleSend"
          :disabled="!query.trim() || chatStore.isLoading"
        >
          <Send :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.chat-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  height: 100%;
  max-width: 900px;
  width: 100%;
}

.message-feed {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;

  .empty-chat {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: var(--text-muted);
    text-align: center;
    h2 {
      margin-top: 1.5rem;
      color: var(--text-main);
    }
    p {
      margin-top: 0.5rem;
    }
  }
}

.message-wrapper {
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;

  &.user {
    flex-direction: row-reverse;
    .message-bubble {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
  }

  .avatar {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-sm);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
  }

  .message-bubble {
    max-width: 70%;
    padding: 0.85rem 1.15rem;
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    line-height: 1.5;

    &.loading {
      padding: 0.75rem;
      background: transparent;
      border: none;
      color: var(--primary-color);
    }
  }
}

.input-area {
  padding: 2rem 0;

  .input-glass {
    display: flex;
    align-items: center;
    padding: 0.4rem 0.4rem 0.4rem 1.25rem;
    border-radius: var(--radius-md);

    input {
      flex: 1;
      background: transparent;
      border: none;
      color: var(--text-main);
      padding: 0.75rem 0;
      outline: none;
      font-size: 1rem;
      &::placeholder {
        color: var(--text-muted);
      }
    }

    .send-btn {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-sm);
      background: var(--primary-color);
      color: #fff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition-smooth);

      &:hover:not(:disabled) {
        transform: translateY(-1px);
        filter: brightness(1.1);
      }

      &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }
    }
  }
}

.spin {
  animation: spin 1s linear infinite;
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
