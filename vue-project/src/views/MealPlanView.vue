<template>
  <div class="min-h-screen bg-gradient-to-b from-[#0f130f] via-[#111411] to-black text-white w-full px-10 py-12">

    <!-- Header -->
    <div class="mb-10">
      <h1 class="text-4xl md:text-5xl font-extrabold mb-2">
        Your <span class="text-green-400">Meal Plan</span>
      </h1>

      <p class="text-gray-400 text-sm">
        Your selected meals for the week.
      </p>
    </div>

    <p v-if="meals.length === 0" class="text-gray-500 text-sm">
      No meals added yet. Go to a recipe and click "Add to Meal Plan".
    </p>

    <!-- Macro Totals -->
    <div
      v-if="meals.length > 0"
      class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10"
    >
      <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-4 text-center shadow">
        <p class="text-xs text-gray-400">Calories</p>
        <p class="text-lg font-bold text-green-400">{{ totals.calories }}</p>
      </div>

      <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-4 text-center shadow">
        <p class="text-xs text-gray-400">Protein</p>
        <p class="text-lg font-bold text-green-400">{{ totals.protein }}g</p>
      </div>

      <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-4 text-center shadow">
        <p class="text-xs text-gray-400">Carbs</p>
        <p class="text-lg font-bold text-green-400">{{ totals.carbs }}g</p>
      </div>

      <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-4 text-center shadow">
        <p class="text-xs text-gray-400">Fat</p>
        <p class="text-lg font-bold text-green-400">{{ totals.fat }}g</p>
      </div>
    </div>

    <!-- Meals Grid -->
    <div class="grid [grid-template-columns:repeat(auto-fit,minmax(260px,1fr))] gap-8 mb-10">

      <div
        v-for="meal in meals"
        :key="meal.idMeal"
        class="group relative bg-white/5 backdrop-blur border border-white/10 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/20 hover:border-green-400/40 hover:-translate-y-1 transition duration-300"
      >

        <RouterLink :to="`/recipe/${meal.idMeal}`">

          <!-- Image Wrapper -->
          <div class="overflow-hidden">
            <img
              :src="meal.strMealThumb"
              class="w-full h-56 object-cover group-hover:scale-110 transition duration-500"
            />
          </div>

          <!-- Content -->
          <div class="p-4 relative z-10">
            <p class="font-semibold text-sm group-hover:text-green-400 transition">
              {{ meal.strMeal }}
            </p>

            <p class="text-xs text-gray-500 mt-1">
              {{ meal.strCategory }}
            </p>
          </div>
        </RouterLink>

        <!-- Actions -->
        <div class="px-4 pb-4 flex justify-between items-center text-xs relative z-10">
          <button
            @click="toggleFavorite(meal)"
            :class="favoritedIds.includes(meal.idMeal)
              ? 'text-red-400'
              : 'text-gray-500 hover:text-red-400'"
            class="transition"
          >
            {{ favoritedIds.includes(meal.idMeal) ? '❤️ Saved' : '🤍 Save' }}
          </button>

          <button
            @click="removeMeal(meal.idMeal)"
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

    <!-- Actions -->
    <div v-if="meals.length > 0" class="flex flex-wrap gap-4">

      <RouterLink
        to="/shopping-list"
        class="bg-green-400 hover:bg-green-300 text-green-950 font-semibold px-6 py-3 rounded-full transition shadow-lg hover:scale-105"
      >
        Generate Shopping List
      </RouterLink>

      <button
        @click="clearAll"
        class="border border-white/20 px-5 py-3 rounded-full text-gray-400 hover:text-white hover:border-white/40 transition"
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
    {
      calories: 0,
      protein: 0,
      carbs: 0,
      fat: 0,
    }
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
    if (e.response?.status === 409) {
      favoritedIds.value.push(meal.idMeal)
    } else {
      alert('Please log in to save favorites!')
    }
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