<script setup>
import { onMounted, ref } from 'vue'
import api from '@/lib/api'
import echo from '@/lib/echo'

const trackingNumber = ref('')
const result = ref(null)
const errorMessage = ref('')
const isLoading = ref(false)
const liveConnected = ref(false)
let currentChannel = null

function subscribe(number) {
  unsubscribe()
  currentChannel = `shipments.${number}`
  echo
    .channel(currentChannel)
    .listen('.ShipmentStatusUpdated', (event) => {
      if (!result.value) return
      result.value.current_status = event.current_status
      result.value.logs.push(event.log)
    })
  liveConnected.value = true
}

function unsubscribe() {
  if (currentChannel) {
    echo.leaveChannel(currentChannel)
    currentChannel = null
  }
  liveConnected.value = false
}

async function handleTrack() {
    console.log('handleTrack terpanggil')
    errorMessage.value = ''
    result.value = null
    unsubscribe()
    
    const number = trackingNumber.value.trim().toUpperCase()
    if (!number) return

    isLoading.value = true
    try {
        const response = await api.get(`/shipments/${number}`)
        result.value = response.data.data
        subscribe(number)
    } catch (error) {
        if (error.response?.status === 404) {
            errorMessage.value = 'Nomor resi tidak ditemukan.'
        } else if (error.response?.status === 429) {
            errorMessage.value = 'Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi.'
        } else {
            errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
        }
    } finally {
        isLoading.value = false
    }
}

function formatDate(iso) {
    return new Date(iso).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    })
}

onMounted(unsubscribe)
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-x1 mx-auto">
            <h1 class="text-2xl font-bold text-center mb-6">Lacak Paket</h1>

            <form @submit.prevent="handleTrack" class="flex gap-2 mb-6">
                <input 
                    v-model="trackingNumber"
                    type="text"
                    placeholder="Masukkan nomor resi (CT-...)"
                    class="flex-1 border-gray-300 rounded px-3 py-2 font-mono"
                />
                <button
                    type="submit"
                    :disabled="isLoading"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ isLoading ? 'Mencari...' : 'Lacak' }}
                </button>
            </form>

            <p v-if="errorMessage" class="text-red-600 text-sm text-center mb-4">{{ errorMessage }}</p>

            <div v-if="result" class="bg-white rounded-lg shadow p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Nomor resi</p>
                    <p class="font-mono font-bold">{{ result.tracking_number }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500">Dari</p>
                        <p class="font-medium">{{ result.origin_city }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tujuan</p>
                        <p class="font-medium">{{ result.destination_city }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jenis barang</p>
                        <p class="font-medium">{{ result.item_type }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold">{{ result.current_status }}</p>
                    </div>
                </div>

                <h2 class="font-semibold mb-3">Riwayat perjalanan</h2>
                <ol class="border-1-2 border-gray-200 pl-4 space-y-4">
                    <li v-for="(log, index) in result.logs" :keys="index", class="relative">
                        <span class="absolute -left-[22px] top-1 w-3 h-3 rounded-full bg-blue-600"></span>
                        <p class="font-medium">{{ log.status }}</p>
                        <p v-if="log.location_description" class="text-sm text-gray-600">{{ log.location_description }}</p>
                        <p v-if="log.notes" class="text-sm text-gray-500">{{ log.notes }}</p>
                        <p class="text-xs text-gray-400">{{ formatDate(log.created_at) }}</p>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</template>