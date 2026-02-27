import { reactive } from 'vue'
import { apiClient } from '../utils/apiClient'
import { notificationStore } from './notificationStore'

export const chatStore = reactive({
  messages: [],
  isLoading: false,

  async sendMessage(query) {
    this.isLoading = true
    
    // Add user message
    this.messages.push({
      role: 'user',
      content: query,
      timestamp: new Date().toISOString()
    })

    try {
      const data = await apiClient.post('/chat', { query })
      if (data?.response) {
        this.messages.push({
          role: 'assistant',
          content: data.response,
          timestamp: new Date().toISOString()
        })
      }
    } catch (err) {
      this.messages.push({
        role: 'system',
        content: 'Failed to retrieve response from AI.',
        timestamp: new Date().toISOString()
      })
      notificationStore.add('Chat error', 'error', err.message)
    } finally {
      this.isLoading = false
    }
  },

  clearHistory() {
    this.messages = []
  }
})
