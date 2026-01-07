<template>
  <apexchart
    v-if="isClient && safeSeries.length"
    type="donut"
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
    default: 150,
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
    height: props.height,
    type: "donut",
  },

  colors: computedColors.value,
  labels: props.labels,
  dataLabels: { enabled: false },

  stroke: {
    width: 1,
    show: true,
    colors: ["#ffffff"],
  },

  plotOptions: {
    pie: {
      expandOnClick: false,
      donut: {
        labels: {
          show: true,
          name: {
            color: "#64748B",
          },
          value: {
            show: true,
            color: "#3A4252",
            fontSize: "28px",
            fontWeight: "600",
          },
          total: {
            show: true,
            color: "#64748B",
          },
        },
      },
    },
  },
  dataLabels: {
    enabled: false,
  },
  tooltip: {
    enabled: false,
  },
}));

onMounted(() => {
  isClient.value = true;
});
</script>
