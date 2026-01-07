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

    <div class="trezo-table-content">
      <v-row>
        <!-- Tabel Kiri: SWA -->
        <v-col cols="12" md="6">
          <div class="v-card-header">
            <h5 class="mb-0">Input SWA By Safety Advisor</h5>
          </div>
          <div class="trezo-table">
            <v-table>
              <thead>
                <tr>
                  <th>Keterangan</th>
                  <th>Nilai</th>
                  <th>Data PDF</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in InputSWA.InputInspection"
                  :key="`swa-${index}`"
                >
                  <td>{{ item.label }}</td>
                  <td>
                    {{
                      item.input_type === "date"
                        ? formatDate(item.answer)
                        : item.answer
                    }}
                  </td>

                  <td>
                    <v-tooltip
                      v-if="
                        isPDF(item.key, InputSWA.file_inputs) ||
                        item.key === 'working_permit'
                      "
                      text="View PDF"
                    >
                      <template v-slot:activator="{ props }">
                        <button
                          v-bind="props"
                          @click="viewPDF(item.key, InputSWA.file_inputs)"
                          type="button"
                        >
                          <i class="material-symbols-outlined">
                            picture_as_pdf
                          </i>
                        </button>
                      </template>
                    </v-tooltip>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </div>
        </v-col>

        <!-- Tabel Kanan: Perbaikan SWA -->
        <v-col cols="12" md="6">
          <div class="v-card-header">
            <h5 class="mb-0">SWA By Pengawas K3</h5>
          </div>
          <div class="trezo-table">
            <v-table>
              <thead>
                <tr>
                  <th>Keterangan</th>
                  <th>Nilai</th>
                  <th>Data PDF</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in ValidasiSWA.InputInspection"
                  :key="`repair-${index}`"
                >
                  <td>{{ item.label }}</td>
                  <td>
                    {{
                      item.input_type === "date"
                        ? formatDate(item.answer)
                        : item.answer
                    }}
                  </td>
                  <td>
                    <v-tooltip
                      v-if="
                        isPDF(item.key, ValidasiSWA.file_inputs) ||
                        item.key === 'working_permit'
                      "
                      text="View PDF"
                    >
                      <template v-slot:activator="{ props }">
                        <button
                          v-bind="props"
                          @click="viewPDF(item.key, ValidasiSWA.file_inputs)"
                          type="button"
                        >
                          <i class="material-symbols-outlined">
                            picture_as_pdf
                          </i>
                        </button>
                      </template>
                    </v-tooltip>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </div>
        </v-col>
      </v-row>
      <v-row v-if="IsView === false">
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
    </div>
  </v-card>
  <PdfViewerDialog />
  <ConfirmDialog />
</template>
<script setup>
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import { formatDate } from "@utils/date";
import { usePdfDialog } from "@composables/usePdfDialog";
import { useConfirmDialog } from "@composables/useConfirmDialog";
const { openDialog } = useConfirmDialog();
const { openPdfDialog } = usePdfDialog();

defineOptions({
  name: "SWAValidation",
});

const props = defineProps({
  InputSWA: {
    type: Object,
    required: true,
  },
  ValidasiSWA: {
    type: Object,
    required: true,
  },
  FileIdWP: {
    type: String,
    required: true,
  },
  IsView: Boolean,
});

const isPDF = (inputInspection, fileInputs) => {
  return fileInputs.some((f) => f.Keytag === inputInspection);
};

const viewPDF = async (key, fileInputs) => {
  const selectPDF = fileInputs.find((f) => f.Keytag === key);
  const pdfUrl = `/storage/uploads/${
    key === "working_permit" ? props.FileIdWP : selectPDF.FileId
  }.pdf`;
  openPdfDialog(pdfUrl, "View Working Permit PDF");
};

const emit = defineEmits(["back-to-list"], ["validation"]);

const goBack = () => {
  emit("back-to-list");
};

const submitForm = async (isValidation) => {
  const confirmed = await openDialog({
    title: "Konfirmasi Simpan",
    message: `Apakah Anda yakin ingin validasi data ini?`,
  });

  if (!confirmed) return;
  emit("validation", isValidation, props.ValidasiSWA.SWAId);
  goBack();
};
</script>
<style scoped>
.add-btn {
  cursor: pointer;
}
</style>
