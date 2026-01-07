<template>
  <div class="sign-in-area">
    <div class="trezo-form ml-auto mr-auto">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 order-2 order-lg-1">
          <div class="form-image">
            <v-img src="/images/sign-in.jpg" alt="sign-in-image" />
          </div>
        </div>
        <div class="col-lg-6 col-md-12 order-1 order-lg-2">
          <div class="form-content">
            <div class="title">
              <h1 class="fw-semibold">Silahkan Register!</h1>
            </div>
            <form @submit.prevent="registerData">
              <div class="trezo-form-group">
                <v-label class="d-block fw-medium text-black"
                  >User Group</v-label
                >
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
                <v-label class="d-block fw-medium text-black"
                  >User Name</v-label
                >
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
        </div>
      </div>
    </div>
  </div>
  <template>
    <ShowSnackbar />
    <ConfirmDialog />
    <ProgressLoader />
  </template>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import apiClient from "@utils/axios";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
const { openDialog } = useConfirmDialog();
const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();

defineOptions({
  name: "Register",
});

const goBack = () => {
  window.location.href = "/authentication/sign-in";
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

const registerData = async () => {
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
      IsActivated: false,
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
.sign-in-area {
  background-color: var(--whiteColor);
  padding: {
    top: 135px;
    bottom: 135px;
  }
  &.dark-theme {
    background-color: #0a0e19;
  }
  .trezo-form {
    max-width: 1255px;
    padding: {
      left: 12.5px;
      right: 12.5px;
    }
    .form-image {
      border-radius: 24px;
      margin-right: -25px;

      .v-img {
        border-radius: 24px;
        margin-bottom: -3px;
      }
    }
    .form-content {
      padding-left: 90px;

      .title {
        margin: {
          top: 23px;
          bottom: 23px;
        }
        h1 {
          font-size: 28px;
          margin-bottom: 7px;
        }
        p {
          font-size: 16px;
          color: #445164;
        }
      }
      form {
        .trezo-form-group {
          margin-bottom: 15px;

          .v-label {
            margin-bottom: 12px;
          }
        }
        .forgot-password {
          a {
            &:hover {
              text-decoration: underline;
            }
          }
        }
        button {
          width: 100%;
          height: auto;
          min-width: auto;
          margin-top: 24px;
          padding: 12px 25px;
          border-radius: 7px;
          color: var(--whiteColor);
          background-color: var(--primaryColor);
          display: flex;
          align-items: center;
          justify-content: center;

          & {
            font: {
              size: 16px;
              weight: 500;
            }
          }
          i {
            margin-right: 5px;
          }
        }
        .info {
          margin-top: 20px;

          a {
            &:hover {
              text-decoration: underline;
            }
          }
        }
      }
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .sign-in-area {
    padding: {
      top: 60px;
      bottom: 60px;
    }
    .trezo-form {
      max-width: 100%;

      .form-image {
        border-radius: 15px;
        margin: {
          right: 0;
          top: 25px;
        }
        img {
          border-radius: 15px;
        }
      }
      .form-content {
        padding-left: 0;

        .title {
          margin: {
            top: 17px;
            bottom: 15px;
          }
          h1 {
            font-size: 22px;
          }
          p {
            font-size: 13px;
          }
        }
        .with-socials {
          margin-bottom: 20px;

          div {
            margin: {
              left: 5px;
              right: 5px;
            }
          }
          button {
            padding: 8px 15px;
          }
        }
        form {
          .trezo-form-group {
            .v-label {
              margin-bottom: 10px;
            }
          }
          button {
            padding: 12px 25px;
            margin-top: 17px;
            font-size: 13px;
            i {
              font-size: 20px;
            }
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
  .sign-in-area {
    .trezo-form {
      max-width: 540px;

      .form-content {
        .title {
          margin: {
            top: 20px;
            bottom: 17px;
          }
        }
        form {
          button {
            margin-top: 20px;
          }
        }
      }
    }
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .sign-in-area {
    padding: {
      top: 80px;
      bottom: 80px;
    }
    .trezo-form {
      max-width: 720px;

      .form-image {
        margin: {
          right: 0;
          top: 25px;
        }
      }
      .form-content {
        padding-left: 0;

        .title {
          margin: {
            top: 20px;
            bottom: 20px;
          }
          h1 {
            font-size: 24px;
          }
          p {
            font-size: 14px;
          }
        }
        .with-socials {
          margin-bottom: 20px;

          div {
            margin: {
              left: 5px;
              right: 5px;
            }
          }
        }
        form {
          button {
            font-size: 14px;
            margin-top: 20px;
            padding: 12px 25px;
            i {
              font-size: 22px;
            }
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .sign-in-area {
    padding: {
      top: 100px;
      bottom: 100px;
    }
    .trezo-form {
      max-width: 960px;

      .form-image {
        margin-right: 0;
      }
      .form-content {
        padding-left: 15px;

        .title {
          h1 {
            font-size: 26px;
          }
          p {
            font-size: 15px;
          }
        }
        form {
          button {
            margin-top: 20px;
            font-size: 15px;
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 1200px to Max width 1399px */
@media only screen and (min-width: 1200px) and (max-width: 1399px) {
  .sign-in-area {
    padding: {
      top: 120px;
      bottom: 120px;
    }
    .trezo-form {
      max-width: 1140px;

      .form-image {
        margin-right: 0;
      }
      .form-content {
        padding-left: 25px;
      }
    }
  }
}

/* Min width 1600px */
@media only screen and (min-width: 1600px) {
  .sign-in-area {
    .trezo-form {
      .form-image {
        margin-right: -45px;
      }
      .form-content {
        padding-left: 120px;
      }
    }
  }
}
</style>
