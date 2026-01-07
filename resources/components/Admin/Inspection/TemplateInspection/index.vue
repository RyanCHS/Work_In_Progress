<template>
  <v-card
    class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <h5 class="mb-0">Template Inspection List</h5>
    </div>

    <div class="trezo-table-content">
      <div class="trezo-table">
        <v-data-table-server
          v-model:items-per-page="itemsPerPage"
          :headers="headers"
          :items="serverItems"
          :items-length="totalItems"
          :loading="loading"
          item-value="name"
          @update:options="onOptionsUpdate"
        >
          <template #item.Activated="{ item }">
            <div class="d-flex align-items-center">
              <v-switch
                v-model="item.IsActivated"
                :label="`${item.IsActivated ? 'Aktif' : 'Tidak Aktif'}`"
                color="primary"
                hide-details
                @change="onToggleActivated(item)"
              ></v-switch>
            </div>
          </template>
          <template #item.actions="{ item }">
            <div
              class="action-buttons d-flex align-items-center justify-center"
            >
              <v-tooltip text="Delete Template Inspection">
                <template v-slot:activator="{ props }">
                  <button
                    v-bind="props"
                    type="button"
                    @click="deleteTemplate(item.TemplateId)"
                  >
                    <i class="material-symbols-outlined"> delete </i>
                  </button>
                </template>
              </v-tooltip>
            </div>
          </template>
        </v-data-table-server>
      </div>
    </div>
  </v-card>
  <template>
    <ShowSnackbar />
    <ConfirmDialog />
    <ProgressLoader />
  </template>
</template>

<script setup>
import { ref } from "vue";
import apiClient from "@utils/axios";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import { useSnackbar } from "@composables/useSnackbar";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useProgress } from "@composables/useProgress";
import { formatDate } from "@utils/date";
const { showSnackbar } = useSnackbar();
const { openDialog } = useConfirmDialog();
const { showProgress } = useProgress();

const headers = ref([
  { title: "Deskripsi", key: "Description" },
  { title: "Tanggal Template", key: "TemplateDate" },
  { title: "Aktivasi", key: "Activated" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);

const serverItems = ref([]);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const loading = ref(true);
const serverOptions = ref({});

defineOptions({
  name: "TemplateInspection",
});

const onOptionsUpdate = (options) => {
  serverOptions.value = options;
  loadItems(options);
};

const loadItems = async ({ page, itemsPerPage }) => {
  try {
    loading.value = true;
    const response = await apiClient.get("/inspection/templates", {
      params: {
        page,
        per_page: itemsPerPage,
      },
    });
    if (!response.data.success) {
      loading.value = false;
      return;
    }
    serverItems.value = response.data.data.data.map((item) => ({
      ...item,
      TemplateDate: formatDate(item.TemplateDate),
    }));
    totalItems.value = serverItems.value.length;
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  } finally {
    loading.value = false;
  }
};

const onToggleActivated = async (item) => {
  try {
    showProgress(true);
    const response = await apiClient.put(
      `/inspection/template/${item.TemplateId}/enabled`,
      {
        IsActivated: item.IsActivated,
      }
    );

    if (!response.data.success) {
      showSnackbar("Gagal memperbarui status", "error");
      return;
    }

    showSnackbar("Status berhasil diperbarui", "success");
  } catch (err) {
    showSnackbar("Terjadi kesalahan: " + err.message, "error");
  } finally {
    showProgress(false);
  }
};

const deleteTemplate = async (templateId) => {
  const confirmed = await openDialog({
    title: "Perhatian",
    message: "Anda yakin ingin menghapus data ini?",
  });
  if (!confirmed) return;
  try {
    showProgress(true);
    const response = await apiClient.delete(
      `/inspection/template/${templateId}`
    );
    if (!response.data.success) {
      showSnackbar("Gagal menghapus data", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil dihapus", "success");
    await loadItems(serverOptions.value);
  } catch (error) {
    showSnackbar("Gagal menghapus data " + error, "error");
  }
};
</script>
