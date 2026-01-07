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

        <v-container fluid>
          <v-checkbox
            v-model="valCheckbox"
            :label="valCheckbox ? form.options[0] : form.options[1]"
          ></v-checkbox>
        </v-container>
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

const valCheckbox = ref(false);

const selectedOption = ref(form.options[0]);

defineOptions({ name: "CheckboxInputDialog" });

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

const close = () => {
  dialog.value = false;
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
    default: valCheckbox.value || "",
    options: [...form.options],
    is_input: true,
    required: true,
    input_type: "checkbox",
    is_template: true,
    is_edit: props.isEdit,
    answer: "",
  };
  emit("save", newField);
  dialog.value = false;
};
</script>
