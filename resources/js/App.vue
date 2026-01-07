<template>
  <Preloader v-if="isLoading" />
  <LeftSidebar v-if="shouldShowSidebar && !isNotFound" />
  <div
    :class="[
      'main-content d-flex flex-column',
      { active: stateStoreInstance.open },
      { 'padding-minus': shouldShowPaddingLeftZero || isNotFound },
    ]"
  >
    <TopHeader v-if="shouldShowHeader && !isNotFound" />
    <div :class="containerClass">
      <router-view />
    </div>
    <div class="flex-grow-1"></div>
    <MainFooter v-if="shouldShowFooter && !isNotFound" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watchEffect } from "vue";
import { useRoute } from "vue-router";

import stateStore from "@utils/store";
import Preloader from "@components/Layouts/Preloader.vue";
import LeftSidebar from "@components/Layouts/LeftSidebar.vue";
import TopHeader from "@components/Layouts/TopHeader/TopHeader.vue";
import MainFooter from "@components/Layouts/MainFooter.vue";
import { useAuth } from "@composables/useAuth";

defineOptions({
  name: "App",
});

const { isAuthenticated, fetchUser } = useAuth();

const isLoading = ref(true);
const stateStoreInstance = stateStore;
const route = useRoute();

const hiddenRoutes = [
  "/",
  "/logout",
  "/features",
  "/errors/not-found",
  "/extra-pages/coming-soon",
  "/authentication/sign-in",
  "/authentication/register",
  "/authentication/sign-up",
  "/authentication/logout",
  "/authentication/forgot-password",
  "/authentication/reset-password",
  "/authentication/confirm-email",
  "/authentication/lock-screen",
];

const shouldShowSidebar = computed(() => !hiddenRoutes.includes(route.path));
const shouldShowHeader = computed(() => !hiddenRoutes.includes(route.path));
const shouldShowFooter = computed(() => !hiddenRoutes.includes(route.path));
const shouldShowPaddingLeftZero = computed(() =>
  hiddenRoutes.includes(route.path)
);

const isNotFound = computed(() =>
  route.matched.some((record) => record.path === "/:pathMatch(.*)*")
);

const containerClass = computed(() => ({
  "main-content-container": ![
    "/",
    "/features",
    "/team",
    "/faq",
    "/contact",
  ].includes(route.path),
}));

onMounted(async () => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);

  if (isAuthenticated()) {
    try {
      await fetchUser();
    } catch (e) {
      console.warn("Gagal mengambil data user:", e);
    }
  }

  watchEffect(() => {
    if (stateStoreInstance.open) {
      document.body.classList.remove("sidebar-show");
      document.body.classList.add("sidebar-hide");
    } else {
      document.body.classList.remove("sidebar-hide");
      document.body.classList.add("sidebar-show");
    }
  });
});
</script>

<style lang="scss" scoped>
.main-content {
  overflow: hidden;
  min-height: 100vh;
  transition: var(--transition);
  padding: {
    top: 95px;
    left: 285px;
    right: 25px;
  }
  &.active {
    padding-left: 85px;
  }
  &.right-sidebar {
    padding: {
      left: 25px;
      right: 285px;
    }
    &.active {
      padding-right: 85px;
    }
  }
  &.hide-sidebar {
    padding-left: 85px;

    &.right-sidebar {
      padding: {
        left: 25px;
        right: 85px;
      }
    }
  }
  &.padding-minus {
    padding-left: 25px;
    padding-top: 0;
  }
}

@media only screen and (max-width: 767px) {
  .main-content {
    padding: {
      top: 0;
      left: 15px;
      right: 15px;
    }
    &.active {
      padding-left: 15px;
    }
  }
}

@media only screen and (min-width: 768px) and (max-width: 991px) {
  .main-content {
    padding: {
      top: 0;
      left: 25px;
      right: 25px;
    }
    &.active {
      padding-left: 25px;
    }
  }
}

@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .main-content {
    padding: {
      top: 0;
      left: 25px;
      right: 25px;
    }
    &.active {
      padding-left: 25px;
    }
  }
}
</style>
