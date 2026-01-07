<template>
  <div class="col-md-6">
    <v-card
      class="mb-25 trezo-card border-radius d-block bg-white border-0 shadow-none"
    >
      <div class="v-card-header">
        <h5 class="mb-0">Ganti Password</h5>
      </div>
      <div class="basic-form">
        <form @submit.prevent="saveChangePassword">
          <div class="trezo-form-group">
            <v-label class="main-label d-block fw-medium text-black">
              Old Password
            </v-label>
            <v-text-field
              v-model="oldPassword"
              type="password"
              label="Type your old password"
              :error-messages="errors.OldPassword"
              required
            ></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="main-label d-block fw-medium text-black">
              New Password
            </v-label>
            <v-text-field
              v-model="newPassword"
              type="password"
              label="Type your new password"
              :error-messages="errors.Password"
              required
            ></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="main-label d-block fw-medium text-black">
              Confirm Password
            </v-label>
            <v-text-field
              v-model="confirmPassword"
              type="password"
              label="Confirm your new password"
              :error-messages="errors.ConfirmPassword"
              required
            ></v-text-field>
          </div>
          <v-row>
            <v-col cols="12" class="d-flex justify-between">
              <v-col>
                <v-btn
                  class="text-none"
                  type="submit"
                  color="primary"
                  prepend-icon="mdi-check-circle-outline"
                >
                  Simpan
                </v-btn>
              </v-col>
            </v-col>
          </v-row>
        </form>
      </div>
    </v-card>
  </div>
  <template>
    <ShowSnackbar />
    <ConfirmDialog />
    <ProgressLoader />
  </template>
</template>

<script setup>
import { ref, watch } from "vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import apiClient from "@utils/axios";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useAuth } from "@composables/useAuth";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";

const { user } = useAuth();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();

const oldPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");
const userid = ref(null);
const errors = ref({
  OldPassword: null,
  Password: null,
  ConfirmPassword: null,
});

watch(
  user,
  (val) => {
    if (val) {
      userid.value = val.UserId;
    }
  },
  { immediate: true }
);

const saveChangePassword = async () => {
  const confirmed = await openDialog({
    title: "Perhatian",
    message: "Anda yakin ingin mengedit data?",
  });
  if (!confirmed) return;
  // Reset errors
  errors.value = { OldPassword: null, Password: null, ConfirmPassword: null };

  // Simple validation
  if (!oldPassword.value) errors.value.OldPassword = "Old password is required";
  if (!newPassword.value) errors.value.Password = "New password is required";
  if (newPassword.value !== confirmPassword.value) {
    errors.value.ConfirmPassword = "Passwords do not match";
    return;
  }

  try {
    showProgress(true);
    await apiClient.put(`/user/${userid.value}/change-password`, {
      OldPassword: oldPassword.value,
      Password: newPassword.value,
    });
    showSnackbar("Password updated successfully", "success");
    oldPassword.value = "";
    newPassword.value = "";
    confirmPassword.value = "";
  } catch (error) {
    const msg = error.response?.data?.message || "Failed to change password";
    showSnackbar(msg, "error");
  } finally {
    showProgress(false);
  }
};
</script>
