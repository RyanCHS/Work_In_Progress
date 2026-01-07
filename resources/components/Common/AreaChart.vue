<template>
  <apexchart
    v-if="isClient && safeSeries.length"
    type="area"
    :height="height"
    :options="chartOptions"
    :series="safeSeries"
  />
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

defineOptions({
  name: "AreaChart",
});

const props = defineProps({
  labels: {
    type: Array,
    default: () => [],
  },

  series: {
    type: Array,
    default: () => [], // ⬅️ PENTING
  },

  colors: {
    type: Array,
    default: () => ["#605DFF", "#FF9F43", "#28C76F", "#EA5455"],
  },

  height: {
    type: Number,
    default: 300,
  },
});

const isClient = ref(false);

/**
 * Pastikan series selalu ARRAY
 */
const safeSeries = computed(() =>
  Array.isArray(props.series) ? props.series : []
);

/**
 * Warna dinamis sesuai jumlah series
 */
const computedColors = computed(() =>
  safeSeries.value.map((_, i) => props.colors[i % props.colors.length])
);

const chartOptions = computed(() => ({
  chart: {
    type: "area",
    height: props.height,
    zoom: { enabled: false },
    toolbar: { show: false },
  },

  colors: computedColors.value,

  dataLabels: { enabled: false },

  stroke: {
    curve: "smooth",
    width: safeSeries.value.map(() => 2),
  },

  fill: {
    type: "gradient",
    gradient: {
      opacityFrom: 0.25,
      opacityTo: 0.6,
    },
  },

  grid: {
    borderColor: "#ffffff",
  },

  xaxis: {
    categories: props.labels,
    axisTicks: { show: false },
    axisBorder: { show: false },
    labels: {
      style: {
        colors: "#8695AA",
        fontSize: "12px",
      },
    },
  },

  yaxis: {
    tickAmount: 5,
    labels: {
      style: {
        colors: "#8695AA",
        fontSize: "12px",
      },
    },
  },

  legend: {
    show: true,
    position: "top",
    horizontalAlign: "left",
    labels: { colors: "#64748B" },
  },
}));

onMounted(() => {
  isClient.value = true;
});
</script>
