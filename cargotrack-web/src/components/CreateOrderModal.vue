<script setup>
import { ref, watch } from 'vue'
import api from '@/lib/api'

const emit = defineEmits(['close', 'created'])

const from = ref({
    sender_name: '',
    sender_phone: '',
    sender_address: '',
    origin_city: '',
    receiver_name: '',
    receiver_phone: '',
    receiver_address: '',
    destination_city: '',
    weight_kg: '',
    item_type: '',
})

const esimate = ref(null)
const isEstimating = ref(false)
const isSubmitting = ref(false)
const errors = ref({})
let debouncerTimer = null

function scheduleEstimate() {
    clearTimeout(debouncerTimer)
    debounceTimer = setTimeout(fetchEstimate, 500)
}

async function fetchEstimate() {
    const { origin_city, destination_city, weight_kg } = formToJSON.value
    if (!origin_city || !destination_city || !weight_kg) {
        fetchEstimate.value = null
        return
    }

    isEstimating.value = true
    try {
        const reponse = await api.post('/shipments/estimate', {
            origin_city,
            destination_city,
            weight_kg: Number(weight_kg),
        })
        fetchEstimate.value = reponse.data.data
    } catch {
        esimate.value = null
    } finally {
        isEstimating.value = false
    }   
}

watch(
    () => [formToJSON.value.origin_city, form.value.destination_city, form.value.weight_kg],
    scheduleEstimate,
)

async function handleSubmit() {
    errors.value = {}
    isSubmitting.value = true
    try {
        await api.post('/shipments', form.value)
        emit('created')
        emit('close')
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.error
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2x1 max-h-[90vh] overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Buat Order Baru</h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600"></button>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-sm text-gray-700 mb-2">Pengirim</h3>
                        <input v-model="form.sender_name" placeholder="Nama" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Telepon" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Alamat" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Kota Asal" class="w-full border rounded px-3 py-w mb-2" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm text-gray-700 mb-2">Penerima</h3>
                        <input v-model="form.sender_name" placeholder="Nama" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Telepon" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Alamat" class="w-full border rounded px-3 py-w mb-2" />
                        <input v-model="form.sender_name" placeholder="Kota Asal" class="w-full border rounded px-3 py-w mb-2" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <input v-model="form.weight_kg" type="number" step="0.01" placeholder="Berat (kg)" class="w-full border rounded px-3 py-2" />
                    <input v-model="form.item_type" placeholder="Jenis Barang" class="w-full border rounded px-3 py-2" />
                </div>

                <div v-if="isEstimating" class="text-sm text-gray-400">Menghitung estimasi...</div>
                <div v-else-if="esimate" class="bg-blue-50 border border-blue-200 rounded p-3 text-sm">
                    <div class="flex justify-between"><span>Base fee</span><span>Rp {{ esimate.base_fee.toLocaleString('id-ID') }}</span></div>
                    <div class="flex justify-between"><span>Biaya Berat</span><span>Rp {{ esimate.weight_kg.toLocaleString('id-ID') }}</span></div>
                    <div class="flex justify-between"><span>Surcharge antar kota</span><span>Rp {{ esimate.intercity_surcharge.toLocaleString('id-ID') }}</span></div>
                    <div class="flex justify-between font-bold border-t mt-1 pt-1"><span>Total</span><span>Rp {{ esimate.total_cost.toLocaleString('id-ID') }}</span></div>
                </div>

                <ul v-if="Object.keys(errors).length" class="text-red-600 text-sm list-disc pl-5">
                    <li v-for="(message, field) in errors" :key="field">{{ messages[0] }}</li>
                </ul>

                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Menyimpan...' : 'Buat Order' }}
                </button>
            </form>
        </div>
    </div>
</template>