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
      <form @submit.prevent="submitForm">
        <div class="row">
          <div
            v-for="item in inspectionForm"
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
                      prepend-icon="mdi-file-document-edit-outline"
                      class="text-none mx-2"
                      color="green"
                      @click="handleEdit(item)"
                    >
                      Edit
                    </v-btn>

                    <v-btn
                      v-if="item.key !== 'working_permit'"
                      prepend-icon="mdi-trash-can-outline"
                      class="text-none mx-2"
                      color="red"
                      @click="handleDelete(item)"
                    >
                      Delete
                    </v-btn>
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
                <div v-else-if="item.input_type === 'checkbox'">
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
        <v-row class="my-6" align="center" justify="end" no-gutters>
          <v-col cols="12" sm="auto">
            <div class="dynamic-width-select">
              <v-select
                v-model="selectedTemplate"
                label="Select Template"
                :items="templates"
                item-title="text"
                item-value="value"
                variant="outlined"
                density="comfortable"
                hide-details
                dense
                clearable
              />
            </div>
          </v-col>

          <v-col cols="12" sm="auto" class="mt-2 mt-sm-0 d-flex justify-end">
            <v-btn
              prepend-icon="mdi-tray-arrow-up"
              class="text-none mx-1"
              color="blue"
              :disabled="!selectedTemplate"
              @click="handleLoadTemplate(selectedTemplate)"
            >
              Load Template
            </v-btn>
            <v-btn
              prepend-icon="mdi-inspectionForm-save-all-outline"
              class="text-none mx-1"
              color="primary"
              :disabled="!hasTemplates"
              @click="handleReplaceSaveTemplate"
            >
              Save Template
            </v-btn>
          </v-col>
        </v-row>

        <div class="col-sm-12 my-6">
          <v-btn
            color="primary"
            class="w-100 text-none"
            prepend-icon="mdi-plus"
            @click="handleAddModal"
          >
            Tambah Field
          </v-btn>
        </div>
        <div class="col-sm-12">
          <div>
            <v-label class="d-block fw-medium text-black">
              Status Dokumen
            </v-label>
            <v-switch
              v-model="isDraft"
              color="primary"
              :label="!isDraft ? 'Final' : 'Draft'"
            ></v-switch>
          </div>
        </div>
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

  <AddFieldModal
    v-if="modalType === 'add'"
    v-model="modalType"
    :disableWorkingPermit="isWorkingPermitAdded"
    @choose="handleAddChoice"
  />

  <RadioInputDialog
    v-if="modalType === 'permit'"
    :fieldKey="'working_permit'"
    headerLabel="Tambah Working Permit"
    v-model="modalRadioInput"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <AddGeneralFieldModal
    v-if="modalType === 'general'"
    v-model="modalType"
    @choose="handleAddChoice"
  />

  <TextInputDialog
    v-if="modalType === 'text'"
    v-model="modalTextInput"
    headerLabel="Tambah Text"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <RadioInputDialog
    v-if="modalType === 'selector'"
    v-model="modalRadioInput"
    headerLabel="Tambah Selector"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <ShowSnackbar />
  <PdfViewerDialog />
  <ConfirmDialog />
  <InputBoxDialog />
  <ProgressLoader />
</template>
<script setup>
import { ref, computed, nextTick, watch } from "vue";
import apiClient from "@utils/axios";
import { useAuth } from "@composables/useAuth";
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import DynamicForm from "@components/Common/DynamicForm.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import InputBoxDialog from "@components/Common/InputBoxDialog.vue";
import AddFieldModal from "@components/Admin/Inspection/General/AddFieldModal.vue";
import AddGeneralFieldModal from "@components/Admin/Inspection/General/AddGeneralFieldModal.vue";
import RadioInputDialog from "@components/Admin/Inspection/General/RadioInputDialog.vue";
import TextInputDialog from "@components/Admin/Inspection/General/TextInputDialog.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useInputTextDialog } from "@composables/useInputTextDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
import { useFileUploaderDialog } from "@composables/useFileUploaderDialog";

