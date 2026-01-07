<template>
  <div v-if="hasRole('Safety Advisor')">
    <template v-if="currentView === 'list'">
      <v-card
        class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
      >
        <div class="v-card-header">
          <h5 class="mb-0">Inspection List</h5>
          <a href="#" class="add-btn" @click.prevent="currentView = 'create'">
            <i class="material-symbols-outlined"> add </i>
            Add New Inspection
          </a>
        </div>

        <div class="trezo-table-content">
          <div class="trezo-table">
            <v-data-table-server
              v-model:items-per-page="itemsPerPage"
              :headers="headers"
              :items="serverItems"
              :items-length="totalItems"
              :loading="loading"
              :search="search"
              item-value="name"
              @update:options="onOptionsUpdate"
            >
              <template #item.status="{ item }">
                <div class="d-flex align-items-center">
                  <span
                    class="trezo-status-badge"
                    :class="computeClass(item.StatusText)"
                  >
                    {{ item.StatusText }}
                  </span>
                </div>
              </template>
              <template #item.actions="{ item }">
                <div
                  class="action-buttons d-flex align-items-center justify-center"
                >
                  <v-tooltip text="View Inspection">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="viewInspection(item.InspectionId, false)"
                      >
                        <i class="material-symbols-outlined"> visibility </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip text="Edit Inspection">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        v-if="item.StatusText === 'Draft'"
                        @click="viewInspection(item.InspectionId, true)"
                      >
                        <i class="material-symbols-outlined"> edit </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip text="Delete Inspection">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        v-if="item.StatusText === 'Draft'"
                        @click="deleteInspection(item.InspectionId)"
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
    </template>
    <template v-else-if="currentView === 'create'">
      <CreateInspection @back-to-list="currentView = 'list'" />
    </template>
    <template v-else-if="currentView === 'view'">
      <ViewInspection
        :inspectionForm="selectedInspection.InspectionForm"
        :status="selectedInspection.IsDraft ? false : true"
        @back-to-list="currentView = 'list'"
      />
    </template>
    <template v-else-if="currentView === 'edit'">
      <EditInspection
        :id="selectedInspection.InspectionId"
        :inspectionForm="selectedInspection.InspectionForm"
        @back-to-list="currentView = 'list'"
      />
    </template>
  </div>
  <div v-else-if="hasRole('Pengawas K3') || hasRole('Administrator')">
    <template v-if="currentView === 'list'">
      <v-card
        class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
      >
        <div class="v-card-header">
          <h5 class="mb-0">Inspection List</h5>
        </div>

        <div class="trezo-table-content">
          <div class="trezo-table">
            <v-data-table-server
              v-model:items-per-page="itemsPerPage"
              :headers="headersK3"
              :items="serverItems"
              :items-length="totalItems"
              :loading="loading"
              :search="search"
              item-value="name"
              @update:options="loadItems"
            >
              <template #item.actions="{ item }">
                <div class="action-buttons d-flex align-items-center">
                  <button
                    type="button"
                    @click="viewInspection(item.InspectionId, false)"
                  >
                    <i class="material-symbols-outlined"> visibility </i>
                  </button>
                </div>
              </template>
            </v-data-table-server>
          </div>
        </div>
      </v-card>
    </template>
    <template v-else-if="currentView === 'view'">
      <ViewInspection
        :inspectionForm="selectedInspection.InspectionForm"
        :status="selectedInspection.IsDraft"
        @back-to-list="currentView = 'list'"
      />
    </template>
  </div>
  <ShowSnackbar />
  <ConfirmDialog />
  <ProgressLoader />
</template>

<script setup>
import { ref } from "vue";
import CreateInspection from "@components/Admin/Inspection/CreateInspection/index.vue";
import ViewInspection from "@components/Admin/Inspection/ViewInspection/index.vue";
import EditInspection from "@components/Admin/Inspection/EditInspection/index.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import apiClient from "@utils/axios";
import { formatDate } from "@utils/date";
import { useSnackbar } from "@composables/useSnackbar";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useAuth } from "@composables/useAuth";
import { useProgress } from "@composables/useProgress";

defineOptions({ name: "InspectionList" });

const { showSnackbar } = useSnackbar();
const { openDialog } = useConfirmDialog();
const { hasRole } = useAuth();
const { showProgress } = useProgress();

