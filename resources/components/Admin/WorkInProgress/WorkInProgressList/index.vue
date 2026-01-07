<template>
  <div v-if="hasRole('Safety Advisor')">
    <template v-if="currentView === 'list'">
      <v-card
        class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
      >
        <div class="v-card-header">
          <h5 class="mb-0">Work In Progress List</h5>
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
              <template #item.request="{ item }">
                <div
                  class="action-buttons d-flex align-items-center justify-start"
                >
                  {{
                    item.working_permits.user_sendto
                      ? item.working_permits.user_sendto.Name
                      : ""
                  }}
                </div>
              </template>
              <template #item.actions="{ item }">
                <div
                  class="action-buttons d-flex align-items-center justify-center"
                >
                  <v-tooltip
                    v-if="
                      item.work_in_progress.stop_work_authorities_count === 1 &&
                      item.work_in_progress.stop_work_authorities[0].Status &&
                      item.work_in_progress.stop_work_authorities[0]
                        .RepairSWA === false &&
                      item.work_in_progress.stop_work_authorities[0]
                        .DoRepairSWA === null
                    "
                    text="View Inspection"
                  >
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="viewInspection(item.InspectionId)"
                      >
                        <i class="material-symbols-outlined"> visibility </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip
                    v-if="item.StatusText === 'Belum Verifikasi'"
                    text="Verifikasi Work In Progress"
                  >
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="verification(item.InspectionId)"
                      >
                        <i class="material-symbols-outlined"> checklist_rtl </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip
                    v-if="
                      item.StatusText === 'Melanjutkan Pekerjaan' ||
                      item.StatusText ===
                        'Tidak Comply dan Safe, Perbaikan Ulang Pengawas K3' ||
                      item.StatusText ===
                        'Tidak Comply dan Safe, Harap Input data SWA' ||
                      item.StatusText ===
                        'Tidak Comply dan Safe, Mohon Validasi'
                    "
                    text="View Stop Work Authority"
                  >
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="viewSWAList(item.InspectionId)"
                      >
                        <i class="material-symbols-outlined"> monitoring </i>
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
    <template v-else-if="currentView === 'verification'">
      <WorkInProgressCreate
        :id="selectedInspection.InspectionId"
        :inspectionForm="selectedInspection.InspectionForm"
        :WIPId="selectedInspection.work_in_progress.WIPId"
        :WPId="selectedInspection.working_permits.WPId"
        @back-to-list="currentView = 'list'"
      />
    </template>
    <template v-else-if="currentView === 'swalist'">
      <SWAList
        :InspectionId="selectedInspection.InspectionId"
        :WIPId="selectedInspection.work_in_progress.WIPId"
        :WPId="selectedInspection.working_permits.WPId"
        :SWAPIC="selectedInspection.SWAPIC"
        :Location="selectedInspection.Location"
        :WorkType="selectedInspection.WorkType"
        :InspectionDate="getInspectionDate(selectedInspection.InspectionForm)"
        :SendTo="selectedInspection.working_permits.SendTo"
        :Status="selectedInspection.work_in_progress.Status"
        @back-to-list="currentView = 'list'"
      />
    </template>
    <template v-else-if="currentView === 'view'">
      <ViewInspection
        :inspectionForm="
          selectedInspection.work_in_progress.stop_work_authorities[0]
            .InputInspection
        "
        :status="selectedInspection.Status"
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
          <h5 class="mb-0">Work In Progress List</h5>
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
                <div class="action-buttons d-flex align-items-center">
                  <v-tooltip
                    v-if="hasRole('Administrator')"
                    text="View Inspection"
                  >
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="viewSWAList(item.InspectionId)"
                      >
                        <i class="material-symbols-outlined"> visibility </i>
                      </button>
                    </template>
                  </v-tooltip>
                  <v-tooltip
                    v-if="
                      item.StatusText ===
                        'Stop Pekerjaan, buat perbaikan SWA' ||
                      item.StatusText === 'Stop Pekerjaan, harap perbaikan SWA'
                    "
                    text="Create Stop Work Authority"
                  >
                    <template v-slot:activator="{ props }">
                      <button
                        v-bind="props"
                        type="button"
                        @click="createSWASafety(item.InspectionId)"
                      >
                        <i class="material-symbols-outlined">
                          free_cancellation
                        </i>
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
    <!-- <template v-else-if="currentView === 'view'">
      <ViewInspection
        :inspectionForm="
          selectedInspection.StatusText === 'Melanjutkan Pekerjaan'
            ? selectedInspection.work_in_progress.stop_work_authorities[0]
                .InputInspection
            : selectedInspection.InspectionForm
        "
        :status="selectedInspection.Status"
        @back-to-list="currentView = 'list'"
      />
    </template> -->
    <template v-else-if="currentView === 'createSWASafety'">
      <SWACreateSafety
        :InspectionId="selectedInspection.InspectionId"
        :SWAPIC="selectedInspection.SWAPIC"
        :Location="selectedInspection.Location"
        :WorkType="selectedInspection.WorkType"
        :InspectionForm="selectedInspection.InspectionForm"
        :WPId="selectedInspection.working_permits.WPId"
        :WIPId="selectedInspection.work_in_progress.WIPId"
        @back-to-list="currentView = 'list'"
      />
    </template>
    <template v-else-if="currentView === 'swalist'">
      <SWAList
        :InspectionId="selectedInspection.InspectionId"
        :WIPId="selectedInspection.work_in_progress.WIPId"
        :WPId="selectedInspection.working_permits.WPId"
        :SWAPIC="selectedInspection.SWAPIC"
        :Location="selectedInspection.Location"
        :WorkType="selectedInspection.WorkType"
        :InspectionDate="getInspectionDate(selectedInspection.InspectionForm)"
        :SendTo="selectedInspection.working_permits.SendTo"
        :Status="selectedInspection.work_in_progress.Status"
        @back-to-list="currentView = 'list'"
      />
    </template>
  </div>
  <ShowSnackbar />
  <PdfViewerDialog />
  <ProgressLoader />
  <FileUploaderDialog />
