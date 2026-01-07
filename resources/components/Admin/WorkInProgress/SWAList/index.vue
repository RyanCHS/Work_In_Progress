<template>
  <div v-if="hasRole('Safety Advisor') || hasRole('Administrator')">
    <template v-if="currentView === 'list'">
      <v-card
        class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
      >
        <div class="v-card-header">
          <v-row align="center">
            <v-col cols="12" class="d-flex justify-start"
              ><div class="add-btn cursor-pointer" @click="goBack">
                <i class="material-symbols-outlined">
                  <span class="material-symbols-outlined">arrow_back</span>
                </i>
                Back
              </div></v-col
            >
            <v-col cols="12" class="d-flex justify-start"
              ><h5 class="ml-2 mb-0">Data Stop Work Authority</h5></v-col
            >
          </v-row>
        </div>
        <div class="trezo-table-content mb-10">
          <div class="trezo-table">
            <v-table>
              <thead>
                <tr>
                  <th>Keterangan</th>
                  <th>Nilai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Pemegang SWA</td>
                  <td>{{ SWAPIC }}</td>
                </tr>
                <tr>
                  <td>Lokasi Pekerjaan</td>
                  <td>{{ Location }}</td>
                </tr>
                <tr>
                  <td>Jenis Pekerjaan</td>
                  <td>{{ WorkType }}</td>
                </tr>
                <tr>
                  <td>Waktu Inspeksi</td>
                  <td>{{ InspectionDate }}</td>
                </tr>
              </tbody>
            </v-table>
          </div>
        </div>
        <v-container class="v-card-header">
          <v-row justify="space-between" align="center">
            <v-col><h5 class="mb-0">Stop Work Authority</h5></v-col>
            <v-col class="d-flex justify-end">
              <a
                v-if="disableAddButton"
                href="#"
                class="add-btn flex items-center gap-1"
                @click.prevent="currentView = 'create'"
              >
                <i class="material-symbols-outlined">add</i>
                Add Stop Work Authority
              </a>
            </v-col>
          </v-row>
        </v-container>

        <div class="trezo-table-content mb-10">
          <div class="trezo-table">
            <v-data-table-server
              v-model:items-per-page="itemsPerPage"
              :headers="headers"
              :items="swaItems"
              :loading="loading"
              :items-length="totalSWAItems"
              item-value="name"
              hide-default-footer
              @update:options="loadItems"
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
                  <v-tooltip text="View SWA">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="viewSWA(item.SWAId)"
                      >
                        <i class="material-symbols-outlined"> visibility </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip text="Edit SWA" v-if="!hasRole('Administrator')">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        v-if="item.IsDraft === true"
                        @click="editSWA(item.SWAId)"
                      >
                        <i class="material-symbols-outlined"> edit </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip text="Delete SWA" v-if="!hasRole('Administrator')">
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        v-if="item.IsDraft === true"
                        @click="deleteSWA(item.SWAId)"
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
        <div v-if="swaValidationItems.length > 0" class="mb-5">
          <v-container class="v-card-header">
            <v-row justify="space-between" align="center">
              <v-col><h5 class="mb-0">Validasi Stop Work Authority</h5></v-col>
            </v-row>
          </v-container>
          <div class="trezo-table-content mb-5">
            <div class="trezo-table">
              <v-data-table-server
                v-model:items-per-page="itemsPerPage"
                :headers="headersValidation"
                :items="swaValidationItems"
                :loading="loading"
                :items-length="totalSWAValidationItems"
                item-value="name"
                @update:options="loadItems"
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
                    <v-tooltip
                      text="View SWA"
                      v-if="
                        item.StatusText === 'Data SWA Berhasil di Validasi' ||
                        item.StatusText === 'Tidak Comply dan Save'
                      "
                    >
                      <template v-slot:activator="{ props }">
                        <button
                          v-bind="props"
                          type="button"
                          @click="viewSWAValidation(item.SWAId)"
                        >
                          <i class="material-symbols-outlined"> visibility </i>
                        </button>
                      </template>
                    </v-tooltip>
                    <v-tooltip
                      text="Validasi"
                      v-if="
                        item.StatusText === 'Harap di validasi' &&
                        !hasRole('Administrator')
                      "
                    >
                      <template v-slot:activator="{ props }">
                        <button
                          v-bind="props"
                          type="button"
                          @click="requestValidation(item.SWAId)"
                        >
                          <i class="material-symbols-outlined">
                            done_outline
                          </i>
                        </button>
                      </template>
                    </v-tooltip>
                  </div>
                </template>
              </v-data-table-server>
            </div>
          </div>
        </div>
      </v-card>
    </template>
    <template v-else-if="currentView === 'view'">
      <SWAView
        :InputInspection="selectedInspection.InputInspection"
        :FileInputs="selectedInspection.file_inputs"
        :FileIdWP="fileId"
        @back-to-list="currentView = 'list'"
      />
    </template>
    <template v-else-if="currentView === 'edit'">
      <SWAEdit
        :SWAId="selectedInspection.SWAId"
        :InputInspection="selectedInspection.InputInspection"
        :FileInputs="selectedInspection.file_inputs"
        :FileIdWP="fileId"
        @back-to-list="currentView = 'list'"
        @refreshData="refreshData"
      />
    </template>
    <template v-else-if="currentView === 'create'">
      <SWACreate
        :inspectionId="InspectionId"
        :FileIdWP="fileId"
        @back-to-list="currentView = 'list'"
        @refreshData="refreshData"
      />
    </template>
    <template v-else-if="currentView === 'request'">
      <SWAValidation
        :InputSWA="inputSWA"
        :ValidasiSWA="validasiSWA"
        :FileIdWP="fileId"
        :IsView="false"
        @back-to-list="currentView = 'list'"
        @validation="validationSWA"
      />
    </template>
    <template v-else-if="currentView === 'viewValidation'">
      <SWAValidation
        :InputSWA="inputSWA"
        :ValidasiSWA="validasiSWA"
        :FileIdWP="fileId"
        :IsView="true"
        @back-to-list="currentView = 'list'"
      />
    </template>
  </div>

  <ShowSnackbar />
  <PdfViewerDialog />
  <ProgressLoader />
  <FileUploaderDialog />
  <ConfirmDialog />
