<template>
  <div class="max-w-3xl mx-auto px-8 py-12">
    <button @click="$router.back()" class="text-sm text-gray-400 hover:text-white mb-6 flex items-center gap-1 transition">
      ← Back
    </button>

    <p v-if="loading" class="text-gray-400 text-sm">Loading...</p>

    <div v-if="recipe">
      <img :src="recipe.strMealThumb" class="w-full rounded-2xl mb-6 object-cover max-h-72" />

      <div class="flex items-start justify-between gap-4 mb-2">
        <h1 class="text-3xl font-bold">{{ recipe.strMeal }}</h1>
        <button
          @click="saveFavorite"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-full font-semibold text-sm transition"
          :class="saved
            ? 'bg-red-500/20 border border-red-400/40 text-red-400'
            : 'bg-white/10 border border-white/20 text-white hover:bg-red-500/10 hover:border-red-400/30 hover:text-red-400'"
        >
          {{ saved ? '❤️ Saved to Favorites' : '🤍 Save to Favorites' }}
        </button>
      </div>

      <p class="text-gray-400 text-sm mb-6">{{ recipe.strCategory }} · {{ recipe.strArea }}</p>

      <!-- Macros -->
      <div class="grid grid-cols-4 gap-3 mb-8">
        <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
          <p class="text-xs text-gray-400 mb-1">Calories</p>
          <p class="font-semibold">{{ macros.calories }}</p>
          <p class="text-xs text-gray-500">kcal</p>
        </div>
        <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
          <p class="text-xs text-gray-400 mb-1">Protein</p>
          <p class="font-semibold">{{ macros.protein }}g</p>
        </div>
        <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
          <p class="text-xs text-gray-400 mb-1">Carbs</p>
          <p class="font-semibold">{{ macros.carbs }}g</p>
        </div>
        <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
          <p class="text-xs text-gray-400 mb-1">Fat</p>
          <p class="font-semibold">{{ macros.fat }}g</p>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex gap-3 mb-8">
        <button
          @click="addToMealPlan"
          class="flex items-center gap-2 text-sm border border-green-500/30 text-green-400 px-5 py-2.5 rounded-full hover:bg-green-500/10 transition font-semibold"
          :class="addedToMealPlan ? 'bg-green-500/10 border-green-400/50' : ''"
        >
          {{ addedToMealPlan ? '✓ In Meal Plan' : '+ Add to Meal Plan' }}
        </button>
      </div>

      <h2 class="text-lg font-semibold mb-3">Ingredients</h2>
      <ul class="grid grid-cols-2 gap-1 mb-8">
        <li
          v-for="ingredient in ingredients"
          :key="ingredient"
          class="text-sm text-gray-400 border-b border-white/5 pb-1"
        >
          {{ ingredient }}
        </li>
      </ul>

      <h2 class="text-lg font-semibold mb-3">Instructions</h2>
      <p class="text-sm text-gray-400 whitespace-pre-line leading-relaxed">{{ recipe.strInstructions }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '../axios.js'

const route = useRoute()
const recipe = ref(null)
const loading = ref(true)
const ingredients = ref([])
const saved = ref(false)
const addedToMealPlan = ref(false)

const categoryMacros = {
  Chicken:    { calories: 335, protein: 38, carbs: 22, fat: 10 },
  Beef:       { calories: 450, protein: 35, carbs: 20, fat: 22 },
  Pork:       { calories: 400, protein: 30, carbs: 18, fat: 20 },
  Seafood:    { calories: 280, protein: 32, carbs: 15, fat: 8  },
  Lamb:       { calories: 420, protein: 33, carbs: 18, fat: 21 },
  Pasta:      { calories: 380, protein: 14, carbs: 58, fat: 10 },
  Dessert:    { calories: 480, protein: 6,  carbs: 72, fat: 18 },
  Vegetarian: { calories: 280, protein: 10, carbs: 42, fat: 8  },
  Vegan:      { calories: 260, protein: 8,  carbs: 44, fat: 6  },
  Breakfast:  { calories: 350, protein: 15, carbs: 38, fat: 14 },
  Side:       { calories: 200, protein: 5,  carbs: 30, fat: 7  },
  Starter:    { calories: 220, protein: 8,  carbs: 25, fat: 9  },
}

const macros = computed(() => {
  const category = recipe.value?.strCategory
  return categoryMacros[category] ?? { calories: 350, protein: 20, carbs: 30, fat: 12 }
})

onMounted(async () => {
  const res = await fetch(`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${route.params.id}`)
  const data = await res.json()
  recipe.value = data.meals[0]

  for (let i = 1; i <= 20; i++) {
    const ingredient = recipe.value[`strIngredient${i}`]
    const measure = recipe.value[`strMeasure${i}`]
    if (ingredient && ingredient.trim()) {
      ingredients.value.push(`${measure} ${ingredient}`.trim())
    }
  }

  // Check if already in meal plan
  const existing = JSON.parse(localStorage.getItem('mealPlan') || '[]')
  addedToMealPlan.value = !!existing.find(m => m.idMeal === recipe.value.idMeal)

  loading.value = false
})

async function saveFavorite() {
  try {
    await axios.post('/api/favorites', {
      meal_id: recipe.value.idMeal,
      meal_name: recipe.value.strMeal,
      meal_thumb: recipe.value.strMealThumb,
    })
    saved.value = true
  } catch (e) {
    if (e.response?.status === 409) saved.value = true
    else alert('Please log in to save favorites!')
  }
}

function addToMealPlan() {
  const existing = JSON.parse(localStorage.getItem('mealPlan') || '[]')
  if (existing.find(m => m.idMeal === recipe.value.idMeal)) {
    addedToMealPlan.value = true
    return
  }
  existing.push({
    idMeal: recipe.value.idMeal,
    strMeal: recipe.value.strMeal,
    strMealThumb: recipe.value.strMealThumb,
    strCategory: recipe.value.strCategory,
    ingredients: ingredients.value,
    macros: macros.value,
  })
  localStorage.setItem('mealPlan', JSON.stringify(existing))
  addedToMealPlan.value = true
}
</script>