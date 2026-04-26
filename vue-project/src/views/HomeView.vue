<template>
  <div>
    <!-- Hero Section -->
    <div class="relative h-screen flex items-center justify-center overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1600&auto=format&fit=crop"
          class="w-full h-full object-cover object-center"
          alt="Fresh healthy food"
        />
        <div class="absolute inset-0" style="background: rgba(20,26,20,0.75);"></div>
      </div>

      <div class="relative z-10 text-center max-w-3xl px-8">
        <p class="text-green-400 text-xs font-semibold tracking-widest uppercase mb-4">Your personal kitchen assistant</p>
        <h1 class="text-6xl font-extrabold leading-tight mb-6 drop-shadow-lg">
          Find recipes<br>with
          <span class="text-green-400"> what<br>you have.</span>
        </h1>
        <p class="text-gray-200 text-xl mb-12 drop-shadow">
          Add your ingredients, filter out allergens, and discover meals you can actually cook tonight.
        </p>
        <div class="flex flex-col items-center gap-4">
          <RouterLink
            to="/search"
            class="bg-green-400 hover:bg-green-300 text-green-950 font-bold px-16 py-5 rounded-full text-lg transition shadow-lg shadow-green-900/40 w-72 text-center"
          >
            Search Recipes →
          </RouterLink>
          <RouterLink
            to="/meal-plan"
            class="bg-white/10 backdrop-blur border border-white/30 hover:bg-white/20 text-white font-semibold px-16 py-5 rounded-full text-lg transition w-72 text-center"
          >
            View Meal Plan
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Carousel Section -->
    <div class="py-14">
      <div class="flex items-center justify-between mb-10 px-16">
        <div>
          <h2 class="text-2xl font-bold">Suggested Recipes</h2>
          <p class="text-gray-500 text-sm mt-1">Randomly picked for you today</p>
        </div>
        <button @click="init" class="text-xs text-gray-400 hover:text-white transition px-3 py-1.5 border border-white/10 rounded-full hover:border-white/30">↻ Refresh</button>
      </div>

      <p v-if="loading" class="text-gray-500 text-sm px-16">Loading suggestions...</p>

      <div v-else ref="trackWrapper" class="overflow-hidden">
        <div
          class="flex gap-6 px-16"
          :style="{ transform: `translateX(${offset}px)`, transition: animating ? 'transform 0.6s ease-in-out' : 'none' }"
        >
          <div
            v-for="meal in carousel"
            :key="meal.idMeal"
            class="flex-shrink-0 bg-[#1e241e] rounded-3xl overflow-hidden shadow-lg group"
            style="width: 260px;"
          >
            <div class="flex justify-center pt-8 pb-4">
              <div class="w-40 h-40 rounded-full overflow-hidden shadow-xl border-4 border-white/5">
                <RouterLink :to="`/recipe/${meal.idMeal}`">
                  <img :src="meal.strMealThumb" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                </RouterLink>
              </div>
            </div>
            <div class="text-center px-5 pb-6">
              <RouterLink :to="`/recipe/${meal.idMeal}`">
                <p class="font-bold text-sm leading-snug mb-1 hover:text-green-400 transition">{{ meal.strMeal }}</p>
              </RouterLink>
              <p class="text-xs text-gray-500 mb-4">{{ meal.strCategory }}</p>
              <div class="border-t border-white/5 pt-3 flex justify-center gap-4">
                <button
                  @click="favorite(meal)"
                  :class="favoritedIds.includes(meal.idMeal) ? 'text-red-400' : 'text-gray-500 hover:text-red-400'"
                  class="text-xs transition"
                >
                  {{ favoritedIds.includes(meal.idMeal) ? '❤️ Saved' : '🤍 Save' }}
                </button>
                <span class="text-gray-700 text-xs">|</span>
                <button
                  @click="addToMealPlan(meal)"
                  :class="mealPlanIds.includes(meal.idMeal) ? 'text-green-400' : 'text-gray-500 hover:text-green-400'"
                  class="text-xs transition"
                >
                  {{ mealPlanIds.includes(meal.idMeal) ? '✓ In Plan' : '+ Meal Plan' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from '../axios.js'

const carousel = ref([])
const loading = ref(true)
const favoritedIds = ref([])
const mealPlanIds = ref([])
const offset = ref(0)
const animating = ref(false)
const seenIds = ref(new Set())
const trackWrapper = ref(null)
const CARD_WIDTH = 260 + 24
let autoScrollInterval = null

async function fetchOne() {
  let meal = null
  let attempts = 0
  while (attempts < 5) {
    const res = await fetch('https://www.themealdb.com/api/json/v1/1/random.php')
    const data = await res.json()
    const candidate = data.meals[0]
    if (!seenIds.value.has(candidate.idMeal)) {
      seenIds.value.add(candidate.idMeal)
      meal = candidate
      break
    }
    attempts++
  }
  return meal
}

async function init() {
  loading.value = true
  carousel.value = []
  offset.value = 0
  animating.value = false
  seenIds.value = new Set()
  clearInterval(autoScrollInterval)

  const results = []
  for (let i = 0; i < 8; i++) {
    const meal = await fetchOne()
    if (meal) results.push(meal)
  }
  carousel.value = results
  loading.value = false
  autoScrollInterval = setInterval(tick, 3000)
}

async function tick() {
  const newMeal = await fetchOne()
  if (!newMeal) return
  carousel.value.push(newMeal)

  animating.value = true
  offset.value -= CARD_WIDTH

  setTimeout(() => {
    carousel.value.shift()
    animating.value = false
    offset.value += CARD_WIDTH
  }, 650)
}

onMounted(init)
onUnmounted(() => clearInterval(autoScrollInterval))

async function favorite(meal) {
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
    else alert('Log in to save favorites!')
  }
}

function addToMealPlan(meal) {
  if (mealPlanIds.value.includes(meal.idMeal)) return
  const existing = JSON.parse(localStorage.getItem('mealPlan') || '[]')
  existing.push({
    idMeal: meal.idMeal,
    strMeal: meal.strMeal,
    strMealThumb: meal.strMealThumb,
    strCategory: meal.strCategory,
    ingredients: [],
    macros: null,
  })
  localStorage.setItem('mealPlan', JSON.stringify(existing))
  mealPlanIds.value.push(meal.idMeal)
}
</script>