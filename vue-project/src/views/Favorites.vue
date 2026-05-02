<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0f130f] via-[#111411] to-black text-white w-full px-10 py-12">

    <!-- Header -->
    <div class="mb-10">
      <h1 class="text-4xl md:text-5xl font-extrabold mb-2">
        Your <span class="text-green-400">Favorites</span>
      </h1>

      <p class="text-gray-400 text-sm">
        Recipes you've saved for later.
      </p>
    </div>

    <!-- Loading -->
    <p v-if="loading" class="text-gray-500 text-sm">
      Loading...
    </p>

    <!-- Empty -->
    <p
      v-else-if="favorites.length === 0"
      class="text-gray-500 text-sm"
    >
      No saved recipes yet.
    </p>

    <!-- Favorites Grid -->
    <div
      v-else
      class="grid [grid-template-columns:repeat(auto-fit,minmax(260px,1fr))] gap-8 mb-10"
    >

      <div
        v-for="fav in favorites"
        :key="fav.meal_id"
        class="group relative bg-white/5 backdrop-blur border border-white/10 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/20 hover:border-green-400/40 hover:-translate-y-1 transition duration-300"
      >

        <RouterLink :to="`/recipe/${fav.meal_id}`">

          <!-- Image Wrapper -->
          <div class="overflow-hidden">
            <img
              :src="fav.meal_thumb"
              class="w-full h-56 object-cover group-hover:scale-110 transition duration-500"
            />
          </div>

          <!-- Content -->
          <div class="p-4 relative z-10">
            <p class="font-semibold text-sm group-hover:text-green-400 transition leading-tight">
              {{ fav.meal_name }}
            </p>

            <p class="text-xs text-gray-500 mt-1">
              Saved recipe
            </p>
          </div>

        </RouterLink>

        <!-- Actions -->
        <div class="px-4 pb-4 flex justify-between items-center text-xs relative z-10">

          <span class="text-red-400">
            ❤️ Saved
          </span>

          <button
            @click="removeFavorite(fav.meal_id)"
            class="text-red-400 hover:text-red-300 transition"
          >
            Remove
          </button>

        </div>

        <!-- Hover Glow -->
        <div
          class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-t from-green-400/10 to-transparent"
        ></div>

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
  } catch (e) {
    console.error(e)
  }

  loading.value = false
})

async function removeFavorite(mealId) {
  try {
    await axios.delete(`/api/favorites/${mealId}`)

    favorites.value = favorites.value.filter(
      fav => fav.meal_id !== mealId
    )
  } catch (e) {
    console.error(e)
  }
}
</script>