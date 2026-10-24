<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {useAuthStore} from '@/stores/auth';
import api from '@/lib/api';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const errorMessage = ref('');
const isLoading = ref(false);

async function handleLogin() {
  errorMessage.value = '';
  isLoading.value = true;

  try {
    const response = await api.post('/auth/login', {
      email: email.value,
      password: password.value,
    });

    const { token, user } = response.data.data
    authStore.setAuth(token, user);

    router.push('/dashboard');
  } catch (error) {
    if (error.response?.status === 422) {
      errorMessage.value = 'Email dan password wajib diisi.';
    } else if (error.response?.status === 401) {
      errorMessage.value = 'Email atau password salah.';
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi nanti.';
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
    <div class="min-h-screen" flex items-center justify-center bg-gray-50>
        <form @submit.prevent="handleLogin" class="bg-white shadow-md w-full max-w-sm">
            <h1 class="text-2xl font-bold mb-4 text-center">Cargotrack</h1>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    v-model="email"
                    type="email"
                    required
                    class="w-full border border-gray-300 rounded-md py-2 px-3"
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    v-model="password"
                    type="password"
                    required
                    class="w-full border border-gray-300 rounded-md py-2 px-3"
                />
            </div>

            <p v-if="errorMessage" class="text-red-600 text-sm mb-4">{{ errorMessage }}</p>

            <button
                type="submit"
                :disabled="isLoading"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
            >
                {{ isLoading ? 'Memproses...' : 'Login' }}
            </button>
        </form>
    </div>
</template>