<template>
  <div class="form-wrapper">
    <h5>{{ title }}</h5>

    <div v-if="data.length > 0">
      <!-- CHECKBOX MODE -->
      <div v-if="mode === 'checkbox'" class="table-wrapper">
        <v-table>
          <thead>
            <tr>
              <th>Pilih</th>
              <th>Nama</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in data" :key="item.UserId">
              <td class="text-left">
                <v-checkbox
                  v-model="selectedCheckbox"
                  :value="item.UserId"
                  hide-details
                  density="compact"
                />
              </td>
              <td class="text-left">{{ item.user_detail.Name }}</td>
            </tr>
          </tbody>
        </v-table>
      </div>

      <!-- RADIO MODE -->
      <div v-else class="radio-group-wrapper">
        <v-radio-group v-model="selectedRadio" class="my-2" hide-details>
          <div v-for="(item, index) in data" :key="item.UserId">
            <v-radio
              :value="item.UserId"
              :label="item.user_detail.Name"
              hide-details
            />
          </div>
        </v-radio-group>
      </div>
    </div>
    <v-skeleton-loader v-else type="list-item-avatar"></v-skeleton-loader>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

defineOptions({ name: "PengawasK3" });

const props = defineProps({
  title: { type: String, required: true },
  data: { type: Array, required: true },
  mode: { type: String, default: "checkbox" },
});

const emit = defineEmits(["update:selected"]);

const selectedCheckbox = ref([]);
const selectedRadio = ref(null);

watch(selectedCheckbox, (val) => {
  if (props.mode === "checkbox") emit("update:selected", val);
});

watch(selectedRadio, (val) => {
  if (props.mode === "radio") emit("update:selected", val);
});
</script>

<style scoped>
.form-wrapper {
  max-width: 500px;
  width: 100%;
  margin: 0 auto;
}

.table-wrapper {
  max-width: 300px;
  width: 100%;
  margin: 0 auto;
  overflow-x: auto;
}

.radio-group-wrapper {
  max-width: 300px;
  width: 100%;
  margin: 0 auto;
}
</style>
