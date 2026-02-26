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

const loginForm = reactive({
  email: "",
  password: "",
});

const registerForm = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const toggleMode = () => {
  isLogin.value = !isLogin.value;
  // Sync emails when toggling
  if (isLogin.value) {
    loginForm.email = registerForm.email;
    loginForm.password = "";
  } else {
    registerForm.email = loginForm.email;
    registerForm.password = "";
    registerForm.password_confirmation = "";
  }
  error.value = "";
};

const handleSubmit = async () => {
  error.value = "";
  isLoading.value = true;

  const endpoint = isLogin.value ? "/login" : "/register";
  const payload = isLogin.value ? { ...loginForm } : { ...registerForm };

  try {
    const data = await apiClient.post(endpoint, payload);
    if (data?.token) {
      authStore.setToken(data.token);
      authStore.setUser(data.user);
      router.push("/dashboard");
    }
  } catch (err) {
    if (err.errors) {
      const firstErrorKey = Object.keys(err.errors)[0];
      error.value = err.errors[firstErrorKey][0];
    } else {
      error.value = err.message || "Authentication failed";
    }
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

      <!-- Login Form -->
      <form v-if="isLogin" key="login-form" @submit.prevent="handleSubmit" class="auth-form">
        <div class="input-group glass">
          <Mail :size="18" />
          <input
            v-model="loginForm.email"
            type="email"
            name="login_email"
            id="login_email"
            placeholder="yourname@example.com"
            required
          />
        </div>

        <div class="input-group glass">
          <Lock :size="18" />
          <input
            v-model="loginForm.password"
            type="password"
            name="login_password"
            id="login_password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
          />
        </div>

        <div v-if="error" class="error-msg">
          {{ error }}
        </div>

        <button type="submit" :disabled="isLoading" class="submit-btn glass">
          <Loader2 v-if="isLoading" class="spin" :size="20" />
          <template v-else>
            <span>Sign In</span>
            <ArrowRight :size="18" />
          </template>
        </button>
      </form>

      <!-- Register Form -->
      <form v-else key="register-form" @submit.prevent="handleSubmit" class="auth-form">
        <div class="input-group glass">
          <User :size="18" />
          <input
            v-model="registerForm.name"
            type="text"
            name="register_name"
            id="register_name"
            placeholder="Your full name (e.g. John Doe)"
            required
          />
        </div>

        <div class="input-group glass">
          <Mail :size="18" />
          <input
            v-model="registerForm.email"
            type="email"
            name="register_email"
            id="register_email"
            placeholder="yourname@example.com"
            required
          />
        </div>

        <div class="input-group glass">
          <Lock :size="18" />
          <input
            v-model="registerForm.password"
            type="password"
            name="register_password"
            id="register_password"
            placeholder="Create a strong password (min. 8 chars)"
            autocomplete="new-password"
            required
          />
        </div>

        <div class="input-hint">
          Must include letters, numbers, and symbols.
        </div>

        <div class="input-group glass">
          <Lock :size="18" />
          <input
            v-model="registerForm.password_confirmation"
            type="password"
            name="register_password_confirmation"
            id="register_password_confirmation"
            placeholder="Confirm Password"
            autocomplete="new-password"
            required
          />
        </div>

        <div v-if="error" class="error-msg">
          {{ error }}
        </div>

        <button type="submit" :disabled="isLoading" class="submit-btn glass">
          <Loader2 v-if="isLoading" class="spin" :size="20" />
          <template v-else>
            <span>Sign Up</span>
            <ArrowRight :size="18" />
          </template>
        </button>
      </form>

      <div class="auth-footer">
        <button @click="toggleMode" class="toggle-btn">
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
  position: relative;
  z-index: 1;
}

.auth-card {
  width: 100%;
  max-width: 420px;
  padding: 3rem;
  border-radius: var(--radius-lg);
  text-align: center;
  z-index: 1;

  .auth-header {
    margin-bottom: 2.5rem;
    h1 {
      font-size: 2.5rem;
      color: var(--text-main);
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
    border-radius: var(--radius-md);
    padding: 0 1.25rem;
    transition: var(--transition-smooth);
    color: var(--text-muted);

    &:focus-within {
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

      // Override Chrome autofill via transition-delay hack.
      // Chrome internally forces autofill colors, so delaying
      // transitions on all affected properties neutralizes it.
      &:-webkit-autofill,
      &:-webkit-autofill:hover,
      &:-webkit-autofill:focus,
      &:-webkit-autofill:active {
        -webkit-text-fill-color: var(--text-main);
        background-color: transparent;
        caret-color: white;
        transition:
          background-color 9999s ease-in-out 0s,
          color 9999s ease-in-out 0s,
          -webkit-text-fill-color 9999s ease-in-out 0s;
      }
    }
  }
}

.input-hint {
  font-size: 0.75rem;
  color: var(--text-muted);
  text-align: left;
  padding-left: 1.25rem;
  margin-top: -0.75rem;
}

.submit-btn {
  margin-top: 1rem;
  padding: 1rem;
  color: #fff;
  border: none;
  border-radius: var(--radius-md);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  cursor: pointer;
  transition: var(--transition-smooth);

  &:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.08);
  }

  &:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
}

.error-msg {
  color: var(--color-danger);
  font-size: 0.85rem;
  background: rgba(var(--color-danger-rgb), 0.1);
  padding: 0.75rem;
  border-radius: 8px;
  border: 1px solid rgba(var(--color-danger-rgb), 0.2);
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
