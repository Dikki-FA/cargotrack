<script setup>
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import api from '@/lib/api'

const authStore = useAuthStore()
const router = useRouter()

const shipments = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const isLoading = ref(false)

const search = ref('')
const statusFilter = ref('')
const page = ref(1)

const statusOptions = [
  'pending',
  'received_at_warehouse',
  'in_transit',
  'out_for_delivery',
  'delivered',
  'failed',
]

async function fetchShipments() {
  isLoading.value = true
  try {
    const response = await api.get('/shipments', {
      params: {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        page: page.value,
      },
    })
    shipments.value = response.data.data
    meta.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
    }
  } catch (error) {
    console.error('Gagal memuat shipment', error)
  } finally {
    isLoading.value = false
  }
}

function goToPage(newPage) {
  if (newPage < 1 || newPage > meta.value.last_page) return
  page.value = newPage
}

function handleLogout() {
  authStore.clearToken()
  router.push('/login')
}

onMounted(fetchShipments)
watch([search, statusFilter, page], fetchShipments)
watch([search, statusFilter], () => { page.value = 1 })
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">CargoTrack Dashboard</h1>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ authStore.user?.name }} ({{ authStore.role }})</span>
        <button @click="handleLogout" class="text-sm text-red-600 hover:underline">Logout</button>
      </div>
    </nav>

    <div class="p-6">
      <div class="flex flex-wrap gap-4 mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Cari resi / nama..."
          class="border border-gray-300 rounded px-3 py-2 flex-1 min-w-[200px]"
        />
        <select v-model="statusFilter" class="border border-gray-300 rounded px-3 py-2">
          <option value="">Semua status</option>
          <option v-for="status in statusOptions" :key="status" :value="status">
            {{ status }}
          </option>
        </select>
      </div>

      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-100 text-gray-600">
            <tr>
              <th class="px-4 py-3">Resi</th>
              <th class="px-4 py-3">Pengirim</th>
              <th class="px-4 py-3">Penerima</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Biaya</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="5" class="px-4 py-6 text-center text-gray-400">Memuat...</td>
            </tr>
            <tr v-else-if="shipments.length === 0">
              <td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada data.</td>
            </tr>
            <tr v-for="shipment in shipments" :key="shipment.id" class="border-t">
              <td class="px-4 py-3 font-mono">{{ shipment.tracking_number }}</td>
              <td class="px-4 py-3">{{ shipment.sender_name }}</td>
              <td class="px-4 py-3">{{ shipment.receiver_name }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded text-xs bg-gray-100">{{ shipment.current_status }}</span>
              </td>
              <td class="px-4 py-3">Rp {{ Number(shipment.shipping_cost).toLocaleString('id-ID') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
        <span>Total: {{ meta.total }} shipment</span>
        <div class="flex gap-2">
          <button
            @click="goToPage(meta.current_page - 1)"
            :disabled="meta.current_page <= 1"
            class="px-3 py-1 border rounded disabled:opacity-40"
          >
            Prev
          </button>
          <span>Halaman {{ meta.current_page }} / {{ meta.last_page }}</span>
          <button
            @click="goToPage(meta.current_page + 1)"
            :disabled="meta.current_page >= meta.last_page"
            class="px-3 py-1 border rounded disabled:opacity-40"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>