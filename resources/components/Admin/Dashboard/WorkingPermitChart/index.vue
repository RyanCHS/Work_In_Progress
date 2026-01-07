<template>
  <v-card
    class="leads-by-source-card trezo-card mb-25 border border-radius d-block bg-white custom-shadow"
  >
    <div class="v-card-header">
      <h5 class="mb-0">Working Permit</h5>
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
    <div class="source-card-content">
      <div class="chart">
        <BarChart :labels="labels" :series="series" />
      </div>
      <ul class="pl-0 mb-0 mt-0 list-unstyled">
        <li
          class="completed position-relative d-flex aling-items-center justify-space-between"
        >
          <span class="d-block"> Approved </span>
          <span class="d-block"> {{ approved }} </span>
        </li>
        <li
          class="pending position-relative d-flex aling-items-center justify-space-between"
        >
          <span class="d-block"> Requested </span>
          <span class="d-block"> {{ requested }} </span>
        </li>
        <li
          class="pending position-relative d-flex aling-items-center justify-space-between"
        >
          <span class="d-block"> Total Data </span>
          <span class="d-block"> {{ total }} </span>
        </li>
      </ul>
    </div>
  </v-card>
  <ShowSnackbar />
  <ProgressLoader />
</template>

<script setup>
import { onMounted, ref } from "vue";
import apiClient from "@utils/axios";
import BarChart from "@components/Common/BarChart.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";

const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
const activeTab = ref("totalChart");

const approved = ref(0);
const requested = ref(0);
const total = ref(0);

const activeRange = ref("week");
const activeLabel = ref("This Week");
const range = ref({
  group_by: "week",
});
const labels = ref([]);
const series = ref([]);

defineOptions({
  name: "WorkingPermitChart",
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

  fetchWorkingPermitChart();
};

const fetchWorkingPermitChart = async () => {
  try {
    showProgress(true);
    const response = await apiClient.get("/working-permit/chart", {
      params: {
        group_by: range.value.group_by,
      },
    });
    const result = response.data;
    if (result.success) {
      labels.value = result.data.labels;
      series.value = result.data.series;
      total.value = result.data.summary.total;
      approved.value = result.data.summary.approved;
      requested.value = result.data.summary.requested;
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

<style lang="scss" scoped>
.leads-by-source-card {
  .source-card-content {
    .chart {
      margin-top: -10px;
    }
    .list {
      margin: {
        top: -4px;
        left: -5px;
        right: -5px;
      }
      li {
        flex: 0 0 auto;
        margin-top: 24px;
        width: 33.33333333%;
        padding: {
          left: 5px;
          right: 5px;
        }
        .title {
          font-size: 13px;
          margin-bottom: 8px;

          .dot {
            width: 11px;
            height: 11px;
            margin-right: 8px;
            border-radius: 3px;
          }
        }
        h6 {
          font-size: 18px;
        }
      }
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .leads-by-source-card {
    .source-card-content {
      .chart {
        margin-top: -5px;
      }
      .list {
        li {
          margin-top: 15px;

          .title {
            margin-bottom: 7px;

            .dot {
              width: 10px;
              height: 10px;
            }
          }
          h6 {
            font-size: 16px;
          }
        }
      }
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .leads-by-source-card {
    .source-card-content {
      .list {
        li {
          margin-top: 20px;
          width: 16.6666666667%;

          h6 {
            font-size: 17px;
          }
        }
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
}

/* Min width 1200px to Max width 1399px */
@media only screen and (min-width: 1200px) and (max-width: 1399px) {
}

/* Min width 1600px */
@media only screen and (min-width: 1600px) {
}
</style>
