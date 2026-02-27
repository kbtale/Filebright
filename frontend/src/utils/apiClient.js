import { authStore } from '../stores/authStore'

const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

export const apiClient = {
  async request(endpoint, options = {}) {
    const url = `${BASE_URL}${endpoint}`
    
    const headers = {
      'Accept': 'application/json',
      ...options.headers
    }

    if (!(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json'
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
        const text = await response.text()
        let errorData = { message: text }
        
        try {
          // Attempt to parse as JSON if possible
          const parsed = JSON.parse(text)
          errorData = parsed
        } catch (e) {
          // Fallback if not JSON
          if (!text) {
            errorData = { message: `Error ${response.status}: ${response.statusText}` }
          }
        }
        
        const apiError = new Error(errorData.message || 'API request failed')
        apiError.errors = errorData.errors
        apiError.status = response.status
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
  },

  delete(endpoint, options = {}) {
    return this.request(endpoint, { ...options, method: 'DELETE' })
  },

  patch(endpoint, data, options = {}) {
    return this.request(endpoint, {
      ...options,
      method: 'PATCH',
      body: JSON.stringify(data)
    })
  }
}
