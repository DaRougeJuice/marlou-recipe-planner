<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-4">Search Recipes 🍳</h1>
    <div class="flex gap-2 mb-6">
      <input
        v-model="query"
        type="text"
        placeholder="Enter an ingredient..."
        class="border p-2 rounded w-full max-w-md"
      />
      <button @click="search" class="bg-green-500 text-white px-4 py-2 rounded">
        Search
      </button>
    </div>

    <p v-if="loading">Loading...</p>

    <p v-if="recipes.length === 0 && !loading && searched">
      No recipes found. Try a different ingredient!
    </p>

    <div class="grid grid-cols-3 gap-4">
      <div v-for="recipe in recipes" :key="recipe.idMeal" class="border rounded p-4">
        <img :src="recipe.strMealThumb" class="w-full rounded mb-2" />
        <h2 class="font-bold text-lg">{{ recipe.strMeal }}</h2>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const query = ref('')
const recipes = ref([])
const loading = ref(false)
const searched = ref(false)

async function search() {
  if (!query.value) return
  loading.value = true
  searched.value = true
  recipes.value = []

  const res = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${query.value}`)
  const data = await res.json()

  recipes.value = data.meals || []
  loading.value = false
}
</script>