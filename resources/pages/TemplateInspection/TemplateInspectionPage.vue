<template>
  <PageTitle pageTitle="Template Inspection" subTitle="Error" />
  <TemplateInspection />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import TemplateInspection from "@components/Admin/Inspection/TemplateInspection/index.vue";

defineOptions({
  name: "TemplateInspectionPage",
});
</script>
