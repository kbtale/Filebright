import { reactive } from 'vue'
import { apiClient } from '../utils/apiClient'

export const documentStore = reactive({
  documents: [],
  isLoading: false,
  error: null,
  isPolling: false,

  async fetchDocuments() {
    this.isLoading = true
    try {
      const data = await apiClient.get('/documents')
      if (data) this.documents = data
    } catch (err) {
      this.error = 'Failed to load documents'
    } finally {
      this.isLoading = false
    }
  },

  async uploadFile(file) {
    const formData = new FormData()
    formData.append('file', file)

    const tempId = Date.now() + Math.random()
    const optimisticDoc = {
      id: tempId,
      filename: file.name,
      status: 'pending',
      created_at: new Date().toISOString()
    }
    this.documents.unshift(optimisticDoc)

    try {
      const data = await apiClient.postFile('/upload', formData)
      if (data?.document) {
        const index = this.documents.findIndex(d => d.id === tempId)
        if (index !== -1) {
          this.documents[index] = data.document
        }
        this.startPolling()
      }
    } catch (err) {
      const index = this.documents.findIndex(d => d.id === tempId)
      if (index !== -1) {
        this.documents[index].status = 'failed'
      }
    }
  },

  async startPolling() {
    if (this.isPolling) return
    this.isPolling = true
    
    const poll = async () => {
      const hasProcessing = this.documents.some(d => d.status === 'processing' || d.status === 'pending')
      if (!hasProcessing) {
        this.isPolling = false
        return
      }

      try {
        const data = await apiClient.get('/documents')
        if (data) this.documents = data
      } catch (err) {
      }

      if (this.isPolling) setTimeout(poll, 3000)
    }
    
    poll()
  }
})