</template>

<script setup>
import { watch, computed, ref, normalizeProps } from "vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import SWAView from "@components/Admin/WorkInProgress/SWAView/index.vue";
import WorkInProgressCreate from "@components/Admin/WorkInProgress/WorkInProgressCreate/index.vue";
import SWACreate from "@components/Admin/WorkInProgress/SWACreate/index.vue";
import SWAValidation from "@components/Admin/WorkInProgress/SWAValidation/index.vue";
import SWAEdit from "@components/Admin/WorkInProgress/SWAEdit/index.vue";
import apiClient from "@utils/axios";
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import FileUploaderDialog from "@components/Common/FileUploaderDialog.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { formatDate, formatDateTime } from "@utils/date";
import { useSnackbar } from "@composables/useSnackbar";
import { useAuth } from "@composables/useAuth";
import { useProgress } from "@composables/useProgress";
import { useFileUploaderDialog } from "@composables/useFileUploaderDialog";

defineOptions({ name: "SWAList" });
const props = defineProps({
  InspectionId: {
    type: String,
    required: true,
  },
  WIPId: {
    type: String,
    required: true,
  },
  WPId: {
    type: String,
    required: true,
  },
  SWAPIC: {
    type: String,
    required: true,
  },
  Location: {
    type: String,
    required: true,
  },
  WorkType: {
    type: String,
    required: true,
  },
  InspectionDate: {
    type: String,
    required: true,
  },
  SendTo: {
    type: String,
    required: true,
  },
  Status: {
    type: Boolean,
    required: true,
  },
});
const emit = defineEmits(["back-to-list"]);
const { openPdfDialog } = usePdfDialog();
const { showProgress } = useProgress();
const { showSnackbar } = useSnackbar();
const { hasRole, user } = useAuth();
const { openFileUploader } = useFileUploaderDialog();
const { openDialog } = useConfirmDialog();

