<template>
  <div class="max-w-2xl mx-auto px-8 py-12">

    <!-- Avatar + Name -->
    <div class="flex items-center gap-5 mb-10">
      <div class="w-20 h-20 rounded-full bg-green-500/20 border-2 border-green-400/40 flex items-center justify-center text-green-400 text-3xl font-bold">
        {{ currentUser?.name?.charAt(0).toUpperCase() }}
      </div>
      <div>
        <h1 class="text-3xl font-bold">{{ currentUser?.name }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ currentUser?.email }}</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-8">
      <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-2xl font-bold text-green-400">{{ favoriteCount }}</p>
        <p class="text-gray-500 text-sm mt-1">Saved Recipes</p>
      </div>
      <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-2xl font-bold text-green-400">{{ mealPlanCount }}</p>
        <p class="text-gray-500 text-sm mt-1">Meals in Plan</p>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
      <RouterLink to="/favorites" class="flex items-center justify-between px-5 py-4 hover:bg-white/5 transition border-b border-white/10">
        <span class="text-sm">❤️ My Favorites</span>
        <span class="text-gray-500 text-xs">→</span>
      </RouterLink>
      <RouterLink to="/meal-plan" class="flex items-center justify-between px-5 py-4 hover:bg-white/5 transition">
        <span class="text-sm">📋 My Meal Plan</span>
        <span class="text-gray-500 text-xs">→</span>
      </RouterLink>
    </div>

    <!-- Account -->
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
      <button
        @click="logout"
        class="w-full flex items-center justify-between px-5 py-4 hover:bg-white/5 transition text-red-400 text-sm"
      >
        <span>🚪 Log Out</span>
        <span class="text-xs">→</span>
      </button>
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-6">
      <h2 class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-2">Danger Zone</h2>
      <p class="text-gray-500 text-sm mb-4">Permanently delete your account and all your data. This cannot be undone.</p>
      <button
        @click="showConfirm = true"
        class="px-5 py-2.5 rounded-full bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 text-sm font-semibold transition"
      >
        🗑️ Delete Account
      </button>
    </div>

    <!-- Confirm delete modal -->
    <div v-if="showConfirm" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div class="bg-[#1e241e] border border-white/10 rounded-2xl p-8 max-w-sm w-full mx-4">
        <h2 class="text-xl font-bold mb-2">Delete account?</h2>
        <p class="text-gray-400 text-sm mb-6">This will permanently delete your account and all saved data. There's no going back.</p>
        <div class="flex gap-3">
          <button
            @click="deleteAccount"
            class="flex-1 bg-red-500 hover:bg-red-400 text-white font-semibold py-2.5 rounded-full text-sm transition"
          >
            Yes, delete
          </button>
          <button
            @click="showConfirm = false"
            class="flex-1 border border-white/10 hover:border-white/30 text-gray-400 hover:text-white py-2.5 rounded-full text-sm transition"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../axios.js'
import { currentUser, clearUser } from '../store/auth.js'

const router = useRouter()
const showConfirm = ref(false)
const favoriteCount = ref(0)
const mealPlanCount = ref(0)

onMounted(async () => {
  // meal plan count from localStorage
  const plan = JSON.parse(localStorage.getItem('mealPlan') || '[]')
  mealPlanCount.value = plan.length

  // favorites count from API
  try {
    const res = await axios.get('/api/favorites')
    favoriteCount.value = res.data.length
  } catch (e) {}
})

async function logout() {
  try { await axios.post('/api/logout') } catch (e) {}
  clearUser()
  router.push('/')
}

async function deleteAccount() {
  try { await axios.delete('/api/user') } catch (e) {}
  clearUser()
  router.push('/')
}
</script>