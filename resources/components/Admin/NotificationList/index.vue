<template>
  <v-card
    class="projects-list-card trezo-card border-radius d-block bg-white border-0 shadow-none"
  >
    <div class="v-card-header">
      <h5 class="mb-0">Notification List</h5>
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
          <template #item.Notification="{ item }">
            <div class="info">
              <span class="fw-medium d-block">
                {{ item.Title }}
              </span>
              <span class="email d-block text-body">
                {{ item.Message }}
              </span>
            </div>
          </template>
        </v-data-table-server>
      </div>
    </div>
  </v-card>

  <template>
    <ShowSnackbar />
  </template>
</template>

<script setup>
import { ref } from "vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import apiClient from "@utils/axios";
import { timeAgo } from "@utils/date";
import { useSnackbar } from "@composables/useSnackbar";
import { useAuth } from "@composables/useAuth";

defineOptions({ name: "NotificationList" });

const { showSnackbar } = useSnackbar();
const { user } = useAuth();

const headers = ref([
  { title: "Category", key: "Category" },
  { title: "Waktu", key: "CreatedAt" },
  { title: "Notifikasi", key: "Notification" },
]);
const serverItems = ref([]);
const itemsPerPage = ref(10);
const totalItems = ref(0);
const loading = ref(true);
const serverOptions = ref({});

const onOptionsUpdate = (options) => {
  serverOptions.value = options;
  loadItems(options);
};

const loadItems = async ({ page, itemsPerPage }) => {
  loading.value = true;
  try {
    const response = await apiClient.get("/notifications", {
      params: {
        per_page: itemsPerPage,
        mode: "pagination",
      },
    });

    const paginatedData = response.data;

    serverItems.value = paginatedData.data.data.map((item) => {
      const baseItem = {
        ...item,
        CreatedAt: timeAgo(item.CreatedAt),
      };

      return baseItem;
    });

    totalItems.value = paginatedData.data.total;
  } catch (error) {
    showSnackbar("Gagal memuat data " + error, "error");
  } finally {
    loading.value = false;
  }
};
</script>

<style lang="scss" scoped>
.projects-list-card {
  .trezo-table-content {
    .trezo-table {
      margin-left: -25px;
      margin-right: -25px;
      table {
        thead {
          tr {
            th {
              background-color: #ecf0ff !important;

              &:first-child {
                border-top-left-radius: 0 !important;
                padding-left: 25px;
              }
              &:last-child {
                border-top-right-radius: 0 !important;
                padding-right: 25px;
              }
            }
          }
        }
        tbody {
          tr {
            td {
              &:first-child {
                padding-left: 25px;
                border-left-width: 0 !important;
              }
              &:last-child {
                padding-right: 25px;
                border-right-width: 0 !important;
              }
            }
            &:first-child {
              td {
                border-top-width: 0;
              }
            }
          }
        }
      }
    }
  }
}
</style>
