<template>
  <div class="col-md-6">
    <v-card
      class="mb-25 trezo-card border-radius d-block bg-white border-0 shadow-none"
    >
      <div class="v-card-header">
        <h5 class="mb-0">Edit Data</h5>
      </div>
      <div class="basic-form">
        <form @submit.prevent="saveEditData">
          <div class="trezo-form-group">
            <v-label class="d-block fw-medium text-black">Nama</v-label>
            <v-text-field v-model="name" label="Masukkan Nama"></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="d-block fw-medium text-black">
              Alamat Email
            </v-label>
            <v-text-field
              v-model="email"
              label="Masukkan Alamat Email"
            ></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="d-block fw-medium text-black"
              >Alamat Domisili</v-label
            >
            <v-text-field
              v-model="address"
              label="Masukkan Alamat Domisili"
            ></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="d-block fw-medium text-black">Kota</v-label>
            <v-text-field v-model="city" label="Masukkan Kota"></v-text-field>
          </div>
          <div class="trezo-form-group">
            <v-label class="d-block fw-medium text-black">Telepon</v-label>
            <v-text-field
              v-model="phone"
              label="Masukkan Telepon"
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
import { watch, ref } from "vue";
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
  name: "EditProfile",
});

const userid = ref(null);
const name = ref(null);
const email = ref(null);
const address = ref(null);
const city = ref(null);
const phone = ref(null);

watch(
  user,
  async (val) => {
    console.log("🚀 ~ watch ~ val:", val);
    if (val) {
      showProgress(true);
      const response = await apiClient.get(`/user/${val.UserId}`);
      const data = response.data.data;
      console.log("🚀 ~ watch ~ data:", data);

      userid.value = data.UserId;
      name.value = data.user_detail.Name;
      email.value = data.user_detail.Email;
      address.value = data.user_detail.Address;
      city.value = data.user_detail.City;
      phone.value = data.user_detail.Phone;
      showProgress(false);
    }
  },
  { immediate: true }
);

const saveEditData = async () => {
  const confirmed = await openDialog({
    title: "Perhatian",
    message: "Anda yakin ingin mengedit data?",
  });
  if (!confirmed) return;
  try {
    const data = {
      Name: name.value,
      Email: email.value,
      Address: address.value,
      City: city.value,
      Phone: phone.value,
    };
    showProgress(true);
    const response = await apiClient.put(`/user/${userid.value}`, data);
    if (!response.data.success) {
      showProgress(false);
      showSnackbar("Gagal mengedit data", "error");
      return;
    }
    await fetchUser();
    showProgress(false);
    showSnackbar("Data berhasil diupdate", "success");
  } catch (error) {
    showSnackbar("Gagal mengedit data " + error, "error");
  }
};
</script>

<style lang="scss" scoped>
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
