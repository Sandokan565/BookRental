<template>
  <label class="form-check-label" :for="id">{{ label }}</label>
  <input
      class="form-check-input"
      type="checkbox"
      :id="id"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
  />
</template>

<script setup lang="ts">
  defineProps<{
    modelValue: boolean | number
    label: string
    id: string
  }>()

  defineEmits(['update:modelValue'])
</script>