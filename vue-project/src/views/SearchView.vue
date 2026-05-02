<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0f130f] via-[#111411] to-black text-white">

    <div class="w-full px-10 py-12">

      <!-- Header -->
      <div class="mb-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-2">
          Search <span class="text-green-400">Recipes</span>
        </h1>
        <p class="text-gray-400 text-sm">
          Type an ingredient and discover meals instantly.
        </p>
      </div>

      <!-- Search Bar -->
      <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input
          v-model="query"
          type="text"
          placeholder="e.g. chicken, avocado..."
          class="bg-white/5 backdrop-blur border border-white/10 px-4 py-3 rounded-xl w-full focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-400/30 transition"
          @keyup.enter="search"
        />

        <button
          @click="search"
          class="bg-green-400 hover:bg-green-300 text-green-950 font-semibold px-6 py-3 rounded-xl transition shadow-lg hover:scale-105"
        >
          Search
        </button>
      </div>

      <!-- Allergen Filters -->
      <div class="mb-10">
        <p class="text-xs text-gray-500 mb-3 uppercase tracking-widest">
          Filter allergens
        </p>

        <div class="flex flex-wrap gap-2">
          <button
            v-for="allergen in allergenOptions"
            :key="allergen"
            @click="toggleAllergen(allergen)"
            :class="selectedAllergens.includes(allergen)
              ? 'bg-red-500/20 text-red-400 border-red-500/40 shadow-md'
              : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/30 hover:bg-white/10'"
            class="px-3 py-1.5 rounded-full text-xs border transition hover:scale-105"
          >
            {{ allergen }}
          </button>
        </div>
      </div>

      <!-- States -->
      <p v-if="loading" class="text-gray-400 text-sm animate-pulse">
        Finding recipes...
      </p>

      <p v-if="recipes.length === 0 && !loading && searched" class="text-gray-500 text-sm">
        No recipes found. Try a different ingredient!
      </p>

      <p v-if="recipes.length > 0 && searchedQuery" class="text-gray-400 text-sm mb-6">
        Results for <span class="text-green-400 font-medium">{{ searchedQuery }}</span>
      </p>

      <!-- Results Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-8">

        <RouterLink
          v-for="recipe in recipes"
          :key="recipe.idMeal"
          :to="`/recipe/${recipe.idMeal}`"
          class="group relative bg-white/5 backdrop-blur border border-white/10 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/10 hover:border-green-400/40 transition duration-300"
        >

          <!-- Image -->
          <div class="overflow-hidden">
            <img
              :src="recipe.strMealThumb"
              class="w-full h-56 object-cover group-hover:scale-110 transition duration-500"
            />
          </div>

          <!-- Content -->
          <div class="p-4">
            <h2 class="font-semibold text-sm leading-tight mb-1 group-hover:text-green-400 transition">
              {{ recipe.strMeal }}
            </h2>

            <p class="text-xs text-gray-500 mb-2">
              {{ recipe.strCategory }}
            </p>

            <!-- Allergen Tags -->
            <div class="flex flex-wrap gap-1 mt-2">
              <span
                v-for="tag in getDetectedAllergens(recipe)"
                :key="tag"
                class="text-[10px] bg-red-500/10 text-red-400 px-2 py-0.5 rounded-full border border-red-500/20"
              >
                ⚠ {{ tag }}
              </span>
            </div>
          </div>

          <!-- Hover Glow -->
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-gradient-to-t from-green-400/10 to-transparent"></div>

        </RouterLink>
      </div>
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
