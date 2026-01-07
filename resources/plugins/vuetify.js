// Styles
import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";

// Vuetify
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

// Labs (tambahkan ini)
import { VDateInput } from "vuetify/labs/VDateInput";
import { VFileUpload } from "vuetify/labs/VFileUpload";

const vuetify = createVuetify({
  components: {
    ...components,
    VDateInput,
    VFileUpload, // daftar manual komponen labs
  },
  directives,
});

export default vuetify;
