<template>
  <SignUp />
</template>

<script>
import { defineComponent } from "vue";
import SignUp from "@components/Modules/Authentication/SignUp/index.vue";

export default defineComponent({
  name: "SignUpPage",
  components: {
    SignUp,
  },
});
</script>
