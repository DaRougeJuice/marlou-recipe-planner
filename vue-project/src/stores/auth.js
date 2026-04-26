import { ref } from 'vue'

export const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'))

export function setUser(user) {
  currentUser.value = user
  localStorage.setItem('user', JSON.stringify(user))
}

export function clearUser() {
  currentUser.value = null
  localStorage.removeItem('user')
  localStorage.removeItem('token')
}