<template>
    <v-switch @click="toggleTheme"></v-switch>
</template>

<script>
import { defineComponent, ref, onMounted } from "vue";

export default defineComponent({
    name: "DarkModeSwitch",
    setup() {
        const icon = ref("light_mode");

        const toggleTheme = () => {
            const body = document.body;
            const darkThemeClass = "dark-theme";

            if (body.classList.contains(darkThemeClass)) {
                body.classList.remove(darkThemeClass);
                icon.value = "light_mode";
                localStorage.setItem("theme", "light");
            } else {
                body.classList.add(darkThemeClass);
                icon.value = "dark_mode";
                localStorage.setItem("theme", "dark");
            }
        };

        onMounted(() => {
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme === "dark") {
                document.body.classList.add("dark-theme");
                icon.value = "dark_mode";
            }
        });

        return {
            icon,
            toggleTheme,
        };
    },
});
</script>
