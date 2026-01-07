<template>
  <v-dialog v-model="dialog" max-width="400" persistent>
    <v-card>
      <v-card-title class="text-h6">Tambah Field Baru</v-card-title>
      <v-card-text>
        <v-btn
          color="primary"
          class="w-100 text-none mb-4"
          @click="select('text')"
        >
          Tambah Text Input
        </v-btn>
        <v-btn
          color="primary"
          class="w-100 text-none mb-4"
          @click="select('selector')"
        >
          Tambah Selector
        </v-btn>
        <v-btn
          color="primary"
          class="w-100 text-none"
          @click="select('checkbox')"
        >
          Tambah Checkbox
        </v-btn>
      </v-card-text>

      <v-card-actions class="justify-end">
        <v-btn
          color="red"
          class="text-none"
          prepend-icon="mdi-cancel"
          @click="close"
          >Tutup</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed } from "vue";

defineOptions({ name: "AddGeneralFieldModal" });

const props = defineProps({
  modelValue: String,
});
const emit = defineEmits(["update:modelValue", "choose"]);

const dialog = computed({
  get: () => props.modelValue === "general",
  set: (val) => {
    if (!val) emit("update:modelValue", null); // Tutup modal jika false
  },
});

const close = () => {
  dialog.value = false;
};

const select = (type) => {
  emit("choose", type); // Emit ke parent (akan ubah modalType lagi)
  close(); // Menutup modal
};
</script>
