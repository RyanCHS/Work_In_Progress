<template>
  <v-dialog v-model="dialog" max-width="640" persistent>
    <v-card>
      <v-card-title class="text-h6">{{ props.headerLabel }}</v-card-title>
      <v-card-text>
        <v-text-field
          v-model="form.label"
          :rules="[(v) => !!v || 'Label harus diisi']"
          clearable
          required
          variant="underlined"
          class="mb-4"
        />

        <v-radio-group v-model="selectedOption" class="mb-4">
          <div
            v-for="(option, index) in form.options"
            :key="index"
            class="d-flex align-center mb-2"
          >
            <v-radio
              :value="form.options[index]"
              :label="form.options[index]"
              class="me-2"
            />
            <v-text-field
              v-model="form.options[index]"
              placeholder="Nama Opsi"
              density="compact"
              hide-details
              style="max-width: 200px"
              @input="updateSelectedOption(index)"
            />
            <v-btn icon @click="removeOption(index)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
        </v-radio-group>

        <div class="d-flex justify-center">
          <v-btn color="primary" class="text-none" @click="addOption">
            Add Option
          </v-btn>
        </div>
      </v-card-text>

      <v-card-actions class="justify-end">
        <v-btn
          color="red"
          class="text-none"
          prepend-icon="mdi-cancel"
          @click="close"
          >Cancel</v-btn
        >
        <v-btn
          color="primary"
          class="text-none"
          :disabled="!form.label || form.options.length === 0"
          prepend-icon="mdi-content-save-all-outline"
          @click="save"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed, reactive, ref, watch, onMounted } from "vue";
import { slugify } from "@utils/text";
const form = reactive({
  label: "",
  options: ["Comply", "Tidak Comply"], // default opsi awal
});

const selectedOption = ref(form.options[0]);

defineOptions({ name: "RadioInputDialog" });

const props = defineProps({
  modelValue: Boolean,
  headerLabel: String,
  fieldKey: String,
  isEdit: Boolean,
  initialValue: Object,
});

const emit = defineEmits(["update:modelValue", "save"]);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => {
    emit("update:modelValue", val);
  },
});

const addOption = () => {
  form.options.push(""); // tambahkan opsi kosong
};

const removeOption = (index) => {
  form.options.splice(index, 1);
};

const close = () => {
  dialog.value = false;
};

const updateSelectedOption = (index) => {
  if (selectedOption.value === form.options[index]) {
    selectedOption.value = form.options[index];
  }
};

onMounted(() => {
  if (props.isEdit) {
    form.label = props.initialValue?.label ?? "";
    form.options = [...(props.initialValue?.options || [])];
    selectedOption.value = props.initialValue?.default || "";
  }
});

watch(
  () => props.initialValue,
  (val) => {
    if (val) {
      form.label = val.label || "";
      form.options = [...(val.options || [])];
      selectedOption.value = val.default || "";
    } else {
      form.label = "";
      form.options = ["Comply", "Tidak Comply"];
      selectedOption.value = "Comply";
    }
  }
);

const save = () => {
  const newField = {
    old_key: props.initialValue?.key ?? "",
    key: props.fieldKey ?? slugify(form.label),
    type: "field",
    label: form.label,
    inline: true,
    default: selectedOption.value || "",
    options: [...form.options],
    is_input: true,
    required: true,
    input_type: "radio",
    is_template: true,
    is_edit: props.isEdit,
    answer: "",
  };
  emit("save", newField);
  dialog.value = false;
};
</script>