</template>

<script setup>
import { ref } from "vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ViewInspection from "@components/Admin/Inspection/ViewInspection/index.vue";
import WorkInProgressCreate from "@components/Admin/WorkInProgress/WorkInProgressCreate/index.vue";
import SWAList from "@components/Admin/WorkInProgress/SWAList/index.vue";
import apiClient from "@utils/axios";
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import FileUploaderDialog from "@components/Common/FileUploaderDialog.vue";
import SWACreateSafety from "@components/Admin/WorkInProgress/SWACreateSafety/index.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
import { formatDate } from "@utils/date";
import { useSnackbar } from "@composables/useSnackbar";
import { useAuth } from "@composables/useAuth";
import { useProgress } from "@composables/useProgress";
import { useFileUploaderDialog } from "@composables/useFileUploaderDialog";

defineOptions({ name: "WorkInProgressList" });

const { openPdfDialog } = usePdfDialog();
const { showProgress } = useProgress();
const { showSnackbar } = useSnackbar();
const { hasRole, user } = useAuth();
const { openFileUploader } = useFileUploaderDialog();

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
  hasRole("Administrator")
    ? { title: "Pengawas K3", key: "PengawasK3" }
    : { title: "Safety Advisor", key: "SafetyAdvisor" },
  { title: "Status", key: "status" },
  { title: "Aksi", key: "actions", sortable: false },
]);
const serverItems = ref([]);
const totalItems = ref(0);
const loading = ref(true);
const search = ref("");
const selectedInspection = ref({});
const serverOptions = ref({});

const onOptionsUpdate = (options) => {
  serverOptions.value = options;
  loadItems(options);
};

