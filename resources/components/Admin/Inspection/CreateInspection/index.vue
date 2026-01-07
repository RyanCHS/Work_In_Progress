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
      <form @submit.prevent="submitForm">
        <div class="row">
          <div
            v-for="item in content"
            :key="item.key || item.label"
            class="mb-4"
          >
            <div>
              <div v-if="item.is_template" class="col-sm-12">
                <v-row>
                  <v-col cols="12" md="10">
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
                  <v-col cols="12" md="2" class="d-flex justify-end">
                    <v-btn
                      prepend-icon="mdi-file-document-edit-outline"
                      class="text-none mx-2"
                      color="green"
                      @click="handleEdit(item)"
                    >
                      Edit
                    </v-btn>

                    <v-btn
                      prepend-icon="mdi-trash-can-outline"
                      class="text-none"
                      color="red"
                      @click="handleDelete(item)"
                    >
                      Delete
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
              prepend-icon="mdi-content-save-all-outline"
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
    <div v-else><v-skeleton-loader type="article"></v-skeleton-loader></div>
  </v-card>
  <!-- Add Field Modal -->
  <AddFieldModal
    v-if="modalType === 'add'"
    v-model="modalType"
    :disableWorkingPermit="isWorkingPermitAdded"
    @choose="handleAddChoice"
  />

  <!-- Working Permit Modal -->
  <CheckboxInputDialog
    v-if="modalType === 'permit'"
    :fieldKey="'working_permit'"
    headerLabel="Tambah Working Permit"
    v-model="modalCheckboxInput"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <!-- General Field Modal -->
  <AddGeneralFieldModal
    v-if="modalType === 'general'"
    v-model="modalType"
    @choose="handleAddChoice"
  />

  <!-- Text Input Modal -->
  <TextInputDialog
    v-if="modalType === 'text'"
    v-model="modalTextInput"
    headerLabel="Tambah Text"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <!-- Selector Modal -->
  <RadioInputDialog
    v-if="modalType === 'selector'"
    v-model="modalRadioInput"
    headerLabel="Tambah Selector"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <CheckboxInputDialog
    v-if="modalType === 'checkbox'"
    v-model="modalCheckboxInput"
    headerLabel="Tambah Checkbox"
    :initialValue="tempObjReady"
    :isEdit="isEdit"
    @save="handleAddField"
  />

  <ShowSnackbar />
  <ConfirmDialog />
  <InputBoxDialog />
  <ProgressLoader />
</template>
<script setup>
import { ref, onMounted, computed, nextTick } from "vue";
import apiClient from "@utils/axios";
import { useAuth } from "@composables/useAuth";
import DynamicForm from "@components/Common/DynamicForm.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import InputBoxDialog from "@components/Common/InputBoxDialog.vue";
import AddFieldModal from "@components/Admin/Inspection/General/AddFieldModal.vue";
import AddGeneralFieldModal from "@components/Admin/Inspection/General/AddGeneralFieldModal.vue";
import RadioInputDialog from "@components/Admin/Inspection/General/RadioInputDialog.vue";
import CheckboxInputDialog from "@components/Admin/Inspection/General/CheckboxInputDialog.vue";
import TextInputDialog from "@components/Admin/Inspection/General/TextInputDialog.vue";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useInputTextDialog } from "@composables/useInputTextDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";

const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
const { openInputBoxDialog } = useInputTextDialog();

defineOptions({
  name: "CreateInspection",
});

const emit = defineEmits(["back-to-list"]);

const goBack = () => {
  emit("back-to-list");
};

const isDraft = ref(false);
const headerContent = ref([
  {
    key: "nama_pemegang",
    type: "field",
    label: "Nama Pemegang SWA",
    inline: true,
    default: "",
    is_input: true,
    required: true,
    input_type: "text",
    answer: "",
  },
  {
    key: "lokasi_pekerjaan",
    type: "field",
    label: "Lokasi Pekerjaan",
    inline: true,
    default: "",
    is_input: true,
    required: true,
    input_type: "text",
    answer: "",
  },
  {
    key: "jenis_pekerjaan",
    type: "field",
    label: "Jenis Pekerjaan",
    inline: true,
    default: "",
    is_input: true,
    required: true,
    input_type: "text",
    answer: "",
  },
  {
    key: "waktu_inspeksi",
    type: "field",
    label: "Waktu Inspeksi",
    inline: true,
    default: "",
    is_input: true,
    required: true,
    input_type: "date",
    answer: "",
  },
]);
const content = ref([]);
const { user } = useAuth();
const tempObj = ref({});
const modalType = ref(null);
const modalTextInput = ref(false);
const modalRadioInput = ref(false);
const modalCheckboxInput = ref(false);
const isEdit = ref(false);
const templates = ref([]);
const selectedTemplate = ref(null);

