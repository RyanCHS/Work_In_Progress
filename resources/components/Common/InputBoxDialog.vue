<template>
  <v-dialog v-model="dialog" max-width="640" persistent>
    <v-card prepend-icon="mdi-import" :title="title">
      <v-card-text>
        <v-text-field
          v-model="result"
          :rules="[(v) => !!v || 'Label harus diisi']"
          clearable
          required
          variant="underlined"
          class="mb-4"
        />
      </v-card-text>

      <v-card-actions class="justify-end">
        <v-btn
          color="red"
          class="text-none"
          prepend-icon="mdi-cancel"
          @click="cancel"
          >Cancel</v-btn
        >
        <v-btn
          color="primary"
          class="text-none"
          :disabled="!result"
          prepend-icon="mdi-content-save-all-outline"
          @click="confirm"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { useInputTextDialog } from "@composables/useInputTextDialog";
defineOptions({ name: "InputBoxDialog" });
const { dialog, title, result, confirm, cancel } = useInputTextDialog();
</script>
