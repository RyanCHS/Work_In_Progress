<template>
  <div
    class="breadcrumb-card mb-25 d-md-flex align-items-center justify-space-between"
  >
    <h5 class="mb-0">{{ pageTitle }}</h5>
    <ol class="breadcrumb list-unstyled mt-0 mb-0 pl-0">
      <li class="breadcrumb-item d-inline-block position-relative">
        <RouterLink to="/admin" class="d-inline-block position-relative">
          <i class="material-symbols-outlined"> home </i>
          Dashboard
        </RouterLink>
      </li>
      <li
        v-if="subTitle"
        class="breadcrumb-item d-inline-block position-relative"
      >
        {{ subTitle }}
      </li>
      <li class="breadcrumb-item d-inline-block position-relative">
        {{ pageTitle }}
      </li>
    </ol>
  </div>
</template>

<script setup>
defineOptions({
  name: "PageTitle",
});

const props = defineProps({
  pageTitle: String,
  subTitle: String,
});
</script>
