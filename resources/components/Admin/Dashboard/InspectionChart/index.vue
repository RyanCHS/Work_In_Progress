<template>
  <v-card
    class="overview-card trezo-card mb-25 border border-radius d-block bg-white custom-shadow"
  >
    <div class="v-card-header">
      <h5 class="mb-0">Inspection</h5>
      <div class="d-flex align-items-center">
        <v-menu>
          <template v-slot:activator="{ props }">
            <button
              type="button"
              v-bind="props"
              class="card-header-menu d-inline-block border-radius"
            >
              {{ activeLabel }}
            </button>
          </template>

          <v-list class="menu-content">
            <button
              @click="changeRange('day')"
              class="bg-transparent border-none"
            >
              Today
            </button>
            <button
              @click="changeRange('week')"
              class="bg-transparent border-none"
            >
              This Week
            </button>
            <button
              @click="changeRange('month')"
              class="bg-transparent border-none"
            >
              This Month
            </button>
            <button
              @click="changeRange('year')"
              class="bg-transparent border-none"
            >
              This Year
            </button>
          </v-list>
        </v-menu>
      </div>
    </div>

    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane">
        <div style="margin: -24px -10px -26px -18px">
          <DonutChart :labels="labels" :series="series" />
        </div>
      </div>
    </div>
  </v-card>
  <ShowSnackbar />
  <ProgressLoader />
</template>

<script setup>
import { onMounted, ref } from "vue";
import apiClient from "@utils/axios";
import DonutChart from "@components/Common/DonutChart.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";
import { format } from "date-fns";

const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();

const activeRange = ref("week");
const activeLabel = ref("This Week");
const range = ref({
  group_by: "week",
});
const labels = ref([]);
const series = ref([]);

defineOptions({
  name: "InspectionChart",
});

const changeRange = (type) => {
  activeRange.value = type;

  switch (type) {
    case "day":
      activeLabel.value = "Today";
      range.value = {
        group_by: "day",
      };
      break;

    case "week":
      activeLabel.value = "This Week";
      range.value = {
        group_by: "week",
      };
      break;

    case "month":
      activeLabel.value = "This Month";
      range.value = {
        group_by: "month",
      };
      break;

    case "year":
      activeLabel.value = "This Year";
      range.value = {
        group_by: "year",
      };
      break;
  }

  fetchInspectionChart();
};

const fetchInspectionChart = async () => {
  try {
    showProgress(true);
    const response = await apiClient.get("/inspection/chart", {
      params: {
        group_by: range.value.group_by,
      },
    });
    const result = response.data;
    if (result.success) {
      labels.value = ["Draft", "Final"];
      series.value = [result.data.summary.draft, result.data.summary.final];
    }
  } catch (error) {
    showSnackbar("Gagal memuat data inspeksi.", "error");
  } finally {
    showProgress(false);
  }
};

onMounted(() => {
  changeRange(activeRange.value);
});
</script>

<style lang="scss" scoped>
.overview-card {
  &.border {
    border: 1px solid #edeff5 !important;
  }
  .call-overview-tabs {
    .bg-primary {
      background-color: rgb(96 93 255 / 10%) !important;
    }
  }
  .p-4 {
    padding: 1.5rem !important;
  }
  .mb-4 {
    margin-bottom: 1.5rem !important;
  }
}
</style>
