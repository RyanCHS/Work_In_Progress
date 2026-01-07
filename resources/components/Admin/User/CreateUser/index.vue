<template>
  <v-card
    class="mb-25 trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <div class="add-btn" @click="goBack">
        <i class="material-symbols-outlined">
          <span class="material-symbols-outlined"> arrow_back </span>
        </i>
        Back
      </div>
    </div>
    <div class="basic-form">
      <form @submit.prevent="saveData">
        <div class="trezo-form-group">
          <v-label class="d-block fw-medium text-black">User Group</v-label>
          <div class="dynamic-width-select">
            <v-select
              v-model="selectedUserGroup"
              label="Select User Group"
              :items="groups"
              item-title="Name"
              item-value="Name"
              variant="outlined"
              density="comfortable"
              required
              hide-details
              dense
              clearable
            />
          </div>
        </div>
        <div class="trezo-form-group">
          <v-label class="d-block fw-medium text-black">User Name</v-label>
          <v-text-field
            v-model="userName"
            label="Masukkan User Name"
            error-messages="User Name harus diisi"
            required
          ></v-text-field>
        </div>
        <div class="trezo-form-group">
          <v-label class="d-block fw-medium text-black">Nama</v-label>
          <v-text-field
            v-model="name"
            label="Masukkan Nama"
            error-messages="Nama harus diisi"
            required
          ></v-text-field>
        </div>
        <div class="trezo-form-group">
          <v-label class="main-label d-block fw-medium text-black">
            Password
          </v-label>
          <v-text-field
            v-model="password"
            type="password"
            label="Password"
            error-messages="Password harus diisi"
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
            label="Confirm Password"
            error-messages="Password harus diisi"
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
            <v-col class="d-flex justify-end">
              <v-btn
                class="text-none"
                color="green"
                prepend-icon="mdi-cancel"
                @click="goBack"
              >
                Batal
              </v-btn>
            </v-col>
          </v-col>
        </v-row>
      </form>
    </div>
  </v-card>

  <template>
    <ShowSnackbar />
    <ConfirmDialog />
    <ProgressLoader />
  </template>
</template>

<script setup>
import { watch, ref, onMounted } from "vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import apiClient from "@utils/axios";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useAuth } from "@composables/useAuth";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
const { user, fetchUser } = useAuth();
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();

defineOptions({
  name: "CreateUser",
});
const emit = defineEmits(["back-to-list"]);

const goBack = () => {
  emit("back-to-list");
};
const selectedUserGroup = ref("");
const groups = ref([]);
const userName = ref("");
const name = ref("");
const password = ref("");
const confirmPassword = ref("");

onMounted(async () => {
  await loadDataGroup();
});

const loadDataGroup = async () => {
  try {
    showProgress(true);
    const response = await apiClient.get("/user/groups");

    if (!response.data.success) {
      showSnackbar("Gagal mengambil data", "error");
      return;
    }
    groups.value = response.data.data;
    showProgress(false);
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  } finally {
    showProgress(false);
  }
};

const saveData = async () => {
  if (password.value !== confirmPassword.value) {
    showSnackbar("Password tidak sama", "error");
    return;
  }
  const confirmed = await openDialog({
    title: "Perhatian",
    message: "Anda yakin ingin menyimpan data?",
  });
  if (!confirmed) return;
  try {
    showProgress(true);
    const response = await apiClient.post("/register", {
      Username: userName.value,
      UserGroup: selectedUserGroup.value,
      Name: name.value,
      Password: password.value,
      IsActivated: true,
    });

    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Gagal menyimpan data", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil disimpan", "success");
    goBack();
  } catch (error) {
    showSnackbar("Gagal menyimpan data " + error, "error");
  }
};
</script>

<style lang="scss" scoped>
.add-btn {
  cursor: pointer;
}
.basic-form {
  form {
    .btn {
      width: 100%;
      height: auto;
      display: block;
      min-width: auto;
      margin-top: 25px;
      padding: 12px 25px;
      border-radius: 7px;
      color: var(--whiteColor);
      background-color: var(--primaryColor);
      font: {
        size: 16px;
        weight: 500;
      }
    }
    .v-checkbox.v-input {
      margin: -18px -9px -40px -10px;
    }
    .v-input__details {
      display: none !important;
    }
    .v-selection-control__input {
      width: 0 !important;
      height: 0 !important;
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .basic-form {
    form {
      .btn {
        padding: 12px 25px;
        margin-top: 17px;
        font-size: 13px;
      }
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
  .basic-form {
    form {
      .btn {
        margin-top: 20px;
      }
    }
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .basic-form {
    form {
      .btn {
        font-size: 14px;
        padding: 12px 25px;
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .basic-form {
    form {
      .btn {
        font-size: 15px;
      }
    }
  }
}
</style>
