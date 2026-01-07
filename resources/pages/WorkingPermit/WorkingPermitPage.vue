<template>
  <PageTitle pageTitle="Working Permit" subTitle="Working Permit" />

  <WorkingPermitPage />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import WorkingPermitPage from "@components/Admin/WorkingPermit/WorkingPermitList/index.vue";

defineOptions({
  name: "WorkingPermitPage",
});
</script>
