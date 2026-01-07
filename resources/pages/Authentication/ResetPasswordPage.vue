<template>
  <ResetPassword />
</template>

<script>
import { defineComponent } from "vue";
import ResetPassword from "@components/Modules/Authentication/ResetPassword/index.vue";

export default defineComponent({
  name: "ResetPasswordPage",
  components: {
    ResetPassword,
  },
});
</script>
