<template>
  <div class="text-center pa-4">
    <v-dialog v-model="dialog" max-width="400" persistent>
      <v-card
        prepend-icon="mdi-message-question-outline"
        :title="title"
        :text="message"
      >
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
            prepend-icon="mdi-check-outline"
            @click="confirm"
          >
            Ok
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { useConfirmDialog } from "@composables/useConfirmDialog";
defineOptions({ name: "ConfirmDialog" });

const { dialog, title, message, confirm, cancel } = useConfirmDialog();
</script>
