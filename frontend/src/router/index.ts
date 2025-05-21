import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'main',
      component: () => import('../views/main.vue'),
    },
    {
      path: '/books',
      name: 'books',
      component: () => import('../views/books.vue'),
    },
    {
      path: '/authors',
      name: 'authors',
      component: () => import('../views/authors.vue'),
    },
  ],
})

export default router
