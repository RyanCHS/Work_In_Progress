<template>
  <v-dialog v-model="dialog" max-width="640" persistent>
    <v-card>
      <v-card-title class="text-h6">
        <div>
          {{ props.headerLabel }}
          <v-tooltip :text="textTooltip">
            <template #activator="{ props }">
              <v-icon
                v-bind="props"
                color="primary"
                icon="mdi-information"
                size="x-small"
                style="vertical-align: super"
              />
            </template>
          </v-tooltip>
        </div>
      </v-card-title>
      <v-card-text>
        <v-text-field
          v-model="form.label"
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
          @click="close"
          >Cancel</v-btn
        >
        <v-btn
          color="primary"
          class="text-none"
          :disabled="!form.label"
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
import { computed, reactive, ref, watch, nextTick, onMounted } from "vue";
import { slugify } from "@utils/text";

const form = reactive({
  label: "",
});

const textTooltip = ref(
  "Text label harus diisi, ini merupakan judul untuk text field. Contoh : 'Nama Pemegang SWA'"
);

defineOptions({ name: "TextInputDialog" });

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
  set: (val) => emit("update:modelValue", val),
});

// Solusi utama: watch dialog (bukan props.initialValue langsung)
onMounted(() => {
  if (props.isEdit) {
    form.label = props.initialValue?.label ?? "";
  }
});

watch(
  () => props.modelValue,
  async (val) => {
    if (val) {
      await nextTick();
      form.label = props.initialValue?.label ?? "";
    }
  }
);

const close = () => {
  dialog.value = false;
};

const save = () => {
  const newField = {
    old_key: props.initialValue?.key ?? "",
    key: props.fieldKey ?? slugify(form.label),
    type: "field",
    label: form.label,
    inline: true,
    default: props.initialValue?.default || "",
    is_input: true,
    required: true,
    input_type: "text",
    is_template: true,
    is_edit: props.isEdit,
    answer: "",
  };
  emit("save", newField);
  close();
};
</script>
