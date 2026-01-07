import { ref } from "vue";

const dialog = ref(false);
const title = ref("Upload File");
const file = ref(null);
const result = ref(null);

let onConfirmCallback = null;

export function useFileUploaderDialog() {
  const openFileUploader = (options = {}) => {
    file.value = null;
    result.value = null;
    title.value = options.title || "Upload File";
    onConfirmCallback = options.onConfirm || null;
    dialog.value = true;
  };

  const cancel = () => {
    dialog.value = false;
    file.value = null;
    result.value = null;
  };

  const confirm = () => {
    if (!file.value || !file.value.name || file.value.size === 0) {
      return;
    }
    result.value = file.value;
    dialog.value = false;

    if (typeof onConfirmCallback === "function") {
      onConfirmCallback(result.value);
    }
  };

  return {
    dialog,
    title,
    file,
    result,
    openFileUploader,
    cancel,
    confirm,
  };
}
