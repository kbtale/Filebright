<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { authStore } from "../stores/authStore";
import { apiClient } from "../utils/apiClient";
import { User, Lock, Mail, ArrowRight, Loader2 } from "lucide-vue-next";

const router = useRouter();
const isLogin = ref(true);
const isLoading = ref(false);
const error = ref("");

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const handleSubmit = async () => {
  error.value = "";
  isLoading.value = true;

  const endpoint = isLogin.value ? "/login" : "/register";

  try {
    const data = await apiClient.post(endpoint, form);
    if (data?.token) {
      authStore.setToken(data.token);
      authStore.setUser(data.user);
      router.push("/dashboard");
    }
  } catch (err) {
    error.value = err.message || "Authentication failed";
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="auth-page">
    <div class="auth-card glass">
      <div class="auth-header">
        <h1>Filebright</h1>
        <p>{{ isLogin ? "Welcome back" : "Create your account" }}</p>
      </div>

      <form @submit.prevent="handleSubmit" class="auth-form">
        <div v-if="!isLogin" class="input-group">
          <User :size="18" />
          <input
            v-model="form.name"
            type="text"
            placeholder="Full Name"
            required
          />
        </div>

        <div class="input-group">
          <Mail :size="18" />
          <input
            v-model="form.email"
            type="email"
            placeholder="Email Address"
            required
          />
        </div>

        <div class="input-group">
          <Lock :size="18" />
          <input
            v-model="form.password"
            type="password"
            placeholder="Password"
            required
          />
        </div>

        <div v-if="!isLogin" class="input-group">
          <Lock :size="18" />
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            required
          />
        </div>

        <div v-if="error" class="error-msg">
          {{ error }}
        </div>

        <button type="submit" :disabled="isLoading" class="submit-btn">
          <Loader2 v-if="isLoading" class="spin" :size="20" />
          <template v-else>
            <span>{{ isLogin ? "Sign In" : "Sign Up" }}</span>
            <ArrowRight :size="18" />
          </template>
        </button>
      </form>

      <div class="auth-footer">
        <button @click="isLogin = !isLogin" class="toggle-btn">
          {{
            isLogin
              ? "Don't have an account? Sign up"
              : "Already have an account? Sign in"
          }}
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.auth-page {
  height: 100vh;
  width: 100vw;
  display: flex;
  justify-content: center;
  align-items: center;
  background:
    radial-gradient(
      circle at top right,
      hsla(260, 80%, 70%, 0.15),
      transparent 40%
    ),
    radial-gradient(
      circle at bottom left,
      hsla(230, 80%, 70%, 0.1),
      transparent 40%
    );
}

.auth-card {
  width: 100%;
  max-width: 440px;
  padding: 3rem;
  border-radius: 32px;
  text-align: center;

  .auth-header {
    margin-bottom: 2.5rem;
    h1 {
      font-size: 2.5rem;
      background: linear-gradient(
        to right,
        var(--text-main),
        var(--primary-color)
      );
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }
    p {
      color: var(--text-muted);
    }
  }
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;

  .input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: hsla(230, 20%, 20%, 0.4);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 0 1.25rem;
    transition: var(--transition-smooth);
    color: var(--text-muted);

    &:focus-within {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 4px var(--primary-glow);
      color: var(--primary-color);
    }

    input {
      flex: 1;
      background: transparent;
      border: none;
      padding: 1rem;
      color: var(--text-main);
      font-family: inherit;
      outline: none;

      &::placeholder {
        color: var(--text-muted);
      }
    }
  }
}

.submit-btn {
  margin-top: 1rem;
  padding: 1rem;
  background: var(--primary-color);
  color: var(--bg-color);
  border: none;
  border-radius: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  cursor: pointer;
  transition: var(--transition-smooth);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--primary-glow);
  }

  &:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
}

.error-msg {
  color: #ef4444;
  font-size: 0.85rem;
  background: hsla(0, 80%, 60%, 0.1);
  padding: 0.75rem;
  border-radius: 8px;
  border: 1px solid hsla(0, 80%, 60%, 0.2);
}

.auth-footer {
  margin-top: 2rem;
  .toggle-btn {
    background: transparent;
    border: none;
    color: var(--text-muted);
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition-smooth);
    &:hover {
      color: var(--text-main);
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
