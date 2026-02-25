<template>
  <div class="app-root">
    <div class="background-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
      <div class="blob blob-3"></div>
      <div class="blob blob-4"></div>
    </div>
    <router-view />
  </div>
</template>

<style lang="scss">
.app-root {
  min-height: 100vh;
  background-color: var(--bg-color);
  position: relative;
  overflow: hidden;
}

.background-blobs {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  pointer-events: none;

  .blob {
    position: absolute;
    border-radius: 50%;
    box-shadow:
      inset -10px -10px 20px rgba(0, 0, 0, 0.2),
      inset 10px 10px 20px rgba(255, 255, 255, 0.1);
    animation: float 20s ease-in-out infinite;
  }

  .blob-1 {
    width: 600px;
    height: 600px;
    background: radial-gradient(
      circle at 30% 30%,
      var(--primary-color),
      #1a8a8a 70%
    );
    top: -200px;
    right: -200px;
    animation-delay: 0s;
  }

  .blob-2 {
    width: 450px;
    height: 450px;
    background: radial-gradient(circle at 30% 30%, #ec4899, #9b1b5e 70%);
    bottom: -100px;
    left: -100px;
    animation-delay: -5s;
  }

  .blob-3 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle at 30% 30%, #8b5cf6, #5227a3 70%);
    top: 50%;
    left: 20%;
    transform: translateY(-50%);
    animation-delay: -10s;
  }

  .blob-4 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle at 30% 30%, #3b82f6, #1e4a9e 70%);
    bottom: 20%;
    right: 15%;
    animation-delay: -15s;
  }
}

@keyframes float {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.05);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.95);
  }
}
</style>
