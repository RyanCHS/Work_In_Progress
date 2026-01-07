import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";
import findUnusedFiles from "vite-plugin-unused-files";
import unusedCode from "vite-plugin-unused-code";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    vue(),
    unusedCode({
      patterns: ["src/**/*.*"],
    }),
    findUnusedFiles({
      include: ["src/**/*"],
      exclude: ["src/**/*.d.ts"],
      dryRun: true,
    }),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        silenceDeprecations: ["legacy-js-api"],
      },
    },
  },
  resolve: {
    alias: {
      "@composables": path.resolve(__dirname, "resources/composables"),
      "@utils": path.resolve(__dirname, "resources/utils"),
      "@components": path.resolve(__dirname, "resources/components"),
      "@pages": path.resolve(__dirname, "resources/pages"),
      "@plugins": path.resolve(__dirname, "resources/plugins"),
    },
  },
});
