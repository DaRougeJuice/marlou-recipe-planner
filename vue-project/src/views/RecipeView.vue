<template>
  <div class="p-8 max-w-3xl mx-auto">
    <p v-if="loading">Loading...</p>

    <div v-if="recipe">
      <img :src="recipe.strMealThumb" class="w-full rounded mb-4" />
      <h1 class="text-3xl font-bold mb-2">{{ recipe.strMeal }}</h1>
      <p class="text-gray-500 mb-4">{{ recipe.strCategory }} · {{ recipe.strArea }}</p>

      <h2 class="text-xl font-bold mb-2">Ingredients</h2>
      <ul class="list-disc pl-5 mb-4">
        <li v-for="ingredient in ingredients" :key="ingredient">{{ ingredient }}</li>
      </ul>

      <h2 class="text-xl font-bold mb-2">Instructions</h2>
      <p class="whitespace-pre-line">{{ recipe.strInstructions }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const recipe = ref(null)
const loading = ref(true)
const ingredients = ref([])

onMounted(async () => {
  console.log('Recipe ID:', route.params.id)
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

  loading.value = false
})
</script>