const currentView = ref("list");
const itemsPerPage = ref(10);
const headers = ref([
  { title: "Tanggal SWA", key: "SWADateText" },
  { title: "Status", key: "status" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);
const headersValidation = ref([
  { title: "Tanggal SWA", key: "SWADateText" },
  { title: "Perbaikan Oleh", key: "CreatedByText" },
  { title: "Tanggal Validasi", key: "ValidationDateText" },
  { title: "Status", key: "status" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);
const headersK3 = ref([
  { title: "Pemegang SWA", key: "SWAPIC" },
  { title: "Lokasi Pekerjaan", key: "Location" },
  { title: "Tipe Pekerjaan", key: "WorkType" },
  { title: "Tanggal Inspeksi", key: "InspectionDate" },
  { title: "Safety Advisor", key: "SafetyAdvisor" },
  { title: "Status", key: "status" },
  { title: "Aksi", key: "actions", sortable: false },
]);
const swaItems = ref([]);
const swaValidationItems = ref([]);
const totalSWAItems = ref(0);
const totalSWAValidationItems = ref(0);
const loading = ref(true);
const search = ref("");
const selectedInspection = ref({});
const inputSWA = ref({});
const validasiSWA = ref({});
const fileId = ref("");

const goBack = () => {
  emit("back-to-list");
};

const loadData = async (id, perPage) => {
  try {
    loading.value = true;
    const response = await apiClient.get(`/work-in-progress/swa/${id}`, {
      params: {
        IsWIP: false,
        per_page: perPage,
      },
    });

    const result = response.data.data;
    swaItems.value = result.safetyAdvisor.map((item) => {
      let statusLabel = "Tidak Ada Data";

      if (item.IsDraft) {
        statusLabel = "Draft";
      } else if (!item.IsDraft) {
        statusLabel = "Input Data SWA Berhasil di buat";
      }

      const baseItem = {
        ...item,
        SWADateText: formatDate(item.SWADate),
        CreatedByText: item.CreatedBy ? item.created_by.Name : "",
        ValidationByText: item.ValidatedBy ? item.validated_by.Name : "",
        StatusText: statusLabel,
      };

      return baseItem;
    });

    swaValidationItems.value = result.repairSWA.data.map((item) => {
      let statusLabel = "Tidak Ada Data";

      if (hasRole("Pengawas K3")) {
      } else {
        if (!item.Status && item.DoRepairSWA && !item.ValidatedBy) {
          statusLabel = "Harap di validasi";
        } else if (!item.Status && item.DoRepairSWA && item.ValidatedBy) {
          statusLabel = "Tidak Comply dan Save";
        } else if (item.Status) {
          statusLabel = "Data SWA Berhasil di Validasi";
        }
      }

      const baseItem = {
        ...item,
        SWADateText: formatDate(item.SWADate),
        CreatedByText: item.CreatedBy ? item.created_by.Name : "",
        ValidationDateText: item.ValidatedAt
          ? formatDateTime(item.ValidatedAt)
          : "",
        StatusText: statusLabel,
      };

      return baseItem;
    });

    totalSWAItems.value = result.safetyAdvisor.length;
    totalSWAValidationItems.value = result.repairSWA.data.length;
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA", "error");
  } finally {
    loading.value = false;
  }
};

const loadFileData = async () => {
  try {
    showProgress(true);
    const response = await apiClient.get(
      `/working-permit/fileid/${props.WPId}`
    );
    if (!response.data.data) {
      return;
    }
    fileId.value = response.data.data.FileId;
  } catch (error) {
    showSnackbar("Gagal mengambil data File Input " + error, "error");
  } finally {
    showProgress(false);
  }
};

const requestValidation = async (id) => {
  const confirmed = await openDialog({
    title: "Konfirmasi Request",
    message: "Apakah Anda yakin ingin permintaan validasi?",
  });

  if (!confirmed) return;
  try {
    const found = swaValidationItems.value.find((i) => i.SWAId === id);
    if (found) {
      inputSWA.value = swaItems.value[0];
      validasiSWA.value = found;
      currentView.value = "request";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA " + error, "error");
  }
};

const refreshData = async () => {
  await loadData(props.WIPId, itemsPerPage.value);
};

const viewSWA = async (id) => {
  try {
    const found = swaItems.value.find((i) => i.SWAId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "view";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA " + error, "error");
  }
};

const viewSWAValidation = async (id) => {
  try {
    const found = swaValidationItems.value.find((i) => i.SWAId === id);
    if (found) {
      inputSWA.value = swaItems.value[0];
      validasiSWA.value = found;
      currentView.value = "viewValidation";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA " + error, "error");
  }
};

const editSWA = async (id) => {
  try {
    const found = swaItems.value.find((i) => i.SWAId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "edit";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA " + error, "error");
  }
};

const deleteSWA = async (id) => {
  const confirmed = await openDialog({
    title: "Konfirmasi Hapus",
    message: "Apakah Anda yakin ingin menghapus SWA ini?",
  });
  if (!confirmed) return;
  try {
    showProgress(true);
    await apiClient.delete(`/work-in-progress/swa/${id}`);
    showSnackbar("Data berhasil dihapus", "success");
    await refreshData();
  } catch (error) {
    showSnackbar("Gagal mengambil data SWA " + error, "error");
  } finally {
    showProgress(false);
  }
};

const validationSWA = async (isValidation, SWAId) => {
  try {
    showProgress(true);
    const response = await apiClient.put(
      `/work-in-progress/swa/${SWAId}/validate`,
      {
        isValidation,
      }
    );
    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Data tidak berhasil validasi", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil divalidasi", "success");
    await refreshData();
  } catch (error) {
    showSnackbar("Gagal validasi data SWA " + error, "error");
  }
};

const computeClass = (statusLabel) => {
  switch (statusLabel) {
    case "Draft":
      return "inProgress";
    case "Menunggu Response":
      return "pending";
    case "Permintaan Working Permit":
      return "inProgress";
    case "Berhasil di Response":
      return "primary";
    case "Working Permit sudah Upload":
      return "primary";
    default:
      return "";
  }
};

watch(
  () => props.WIPId,
  async (val) => {
    if (val) {
      await loadData(val, itemsPerPage.value);
      await loadFileData();
    }
  },
  { immediate: true }
);

const disableAddButton = computed(() => {
  if (loading.value) return false;

  const items = swaItems.value ?? [];
  return items.length === 0 && props.Status === false;
});
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
.add-btn {
  cursor: pointer;
}
</style>
