import { ref } from "vue";

const dialog = ref(false);
const title = ref("");
const result = ref("");
let resolver = null;
export function useInputTextDialog() {
  const openInputBoxDialog = ({ title: t }) => {
    title.value = t;
    result.value = "";
    dialog.value = true;
    return new Promise((resolve) => {
      resolver = resolve;
    });
  };

  const confirm = () => {
    dialog.value = false;
    resolver?.(result.value);
  };

  const cancel = () => {
    dialog.value = false;
    resolver?.(null);
  };

  return {
    dialog,
    title,
    result,
    openInputBoxDialog,
    confirm,
    cancel,
  };
}
