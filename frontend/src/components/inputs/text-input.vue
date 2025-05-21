<template>
  <label v-if="label" :for="id" class="form-label">{{ label }}</label>
  <input
      :id="id"
      :type="type"
      class="form-control"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :required="required"
      :placeholder="placeholder"
  />
</template>

<script setup lang="ts">
  defineProps<{
    modelValue: string
    label?: string
    id: string
    type?: string
    required?: boolean
    placeholder?: string
  }>()

  defineEmits(['update:modelValue'])
</script>
