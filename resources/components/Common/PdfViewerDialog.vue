<template>
  <v-dialog v-model="dialog" max-width="900" full-width>
    <v-card>
      <v-toolbar color="primary" dark>
        <v-toolbar-title>{{ title }}</v-toolbar-title>
        <v-spacer />
        <v-btn icon @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-card-text>
        <pdf-embed :source="pdfUrl" class="pdf-viewer" />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import PdfEmbed from "vue-pdf-embed";
import { usePdfDialog } from "@composables/usePdfDialog";

defineOptions({
  name: "PdfViewerDialog",
});

const { dialog, pdfUrl, title, close } = usePdfDialog();
</script>
<style scoped>
.pdf-viewer {
  width: 100%;
  height: 80vh;
}

@media (max-width: 600px) {
  .pdf-viewer {
    height: 60vh;
  }
}
</style>
