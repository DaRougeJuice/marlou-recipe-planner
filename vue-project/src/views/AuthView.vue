<template>
  <div class="min-h-screen flex items-center justify-center px-8">
    <div class="w-full max-w-sm">
      <h1 class="text-3xl font-bold mb-1">{{ isLogin ? 'Welcome back' : 'Create account' }}</h1>
      <p class="text-gray-500 text-sm mb-8">{{ isLogin ? 'Login to access your favorites.' : 'Register to save your recipes.' }}</p>

      <input v-if="!isLogin" v-model="name" type="text" placeholder="Name"
        class="bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 w-full mb-3 text-sm focus:outline-none focus:border-green-400 transition" />
      <input v-model="email" type="email" placeholder="Email"
        class="bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 w-full mb-3 text-sm focus:outline-none focus:border-green-400 transition" />
      <input v-model="password" type="password" placeholder="Password"
        class="bg-white/5 border border-white/10 text-white placeholder-gray-500 rounded-lg px-4 py-2.5 w-full mb-5 text-sm focus:outline-none focus:border-green-400 transition" />

      <button @click="submit"
        class="bg-green-400 hover:bg-green-300 text-green-950 font-bold text-sm w-full py-2.5 rounded-lg transition">
        {{ isLogin ? 'Login' : 'Register' }}
      </button>

      <p class="mt-5 text-center text-sm text-gray-500">
        {{ isLogin ? "Don't have an account?" : "Already have an account?" }}
        <span @click="isLogin = !isLogin" class="text-green-400 cursor-pointer hover:underline">
          {{ isLogin ? 'Register' : 'Login' }}
        </span>
      </p>

      <p v-if="message" class="mt-4 text-center text-sm text-red-400">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../axios.js'
import { setUser } from '../stores/auth.js'

const router = useRouter()
const isLogin = ref(true)
const name = ref('')
const email = ref('')
const password = ref('')
const message = ref('')

async function submit() {
  message.value = ''
  try {
    const url = isLogin.value ? '/api/login' : '/api/register'
    const body = isLogin.value
      ? { email: email.value, password: password.value }
      : { name: name.value, email: email.value, password: password.value }

    const res = await axios.post(url, body)
    if (res.data.token) {
      localStorage.setItem('token', res.data.token)
      setUser(res.data.user)
      router.push('/')
    }
  } catch (e) {
    message.value = e.response?.data?.message || 'Something went wrong!'
  }
}
</script>