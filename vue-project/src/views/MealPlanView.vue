<template>
  <div class="max-w-5xl mx-auto px-8 py-12">
    <h1 class="text-4xl font-bold mb-1">Meal Plan</h1>
    <p class="text-gray-400 text-sm mb-8">Your planned meals for the week.</p>

    <p v-if="meals.length === 0" class="text-gray-500 text-sm">
      No meals added yet. Go to a recipe and click "Add to Meal Plan"!
    </p>

    <!-- Macro Totals -->
    <div v-if="meals.length > 0" class="grid grid-cols-4 gap-3 mb-8">
      <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
        <p class="text-xs text-gray-400 mb-1">Total Calories</p>
        <p class="font-semibold text-green-400">{{ totals.calories }}</p>
        <p class="text-xs text-gray-500">kcal</p>
      </div>
      <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
        <p class="text-xs text-gray-400 mb-1">Total Protein</p>
        <p class="font-semibold text-green-400">{{ totals.protein }}g</p>
      </div>
      <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
        <p class="text-xs text-gray-400 mb-1">Total Carbs</p>
        <p class="font-semibold text-green-400">{{ totals.carbs }}g</p>
      </div>
      <div class="border border-white/10 rounded-xl p-3 text-center bg-white/5">
        <p class="text-xs text-gray-400 mb-1">Total Fat</p>
        <p class="font-semibold text-green-400">{{ totals.fat }}g</p>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
      <div
        v-for="meal in meals"
        :key="meal.idMeal"
        class="bg-white/5 border border-white/10 rounded-xl overflow-hidden"
      >
        <RouterLink :to="`/recipe/${meal.idMeal}`">
          <img :src="meal.strMealThumb" class="w-full h-36 object-cover" />
          <div class="p-3">
            <p class="text-sm font-medium leading-tight">{{ meal.strMeal }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ meal.strCategory }}</p>
          </div>
        </RouterLink>
        <div class="px-3 pb-3 flex gap-3 items-center">
          <button
            @click="toggleFavorite(meal)"
            :class="favoritedIds.includes(meal.idMeal) ? 'text-red-400' : 'text-gray-500 hover:text-red-400'"
            class="text-xs transition"
          >
            {{ favoritedIds.includes(meal.idMeal) ? '❤️ Saved' : '🤍 Save' }}
          </button>
          <span class="text-gray-700 text-xs">|</span>
          <button @click="removeMeal(meal.idMeal)" class="text-xs text-red-400 hover:text-red-300 transition">
            Remove
          </button>
        </div>
      </div>
    </div>

    <div v-if="meals.length > 0" class="flex gap-3">
      <RouterLink
        to="/shopping-list"
        class="bg-green-500 hover:bg-green-400 text-black font-semibold text-sm px-6 py-2.5 rounded-full transition"
      >
        Generate Shopping List →
      </RouterLink>
      <button
        @click="clearAll"
        class="text-sm border border-white/10 px-4 py-2 rounded-full text-gray-400 hover:text-white hover:border-white/30 transition"
      >
        Clear All
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../axios.js'

const meals = ref([])
const favoritedIds = ref([])

onMounted(() => {
  meals.value = JSON.parse(localStorage.getItem('mealPlan') || '[]')
})

const totals = computed(() => {
  return meals.value.reduce(
    (acc, meal) => ({
      calories: acc.calories + (meal.macros?.calories || 0),
      protein: acc.protein + (meal.macros?.protein || 0),
      carbs: acc.carbs + (meal.macros?.carbs || 0),
      fat: acc.fat + (meal.macros?.fat || 0),
    }),
    { calories: 0, protein: 0, carbs: 0, fat: 0 }
  )
})

async function toggleFavorite(meal) {
  if (favoritedIds.value.includes(meal.idMeal)) return
  try {
    await axios.post('/api/favorites', {
      meal_id: meal.idMeal,
      meal_name: meal.strMeal,
      meal_thumb: meal.strMealThumb,
    })
    favoritedIds.value.push(meal.idMeal)
  } catch (e) {
    if (e.response?.status === 409) favoritedIds.value.push(meal.idMeal)
    else alert('Please log in to save favorites!')
  }
}

function removeMeal(idMeal) {
  meals.value = meals.value.filter(m => m.idMeal !== idMeal)
  localStorage.setItem('mealPlan', JSON.stringify(meals.value))
}

function clearAll() {
  meals.value = []
  localStorage.removeItem('mealPlan')
}
</script>