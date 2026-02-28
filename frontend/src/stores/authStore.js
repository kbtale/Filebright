import { reactive } from 'vue'
import { apiClient } from '../utils/apiClient'

export const authStore = reactive({
  user: JSON.parse(localStorage.getItem('fb_user') || 'null'),
  token: localStorage.getItem('fb_token'),
  isAuthenticated: !!localStorage.getItem('fb_token'),
  isInitializing: false,

  async initialize() {
    if (!this.token) return
    
    this.isInitializing = true
    try {
      const userData = await apiClient.get('/user')
      if (userData) {
        this.setUser(userData)
      }
    } catch (err) {
      console.error('[AuthStore] Session verification failed:', err)
      this.logout()
    } finally {
      this.isInitializing = false
    }
  },

  setToken(token) {
    this.token = token
    this.isAuthenticated = true
    localStorage.setItem('fb_token', token)
  },

  setUser(user) {
    this.user = user
    localStorage.setItem('fb_user', JSON.stringify(user))
  },

  logout() {
    this.token = null
    this.user = null
    this.isAuthenticated = false
    localStorage.removeItem('fb_token')
    localStorage.removeItem('fb_user')
  }
})
