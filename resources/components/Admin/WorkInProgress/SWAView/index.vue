<template>
  <v-card
    class="trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <div class="add-btn" @click="goBack">
        <i class="material-symbols-outlined">
          <span class="material-symbols-outlined"> arrow_back </span>
        </i>
        Back
      </div>
    </div>
    <div v-if="content.length > 0">
      <SWAResultTable :data="content" />
    </div>
    <div v-else><v-skeleton-loader type="article"></v-skeleton-loader></div>
  </v-card>
</template>
<script setup>
import { watch, ref } from "vue";
import { formatDate } from "@utils/date";
import SWAResultTable from "@components/Admin/WorkInProgress/General/SWAResultTable.vue";
defineOptions({
  name: "SWAView",
});

const content = ref([]);
const fileInputs = ref([]);

const props = defineProps({
  InputInspection: {
    type: Array,
    required: true,
  },
  FileInputs: {
    type: Array,
    required: true,
  },
  FileIdWP: { type: String, required: true },
});

const workingPermitPDF = async () => {
  if (props.FileIdWP === null) return;
  const pdfUrl = `/storage/uploads/${props.FileIdWP}.pdf`;
  const matchedFile = content.value.find((f) => f.key === "working_permit");
  matchedFile.pdfUrl = pdfUrl;
};

const loadData = () => {
  content.value = props.InputInspection.map((item) => {
    if (item.input_type === "date") {
      item.answer = formatDate(item.answer);
      item.default = formatDate(item.default);
    }

    if( item.input_type === "checkbox") {
      item.answer = item.answer ? item.options[0] : item.options[1];
      item.default = item.default ? item.options[0] : item.options[1];
    }

    const matchedFile = props.FileInputs.find((f) => f.Keytag === item.key);

    const pdfUrl = matchedFile
      ? `/storage/uploads/${matchedFile.FileId}.pdf`
      : null;

    return {
      ...item,
      pdfUrl,
    };
  });
};

watch(
  () => props.InputInspection,
  async (val) => {
    await loadData();
    await workingPermitPDF();
  },
  { immediate: true }
);

const emit = defineEmits(["back-to-list"]);

const goBack = () => {
  emit("back-to-list");
};
</script>
<style scoped>
.add-btn {
  cursor: pointer;
}
</style>
