<template>
    <v-switch @click="toggleOnlyHeaderDarkMode"></v-switch>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    name: "OnlyHeaderDark",
    data() {
        return {
            isDarkMode: false,
        };
    },
    methods: {
        toggleOnlyHeaderDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            const element = document.getElementById("HeaderTheme");

            if (element) {
                // Toggle the class on the element
                element.classList.toggle("dark-theme", this.isDarkMode);
            }

            if (typeof window !== "undefined") {
                localStorage.setItem("dark-theme", this.isDarkMode.toString());
            }
        },
    },
    mounted() {
        if (typeof window !== "undefined") {
            const storedDarkMode = localStorage.getItem("dark-theme");
            if (storedDarkMode) {
                this.isDarkMode = storedDarkMode === "true";
                const element = document.getElementById("HeaderTheme");
                if (element) {
                    // Toggle the class on the element
                    element.classList.toggle("dark-theme", this.isDarkMode);
                }
            }
        }
    },
});
</script>
