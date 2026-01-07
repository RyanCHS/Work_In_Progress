<template>
  <PageTitle pageTitle="Internal Error" subTitle="Error" />

  <InternalError />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import InternalError from "@components/Modules/Errors/InternalError/index.vue";

defineOptions({
  name: "InternalErrorPage",
});
</script>
