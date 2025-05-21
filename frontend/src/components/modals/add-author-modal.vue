<template>
  <div
      class="modal fade"
      id="addEditAuthorModal"
      tabindex="-1"
      aria-labelledby="addEditAuthorModalLabel"
      aria-hidden="true"
      ref="modal"
  >
    <div class="modal-dialog">
      <form @submit.prevent="submitForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEditAuthorModalLabel">
            {{ isEditMode ? 'Edit Author' : 'Add New Author' }}
          </h5>
          <button
              type="button"
              class="btn-close"
              @click="hideModal"
              aria-label="Close"
          ></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <text-input
                v-model="name"
                id="authorName"
                label="Name"
                required
                placeholder="Enter author's name"
            />
          </div>
          <div class="mb-3">
            <text-input
                v-model="surname"
                id="authorSurname"
                label="Surname"
                required
                placeholder="Enter author's surname"
            />
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            {{ isEditMode ? 'Save Changes' : 'Save Author' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, watch, defineEmits, defineProps, onMounted, onBeforeUnmount } from 'vue'
  import axios from 'axios'
  import { Modal } from 'bootstrap'

  import TextInput from '@/components/inputs/text-input.vue'

  interface Author {
    id?: number
    name: string
    surname: string
  }

  const emit = defineEmits<{
    (e: 'author-saved', author: Author): void
  }>()

  const props = defineProps<{
    initialAuthor: Partial<Author> | null
  }>()

  const name = ref('')
  const surname = ref('')

  const modal = ref<HTMLElement | null>(null)
  let bsModal: Modal | null = null

  const isEditMode = ref(false)

  watch(
      () => props.initialAuthor,
      (newVal) => {
        if (newVal) {
          name.value = newVal.name || ''
          surname.value = newVal.surname || ''
          isEditMode.value = !!newVal.id
        } else {
          name.value = ''
          surname.value = ''
          isEditMode.value = false
        }
      },
      { immediate: true }
  )

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
    try {
      if (isEditMode.value && props.initialAuthor?.id) {
        const res = await axios.put(`/api/author/${props.initialAuthor.id}`, {
          name: name.value,
          surname: surname.value,
        })
        emit('author-saved', res.data)

      } else {
        const res = await axios.post('/api/author', {
          name: name.value,
          surname: surname.value,
        })
        emit('author-saved', res.data)
      }
      hideModal()

    } catch (error) {
      console.error('Failed to save author', error)
    }
  }

  defineExpose({ showModal })
</script>