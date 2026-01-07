<template>
  <ForgotPassword />
</template>

<script>
import { defineComponent } from "vue";
import ForgotPassword from "@components/Modules/Authentication/ForgotPassword/index.vue";

export default defineComponent({
  name: "ForgotPasswordPage",
  components: {
    ForgotPassword,
  },
});
</script>
