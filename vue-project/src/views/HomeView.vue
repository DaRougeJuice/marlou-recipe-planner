<template>
  <div class="bg-gradient-to-b from-[#0f130f] via-[#111411] to-black text-white">

    <!-- Hero Section -->
    <section class="relative h-screen flex items-center overflow-hidden">

      <!-- Background Image -->
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1600&auto=format&fit=crop"
          class="w-full h-full object-cover scale-105"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-transparent"></div>
      </div>

      <!-- Content -->
      <div class="relative z-10 w-full max-w-7xl mx-auto px-8 grid md:grid-cols-2 gap-12 items-center">

        <!-- LEFT -->
        <div class="space-y-6">
          <p class="text-green-400 text-xs tracking-[0.3em] uppercase font-semibold">
            Smart cooking starts here
          </p>

          <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">
            Cook amazing meals<br>
            using <span class="text-green-400">ingredients at home</span>
          </h1>

          <p class="text-gray-300 text-lg max-w-md">
            Discover recipes instantly, save favorites, and build your meal prep in one place.
          </p>

          <div class="flex flex-wrap gap-4">
            <RouterLink
              to="/search"
              class="bg-green-400 hover:bg-green-300 text-green-950 font-semibold px-7 py-3 rounded-full transition hover:scale-105 shadow-xl"
            >
              Explore Recipes
            </RouterLink>

            <RouterLink
              to="/meal-plan"
              class="bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 px-7 py-3 rounded-full transition hover:scale-105"
            >
              My Meal Plan
            </RouterLink>
          </div>
        </div>

        <!-- RIGHT HERO CARD -->
        <div class="flex justify-center">

          <div class="relative w-80">

            <!-- Glow -->
            <div class="absolute -inset-2 bg-green-500/20 blur-3xl rounded-[2rem]"></div>

            <!-- Loading -->
            <div
              v-if="heroLoading"
              class="relative h-[500px] rounded-[2rem] bg-white/5 border border-white/10 flex items-center justify-center"
            >
              <p class="text-gray-400 text-sm">Loading recipe...</p>
            </div>

            <!-- Meal Card -->
            <div
              v-else-if="heroMeal"
              class="relative bg-[#1a1f1a]/90 backdrop-blur-xl rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl transition duration-500 hover:-translate-y-1 hover:border-green-400/30"
            >

              <!-- Image -->
              <RouterLink :to="`/recipe/${heroMeal.idMeal}`">
                <div class="overflow-hidden">
                  <img
                    :src="heroMeal.strMealThumb"
                    class="w-full h-64 object-cover transition duration-700 hover:scale-110"
                    :class="transitioning ? 'opacity-0' : 'opacity-100'"
                  />
                </div>
              </RouterLink>

              <!-- Content -->
              <div class="p-6">

                <div class="flex items-center justify-between mb-3">
                  <span class="text-[11px] uppercase tracking-widest text-green-400">
                    {{ heroMeal.strCategory }}
                  </span>

                  <span class="text-[11px] text-gray-500">
                    {{ heroMeal.strArea }}
                  </span>
                </div>

                <RouterLink :to="`/recipe/${heroMeal.idMeal}`">
                  <h2 class="text-2xl font-bold leading-snug hover:text-green-400 transition cursor-pointer">
                    {{ heroMeal.strMeal }}
                  </h2>
                </RouterLink>

                <p class="text-sm text-gray-400 mt-3 line-clamp-2">
                  Fresh recipe inspiration tailored for tonight’s cooking session.
                </p>

                <!-- Actions -->
                <div class="flex gap-3 mt-6">

                  <button
                    @click="addToMealPlan(heroMeal)"
                    class="flex-1 bg-green-400 hover:bg-green-300 text-green-950 font-semibold py-3 rounded-2xl transition hover:scale-[1.02]"
                  >
                    Add to Meal Plan
                  </button>

                  <button
                    @click="toggleFavorite(heroMeal)"
                    class="w-14 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 transition"
                  >
                    <span v-if="favoritedIds.includes(heroMeal.idMeal)">
                      ❤️
                    </span>
                    <span v-else>
                      🤍
                    </span>
                  </button>

                </div>
              </div>
            </div>

            <!-- Timer -->
            <div class="flex justify-center gap-2 mt-5">
              <div
                v-for="i in 5"
                :key="i"
                class="h-1 rounded-full transition-all duration-300"
                :class="i === timerDot
                  ? 'w-8 bg-green-400 shadow-lg shadow-green-400/40'
                  : 'w-2 bg-white/20'"
              ></div>
            </div>

          </div>

        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="relative border-t border-white/10 mt-24">

      <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-green-400 to-transparent"></div>

      <div class="max-w-7xl mx-auto px-8 py-14 grid md:grid-cols-4 gap-10">

        <div>
          <h2 class="text-2xl font-bold mb-3">MealPlan</h2>
          <p class="text-sm text-gray-400 leading-relaxed">
            Recipe discovery and meal prep designed for modern cooking.
          </p>
        </div>

        <div>
          <h3 class="font-semibold mb-4">Navigation</h3>

          <ul class="space-y-2 text-sm text-gray-400">
            <li>
              <RouterLink to="/search" class="hover:text-green-400 transition">
                Recipes
              </RouterLink>
            </li>

            <li>
              <RouterLink to="/meal-plan" class="hover:text-green-400 transition">
                Meal Plan
              </RouterLink>
            </li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold mb-4">Support</h3>

          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-green-400 transition">Help Center</a></li>
            <li><a href="#" class="hover:text-green-400 transition">Privacy Policy</a></li>
            <li><a href="#" class="hover:text-green-400 transition">Terms</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold mb-4">Connect</h3>

          <div class="flex gap-4 text-xl">
            <a href="#" class="hover:scale-110 hover:text-green-400 transition">🌐</a>
            <a href="#" class="hover:scale-110 hover:text-green-400 transition">📘</a>
            <a href="#" class="hover:scale-110 hover:text-green-400 transition">🐦</a>
          </div>
        </div>

      </div>

      <div class="text-center text-xs text-gray-600 pb-6">
        © 2026 MealPlan. All rights reserved.
      </div>

    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from '../axios.js'