const computeClass = (statusLabel) => {
  switch (statusLabel) {
    case "Permintaan":
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

const loadItems = async (options) => {
  loading.value = true;
  const sortKey = options.sortBy?.[0]?.key;
  const sortOrder = options.sortBy?.[0]?.order;
  try {
    const response = await apiClient.get("/work-in-progress/by-user", {
      params: {
        page: options.page,
        per_page: options.itemsPerPage,
        sort_by: sortKey,
        sort_order: sortOrder,
      },
    });

    const paginatedData = response.data;

    serverItems.value = paginatedData.data.data.map((item) => {
      const WorkInProgress = item.work_in_progress;

      let statusLabel = "Tidak Ada Data";

      if (hasRole("Pengawas K3")) {
        if (WorkInProgress) {
          if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length === 0
          ) {
            statusLabel = "Stop Pekerjaan, menunggu Safety Advisor Input SWA";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0 &&
            WorkInProgress.stop_work_authorities.some(
              (swa) => swa.DoRepairSWA === true && swa.ValidatedBy === null
            )
          ) {
            statusLabel = "Stop Pekerjaan, menunggu validasi Safety Advisor";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0 &&
            WorkInProgress.stop_work_authorities.some(
              (swa) =>
                swa.DoRepairSWA === true &&
                swa.ValidatedBy !== null &&
                swa.Status === false
            )
          ) {
            statusLabel = "Stop Pekerjaan, harap perbaikan SWA";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0 &&
            WorkInProgress.stop_work_authorities.some(
              (swa) => swa.RepairSWA === true
            )
          ) {
            statusLabel = "Stop Pekerjaan, buat perbaikan SWA";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0
          ) {
            statusLabel = "Stop Pekerjaan";
          } else if (
            WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0
          ) {
            statusLabel = "Melanjutkan Pekerjaan";
          }
        }
      } else {
        if (WorkInProgress) {
          if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count === 0
          ) {
            statusLabel = "Belum Verifikasi";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0 &&
            WorkInProgress.stop_work_authorities.some(
              (swa) => swa.DoRepairSWA === true && swa.ValidatedBy !== null
            )
          ) {
            statusLabel = "Tidak Comply dan Safe, Perbaikan Ulang Pengawas K3";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0 &&
            WorkInProgress.stop_work_authorities.some(
              (swa) => swa.DoRepairSWA === true
            )
          ) {
            statusLabel = "Tidak Comply dan Safe, Mohon Validasi";
          } else if (
            !WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0 &&
            WorkInProgress.stop_work_authorities.length > 0
          ) {
            statusLabel = "Tidak Comply dan Safe, Harap Input data SWA";
          } else if (
            WorkInProgress.Status &&
            WorkInProgress.stop_work_authorities_count > 0
          ) {
            statusLabel = "Melanjutkan Pekerjaan";
          }
        }
      }

      const inspectionDate = formatDate(
        item.InspectionForm?.find(
          (f) => f.type === "field" && f.input_type === "date"
        )?.answer ||
          item.InspectionForm?.find(
            (f) => f.type === "field" && f.input_type === "date"
          )?.default
      );

      const baseItem = {
        ...item,
        InspectionDate: inspectionDate,
        StatusText: statusLabel,
      };

      if (hasRole("Pengawas K3")) {
        baseItem.SafetyAdvisor = item.user_detail?.Name;
      }

      if (hasRole("Administrator")) {
        baseItem.PengawasK3 = item.working_permits?.user_verification?.Name;
      }

      return baseItem;
    });

    totalItems.value = paginatedData.data.total;
  } catch (error) {
    showSnackbar("Gagal memuat data " + error, "error");
  } finally {
    loading.value = false;
  }
};

const viewPDF = async (WPId) => {
  showProgress(true);
  const response = await apiClient.get(`/working-permit/fileid/${WPId}`);
  if (!response.data.data) {
    showProgress(false);
    return;
  }
  const fileId = response.data.data.FileId;
  const pdfUrl = `/storage/uploads/${fileId}.pdf`;
  showProgress(false);
  openPdfDialog(pdfUrl, "View Working Permit PDF");
};

const viewInspection = async (id) => {
  try {
    const found = serverItems.value.find((i) => i.InspectionId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "view";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data inspeksi " + error, "error");
  }
};

const verification = async (id) => {
  try {
    const found = serverItems.value.find((i) => i.InspectionId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "verification";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data inspeksi " + error, "error");
  }
};

const viewSWAList = async (id) => {
  try {
    const found = serverItems.value.find((i) => i.InspectionId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "swalist";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data inspeksi " + error, "error");
  }
};

const uploadFile = (wpId) => {
  try {
    openFileUploader({
      title: "Upload Working Permit",
      onConfirm: async (uploadedFile) => {
        const formData = new FormData();
        formData.append("file", uploadedFile);
        formData.append("UploadedBy", user.value.UserId);
        showProgress(true);
        const response = await apiClient.post(
          `/working-permit/${wpId}/upload`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        if (!response.data.success) {
          showProgress(false);
          showSnackbar("Upload gagal.", "error");
          return;
        }
        showProgress(false);
        showSnackbar("Upload berhasil!", "success");
        await loadItems(serverOptions.value);
      },
    });
  } catch (error) {
    showSnackbar("Gagal upload file " + error, "error");
  }
};

const createSWASafety = async (id) => {
  try {
    const found = serverItems.value.find((i) => i.InspectionId === id);
    if (found) {
      selectedInspection.value = found;
      currentView.value = "createSWASafety";
    }
  } catch (error) {
    showSnackbar("Gagal mengambil data inspeksi", "error");
  }
};

const getInspectionDate = (inspectionForm = []) => {
  const waktuInspeksi = inspectionForm.find((f) => f.key === "waktu_inspeksi");
  return formatDate(waktuInspeksi?.answer) ?? null;
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