const { openPdfDialog } = usePdfDialog();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
const { openInputBoxDialog } = useInputTextDialog();
const { user } = useAuth();
const { openFileUploader } = useFileUploaderDialog();

defineOptions({
  name: "SWACreate",
});

const props = defineProps({
  inspectionId: { type: String, required: true },
  FileIdWP: { type: String, required: true },
});

const emit = defineEmits(["back-to-list", "refreshData"]);

const goBack = () => {
  emit("back-to-list");
};

const refreshData = () => {
  emit("refreshData");
};

const isDraft = ref(false);
const inspectionForm = ref([]);
const templates = ref([]);
const selectedTemplate = ref(null);
const tempObj = ref({});
const modalType = ref(null);
const modalTextInput = ref(false);
const modalRadioInput = ref(false);
const isEdit = ref(false);
const data = ref({});
const fileInputs = ref([]);

const loadData = async (id) => {
  try {
    showProgress(true);
    const response = await apiClient.get(`/work-in-progress/swa/detail/${id}`);

    if (!response.data.success) {
      return;
    }
    data.value = response.data.data;
    inspectionForm.value = data.value.inspection.InspectionForm;
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  } finally {
    showProgress(false);
  }
};

const loadDataTemplate = async () => {
  try {
    const responseTemplate = await apiClient.get(
      `/inspection/template?status=true`
    );
    templates.value = responseTemplate.data.data.map((t) => ({
      text: t.Description,
      value: t.TemplateId,
    }));
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  }
};

const tempObjReady = computed(() => ({ ...tempObj.value }));

const handleAddModal = () => {
  tempObj.value = {};
  modalType.value = "add";
};

const disableAllModal = () => {
  modalRadioInput.value = false;
  modalTextInput.value = false;
};

const handleAddChoice = async (choice) => {
  tempObj.value = {};
  modalType.value = null;
  isEdit.value = false;
  disableAllModal();
  await nextTick();
  await new Promise((resolve) => setTimeout(resolve, 100));
  if (choice === "working") {
    modalType.value = "permit";
    modalCheckboxInput.value = true;
  } else if (choice === "field") {
    modalType.value = "general";
  } else if (choice === "text") {
    modalType.value = "text";
    modalTextInput.value = true;
  } else if (choice === "selector") {
    modalType.value = "selector";
    modalRadioInput.value = true;
  } else if (choice === "checkbox") {
    modalType.value = "checkbox";
    modalCheckboxInput.value = true;
  }
};

const handleAddField = (field) => {
  if (field.is_edit) {
    const index = inspectionForm.value.findIndex(
      (f) => f.key === field.old_key
    );
    if (index !== -1) {
      const { old_key, is_edit, ...newField } = field;
      inspectionForm.value[index] = newField;
    }
  } else {
    const exists = inspectionForm.value.some((f) => f.key === field.key);
    if (exists) {
      showSnackbar(`${field.label} sudah ada`, "error");
      return;
    }
    const { old_key, is_edit, ...newField } = field;
    inspectionForm.value.push(newField);
  }
};

const handleDelete = (item) => {
  const index = inspectionForm.value.indexOf(item);
  if (index !== -1) {
    inspectionForm.value.splice(index, 1);
  }
};

const handleEdit = async (item) => {
  if (item.is_template) {
    tempObj.value = {};
    await nextTick();

    tempObj.value = { ...item };
    isEdit.value = true;

    modalType.value = null;
    modalRadioInput.value = false;
    modalTextInput.value = false;

    await nextTick();

    if (item.input_type === "radio") {
      modalCheckboxInput.value = true;
    } else if (item.input_type === "text") {
      modalType.value = "text";
      modalTextInput.value = true;
    } else if (item.input_type === "checkbox") {
      modalType.value = item.key === "working_permit" ? "permit" : "checkbox";
      modalCheckboxInput.value = true;
    }
  }
};

