<template>
  <div class="max-w-3xl mx-auto px-8 py-12">
    <h1 class="text-4xl font-bold mb-1">Shopping List</h1>
    <p class="text-gray-400 text-sm mb-8">All ingredients from your meal plan, combined.</p>

    <p v-if="items.length === 0" class="text-gray-500 text-sm">
      No ingredients found. Add meals to your meal plan first!
    </p>

    <div v-else>
      <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-400">{{ checkedCount }}/{{ items.length }} checked off</p>
        <button @click="clearChecked" class="text-xs text-red-400 hover:text-red-300 transition">
          Clear checked
        </button>
      </div>

      <ul class="space-y-2 mb-8">
        <li
          v-for="(item, index) in items"
          :key="index"
          @click="toggle(index)"
          :class="item.checked ? 'opacity-40 line-through' : ''"
          class="flex items-center gap-3 p-3 rounded-lg border border-white/10 bg-white/5 cursor-pointer hover:bg-white/10 transition text-sm"
        >
          <span class="w-4 h-4 rounded border border-white/30 flex-shrink-0 flex items-center justify-center text-green-400 text-xs">
            {{ item.checked ? '✓' : '' }}
          </span>
          {{ item.name }}
        </li>
      </ul>

      <button
        @click="copyToClipboard"
        class="text-sm border border-white/10 px-4 py-2 rounded-full text-gray-400 hover:text-white hover:border-white/30 transition"
      >
        📋 Copy list
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const items = ref([])

onMounted(() => {
  const meals = JSON.parse(localStorage.getItem('mealPlan') || '[]')

  // Gather all ingredients from all meals, deduplicate by name
  const allIngredients = []
  const seen = new Set()

  meals.forEach(meal => {
    (meal.ingredients || []).forEach(ing => {
      const normalized = ing.toLowerCase().trim()
      if (!seen.has(normalized)) {
        seen.add(normalized)
        allIngredients.push({ name: ing, checked: false })
      }
    })
  })

  items.value = allIngredients
})

const checkedCount = computed(() => items.value.filter(i => i.checked).length)

function toggle(index) {
  items.value[index].checked = !items.value[index].checked
}

function clearChecked() {
  items.value = items.value.filter(i => !i.checked)
}

function copyToClipboard() {
  const text = items.value.map(i => i.name).join('\n')
  navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'))
}
</script>