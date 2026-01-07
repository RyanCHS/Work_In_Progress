<template>
  <PageTitle pageTitle="Work In Progress" subTitle="Work In Progress List" />

  <WorkInProgressList />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import WorkInProgressList from "@components/Admin/WorkInProgress/WorkInProgressList/index.vue";

defineOptions({
  name: "WorkInProgressPage",
});
</script>
