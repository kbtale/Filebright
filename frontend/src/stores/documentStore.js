import { reactive, computed } from 'vue'

export const documentStore = reactive({
  documents: [],
  isLoading: false,
  error: null,

  // Actions
  async fetchDocuments() {
    this.isLoading = true
    try {
      
    } catch (err) {
      this.error = 'Failed to load documents'
    } finally {
      this.isLoading = false
    }
  },

  addDocument(doc) {
    this.documents.unshift(doc)
  },

  updateDocumentStatus(id, status) {
    const doc = this.documents.find(d => d.id === id)
    if (doc) doc.status = status
  }
})
