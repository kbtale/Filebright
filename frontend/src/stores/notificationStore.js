import { reactive } from 'vue'

export const notificationState = reactive({
  notifications: []
})

export const notificationStore = {
  add(message, type = 'error', details = null) {
    const id = Date.now() + Math.random()
    const notification = {
      id,
      message,
      type,
      details,
      timestamp: new Date()
    }
    notificationState.notifications.push(notification)
    
    // Auto-dismiss after 10 seconds
    setTimeout(() => {
      this.remove(id)
    }, 10000)

    return id
  },

  remove(id) {
    const index = notificationState.notifications.findIndex(n => n.id === id)
    if (index !== -1) {
      notificationState.notifications.splice(index, 1)
    }
  }
}
