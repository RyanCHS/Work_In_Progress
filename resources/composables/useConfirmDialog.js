import { ref } from "vue";

const dialog = ref(false);
const title = ref("");
const message = ref("");
let resolveAction;

export function useConfirmDialog() {
  const openDialog = ({ title: t, message: m }) => {
    title.value = t;
    message.value = m;
    dialog.value = true;

    return new Promise((resolve) => {
      resolveAction = resolve;
    });
  };

  const confirm = () => {
    dialog.value = false;
    resolveAction(true);
  };

  const cancel = () => {
    dialog.value = false;
    resolveAction(false);
  };

  return {
    dialog,
    title,
    message,
    openDialog,
    confirm,
    cancel,
  };
}
