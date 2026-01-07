<template>
  <v-card
    class="trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <div class="add-btn" @click="goBack">
        {{ modalType }}
        <i class="material-symbols-outlined">
          <span class="material-symbols-outlined"> arrow_back </span>
        </i>
        Back
      </div>
    </div>
    <div v-if="inspectionForm.length > 0">
      <form>
        <div class="row">
          <div
            v-for="item in content"
            :key="item.key || item.label"
            class="mb-4"
          >
            <div>
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
                    v-if="item.key === 'working_permit'"
                    prepend-icon="mdi-file-pdf-box"
                    class="text-none ml-2"
                    color="primary"
                    @click="viewPDF(WPId)"
                  >
                    View PDF
                  </v-btn>
                </v-col>
              </v-row>
            </div>
          </div>
        </div>
        <v-row>
          <v-col cols="12" class="d-flex justify-between">
            <v-col>
              <v-btn
                class="me-4 text-none"
                color="primary"
                prepend-icon="mdi-check-circle-outline"
                @click="submitForm(true)"
              >
                Verifikasi
              </v-btn>
              <v-btn
                class="text-none"
                color="red"
                prepend-icon="mdi-alpha-x-circle-outline"
                @click="submitForm(false)"
              >
                Tolak
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

  <ShowSnackbar />
  <ConfirmDialog />
  <ProgressLoader />
  <PdfViewerDialog />
</template>
<script setup>
import { ref, computed } from "vue";
import apiClient from "@utils/axios";
import DynamicForm from "@components/Common/DynamicForm.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";

const { openPdfDialog } = usePdfDialog();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();

defineOptions({
  name: "WorkInProgressCreate",
});

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  inspectionForm: {
    type: Array,
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
});

const emit = defineEmits(["back-to-list"]);

const goBack = () => {
  emit("back-to-list");
};

const cleanedFormTemplate = computed(() =>
  props.inspectionForm.map(({ is_template, ...rest }) => rest)
);

const content = ref([...cleanedFormTemplate.value]);
const modalType = ref(null);

const submitForm = async (isVerified) => {
  const confirmed = await openDialog({
    title: "Konfirmasi Simpan",
    message: `Apakah Anda yakin ingin ${
      isVerified ? "verifikasi" : "tolak"
    } data ini?`,
  });

  if (!confirmed) return;
  await saveInspection(isVerified);
};

const viewPDF = async (key) => {
  showProgress(true);
  const response = await apiClient.get(`/working-permit/fileid/${key}`);
  const FileId = response.data.data.FileId;
  const pdfUrl = `/storage/uploads/${FileId}.pdf`;
  showProgress(false);
  openPdfDialog(pdfUrl, "View Working Permit PDF");
};

const saveInspection = async (isVerified) => {
  try {
    const updatedContent = content.value.map((item) => ({
      ...item,
      answer: item.default ?? "",
    }));

    const payload = {
      InputInspection: updatedContent,
      IsVerified: isVerified,
    };

    showProgress(true);
    const response = await apiClient.post(
      `/work-in-progress/verification/${props.WIPId}`,
      payload
    );
    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Data tidak berhasil disimpan", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil disimpan", "success");
    goBack();
  } catch (error) {
    showSnackbar("Data tidak berhasil disimpan " + error, "error");
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
