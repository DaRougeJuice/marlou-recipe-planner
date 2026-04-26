<template>
  <div class="max-w-5xl mx-auto px-8 py-12">
    <h1 class="text-4xl font-bold mb-2">Search Recipes</h1>
    <p class="text-gray-400 text-sm mb-8">Type an ingredient and press Enter.</p>

    <!-- Search Bar -->
    <div class="flex gap-2 mb-6">
      <input
        v-model="query"
        type="text"
        placeholder="e.g. chicken, avocado..."
        class="bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2 rounded-lg w-full max-w-md focus:outline-none focus:border-green-400 transition"
        @keyup.enter="search"
      />
      <button @click="search" class="bg-green-400 hover:bg-green-500 text-black font-semibold px-5 py-2 rounded-lg text-sm transition">
        Search
      </button>
    </div>

    <!-- Allergen Filters -->
    <div class="mb-8">
      <p class="text-xs text-gray-500 mb-2 uppercase tracking-widest">Filter out allergens</p>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="allergen in allergenOptions"
          :key="allergen"
          @click="toggleAllergen(allergen)"
          :class="selectedAllergens.includes(allergen)
            ? 'bg-red-500/20 text-red-400 border-red-500/40'
            : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/30'"
          class="px-3 py-1 rounded-full text-xs border transition"
        >
          {{ allergen }}
        </button>
      </div>
    </div>

    <p v-if="loading" class="text-gray-400 text-sm">Finding recipes...</p>
    <p v-if="recipes.length === 0 && !loading && searched" class="text-gray-500 text-sm">
      No recipes found. Try a different ingredient!
    </p>

    <!-- Results label -->
    <p v-if="recipes.length > 0 && searchedQuery" class="text-gray-400 text-sm mb-4">
      Recipes related to <span class="text-white font-medium">{{ searchedQuery }}</span>
    </p>

    <!-- Results Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <RouterLink
        v-for="recipe in recipes"
        :key="recipe.idMeal"
        :to="`/recipe/${recipe.idMeal}`"
        class="group bg-white/5 border border-white/10 rounded-xl overflow-hidden hover:border-green-400/40 transition"
      >
        <div class="overflow-hidden">
          <img :src="recipe.strMealThumb" class="w-full h-44 object-cover group-hover:scale-105 transition duration-300" />
        </div>
        <div class="p-4">
          <h2 class="font-semibold text-sm leading-tight mb-1">{{ recipe.strMeal }}</h2>
          <p class="text-xs text-gray-500">{{ recipe.strCategory }}</p>
          <div class="flex flex-wrap gap-1 mt-2">
            <span
              v-for="tag in getDetectedAllergens(recipe)"
              :key="tag"
              class="text-xs bg-red-500/10 text-red-400 px-2 py-0.5 rounded-full border border-red-500/20"
            >
              ⚠ {{ tag }}
            </span>
          </div>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const query = ref('')
const searchedQuery = ref('')
const recipes = ref([])
const loading = ref(false)
const searched = ref(false)

const allergenOptions = ['Gluten', 'Dairy', 'Nuts', 'Eggs', 'Shellfish', 'Soy']
const selectedAllergens = ref([])

const allergenKeywords = {
  Gluten: ['flour', 'wheat', 'bread', 'pasta', 'barley', 'rye', 'oats', 'breadcrumbs', 'noodles'],
  Dairy: ['milk', 'cheese', 'butter', 'cream', 'yogurt', 'ghee', 'parmesan', 'mozzarella', 'cheddar'],
  Nuts: ['almond', 'peanut', 'cashew', 'walnut', 'pecan', 'pistachio', 'hazelnut', 'nut'],
  Eggs: ['egg', 'eggs', 'mayonnaise', 'mayo'],
  Shellfish: ['shrimp', 'prawn', 'lobster', 'crab', 'scallop', 'clam', 'oyster', 'mussel'],
  Soy: ['soy', 'tofu', 'tempeh', 'edamame', 'miso', 'soya'],
}

function toggleAllergen(allergen) {
  const i = selectedAllergens.value.indexOf(allergen)
  if (i === -1) selectedAllergens.value.push(allergen)
  else selectedAllergens.value.splice(i, 1)
}

function getRecipeIngredients(recipe) {
  const ings = []
  for (let i = 1; i <= 20; i++) {
    const ing = recipe[`strIngredient${i}`]
    if (ing && ing.trim()) ings.push(ing.toLowerCase())
  }
  return ings
}

function getDetectedAllergens(recipe) {
  const ings = getRecipeIngredients(recipe)
  return Object.entries(allergenKeywords)
    .filter(([, keywords]) => keywords.some(kw => ings.some(ing => ing.includes(kw))))
    .map(([allergen]) => allergen)
}

function hasSelectedAllergens(recipe) {
  if (selectedAllergens.value.length === 0) return false
  const ings = getRecipeIngredients(recipe)
  return selectedAllergens.value.some(allergen =>
    allergenKeywords[allergen].some(kw => ings.some(ing => ing.includes(kw)))
  )
}

async function search() {
  const val = query.value.trim()
  if (!val) return

  searchedQuery.value = val
  loading.value = true
  searched.value = true
  recipes.value = []

  try {
    const res = await fetch(`https://www.themealdb.com/api/json/v1/1/filter.php?i=${val}`)
    const data = await res.json()
    const results = data.meals || []

    const detailed = await Promise.all(
      results.slice(0, 12).map(async (meal) => {
        const r = await fetch(`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${meal.idMeal}`)
        const d = await r.json()
        return d.meals[0]
      })
    )

    recipes.value = detailed.filter(recipe => !hasSelectedAllergens(recipe))
  } catch (e) {
    console.error(e)
  }

  loading.value = false
}
</script>