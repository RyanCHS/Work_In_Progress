// composables/usePdfDialog.js
import { ref } from "vue";

const dialog = ref(false);
const pdfUrl = ref("");
const title = ref("Preview PDF");

export const usePdfDialog = () => {
  const openPdfDialog = (url, customTitle = "Preview PDF") => {
    pdfUrl.value = url;
    title.value = customTitle;
    dialog.value = true;
  };

  const close = () => {
    dialog.value = false;
  };

  return {
    dialog,
    pdfUrl,
    title,
    openPdfDialog,
    close,
  };
};
