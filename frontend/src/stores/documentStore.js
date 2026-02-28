import { reactive, computed } from 'vue'
import { apiClient } from '../utils/apiClient'
import { notificationStore } from './notificationStore'

export const documentStore = reactive({
  documents: [],
  isLoading: false,
  error: null,
  isPolling: false,
  searchQuery: '',

  get filteredDocuments() {
    if (!this.searchQuery.trim()) return this.documents
    const q = this.searchQuery.toLowerCase()
    return this.documents.filter(d =>
      d.filename.toLowerCase().includes(q)
    )
  },

  async fetchDocuments() {
    this.isLoading = true
    try {
      const data = await apiClient.get('/documents')
      if (data) this.documents = data
    } catch (err) {
      this.error = 'Failed to load documents'
      notificationStore.add('Could not load documents', 'error', err.message)
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
      notificationStore.add('Document upload failed', 'error', err.message + (err.errors ? ': ' + JSON.stringify(err.errors) : ''))
    }
  },

  async deleteDocument(id) {
    try {
      await apiClient.delete(`/documents/${id}`)
      this.documents = this.documents.filter(d => d.id !== id)
      return true
    } catch (err) {
      this.error = 'Failed to delete document'
      notificationStore.add('Delete failed', 'error', err.message)
      return false
    }
  },

  async renameDocument(id, newFilename) {
    try {
      const data = await apiClient.patch(`/documents/${id}/rename`, { filename: newFilename })
      if (data?.document) {
        const index = this.documents.findIndex(d => d.id === id)
        if (index !== -1) {
          this.documents[index] = data.document
        }
      }
      return true
    } catch (err) {
      this.error = 'Failed to rename document'
      notificationStore.add('Rename failed', 'error', err.message)
      return false
    }
  },

  async startPolling() {
    if (this.isPolling) return
    this.isPolling = true
    
    const poll = async () => {
      const activeStatuses = ['pending', 'processing', 'parsing', 'vectorizing', 'indexing']
      const hasProcessing = this.documents.some(d => activeStatuses.includes(d.status))
      if (!hasProcessing) {
        this.isPolling = false
        return
      }

      try {
        const oldDocs = [...this.documents]
        const data = await apiClient.get('/documents')
        if (data) {
          this.documents = data
          
          // Check for background transition to 'failed'
          data.forEach(newDoc => {
            const oldDoc = oldDocs.find(d => d.id === newDoc.id)
            if (oldDoc && oldDoc.status !== 'failed' && newDoc.status === 'failed') {
              notificationStore.add(`Processing failed: ${newDoc.filename}`, 'error', 'The background extraction task failed. Check file integrity or size.')
            }
          })
        }
      } catch (err) {
      }

      if (this.isPolling) setTimeout(poll, 3000)
    }
    
    poll()
  }
})
