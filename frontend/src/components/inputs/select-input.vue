<template>
  <label v-if="label" :for="id" class="form-label">{{ label }}</label>
  <select
      :id="id"
      class="form-select"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :required="required"
  >
    <option value="" disabled>
      Select {{ label ? label.toLowerCase() : 'option' }}
    </option>

    <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>

<script setup lang="ts">
  defineProps<{
    modelValue: string | number | boolean
    id: string
    options: { value: string | number | boolean; label: string }[]
    label?: string
    required?: boolean
  }>()

  defineEmits(['update:modelValue'])
</script>