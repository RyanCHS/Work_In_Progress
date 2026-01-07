<template>
  <v-dialog v-model="progress" max-width="320" persistent>
    <v-list class="py-2" color="primary" elevation="12" rounded="lg">
      <v-list-item
        prepend-icon="mdi-timer-refresh-outline"
        title="Please wait..."
      >
        <template #prepend>
          <div class="pe-4">
            <v-icon color="primary" size="x-large"></v-icon>
          </div>
        </template>
        <template #append>
          <v-progress-circular
            color="primary"
            indeterminate
            size="16"
            width="2"
          />
        </template>
      </v-list-item>
    </v-list>
  </v-dialog>
</template>

<script setup>
import { useProgress } from "@composables/useProgress";
defineOptions({ name: "ProgressLoader" });

const { progress } = useProgress();
</script>
