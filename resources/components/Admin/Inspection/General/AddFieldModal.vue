<template>
  <v-dialog v-model="dialog" max-width="400" persistent>
    <v-card>
      <v-card-title class="text-h6">Tambah Field Baru</v-card-title>
      <v-card-text>
        <v-btn
          block
          color="primary"
          class="text-none mb-3"
          @click="select('working')"
          :disabled="disableWorkingPermit"
        >
          Tambah Working Permit
        </v-btn>
        <v-btn
          block
          color="secondary"
          class="text-none"
          @click="select('field')"
        >
          Tambah Field Lainnya
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
import { watch, computed } from "vue";

defineOptions({
  name: "AddFieldModal",
});

const props = defineProps({
  modelValue: String,
  disableWorkingPermit: Boolean,
});
const emit = defineEmits(["update:modelValue", "choose"]);

const dialog = computed({
  get: () => !!props.modelValue,
  set: (val) => {
    if (!val) emit("update:modelValue", null); // close modal
  },
});

watch(
  () => props.modelValue,
  (val) => {
    dialog.value = val;
  }
);

const close = () => {
  dialog.value = false;
};

const select = (type) => {
  emit("choose", type); // emit 'working' atau 'field'
  close();
};
</script>