onMounted(async () => {
  try {
    content.value.push(...headerContent.value);
    content.value.forEach((item) => {
      if (item.type === "category") {
        item.answer = item.table.rows.map((row, i) => ({
          ...row,
          no: i + 1,
        }));
      }
    });
    await loadDataTemplate();
  } catch (error) {
    showSnackbar("Gagal mengambil data form " + error, "error");
  }
});

const hasTemplates = computed(
  () => content.value.filter((item) => item.is_template === true).length > 0
);

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
    showSnackbar("Gagal mengambil data template " + error, "error");
  }
};

const tempObjReady = computed(() => ({ ...tempObj.value }));

const handleAddModal = () => {
  tempObj.value = {};
  modalType.value = "add";
};

const disableAllModal = () => {
  modalRadioInput.value = false;
  modalCheckboxInput.value = false;
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
    const index = content.value.findIndex((f) => f.key === field.old_key);
    if (index !== -1) {
      const { old_key, is_edit, ...newField } = field;
      content.value[index] = newField;
    }
  } else {
    const exists = content.value.some((f) => f.key === field.key);
    if (exists) {
      showSnackbar(`${field.label} sudah ada`, "error");
      return;
    }
    const { old_key, is_edit, ...newField } = field;
    content.value.push(newField);
  }
};

