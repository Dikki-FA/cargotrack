<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/lib/api'

const props = defineProps({
    shipment: { type: Object, required: true },
})
const emit = defineEmits(['close', 'updated'])

const authStore = useAuthStore()

const statusByRole = {
    admin: ['pending', 'received_at_warehouse', 'in_transit', 'out_for_delivery', 'delivered', 'failed'],
    warehouse_staff: ['received_at_warehouse', 'in_transit'],
    courier: ['out_for delivery', 'delivered'],
}

const allowedStatuses = computed(() => statusByRole[authStore.role] ?? [])

const status = ref('')
const locationDescription = ref('')
const notes = ref('')
const isSubmitting = ref(false)
const errorMessage = ref('')

async function handleSubmit() {
    errorMessage.value = ''
    isSubmitting.value = true
    
    try {
        await api.patch('/shipments/${props.shipment.id}/status', {
            status: status.value,
            location_description: locationDescription.value || null,
            notes: notes.value || null,
        })
        emit('updated')
        emit('close')
    } catch (error) {
        if (error.response?.status === 403) {
            errorMessage.value = 'Kamu tidak berhak mengubah status ini (bukan gudang/tugasmu, atau status tidak sesuai perananmu).'
        } else if (error.response?.status === 422) {
            errorMessage.value = Object.values(error.response.data.errors)[0][0]
        } else {
            errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Update Status</h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">X</button>
            </div>

            <p class="text-sm text-gray-500 mb-4">
                Resi: <span class="font-mono">{{ shiupment.tracking_number }}</span><br />
                Status saat ini: <span class="font-semibold">{{ shipment.current_status }}</span>
            </p>

            <form @submit.prevent="handleSubmit" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status baru</label>
                    <select v-model="status" required class="w-full border rounded px-3 py-2">
                        <option value="" disabled>Pilih Status</option>
                        <option v-for="s in allowedStatuses" :key="s" value="s">{{ s }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi (optional)</label>
                    <input v-model="locationDescription" class="w-full border rounded px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (optional)</label>
                    <textarea v-model="notes" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                </div>

                <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>

                <button
                    type="submit"
                    :disabled="isSubmitting || !status"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Menyimpan..' : 'Update Status' }}
                </button>
            </form>
        </div>
    </div>
</template>