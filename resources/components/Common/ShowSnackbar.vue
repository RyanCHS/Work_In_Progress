<template>
  <v-snackbar
    v-model="snackbar.show"
    :color="snackbar.color"
    location="bottom right"
  >
    {{ snackbar.message }}
  </v-snackbar>
</template>

<script setup>
import { useSnackbar } from "@composables/useSnackbar";

const { snackbar } = useSnackbar();
</script>
