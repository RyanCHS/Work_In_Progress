<template>
  <PageTitle pageTitle="My Profile" subTitle="Error" />
  <MyProfile />
</template>

<script setup>
import PageTitle from "@components/Common/PageTitle.vue";
import MyProfile from "@components/Admin/MyProfile/index.vue";

defineOptions({
  name: "MyProfilePage",
});
</script>
