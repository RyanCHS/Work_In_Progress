<template>
  <!-- Field -->
  <template v-if="item.type === 'field'">
    <!-- Radio Group -->
    <div v-if="item.input_type === 'radio'">
      <v-label class="d-block fw-normal text-black ml-2">
        {{ item.label }}
      </v-label>
      <v-radio-group
        v-model="model"
        :rules="[item.required ? (v) => !!v || 'Wajib diisi' : null]"
        class="my-2"
        :inline="item.inline ?? false"
      >
        <v-radio
          v-for="option in item.options"
          :key="option"
          :label="option"
          :value="option"
          class="mr-4"
        />
      </v-radio-group>
    </div>
    <div v-if="item.input_type === 'checkbox'">
      <v-label class="d-block fw-normal text-black ml-2">
        {{ item.label }}
      </v-label>
      <v-checkbox
        v-model="model"
        :label="model ? item.options[0] : item.options[1]"
      ></v-checkbox>
    </div>
    <!-- Select, Text Field, etc -->
    <component
      v-else-if="item.input_type !== 'date'"
      :is="resolveComponent(item.input_type)"
      v-model="model"
      :label="item.label"
      :items="item.options"
      :rules="[item.required ? (v) => !!v || 'Wajib diisi' : null]"
      clearable
    />

    <DateInput
      v-else-if="item.input_type === 'date'"
      v-model="model"
      :label="item.label"
      :rules="[item.required ? (v) => !!v || 'Wajib diisi' : null]"
    />
  </template>

  <!-- Category (Dynamic Table) -->
  <div v-else-if="item.type === 'category'">
    <h5 class="text-md font-bold mb-2">{{ item.label }}</h5>
    <DynamicTable :category="item" v-model="model" />
  </div>
</template>

<script setup>
import { computed } from "vue";
import DynamicTable from "@components/Common/DynamicFormTable.vue";
import DateInput from "@components/Common/DateInput.vue";

defineOptions({
  name: "DynamicForm",
});

const props = defineProps({
  item: Object,
  modelValue: [String, Number, Array, Object, Boolean],
});

const emit = defineEmits(["update:modelValue"]);

// Gunakan computed agar reaktif dan sinkron dua arah
const model = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

// Resolver komponen berdasarkan input_type
function resolveComponent(type) {
  switch (type) {
    case "text":
    case "number":
      return "v-text-field";
    case "select":
      return "v-select";
    default:
      return "v-text-field";
  }
}
</script>

<style scoped>
.v-date-input {
  width: 100%;
}
</style>
