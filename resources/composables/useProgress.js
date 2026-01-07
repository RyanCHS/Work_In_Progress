import { ref } from "vue";

const progress = ref(false);

export const useProgress = () => {
  const showProgress = (isShow) => {
    progress.value = isShow;
  };

  return {
    progress,
    showProgress,
  };
};
