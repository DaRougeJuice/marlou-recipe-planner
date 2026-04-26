import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import RecipeView from '../views/RecipeView.vue'
import AuthView from '../views/AuthView.vue'
import FavoritesView from '../views/Favorites.vue'
import MealPlanView from '../views/MealPlanView.vue'
import ShoppingListView from '../views/ShoppingListView.vue'

const routes = [
  { path: '/', name: 'Home', component: HomeView },
  { path: '/search', name: 'Search', component: SearchView },
  { path: '/recipe/:id', name: 'Recipe', component: RecipeView },
  { path: '/auth', name: 'Auth', component: AuthView },
  { path: '/favorites', name: 'Favorites', component: FavoritesView },
  { path: '/meal-plan', name: 'MealPlan', component: MealPlanView },
  { path: '/shopping-list', name: 'ShoppingList', component: ShoppingListView },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router