<template>
  <v-data-table :items="modelValue" class="elevation-1" hide-default-footer>
    <!-- Header Kustom -->
    <template #headers>
      <!-- Header Baris Pertama -->
      <tr>
        <template v-for="col in category.table.columns" :key="col.key">
          <th
            :colspan="col.type === 'group' ? col.children.length : 1"
            :rowspan="col.type !== 'group' ? 2 : 1"
            :class="[
              col.align_horizontal ? `text-${col.align_horizontal}` : '',
              col.align_vertical ? `align-${col.align_vertical}` : '',
            ]"
          >
            {{ col.label }}
          </th>
        </template>
      </tr>
      <!-- Header Baris Kedua (untuk kolom group) -->
      <tr v-if="hasGroupedColumns">
        <template v-for="col in category.table.columns" :key="col.key">
          <template v-if="col.type === 'group'">
            <th
              v-for="child in col.children"
              :key="child.key"
              :class="[
                child.align_horizontal ? `text-${child.align_horizontal}` : '',
                child.align_vertical ? `align-${child.align_vertical}` : '',
              ]"
            >
              {{ child.label }}
            </th>
          </template>
        </template>
      </tr>
    </template>

    <!-- Data Baris -->
    <template #item="{ item, index }">
      <tr :class="item.spacing">
        <template v-for="col in category.table.columns" :key="col.key">
          <!-- Kolom Group -->
          <template v-if="col.type === 'group'">
            <td
              v-for="child in col.children"
              :key="child.key"
              :class="[
                item.spacing,
                child.align_horizontal ? `text-${child.align_horizontal}` : '',
                child.align_vertical ? `align-${child.align_vertical}` : '',
              ]"
            >
              <template v-if="child.is_input">
                <v-select
                  v-if="child.type === 'select'"
                  :items="child.options"
                  v-model="item[col.key][child.key]"
                  density="compact"
                  hide-details
                  variant="outlined"
                />
                <v-text-field
                  v-else-if="child.type === 'text' || child.type === 'number'"
                  v-model="item[col.key][child.key]"
                  density="compact"
                  hide-details
                  variant="outlined"
                />
              </template>
              <template v-else>
                {{ item[col.key][child.key] }}
              </template>
            </td>
          </template>

          <!-- Kolom Biasa -->
          <template v-else>
            <td
              :class="[
                item.spacing,
                col.align_horizontal ? `text-${col.align_horizontal}` : '',
                col.align_vertical ? `align-${col.align_vertical}` : '',
              ]"
            >
              <span v-if="col.type === 'autonumber'">{{ index + 1 }}</span>

              <v-text-field
                v-else-if="
                  col.is_input && (col.type === 'text' || col.type === 'number')
                "
                v-model="item[col.key]"
                density="compact"
                hide-details
                variant="outlined"
              />

              <v-select
                v-else-if="col.is_input && col.type === 'select'"
                :items="col.options"
                v-model="item[col.key]"
                density="compact"
                hide-details
                variant="outlined"
              />

              <template v-else>
                {{ item[col.key] }}
              </template>
            </td>
          </template>
        </template>
      </tr>
    </template>
  </v-data-table>
</template>

<script setup>
import { computed } from "vue";

defineOptions({
  name: "DynamicFormTable",
});

const props = defineProps({
  category: Object,
  modelValue: Array,
});

defineEmits(["update:modelValue"]);

const hasGroupedColumns = computed(() =>
  props.category.table.columns.some((col) => col.type === "group")
);
</script>

<style scoped>
.align-top {
  vertical-align: top !important;
}
.align-middle {
  vertical-align: middle !important;
}
.align-bottom {
  vertical-align: bottom !important;
}
</style>
