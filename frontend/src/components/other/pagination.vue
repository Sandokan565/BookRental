<template>
  <nav aria-label="Page navigation" v-if="lastPage > 1">
    <ul class="pagination justify-content-center">
      <li :class="['page-item', { disabled: currentPage === 1 }]">
        <button
            class="page-link"
            @click="$emit('page-change', currentPage - 1)"
            :disabled="currentPage === 1"
        >
          Previous
        </button>
      </li>

      <li
          v-for="page in lastPage"
          :key="page"
          :class="['page-item', { active: page === currentPage }]"
      >
        <button class="page-link" @click="$emit('page-change', page)">
          {{ page }}
        </button>
      </li>

      <li :class="['page-item', { disabled: currentPage === lastPage }]">
        <button
            class="page-link"
            @click="$emit('page-change', currentPage + 1)"
            :disabled="currentPage === lastPage"
        >
          Next
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">
  defineProps<{
    currentPage: number
    lastPage: number
  }>()

  defineEmits<{
    (e: 'page-change', page: number): void
  }>()
</script>