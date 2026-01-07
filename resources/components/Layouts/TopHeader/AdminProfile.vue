<template>
  <v-menu>
    <template v-slot:activator="{ props }">
      <button class="profile-btn align-items-center" v-bind="props">
        <div class="me-3"><AvatarInitial :name="user?.Name" /></div>
        <span class="fw-semibold text-black">{{ user?.Name }}</span>
      </button>
    </template>
    <v-list class="profile-menu-dropdown">
      <div class="admin d-flex align-items-center">
        <div class="me-3"><AvatarInitial :name="user?.Name" /></div>
        <div>
          <span class="d-block text-black fw-semibold"> {{ user?.Name }} </span>
          <span class="d-block text-black fw-medium">
            User : {{ user?.Username }}
          </span>
          <span class="d-block"> {{ user?.UserGroup }} </span>
        </div>
      </div>
      <ul
        v-if="!hasRole('Administrator')"
        class="list pl-0 mt-0 mb-0 list-unstyled"
      >
        <li>
          <router-link to="/admin/myprofile" class="d-block position-relative">
            <i class="material-symbols-outlined">account_circle</i>
            My Profile
          </router-link>
        </li>
      </ul>
      <div v-if="!hasRole('Administrator')" class="border-top"></div>
      <ul class="list pl-0 mt-0 mb-0 list-unstyled">
        <li>
          <router-link to="/logout" class="d-block position-relative">
            <i class="material-symbols-outlined">logout</i>
            Logout
          </router-link>
        </li>
      </ul>
    </v-list>
  </v-menu>
</template>

<script setup>
import { useAuth } from "@composables/useAuth";
import AvatarInitial from "@components/Modules/AvatarInitial/index.vue";
defineOptions({
  name: "AdminProfile",
});
const { hasRole, user } = useAuth();
</script>
