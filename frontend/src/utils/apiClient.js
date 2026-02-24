import { authStore } from '../stores/authStore'

const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

export const apiClient = {
  async request(endpoint, options = {}) {
    const url = `${BASE_URL}${endpoint}`
    
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers
    }

    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }

    const config = {
      ...options,
      headers
    }

    try {
      const response = await fetch(url, config)
      
      if (response.status === 401) {
        authStore.logout()
        window.location.href = '/auth'
        return null
      }

      if (!response.ok) {
        const errorData = await response.json()
        const apiError = new Error(errorData.message || 'API request failed')
        apiError.errors = errorData.errors
        throw apiError
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  },

  get(endpoint, options = {}) {
    return this.request(endpoint, { ...options, method: 'GET' })
  },

  post(endpoint, data, options = {}) {
    return this.request(endpoint, { 
      ...options, 
      method: 'POST', 
      body: JSON.stringify(data) 
    })
  },

  postFile(endpoint, formData, options = {}) {
    const headers = { ...options.headers }
    delete headers['Content-Type']
    
    return this.request(endpoint, {
      ...options,
      method: 'POST',
      body: formData,
      headers
    })
  }
}
