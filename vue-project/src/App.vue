<template>
  <div class="min-h-screen bg-[#1a1f1a] text-white">
    <nav class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-8 py-4 bg-[#1a1f1a]/90 backdrop-blur border-b border-white/10">
      <RouterLink to="/" class="text-lg font-bold tracking-tight text-white">🍳 Marlou's</RouterLink>
      <div class="flex items-center gap-6 text-sm text-gray-400">
        <RouterLink to="/" class="hover:text-white transition">Home</RouterLink>
        <RouterLink to="/search" class="hover:text-white transition">Search</RouterLink>
        <RouterLink to="/favorites" class="hover:text-white transition">Favorites</RouterLink>
        <RouterLink to="/meal-plan" class="hover:text-white transition">Meal Plan</RouterLink>

        <RouterLink v-if="!currentUser" to="/auth" class="text-white hover:text-green-400 font-semibold transition">
          Login
        </RouterLink>

        <div v-else class="relative" ref="dropdownRef">
          <button
            @click="dropdownOpen = !dropdownOpen"
            class="flex items-center gap-2 text-white hover:text-green-400 font-semibold transition"
          >
            <div class="w-7 h-7 rounded-full bg-green-500/20 border border-green-400/40 flex items-center justify-center text-green-400 text-xs font-bold">
              {{ currentUser.name?.charAt(0).toUpperCase() }}
            </div>
            {{ currentUser.name }}
            <span class="text-gray-500 text-xs">▾</span>
          </button>

          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-3 w-48 bg-[#1e241e] border border-white/10 rounded-xl shadow-xl overflow-hidden z-50"
          >
            <RouterLink
              to="/profile"
              @click="dropdownOpen = false"
              class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition"
            >
              👤 Profile
            </RouterLink>
            <RouterLink
              to="/favorites"
              @click="dropdownOpen = false"
              class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition"
            >
              ❤️ Favorites
            </RouterLink>
            <RouterLink
              to="/meal-plan"
              @click="dropdownOpen = false"
              class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition"
            >
              📋 Meal Plan
            </RouterLink>
            <div class="border-t border-white/10"></div>
            <button
              @click="logout"
              class="block w-full text-left px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-white/5 transition"
            >
              🚪 Log Out
            </button>
          </div>
        </div>
      </div>
    </nav>
    <main class="pt-16">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from './axios.js'
import { currentUser, clearUser } from './stores/auth.js'

const router = useRouter()
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    dropdownOpen.value = false
  }
}

async function logout() {
  try {
    await axios.post('/api/logout')
  } catch (e) {}
  clearUser()
  dropdownOpen.value = false
  router.push('/')
}
</script>