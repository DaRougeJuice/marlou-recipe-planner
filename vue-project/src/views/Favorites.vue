<template>
  <div class="max-w-5xl mx-auto px-8 py-12">
    <h1 class="text-4xl font-bold mb-1">Favorites</h1>
    <p class="text-gray-400 text-sm mb-8">Recipes you've saved.</p>

    <p v-if="loading" class="text-gray-500 text-sm">Loading...</p>
    <p v-else-if="favorites.length === 0" class="text-gray-500 text-sm">No saved recipes yet.</p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="fav in favorites" :key="fav.meal_id"
        class="group bg-white/5 border border-white/10 rounded-xl overflow-hidden hover:border-orange-400/40 transition">
        <RouterLink :to="`/recipe/${fav.meal_id}`">
          <img :src="fav.meal_thumb" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300" />
          <div class="p-3">
            <p class="text-sm font-medium leading-tight">{{ fav.meal_name }}</p>
          </div>
        </RouterLink>
        <div class="px-3 pb-3">
          <button @click="removeFavorite(fav.meal_id)" class="text-xs text-red-400 hover:text-red-300 transition">
            Remove
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios.js'

const favorites = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await axios.get('/api/favorites')
    favorites.value = res.data
  } catch (e) { console.error(e) }
  loading.value = false
})

async function removeFavorite(mealId) {
  await axios.delete(`/api/favorites/${mealId}`)
  favorites.value = favorites.value.filter(f => f.meal_id !== mealId)
}
</script>