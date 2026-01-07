<template>
  <div class="row">
    <div class="col-xxl-8">
      <WorkInProgressChart />
    </div>
    <div class="col-xxl-4">
      <div class="row">
        <div class="col-xxl-12 col-md-6">
          <InspectionChart />
        </div>
        <div class="col-xxl-12 col-md-6">
          <WorkingPermitChart />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import WorkInProgressChart from "@components/Admin/Dashboard/WorkInProgressChart/index.vue";
import InspectionChart from "@components/Admin/Dashboard/InspectionChart/index.vue";
import WorkingPermitChart from "@components/Admin/Dashboard/WorkingPermitChart/index.vue";
defineOptions({
  name: "DashboardPage",
});
</script>
