<template>
  <PageTitle pageTitle="User Management" subTitle="User Management List" />
  <UserManagementList />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import UserManagementList from "@components/Admin/User/UserManagementList/index.vue";
defineOptions({
  name: "UserManagementPage",
});
</script>
