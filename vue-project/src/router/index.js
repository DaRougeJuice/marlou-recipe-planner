import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SearchView from '../views/SearchView.vue'
import RecipeView from '../views/RecipeView.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomeView
  },
  {
    path: '/search',
    name: 'Search',
    component: SearchView
  },
  {
    path: '/recipe/:id',
    name: 'Recipe',
    component: RecipeView
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router