<template>
    <v-switch
        @click="toggleOnlySidebarLightMode"
        v-model="isDarkMode"
    ></v-switch>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";

export default defineComponent({
    name: "OnlySidebarDark",
    setup() {
        const isDarkMode = ref(false);

        const toggleOnlySidebarLightMode = () => {
            isDarkMode.value = !isDarkMode.value;
            const element = document.getElementById("SidebarTheme");

            if (element) {
                // Toggle the class on the element
                element.classList.toggle("dark-theme", isDarkMode.value);
            }

            localStorage.setItem("dark-theme", isDarkMode.value.toString());
        };

        onMounted(() => {
            const storedDarkMode = localStorage.getItem("dark-theme");
            if (storedDarkMode) {
                isDarkMode.value = storedDarkMode === "true";
                const element = document.getElementById("SidebarTheme");
                if (element) {
                    // Toggle the class on the element
                    element.classList.toggle("dark-theme", isDarkMode.value);
                }
            }
        });

        return {
            isDarkMode,
            toggleOnlySidebarLightMode,
        };
    },
});
</script>
