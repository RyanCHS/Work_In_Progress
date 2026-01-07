<template>
  <PageTitle pageTitle="Notification" />
  <NotificationList />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import NotificationList from "@components/Admin/NotificationList/index.vue";

defineOptions({
  name: "NotificationsPage",
});
</script>
