<template>
  <div class="max-w-5xl mx-auto px-8 py-12">
    <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
    <p class="text-gray-500 text-sm mb-10">Super admin controls</p>

    <!-- Users -->
    <div class="mb-12">
      <h2 class="text-lg font-semibold mb-4">👥 All Users</h2>
      <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider">
              <th class="text-left px-5 py-3">Name</th>
              <th class="text-left px-5 py-3">Email</th>
              <th class="text-left px-5 py-3">Role</th>
              <th class="text-left px-5 py-3">Joined</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-b border-white/5 hover:bg-white/5 transition">
              <td class="px-5 py-3 font-medium">{{ user.name }}</td>
              <td class="px-5 py-3 text-gray-400">{{ user.email }}</td>
              <td class="px-5 py-3">
                <span :class="user.role === 'admin' ? 'text-green-400' : 'text-gray-500'">{{ user.role }}</span>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ new Date(user.created_at).toLocaleDateString() }}</td>
              <td class="px-5 py-3">
                <button
                  v-if="user.role !== 'admin'"
                  @click="deleteUser(user.id)"
                  class="text-xs text-red-400 hover:text-red-300 transition"
                >Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Saved Recipes -->
    <div>
      <h2 class="text-lg font-semibold mb-4">📋 All Saved Recipes</h2>
      <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider">
              <th class="text-left px-5 py-3">Recipe</th>
              <th class="text-left px-5 py-3">Saved By</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in recipes" :key="r.id" class="border-b border-white/5 hover:bg-white/5 transition">
              <td class="px-5 py-3 font-medium">{{ r.meal_name || r.meal_id }}</td>
              <td class="px-5 py-3 text-gray-400">{{ r.user?.name }}</td>
              <td class="px-5 py-3">
                <button @click="deleteRecipe(r.id)" class="text-xs text-red-400 hover:text-red-300 transition">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios.js'

const users = ref([])
const recipes = ref([])

onMounted(async () => {
  try {
    const [u, r] = await Promise.all([
      axios.get('/api/admin/users'),
      axios.get('/api/admin/recipes'),
    ])
    users.value = u.data
    recipes.value = r.data
  } catch (e) {
    console.error(e)
  }
})

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return
  await axios.delete(`/api/admin/users/${id}`)
  users.value = users.value.filter(u => u.id !== id)
}

async function deleteRecipe(id) {
  if (!confirm('Delete this saved recipe?')) return
  await axios.delete(`/api/admin/recipes/${id}`)
  recipes.value = recipes.value.filter(r => r.id !== id)
}
</script>