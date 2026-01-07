<template>
  <v-card
    class="overview-card trezo-card mb-25 border border-radius d-block bg-white custom-shadow"
  >
    <div class="v-card-header">
      <h5 class="mb-0">Work In Progress</h5>
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

    <div class="call-overview-tabs">
      <div class="row justify-content-center">
        <div class="col-md-4 col-sm-6">
          <div
            class="card bg-primary bg-opacity-10 border-0 rounded-3 p-4 mb-4 calls-card"
            :class="['tab', { active: activeTab === 'totalChart' }]"
            @click="setActiveTab('totalChart')"
            type="button"
          >
            <div class="d-flex align-items-center mb-3">
              <div class="flex-grow-1 me-3">
                <span class="text-body d-block mb-1">All Status</span>
                <h3 class="fs-24 fw-semibold">{{ total }}</h3>
              </div>

              <div class="flex-shrink-0">
                <v-icon size="40">mdi-text-box-multiple-outline</v-icon>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-6">
          <div
            class="card bg-primary-divs border-0 rounded-3 p-4 mb-4 calls-card"
            style="background-color: #faf5ff"
            :class="['tab', { active: activeTab === 'workingChart' }]"
            @click="setActiveTab('workingChart')"
            type="button"
          >
            <div class="d-flex align-items-center mb-3">
              <div class="flex-grow-1 me-3">
                <span class="text-body d-block mb-1">Working</span>
                <h3 class="fs-24 fw-semibold">{{ draft }}</h3>
              </div>

              <div class="flex-shrink-0">
                <v-icon size="40">mdi-file-document-edit-outline</v-icon>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-12">
          <div
            class="card bg-dangers border-0 rounded-3 p-4 mb-4 calls-card"
            style="background-color: #fff5ed"
            :class="['tab', { active: activeTab === 'stopWorkingChart' }]"
            @click="setActiveTab('stopWorkingChart')"
            type="button"
          >
            <div class="d-flex align-items-center mb-3">
              <div class="flex-grow-1 me-3">
                <span class="text-body d-block mb-1">Stop Working</span>
                <h3 class="fs-24 fw-semibold">{{ final }}</h3>
              </div>

              <div class="flex-shrink-0">
                <v-icon size="40">mdi-text-box-outline</v-icon>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane" v-show="activeTab === 'totalChart'">
        <div style="margin: -24px -10px -26px -18px">
          <AreaChart :labels="labels" :series="series" />
        </div>
      </div>
      <div class="tab-pane" v-show="activeTab === 'workingChart'">
        <div style="margin: -24px -10px -26px -18px">
          <AreaChart :labels="labels" :series="[series[0]]"" />
        </div>
      </div>
      <div class="tab-pane" v-show="activeTab === 'stopWorkingChart'">
        <div style="margin: -24px -10px -26px -18px">
          <AreaChart :labels="labels" :series="[series[1]]" />
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
import AreaChart from "@components/Common/AreaChart.vue";
import ShowSnackbar from "@components/Common/ShowSnackbar.vue";
import ProgressLoader from "@components/Common/ProgressLoader.vue";
import { useSnackbar } from "@composables/useSnackbar";
import { useProgress } from "@composables/useProgress";

const { showSnackbar } = useSnackbar();
const { showProgress } = useProgress();
const activeTab = ref("totalChart");

const total = ref(0);
const draft = ref(0);
const final = ref(0);
const setActiveTab = (tab) => (activeTab.value = tab);

const activeRange = ref("week");
const activeLabel = ref("This Week");
const range = ref({
  group_by: "week",
});
const labels = ref([]);
const series = ref([]);

defineOptions({
  name: "WorkInProgressChart",
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

  fetchWorkInProgressChart();
};

const fetchWorkInProgressChart = async () => {
  try {
    showProgress(true);
    const response = await apiClient.get("/work-in-progress/chart", {
      params: {
        group_by: range.value.group_by,
      },
    });
    const result = response.data;
    if (result.success) {
      labels.value = result.data.labels;
      series.value = result.data.series;
      total.value = result.data.summary.total;
      draft.value = result.data.summary.go;
      final.value = result.data.summary.stop;
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
