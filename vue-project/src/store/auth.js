import { ref } from 'vue'

export const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'))

export function setUser(user, token) {
  localStorage.setItem('user', JSON.stringify(user))
  localStorage.setItem('token', token)
  currentUser.value = user
}

export function clearUser() {
  localStorage.removeItem('user')
  localStorage.removeItem('token')
  currentUser.value = null
}