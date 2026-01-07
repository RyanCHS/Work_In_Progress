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
    <div v-if="InspectionForm.length > 0">
      <form @submit.prevent="submitForm">
        <div class="row">
          <div
            v-for="item in InspectionForm"
            :key="item.key || item.label"
            class="mb-4"
          >
            <div>
              <div v-if="item.is_template" class="col-sm-12">
                <v-row>
                  <v-col cols="12" md="9">
                    <div v-if="item.input_type === 'radio'">
                      <DynamicForm :item="item" v-model="item.default" />
                    </div>
                    <div v-else-if="item.input_type === 'checkbox'">
                      <DynamicForm :item="item" v-model="item.default" />
                    </div>
                    <div v-else class="trezo-form-group">
                      <DynamicForm :item="item" v-model="item.default" />
                    </div>
                  </v-col>
                  <v-col cols="12" md="3" class="d-flex justify-end">
                    <v-btn
                      v-if="item.key !== 'working_permit'"
                      prepend-icon="mdi-upload-circle-outline"
                      class="text-none ml-2"
                      color="primary"
                      @click="uploadFile(item.key)"
                    >
                      Upload
                    </v-btn>
                    <v-btn
                      v-if="fileInputMap.has(item.key)"
                      prepend-icon="mdi-file-pdf-box"
                      class="text-none ml-2"
                      color="primary"
                      @click="viewPDF(item.key)"
                    >
                      View PDF
                    </v-btn>
                  </v-col>
                </v-row>
              </div>
              <div v-else class="col-sm-12">
                <div v-if="item.input_type === 'radio'">
                  <DynamicForm :item="item" v-model="item.default" />
                </div>
                <div v-else class="trezo-form-group">
                  <DynamicForm :item="item" v-model="item.default" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <v-divider color="success"></v-divider>

        <v-row>
          <v-col cols="12" class="d-flex justify-between">
            <v-col>
              <v-btn
                class="text-none"
                type="submit"
                color="primary"
                prepend-icon="mdi-check-circle-outline"
              >
                Simpan
              </v-btn>
            </v-col>
            <v-col class="d-flex justify-end">
              <v-btn
                class="text-none"
                color="green"
                prepend-icon="mdi-cancel"
                @click="goBack"
              >
                Batal
              </v-btn>
            </v-col>
          </v-col>
        </v-row>
      </form>
    </div>
  </v-card>

  <ShowSnackbar />
  <PdfViewerDialog />
  <ConfirmDialog />
  <InputBoxDialog />
  <ProgressLoader />
</template>
<script setup>
import { ref, computed, watch } from "vue";
import apiClient from "@utils/axios";
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import DynamicForm from "@components/Common/DynamicForm.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import InputBoxDialog from "@components/Common/InputBoxDialog.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
import { useFileUploaderDialog } from "@composables/useFileUploaderDialog";

const { openPdfDialog } = usePdfDialog();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
const { openFileUploader } = useFileUploaderDialog();

defineOptions({
  name: "SWACreateSafety",
});

const props = defineProps({
  InspectionId: {
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
  InspectionForm: {
    type: Array,
    required: true,
  },
  WPId: {
    type: String,
    required: true,
  },
  WIPId: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["back-to-list", "refreshData"]);

const goBack = () => {
  emit("back-to-list");
};

const refreshData = () => {
  emit("refreshData");
};

const fileInputs = ref([]);

const fileInputMap = computed(() => {
  const map = new Map();
  fileInputs.value.forEach((file) => {
    map.set(file.key, file.fileinput);
  });
  return map;
});

const uploadFile = (key) => {
  try {
    openFileUploader({
      title: "Upload Data Inspeksi",
      onConfirm: async (uploadedFile) => {
        if (!uploadedFile) return;

        fileInputs.value.push({
          key: key,
          fileinput: uploadedFile,
        });

        showSnackbar("File berhasil disimpan sementara", "success");
      },
    });
  } catch (error) {
    showSnackbar("Gagal upload file " + error, "error");
  }
};

const workingPermitPDF = async () => {
  const response = await apiClient.get(`/working-permit/fileid/${props.WPId}`);
  if (!response.data.data) {
    return;
  }
  const fileId = response.data.data.FileId;
  const pdfUrl = `/storage/uploads/${fileId}.pdf`;

  const responseUrl = await fetch(pdfUrl);
  const blob = await responseUrl.blob();
  const file = new File([blob], `${props.FileIdWP}.pdf`, {
    type: "application/pdf",
  });
  fileInputs.value.push({
    key: "working_permit",
    fileinput: file,
  });
};

watch(
  () => props.InspectionId,
  async (val) => {
    if (val) {
      await workingPermitPDF();
    }
  },
  { immediate: true }
);

const submitForm = async () => {
  const confirmed = await openDialog({
    title: "Konfirmasi Simpan",
    message: "Apakah Anda yakin ingin menyimpan data ini?",
  });

  if (!confirmed) return; // jika cancel, langsung stop
  await saveSWA();
};

const saveSWA = async () => {
  try {
    showProgress(true);
    const formData = new FormData();
    formData.append("InputInspection", JSON.stringify(props.InspectionForm));
    formData.append("IsDraft", 0);
    fileInputs.value
      .filter((fileInput) => fileInput.key !== "working_permit")
      .forEach((fileInput, index) => {
        formData.append(`FileInput[${index}][key]`, fileInput.key);
        formData.append(`FileInput[${index}][fileinput]`, fileInput.fileinput);
      });
    const response = await apiClient.post(
      `/work-in-progress/swa/${props.WIPId}`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );
    if (!response.data.success) {
      showSnackbar("Data gagal disimpan", "error");
      return;
    }
    showSnackbar("Data berhasil disimpan", "success");
    refreshData();
    goBack();
  } catch (error) {
    showSnackbar("Terjadi kesalahan saat submit", "error");
  } finally {
    showProgress(false);
  }
};

const viewPDF = async (key) => {
  showProgress(true);
  const selectPDF = fileInputs.value.find((f) => f.key === key);
  const pdfUrl = URL.createObjectURL(selectPDF.fileinput);
  showProgress(false);
  openPdfDialog(pdfUrl, "View Working Permit PDF");
};
</script>
<style scoped>
.add-btn {
  cursor: pointer;
}
.w-100 {
  width: 100%;
}
.dynamic-width-select .v-select {
  width: auto;
  min-width: 300px;
  display: inline-block;
}
</style>
