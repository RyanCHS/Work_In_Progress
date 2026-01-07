<template>
  <template v-if="currentView === 'list'">
    <v-card
      class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
    >
      <div class="v-card-header">
        <h5 class="mb-0">User Management List</h5>
        <a href="#" class="add-btn" @click.prevent="currentView = 'create'">
          <i class="material-symbols-outlined"> add </i>
          Add New User
        </a>
      </div>

      <div class="trezo-table-content">
        <div class="trezo-table">
          <v-data-table-server
            v-model:items-per-page="itemsPerPage"
            :headers="headers"
            :items="serverItems"
            :items-length="totalItems"
            :loading="loading"
            item-value="name"
            @update:options="onOptionsUpdate"
          >
            <template #item.Name="{ item }">
              <div class="d-flex align-items-center">
                {{ item.user_detail.Name }}
              </div>
            </template>
            <template #item.IsActivated="{ item }">
              <div class="d-flex align-items-center">
                <v-switch
                  v-model="item.IsActivated"
                  :label="`${item.IsActivated ? 'Aktif' : 'Tidak Aktif'}`"
                  color="primary"
                  hide-details
                  @change="onToggleActivated(item)"
                ></v-switch>
              </div>
            </template>
            <template #item.actions="{ item }">
              <div
                class="action-buttons d-flex align-items-center justify-center"
              >
                <v-tooltip text="Delete Template Inspection">
                  <template v-slot:activator="{ props }">
                    <button
                      v-bind="props"
                      type="button"
                      @click="deleteUser(item.UserId)"
                    >
                      <i class="material-symbols-outlined"> delete </i>
                    </button>
                  </template>
                </v-tooltip>
              </div>
            </template>
          </v-data-table-server>
        </div>
      </div>
    </v-card>
  </template>
  <template v-else-if="currentView === 'create'">
    <CreateUser @back-to-list="currentView = 'list'" />
  </template>

  <ShowSnackbar />
  <ConfirmDialog />
  <ProgressLoader />
</template>

<script setup>
import { ref } from "vue";
import apiClient from "@utils/axios";
import ConfirmDialog from "@components/Common/ConfirmDialog.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import CreateUser from "@components/Admin/User/CreateUser/index.vue";
import { useSnackbar } from "@composables/useSnackbar";
import { useConfirmDialog } from "@composables/useConfirmDialog";
import { useProgress } from "@composables/useProgress";
import { formatDate } from "@utils/date";
const { showSnackbar } = useSnackbar();
const { openDialog } = useConfirmDialog();
const { showProgress } = useProgress();

const headers = ref([
  { title: "User Name", key: "Username" },
  { title: "User Group", key: "UserGroup" },
  { title: "Name", key: "Name" },
  { title: "Activated", key: "IsActivated" },
  { title: "Aksi", key: "actions", sortable: false, align: "center" },
]);

const serverItems = ref([]);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const loading = ref(true);
const serverOptions = ref({});
const currentView = ref("list");

defineOptions({
  name: "UserManagementList",
});

const onOptionsUpdate = (options) => {
  serverOptions.value = options;
  loadItems(options);
};

const loadItems = async (options) => {
  try {
    loading.value = true;
    const sortKey = options.sortBy?.[0]?.key;
    const sortOrder = options.sortBy?.[0]?.order;
    const response = await apiClient.get("/user", {
      params: {
        page: options.page,
        per_page: options.itemsPerPage,
        sort_by: sortKey,
        sort_order: sortOrder,
      },
    });

    if (!response.data.success) {
      loading.value = false;
      return;
    }
    serverItems.value = response.data.data.data;
    totalItems.value = serverItems.value.length;
  } catch (error) {
    showSnackbar("Gagal mengambil data " + error, "error");
  } finally {
    loading.value = false;
  }
};

const onToggleActivated = async (item) => {
  try {
    showProgress(true);
    const response = await apiClient.put(`/user/${item.UserId}/enabled`, {
      IsActivated: item.IsActivated,
    });

    if (!response.data.success) {
      showSnackbar("Gagal memperbarui status", "error");
      return;
    }

    showSnackbar("Status berhasil diperbarui", "success");
  } catch (err) {
    showSnackbar("Terjadi kesalahan: " + err.message, "error");
  } finally {
    showProgress(false);
  }
};

const deleteUser = async (userId) => {
  const confirmed = await openDialog({
    title: "Perhatian",
    message: "Anda yakin ingin menghapus data ini?",
  });
  if (!confirmed) return;
  try {
    showProgress(true);
    const response = await apiClient.delete(`/user/${userId}`);
    if (!response.data.success) {
      showSnackbar("Gagal menghapus data", "error");
      return;
    }
    showProgress(false);
    showSnackbar("Data berhasil dihapus", "success");
    await loadItems(serverOptions.value);
  } catch (error) {
    showSnackbar("Gagal menghapus data " + error, "error");
  }
};
</script>
