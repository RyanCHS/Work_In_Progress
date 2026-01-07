<template>
  <PageTitle pageTitle="Inspection" subTitle="Inspection List" />

  <InspectionList />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import InspectionList from "@components/Admin/Inspection/InspectionList/index.vue";

defineOptions({
  name: "InspectionPage",
});
</script>
