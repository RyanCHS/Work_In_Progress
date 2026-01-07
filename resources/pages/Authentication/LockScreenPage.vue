<template>
  <LockScreen />
</template>

<script>
import { defineComponent } from "vue";
import LockScreen from "@components/Modules/Authentication/LockScreen/index.vue";

export default defineComponent({
  name: "LockScreenPage",
  components: {
    LockScreen,
  },
});
</script>
