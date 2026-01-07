<template>
  <apexchart
    v-if="isClient && safeSeries.length"
    type="bar"
    :height="height"
    :options="chartOptions"
    :series="safeSeries"
  />
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

defineOptions({
  name: "BarChart",
});

const props = defineProps({
  labels: {
    type: Array,
    default: () => [],
  },

  series: {
    type: Array,
    default: () => [],
  },

  colors: {
    type: Array,
    default: () => ["#605DFF", "#FF9F43", "#28C76F", "#EA5455"],
  },

  height: {
    type: Number,
    default: 95,
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
    type: "bar",
    height: props.height,
    stacked: true,
    toolbar: {
      show: false,
    },
    zoom: {
      enabled: true,
    },
  },
  plotOptions: {
    bar: {
      columnWidth: "55%",
    },
  },
  colors: computedColors.value,
  grid: {
    borderColor: "#ffffff",
  },
  stroke: {
    width: 2,
    show: true,
    colors: ["transparent"],
  },
  dataLabels: {
    enabled: false,
  },
  xaxis: {
    categories: props.labels,
    axisTicks: {
      show: false,
      color: "#B1BBC8",
    },
    axisBorder: {
      show: false,
      color: "#B1BBC8",
    },
    labels: {
      show: false,
      style: {
        colors: "#8695AA",
        fontSize: "9px",
      },
    },
  },
  yaxis: {
    show: false,
    labels: {
      style: {
        colors: "#64748B",
        fontSize: "9px",
      },
    },
  },
  legend: {
    show: true,
    fontSize: "9px",
    position: "bottom",
    horizontalAlign: "center",
    itemMargin: {
      horizontal: 8,
      vertical: 0,
    },
    labels: {
      colors: "#64748B",
    },
    markers: {
      width: 9,
      height: 9,
      offsetX: -2,
      offsetY: -0.5,
    },
  },
  fill: {
    opacity: 1,
  },
}));

onMounted(() => {
  isClient.value = true;
});
</script>
