import { reactive } from 'vue'

export const authStore = reactive({
  user: null,
  token: localStorage.getItem('fb_token'),
  isAuthenticated: !!localStorage.getItem('fb_token'),

  setToken(token) {
    this.token = token
    this.isAuthenticated = true
    localStorage.setItem('fb_token', token)
  },

  setUser(user) {
    this.user = user
  },

  logout() {
    this.token = null
    this.user = null
    this.isAuthenticated = false
    localStorage.removeItem('fb_token')
  }
})
