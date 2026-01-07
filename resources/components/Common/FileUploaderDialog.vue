<template>
  <v-dialog v-model="dialog" max-width="640" persistent>
    <v-card>
      <v-card-text>
        <v-file-upload
          v-model="file"
          :title="title"
          accept=".pdf"
          :multiple="false"
          show-size
          clearable
          density="default"
          @change="validateFile"
        />
        <div v-if="error" class="text-red text-caption mt-2">{{ error }}</div>
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
          :disabled="isDisabled"
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
import { ref, computed } from "vue";
import { useFileUploaderDialog } from "@composables/useFileUploaderDialog";

defineOptions({ name: "FileUploaderDialog" });

const { dialog, title, file, confirm, cancel } = useFileUploaderDialog();

const error = ref("");

const isDisabled = computed(() => {
  return !file.value || !file.value.name || file.value.size === 0;
});

const validateFile = () => {
  const selected = Array.isArray(file.value) ? file.value[0] : file.value;

  if (!selected) {
    error.value = "Silakan pilih file.";
    return;
  }

  if (selected.size > 3 * 1024 * 1024) {
    error.value = "Ukuran file maksimal 3 MB.";
    file.value = null;
  } else if (!selected.type.includes("pdf")) {
    error.value = "Hanya file PDF yang diperbolehkan.";
    file.value = null;
  } else {
    error.value = "";
  }
};
</script>
