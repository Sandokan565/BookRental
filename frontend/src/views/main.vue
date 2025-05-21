<template>
  <div class="container text-center mt-5">
    <h1 class="display-4">Welcome to BookRental</h1>
    <p class="lead mt-3">Assignment developed for Let's Consult.</p>
    <img :src="logo" alt="Logo" />
  </div>
</template>

<script setup lang="ts">
  import logo from '@/assets/lc_logo.jpg'
</script>

<style>
  img {
    width: 15%;
  }
</style>
