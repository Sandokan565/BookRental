<template>
  <div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-book"></i> Books List</h2>

    <button class="btn btn-primary mb-4" @click="openModal">+ Add New Book</button>

    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Borrowed</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
        <tr>
          <th>
            <text-input
                id="filterBooksTitle"
                v-model="filters.title"
                placeholder="Filter by title"
                @update:model-value="onFilterChange"
            />
          </th>
          <th>
            <text-input
                id="filterAuthorName"
                v-model="filters.author_name"
                placeholder="Filter by author"
                @update:model-value="onFilterChange"
            />
          </th>
          <th>
            <select-input
                id="filterIsBorrowed"
                v-model="filters.is_borrowed"
                :options="borrowedOptions"
                @update:modelValue="onFilterChange"
            />
          </th>
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
        <tr v-else-if="books.length === 0">
          <td colspan="5" class="text-center">No books found.</td>
        </tr>
        <tr v-for="book in books" :key="book.id">
          <td>{{ book.title }}</td>
          <td>{{ book.author_name }}</td>
          <td>
            <checkbox-input
                :id="'borrowed-' + book.id"
                :model-value="book.is_borrowed"
                label=""
                @update:model-value="val => toggleBorrowed(book, val)"
            />
          </td>
          <td>
            <i class="bi bi-pencil" @click="editBook(book)"></i>
          </td>
          <td>
            <i class="bi bi-trash" @click="deleteBook(book)"></i>
          </td>
        </tr>
      </tbody>
    </table>

    <pagination
        :current-page="currentPage"
        :last-page="totalPages"
        @page-change="changePage"
    />
    <AddBookModal
        ref="addBookModal"
        :authors="authors"
        :initial-book="modalBook"
        @book-saved="handleBookSaved"
    />
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import axios from 'axios'

  import AddBookModal from '@/components/modals/add-book-modal.vue'
  import TextInput from '@/components/inputs/text-input.vue'
  import SelectInput from '@/components/inputs/select-input.vue'
  import CheckboxInput from '@/components/inputs/checkbox-input.vue'
  import Pagination from "@/components/other/pagination.vue";

  interface Author {
    id: number
    name: string
    surname: string
  }

  interface Book {
    id: number
    title: string
    author_id: number
    author_name: string
    is_borrowed: boolean
  }

  const books = ref<Book[]>([])
  const authors = ref<Author[]>([])
  const loading = ref(false)

  const addBookModal = ref<typeof AddBookModal | null>(null)
  const modalBook = ref<Partial<Book> | null>(null)

  const currentPage = ref(1)
  const totalPages = ref(1)

  const filters = ref({
    title: '',
    author_name: '',
    is_borrowed: '',
  })

  const borrowedOptions = [
    { value: '', label: 'All' },
    { value: '0', label: 'No' },
    { value: '1', label: 'Yes' },
  ]

  function debounce<T extends (...args: any[]) => void>(func: T, wait = 300): T {
    let timeout: ReturnType<typeof setTimeout> | null = null
    return function (this: any, ...args: any[]) {
      if (timeout) clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(this, args), wait)
    } as T
  }

  async function fetchAuthors() {
    try {
      const res = await axios.get('/api/author', { params: { perPage: -1 } })
      authors.value = Array.isArray(res.data) ? res.data : res.data.data

    } catch (err) {
      console.error('Failed to load authors', err)
    }
  }

  async function fetchBooks(page = 1) {
    loading.value = true
    try {
      const params: Record<string, any> = { page }

      if (filters.value.title.trim() !== '') params.title = filters.value.title.trim()
      if (filters.value.author_name.trim() !== '') params.author_name = filters.value.author_name.trim()

      if (filters.value.is_borrowed !== '') {
        // convert string to number before sending
        params.is_borrowed = Number(filters.value.is_borrowed)
      }

      const res = await axios.get('/api/book', { params })

      books.value = res.data.data
      totalPages.value = res.data.last_page
      currentPage.value = res.data.current_page
    } catch (err) {
      console.error('Failed to load books', err)
    } finally {
      loading.value = false
    }
  }

  const debouncedFetchBooks = debounce(() => fetchBooks(1), 500)

  function onFilterChange() {
    debouncedFetchBooks()
  }

  function openModal() {
    modalBook.value = null
    addBookModal.value?.showModal()
  }

  function editBook(book: Book) {
    modalBook.value = { ...book }
    addBookModal.value?.showModal()
  }

  async function handleBookSaved(book: Book) {
    filters.value = {
      title: '',
      author_name: '',
      is_borrowed: '',
    }
    await fetchBooks(1)
  }

  async function deleteBook(book: Book) {
    if (!confirm(`Are you sure you want to delete the book "${book.title}"?`)) return

    try {
      await axios.delete(`/api/book/${book.id}`)
      await fetchBooks(currentPage.value)
    } catch (err) {
      console.error('Failed to delete book', err)
    }
  }

  async function toggleBorrowed(book: Book, newVal: boolean) {
    try {
      const res = await axios.patch(`/api/book/${book.id}`, {
        is_borrowed: newVal,
      })
      book.is_borrowed = res.data.is_borrowed
    } catch (err) {
      console.error('Failed to update borrow status', err)
    }
  }

  function changePage(page: number) {
    if (page < 1 || page > totalPages.value) return
    fetchBooks(page)
  }

  onMounted(async () => {
    await fetchAuthors()
    await fetchBooks()
  })
</script>