const heroMeal = ref(null)
const heroLoading = ref(true)
const transitioning = ref(false)
const timerDot = ref(1)
const favoritedIds = ref([])

let heroInterval = null
let dotInterval = null

async function fetchRandom() {
  const res = await fetch('https://www.themealdb.com/api/json/v1/1/random.php')
  const data = await res.json()
  return data.meals[0]
}

function addToMealPlan(meal) {
  const existing = JSON.parse(localStorage.getItem('mealPlan') || '[]')

  const alreadyExists = existing.find(m => m.idMeal === meal.idMeal)

  if (!alreadyExists) {
    existing.push(meal)
    localStorage.setItem('mealPlan', JSON.stringify(existing))
    alert('Added to meal plan!')
  } else {
    alert('Meal already added!')
  }
}

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
      alert('Please log in first!')
    }
  }
}

async function refreshHero() {
  transitioning.value = true

  await new Promise(r => setTimeout(r, 400))

  heroMeal.value = await fetchRandom()

  transitioning.value = false
  timerDot.value = 1
}

async function initHero() {
  heroLoading.value = true

  heroMeal.value = await fetchRandom()

  heroLoading.value = false

  let count = 1

  dotInterval = setInterval(() => {
    count = count === 5 ? 1 : count + 1
    timerDot.value = count
  }, 2000)

  heroInterval = setInterval(refreshHero, 10000)
}

onMounted(initHero)

onUnmounted(() => {
  clearInterval(heroInterval)
  clearInterval(dotInterval)
})
</script>