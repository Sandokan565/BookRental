<template>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-3">
      <router-link class="navbar-brand" to="/">BookRental</router-link>

      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link" to="/authors">Authors</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/books">Books</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style>
  .nav-link:hover, .navbar-brand {
    background-color: #f8f9fa !important;
  }
</style>