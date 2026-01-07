import { ref } from "vue";

const snackbar = ref({
  show: false,
  message: "",
  color: "success",
});

export const useSnackbar = () => {
  const showSnackbar = (message, color = "success") => {
    snackbar.value = {
      show: true,
      message,
      color,
    };
  };

  return {
    snackbar,
    showSnackbar,
  };
};
