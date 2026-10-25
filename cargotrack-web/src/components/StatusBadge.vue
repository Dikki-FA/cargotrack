<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true },
})

const styles = {
  pending: 'bg-yellow-100 text-yellow-800',
  received_at_warehouse: 'bg-blue-100 text-blue-800',
  in_transit: 'bg-indigo-100 text-indigo-800',
  out_for_delivery: 'bg-orange-100 text-orange-800',
  delivered: 'bg-green-100 text-green-800',
  failed: 'bg-red-100 text-red-800',
}

const classes = computed(() => styles[props.status] ?? 'bg-gray-100 text-gray-800')
</script>

<template>
  <span :class="['px-2 py-1 rounded text-xs font-medium', classes]" data-testid="status-badge">
    {{ status }}
  </span>
</template>