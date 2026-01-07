<template>
  <v-card
    class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <div class="add-btn" @click="goBack">
        <i class="material-symbols-outlined">
          <span class="material-symbols-outlined"> arrow_back </span>
        </i>
        Back
      </div>
    </div>
    <template v-if="hasRole('Safety Advisor')">
      <div class="text-center">
        <!-- mode checkbox atau radio -->
        <PengawasK3
          class="mb-5"
          title="Pengawas K3"
          mode="radio"
          :data="content"
          @update:selected="handleSelectedPengawas"
        />
        <v-btn
          prepend-icon="mdi-email-arrow-right"
          color="primary"
          class="mr-5"
          :disabled="selectedPengawas.length === 0"
          @click="handleRequestWP"
        >
          Request Working Permit
        </v-btn>
      </div>
    </template>
  </v-card>
  <ShowSnackbar />
  <ConfirmDialog />
  <ProgressLoader />
</template>

<script setup>
import { ref, onMounted } from "vue";

import PengawasK3 from "@components/Admin/WorkingPermit/General/PengawasK3.vue";
import apiClient from "@utils/axios";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import { useAuth } from "@composables/useAuth";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
const { hasRole } = useAuth();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
defineOptions({
  name: "RequestWorkingPermit",
});

const props = defineProps({
  inspectionId: { type: String, required: true },
});

const emit = defineEmits(["back-to-list"]);

const goBack = () => {
  emit("back-to-list");
};

const content = ref([]);
const selectedPengawas = ref([]);

const handleSelectedPengawas = (userIds) => {
  selectedPengawas.value = userIds;
};

onMounted(async () => {
  try {
    const response = await apiClient.get("/user/getuserbygroup/Pengawas%20K3");
    content.value = response.data.data ?? [];
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  }
});

const handleRequestWP = async () => {
  const confirmed = await openDialog({
    title: "Konfirmasi Request",
    message: "Apakah Anda yakin ingin melakukan request working permit?",
  });
  if (!confirmed) return;

  try {
    showProgress(true);
    const response = await apiClient.put(
      `/working-permit/request/${props.inspectionId}`,
      { SendTo: selectedPengawas.value }
    );
    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Request working permit gagal.", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Request working permit berhasil.", "success");
    goBack();
  } catch (error) {
    showSnackbar("Gagal request working permit", "error");
  }
};
</script>

<style scoped>
.add-btn {
  cursor: pointer;
}
</style>
