<template>
  <div class="trezo-table-content">
    <div class="trezo-table">
      <v-table>
        <thead>
          <tr>
            <th>Keterangan</th>
            <th>{{ status ? "Nilai" : "Nilai Default" }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in content" :key="index">
            <td>{{ item.label }}</td>
            <td>{{ status ? item.answer : item.default }}</td>
          </tr>
        </tbody>
      </v-table>
    </div>
  </div>
</template>

<script setup>
import { watch, ref } from "vue";
import { formatDate } from "@utils/date";
defineOptions({
  name: "InspectionResultTable",
});

const content = ref([]);

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
  status: {
    type: Boolean,
    required: true,
  },
});

const boolToText = (bool, options) => {
  if (bool === true) {
    return options[0];
  } else {
    return options[1];
  }
};

watch(
  () => props.data,
  (val) => {
    content.value = val.map((item) => {
      if (item.input_type === "date") {
        return {
          ...item,
          answer: formatDate(item.answer) ?? "",
          default: formatDate(item.default) ?? "",
        };
      } else {
        if (item.input_type === "checkbox") {
          return {
            ...item,
            answer: boolToText(item.answer, item.options) ?? "",
            default: boolToText(item.default, item.options) ?? "",
          };
        }
      }
      return item;
    });
  },
  { immediate: true }
);
</script>