const handleLoadTemplate = async (templateId) => {
  try {
    inspectionForm.value = inspectionForm.value.filter(
      (item) => !item.is_template
    );
    showProgress(true);
    const response = await apiClient.get(
      `/inspection/template-content/${templateId}`
    );
    const result = response.data.data;
    inspectionForm.value.push(...result.inspectionForm);
    showProgress(false);
  } catch (error) {
    showSnackbar("Gagal mengambil data form " + error, "error");
  }
};

const handleReplaceSaveTemplate = async () => {
  if (selectedTemplate.value) {
    const confirmed = await openDialog({
      title: "Perhatian",
      message: "Anda yakin ingin menumpuk template?",
    });
    await handleMessageBox(confirmed);
  } else {
    await handleSaveTemplate();
  }
};

const handleMessageBox = async (confirmed) => {
  if (confirmed) {
    const tempTemplate = templates.value.find(
      (t) => t.value === selectedTemplate.value
    );
    await handleUpdateTemplate(tempTemplate.value, tempTemplate.text);
  } else {
    await handleSaveTemplate();
  }
};

const handleSaveTemplate = async () => {
  const description = await openInputBoxDialog({
    title: "Masukkan Deskripsi Template",
  });
  if (!description || description.trim() === "") {
    showSnackbar("Deskripsi template tidak boleh kosong", "error");
    return;
  }
  try {
    const disallowedKeys = [
      "nama_pemegang",
      "lokasi_pekerjaan",
      "jenis_pekerjaan",
      "waktu_inspeksi",
    ];

    const tempinspectionForm = inspectionForm.value
      .filter((item) => !disallowedKeys.includes(item.key))
      .map(({ old_key, is_edit, ...rest }) => rest);

    showProgress(true);

    const response = await apiClient.post("/inspection/template", {
      inspectionForm: tempinspectionForm,
      Description: description,
      IsActivated: true,
    });

    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Template gagal disimpan", "error");
      return;
    }
    await loadDataTemplate();
    showProgress(false);
    showSnackbar("Template berhasil disimpan", "success");
  } catch (error) {
    showSnackbar("Gagal simpan template", "error");
  }
};

const handleUpdateTemplate = async (templateId, description) => {
  if (!description || description.trim() === "") {
    showSnackbar("Deskripsi template tidak boleh kosong", "error");
    return;
  }
  try {
    const disallowedKeys = [
      "nama_pemegang",
      "lokasi_pekerjaan",
      "jenis_pekerjaan",
      "waktu_inspeksi",
    ];
    const tempinspectionForm = inspectionForm.value
      .filter((item) => !disallowedKeys.includes(item.key))
      .map(({ old_key, is_edit, ...rest }) => rest);
    showProgress(true);
    const response = await apiClient.put(`/inspection/template/${templateId}`, {
      inspectionForm: tempinspectionForm,
      Description: description,
    });

    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Template gagal diupdate", "error");
      return;
    }
    await loadDataTemplate();
    showProgress(false);
    showSnackbar("Template berhasil diupdate", "success");
  } catch (error) {
    showSnackbar("Gagal update template", "error");
  }
};

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
  if (props.FileIdWP === null) return;
  const pdfUrl = `/storage/uploads/${props.FileIdWP}.pdf`;
  const response = await fetch(pdfUrl);
  const blob = await response.blob();
  const file = new File([blob], `${props.FileIdWP}.pdf`, {
    type: "application/pdf",
  });
  fileInputs.value.push({
    key: "working_permit",
    fileinput: file,
  });
};

watch(
  () => props.inspectionId,
  async (val) => {
    if (val) {
      await loadData(val);
      await loadDataTemplate();
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
    formData.append("InputInspection", JSON.stringify(inspectionForm.value));
    formData.append("IsDraft", Number(isDraft.value));
    fileInputs.value
      .filter((fileInput) => fileInput.key !== "working_permit")
      .forEach((fileInput, index) => {
        formData.append(`FileInput[${index}][key]`, fileInput.key);
        formData.append(`FileInput[${index}][fileinput]`, fileInput.fileinput);
      });

    const response = await apiClient.post(
      `/work-in-progress/swa/${data.value.inspection.work_in_progress.WIPId}`,
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
