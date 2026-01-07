<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    max-width="290px"
    min-width="auto"
  >
    <template #activator="{ props: activatorProps }">
      <v-text-field
        v-bind="activatorProps"
        v-model="displayDate"
        :label="label"
        readonly
        clearable
        :rules="rules"
        @click:clear="clearDate"
      />
    </template>

    <v-date-picker
      v-model="internalDate"
      @update:model-value="onDateSelected"
      hide-header
    />
  </v-menu>
</template>

<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
  modelValue: String,
  label: String,
  rules: Array,
});
const emit = defineEmits(["update:modelValue"]);

const activatorRef = ref(null);
const menu = ref(false);
const internalDate = ref(props.modelValue);

watch(
  () => props.modelValue,
  (val) => {
    internalDate.value = val;
  }
);

function onDateSelected(val) {
  emit("update:modelValue", val);
  menu.value = false;
}

const displayDate = computed(() => {
  if (!internalDate.value) return "";
  const date = new Date(internalDate.value);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
});

function clearDate() {
  internalDate.value = null;
  emit("update:modelValue", null);
}
</script>