const handleDelete = (item) => {
  const index = content.value.indexOf(item);
  if (index !== -1) {
    content.value.splice(index, 1);
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
    content.value = content.value.filter((item) => !item.is_template);
    showProgress(true);
    const response = await apiClient.get(
      `/inspection/template-content/${templateId}`
    );
    const result = response.data.data;
    content.value.push(...result.Content);
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

    const tempContent = content.value
      .filter((item) => !disallowedKeys.includes(item.key))
      .map(({ old_key, is_edit, ...rest }) => rest);

    showProgress(true);

    const response = await apiClient.post("/inspection/template", {
      Content: tempContent,
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
    showSnackbar("Gagal simpan template " + error, "error");
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
    const tempContent = content.value
      .filter((item) => !disallowedKeys.includes(item.key))
      .map(({ old_key, is_edit, ...rest }) => rest);
    showProgress(true);
    const response = await apiClient.put(`/inspection/template/${templateId}`, {
      Content: tempContent,
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
    showSnackbar("Gagal update template " + error, "error");
  }
};

const submitForm = async () => {
  const confirmed = await openDialog({
    title: "Konfirmasi Simpan",
    message: "Apakah Anda yakin ingin menyimpan data ini?",
  });

  if (!confirmed) return; // jika cancel, langsung stop
  await saveInspection();
};

const isWorkingPermitAdded = computed(() =>
  content.value.some((item) => item.key === "working_permit")
);

const saveInspection = async () => {
  try {
    const getValue = (key) =>
      content.value.find((item) => item.key === key)?.default ?? "";

    const updatedContent = content.value.map((item) => ({
      ...item,
      answer: item.default ?? "",
    }));

    const payload = {
      SWAPIC: getValue("nama_pemegang"),
      Location: getValue("lokasi_pekerjaan"),
      WorkType: getValue("jenis_pekerjaan"),
      InspectionDate: getValue("waktu_inspeksi"),
      InspectionForm: !isDraft.value ? updatedContent : content.value,
      IsDraft: isDraft.value,
      CreatedBy: user.value.UserId,
    };

    // Validasi manual
    if (
      !payload.SWAPIC ||
      !payload.Location ||
      !payload.WorkType ||
      !payload.InspectionDate
    ) {
      showSnackbar("Harap lengkapi semua field yang wajib diisi", "error");
      return;
    }

    const isWorkingPermitExist = content.value.some(
      (item) => item.key === "working_permit"
    );
    if (!isWorkingPermitExist) {
      showSnackbar("Working Permit harus ditambah", "error");
      return;
    }
    showProgress(true);
    const response = await apiClient.post("/inspection", payload);
    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Data tidak berhasil disimpan", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil disimpan", "success");
    goBack();
  } catch (error) {
    showSnackbar("Data tidak berhasil disimpan", "error");
  }
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

<!-- content.value = [
console.log("🚀 ~ content:", content);
{
  type: "field",
  label: "Nama Pemegang SWA",
  input_type: "text",
  key: "nama_pemegang_swa",
  is_input: true,
  required: true,
},
{
  type: "field",
  label: "Jenis Pekerjaan",
  input_type: "select",
  options: ["Konstruksi", "Pemeliharaan"],
  key: "jenis_pekerjaan",
  is_input: true,
  required: true,
},
{
  type: "category",
  label: "Kegiatan",
  table: {
    columns: [
      {
        key: "no",
        label: "No",
        type: "autonumber",
        is_input: true,
        align_horizontal: "center",
        align_vertical: "middle",
      },
      {
        key: "kriteria",
        label: "Kriteria",
        type: "text",
        align_horizontal: "center",
        align_vertical: "middle",
      },
      {
        key: "pemenuhan",
        label: "Pemenuhan",
        type: "select",
        is_input: true,
        align_horizontal: "center",
        align_vertical: "middle",
        options: ["Ya", "Tidak"],
      },
      {
        key: "keterangan",
        label: "Keterangan",
        type: "text",
        align_horizontal: "center",
        align_vertical: "middle",
      },
    ],
    rows: [
      {
        kriteria: "Menggunakan APD lengkap",
        pemenuhan: "",
        keterangan: "Keterangan 1",
        align_horizontal: "left",
        align_vertical: "middle",
        spacing: "py-2",
      },
      {
        kriteria: "Area kerja bersih dan aman",
        pemenuhan: "",
        keterangan: "Keterangan 1",
        align_horizontal: "left",
        align_vertical: "middle",
        spacing: "py-2",
      },
    ],
  },
},
{
  type: "category",
  label: "Pemeriksaan Alat",
  table: {
    columns: [
      {
        key: "no",
        label: "No",
        type: "autonumber",
        is_input: true,
        align_horizontal: "center",
        align_vertical: "middle",
      },
      {
        key: "nama_alat",
        label: "Nama Alat",
        type: "text",
        align_horizontal: "center",
        align_vertical: "middle",
      },
      {
        key: "kondisi",
        label: "Kondisi",
        type: "group",
        align_horizontal: "center",
        align_vertical: "middle",
        children: [
          {
            key: "fisik",
            label: "Fisik",
            type: "select",
            is_input: true,
            align_horizontal: "center",
            align_vertical: "middle",
            options: ["Baik", "Rusak"],
          },
          {
            key: "fungsi",
            label: "Fungsi",
            type: "select",
            is_input: true,
            align_horizontal: "center",
            align_vertical: "middle",
            options: ["Berfungsi", "Tidak Berfungsi"],
          },
        ],
      },
      {
        key: "sertifikasi",
        label: "Sertifikasi",
        type: "group",
        align_horizontal: "center",
        align_vertical: "middle",
        children: [
          {
            key: "ada",
            label: "Ada",
            type: "select",
            is_input: true,
            align_horizontal: "center",
            align_vertical: "middle",
            options: ["Ya", "Tidak"],
          },
          {
            key: "valid",
            label: "Valid",
            type: "select",
            is_input: true,
            align_horizontal: "center",
            align_vertical: "middle",
            options: ["Ya", "Tidak"],
          },
        ],
      },
    ],
    rows: [
      {
        nama_alat: "Palu",
        kondisi: { fisik: "", fungsi: "" },
        sertifikasi: { ada: "", valid: "" },
        align_horizontal: "left",
        align_vertical: "middle",
        spacing: "py-2",
      },
      {
        nama_alat: "Obeng",
        kondisi: { fisik: "", fungsi: "" },
        sertifikasi: { ada: "", valid: "" },
        align_horizontal: "left",
        align_vertical: "middle",
        spacing: "py-2",
      },
    ],
  },
}, -->
