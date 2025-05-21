<template>
  <div
      class="modal fade"
      id="addBookModal"
      tabindex="-1"
      aria-labelledby="addBookModalLabel"
      aria-hidden="true"
      ref="modal"
  >
    <div class="modal-dialog">
      <form @submit.prevent="submitForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addBookModalLabel">
            {{ isEditMode ? 'Edit Book' : 'Add New Book' }}
          </h5>
          <button type="button" class="btn-close" @click="hideModal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <text-input
                v-model="title"
                id="bookTitle"
                label="Title"
                required
                placeholder="Enter book title"
            />
          </div>
          <div class="mb-3">
            <select-input
                v-model="authorId"
                id="authorSelect"
                label="Author"
                :options="authorOptions"
                required
            />
          </div>
          <div class="mb-3">
            <select-input
                v-model="borrowed"
                id="borrowedSelect"
                label="Borrowed"
                :options="borrowedOptions"
                required
            />
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            {{ isEditMode ? 'Update Book' : 'Save Book' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, defineProps, defineEmits, watch, onMounted, onBeforeUnmount, computed } from 'vue'
  import axios from 'axios'
  import { Modal } from 'bootstrap'

  import TextInput from '@/components/inputs/text-input.vue'
  import SelectInput from '@/components/inputs/select-input.vue'

  interface Author {
    id: number
    name: string
    surname: string
  }

  interface Book {
    id?: number
    title: string
    author_id: number
    is_borrowed: boolean
  }

  const props = defineProps<{
    authors: Author[]
    initialBook: Partial<Book> | null
  }>()

  const emit = defineEmits<{
    (e: 'book-saved', book: Book): void
  }>()

  const modal = ref<HTMLElement | null>(null)
  let bsModal: Modal | null = null

  const title = ref('')
  const authorId = ref<number | ''>('')
  const borrowed = ref(false)
  const isEditMode = ref(false)
  let editingBookId: number | null = null

  const authorOptions = computed(() =>
      props.authors.map(a => ({ value: a.id, label: `${a.name} ${a.surname}` }))
  )

  const borrowedOptions = [
    { label: 'No', value: false },
    { label: 'Yes', value: true }
  ]

  watch(
      () => props.initialBook,
      (book) => {
        if (book) {
          isEditMode.value = true
          editingBookId = book.id ?? null
          title.value = book.title ?? ''
          authorId.value = book.author_id ?? ''
          borrowed.value = Boolean(book.is_borrowed)
        } else {
          resetForm()
        }
      },
      { immediate: true }
  )

  function resetForm() {
    isEditMode.value = false
    editingBookId = null
    title.value = ''
    authorId.value = ''
    borrowed.value = false
  }

  onMounted(() => {
    if (modal.value) {
      bsModal = new Modal(modal.value)
    }
  })

  onBeforeUnmount(() => {
    bsModal?.dispose()
  })

  function showModal() {
    bsModal?.show()
  }

  function hideModal() {
    bsModal?.hide()
  }

  async function submitForm() {
    const payload = {
      title: title.value,
      author_id: authorId.value,
      is_borrowed: borrowed.value === true || borrowed.value === 'true'
    }

    try {
      let res

      if (isEditMode.value && editingBookId !== null) {
        res = await axios.patch(`/api/book/${editingBookId}`, payload)

      } else {
        res = await axios.post('/api/book', payload)
      }
      emit('book-saved', res.data)

      hideModal()
      resetForm()

    } catch (err) {
      console.error('Failed to save book:', err)
    }
  }

  defineExpose({ showModal })
</script>