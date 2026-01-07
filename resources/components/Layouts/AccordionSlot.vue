<template>
  <ul class="accordion">
    <slot></slot>
  </ul>
</template>

<script setup>
import { reactive, provide } from "vue";

defineOptions({
  name: "AccordionSlot",
});

const Accordion = reactive({
  count: 0,
  active: null,
});

provide("Accordion", Accordion);
</script>
