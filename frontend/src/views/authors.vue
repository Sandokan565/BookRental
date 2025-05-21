<template>
  <div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-person"></i> Authors List</h2>

    <button class="btn btn-primary mb-4" @click="openModal(null)">+ Add New Author</button>

    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Surname</th>
          <th>Number of Books</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
        <tr>
          <th>
            <text-input
                v-model="filters.name"
                placeholder="Filter by name"
                @update:model-value="onFilterChange"
                id="filterName"
            />
          </th>
          <th>
            <text-input
                v-model="filters.surname"
                placeholder="Filter by surname"
                @update:model-value="onFilterChange"
                id="filterSurname"
            />
          </th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        <tr v-if="loading">
          <td colspan="5" class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </td>
        </tr>
        <tr v-else-if="authors.data.length === 0">
          <td colspan="5" class="text-center">No authors found.</td>
        </tr>
        <tr v-for="author in authors.data" :key="author.id">
          <td>{{ author.name }}</td>
          <td>{{ author.surname }}</td>
          <td>
            <span class="badge bg-secondary">{{ author.books_count }}</span>
          </td>
          <td>
            <i class="bi bi-pencil" @click="openModal(author)"></i>
          </td>
          <td>
            <i class="bi bi-trash" @click="deleteAuthor(author)"></i>
          </td>
        </tr>
      </tbody>
    </table>

    <pagination
        :current-page="authors.current_page"
        :last-page="authors.last_page"
        @page-change="fetchAuthors"
    />
    <AddEditAuthorModal
        ref="addEditAuthorModal"
        :initial-author="modalAuthor"
        @author-saved="handleAuthorSaved"
    />
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import AddEditAuthorModal from '@/components/modals/add-author-modal.vue'
  import TextInput from "@/components/inputs/text-input.vue"
  import Pagination from "@/components/other/pagination.vue";

  interface Author {
    id: number
    name: string
    surname: string
    books_count: number
  }

  const authors = ref({
    data: [] as Author[],
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const loading = ref(false)
  const addEditAuthorModal = ref<InstanceType<typeof AddEditAuthorModal> | null>(null)
  const modalAuthor = ref<Partial<Author> | null>(null)

  const filters = ref({
    name: '',
    surname: '',
  })

  function debounce<T extends (...args: any[]) => void>(func: T, wait = 300): T {
    let timeout: ReturnType<typeof setTimeout> | null = null
    return function (this: any, ...args: any[]) {
      if (timeout) clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(this, args), wait)
    } as T
  }

  async function fetchAuthors(page = 1) {
    loading.value = true
    try {
      const params: Record<string, any> = { page }

      if (filters.value.name.trim() !== '') {
        params.name = filters.value.name.trim()
      }
      if (filters.value.surname.trim() !== '') {
        params.surname = filters.value.surname.trim()
      }

      const res = await axios.get('/api/author', { params })
      authors.value = res.data
    } catch (err) {
      console.error('Failed to load authors', err)
    } finally {
      loading.value = false
    }
  }

  const debouncedFetchAuthors = debounce(() => fetchAuthors(1), 500)

  function onFilterChange() {
    debouncedFetchAuthors()
  }

  function openModal(author: Author | null) {
    modalAuthor.value = author ? { ...author } : null
    addEditAuthorModal.value?.showModal()
  }

  async function handleAuthorSaved(author: Partial<Author>) {
    await fetchAuthors(authors.value.current_page)
  }

  async function deleteAuthor(author: Author) {
    if (!confirm(`Are you sure you want to delete author "${author.name} ${author.surname}"?`)) {
      return
    }
    try {
      await axios.delete(`/api/author/${author.id}`)
      await fetchAuthors(authors.value.current_page)
    } catch (err) {
      console.error('Failed to delete author', err)
    }
  }

  onMounted(() => {
    fetchAuthors()
  })
</script>