const currentView = ref("list");
const itemsPerPage = ref(10);
const headers = ref([
  { title: "Pemegang SWA", key: "SWAPIC" },
  { title: "Lokasi Pekerjaan", key: "Location" },
  { title: "Tipe Pekerjaan", key: "WorkType" },
  { title: "Tanggal Inspeksi", key: "InspectionDate" },
  { title: "Status", key: "status" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);
const headersK3 = ref([
  { title: "Pemegang SWA", key: "SWAPIC" },
  { title: "Lokasi Pekerjaan", key: "Location" },
  { title: "Tipe Pekerjaan", key: "WorkType" },
  { title: "Tanggal Inspeksi", key: "InspectionDate" },
  { title: "Safety Advisor", key: "SafetyAdvisor" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);
const serverItems = ref([]);
const totalItems = ref(0);
const loading = ref(true);
const search = ref("");
const serverOptions = ref({});
const selectedInspection = ref({});

const onOptionsUpdate = (options) => {
  serverOptions.value = options;
  loadItems(options);
};

const computeClass = (statusLabel) => {
  switch (statusLabel) {
    case "Draft":
      return "inProgress";
    case "Final":
      return "primary";
  }
};

const loadItems = async (options) => {
  loading.value = true;
  const sortKey = options.sortBy?.[0]?.key;
  const sortOrder = options.sortBy?.[0]?.order;
  try {
    const response = await apiClient.get("/inspection/by-user", {
      params: {
        page: options.page,
        per_page: options.itemsPerPage,
        sort_by: sortKey,
        sort_order: sortOrder,
      },
    });
    const paginatedData = response.data;

    serverItems.value = hasRole("Safety Advisor")
      ? paginatedData.data.data.map((item) => ({
          ...item,
          InspectionDate: formatDate(
            item.InspectionForm?.find(
              (f) => f.type === "field" && f.input_type === "date"
            )?.answer ||
              item.InspectionForm?.find(
                (f) => f.type === "field" && f.input_type === "date"
              )?.default
          ),
          StatusText: item.IsDraft ? "Draft" : "Final",
        }))
      : paginatedData.data.data.map((item) => ({
          ...item,
          InspectionDate: formatDate(
            item.InspectionForm?.find(
              (f) => f.type === "field" && f.input_type === "date"
            )?.answer ||
              item.InspectionForm?.find(
                (f) => f.type === "field" && f.input_type === "date"
              )?.default
          ),
          SafetyAdvisor: item.user_detail.Name,
        }));
    totalItems.value = paginatedData.data.total;
  } catch (error) {
    showSnackbar("Gagal memuat data inspeksi.", "error");
  } finally {
    loading.value = false;
  }
};

const deleteInspection = async (id) => {
  const confirmed = await openDialog({
    title: "Konfirmasi Hapus",
    message: "Apakah Anda yakin ingin menghapus data ini?",
  });

  if (!confirmed) return;
  try {
    showProgress(true);
    await apiClient.delete(`/inspection/${id}`);
    await loadItems(serverOptions.value);
    showSnackbar("Data berhasil dihapus.", "success");
  } catch (error) {
    showSnackbar("Gagal menghapus data.", "error");
  } finally {
    showProgress(false);
  }
};

const viewInspection = async (id, isEdit) => {
  try {
    const found = serverItems.value.find((i) => i.InspectionId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = isEdit ? "edit" : "view";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data inspeksi", "error");
  }
};
</script>

<style lang="scss" scoped>
.projects-list-card {
  .trezo-table-content {
    .trezo-table {
      margin-left: -25px;
      margin-right: -25px;
      table {
        thead {
          tr {
            th {
              background-color: #ecf0ff !important;

              &:first-child {
                border-top-left-radius: 0 !important;
                padding-left: 25px;
              }
              &:last-child {
                border-top-right-radius: 0 !important;
                padding-right: 25px;
              }
            }
          }
        }
        tbody {
          tr {
            td {
              &:first-child {
                padding-left: 25px;
                border-left-width: 0 !important;
              }
              &:last-child {
                padding-right: 25px;
                border-right-width: 0 !important;
              }
            }
            &:first-child {
              td {
                border-top-width: 0;
              }
            }
          }
        }
      }
    }
  }
}
</style>
