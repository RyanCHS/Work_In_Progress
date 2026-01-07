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
    <div v-if="inspectionForm.length > 0">
      <InspectionResultTable :data="inspectionForm" :status="status" />
    </div>
    <div v-else><v-skeleton-loader type="article"></v-skeleton-loader></div>
  </v-card>
</template>
<script setup>
import InspectionResultTable from "@components/Admin/Inspection/General/InspectionResultTable.vue";
defineOptions({
  name: "ViewInspection",
});

const props = defineProps({
  inspectionForm: {
    type: Array,
    required: true,
  },
  status: {
    type: Boolean,
    required: true,
  },
});

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
