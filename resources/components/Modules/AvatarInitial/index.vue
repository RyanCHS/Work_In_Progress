<template>
  <v-avatar :color="color" :size="size">
    <template v-if="image">
      <v-img :src="image" />
    </template>
    <template v-else>
      <span class="white--text text-subtitle-1">{{ initials }}</span>
    </template>
  </v-avatar>
</template>

<script setup>
import { computed } from "vue";

defineOptions({
  name: "AvatarInitial", // ← Nama komponen walau file bernama index.vue
});

const props = defineProps({
  name: String,
  image: { type: String, default: "" }, // Opsional untuk gambar profil
  size: { type: [String, Number], default: 40 },
  color: { type: String, default: "primary" },
});

const initials = computed(() => {
  if (!props.name) return "";
  const words = props.name.trim().split(/\s+/);
  return words
    .slice(0, 2)
    .map((w) => w[0])
    .join("")
    .toUpperCase();
});
</script>
