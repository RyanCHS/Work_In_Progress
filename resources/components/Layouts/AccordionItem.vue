<template>
  <li class="accordion__item">
    <div
      class="accordion__trigger"
      :class="{ accordion__trigger_active: visible }"
      @click="open"
    >
      <slot name="accordion-trigger"></slot>
    </div>

    <transition
      name="accordion"
      @enter="start"
      @after-enter="end"
      @before-leave="start"
      @after-leave="end"
    >
      <div class="accordion__content" v-show="visible">
        <ul>
          <slot name="accordion-content"></slot>
        </ul>
      </div>
    </transition>
  </li>
</template>

<script setup>
import { inject, ref, computed, onBeforeMount } from "vue";

defineOptions({
  name: "AccordionItem",
});

const Accordion = inject("Accordion");
const index = ref(null);

onBeforeMount(() => {
  index.value = Accordion.count++;
});

const visible = computed(() => index.value === Accordion.active);

function open() {
  Accordion.active = visible.value ? null : index.value;
}

function start(el) {
  el.style.height = el.scrollHeight + "px";
}

function end(el) {
  el.style.height = "";
}
</script>
