<template>
  <div class="trezo-table-content">
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
          <tr v-for="(item, index) in data" :key="index">
            <td>{{ item.label }}</td>
            <td>{{ item.answer }}</td>
            <td>
              <v-tooltip v-if="item.pdfUrl !== null" text="View PDF">
                <template v-slot:activator="{ props }">
                  <button
                    v-bind="props"
                    type="button"
                    @click="viewPDF(item.pdfUrl)"
                  >
                    <i class="material-symbols-outlined"> picture_as_pdf </i>
                  </button>
                </template>
              </v-tooltip>
            </td>
          </tr>
        </tbody>
      </v-table>
    </div>
  </div>
  <PdfViewerDialog />
</template>

<script setup>
import PdfViewerDialog from "@components/Common/PdfViewerDialog.vue";
import { usePdfDialog } from "@composables/usePdfDialog";
const { openPdfDialog } = usePdfDialog();
defineOptions({
  name: "SWAResultTable",
});

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
});

const viewPDF = (pdfUrl) => {
  openPdfDialog(pdfUrl, "View Working Permit PDF");
};
</script>
