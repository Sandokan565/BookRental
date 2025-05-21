<template>
  <div>
    <main-navbar />
    <router-view />
  </div>
</template>

<script setup lang="ts">
  import { RouterView } from 'vue-router'
  import MainNavbar from "@/components/navbars/main-navbar.vue";
</script>

<style>
  body {
    all: unset;
  }
  #app {
    all: unset;
  }
  .bi {
    cursor: pointer;
  }
</style>
