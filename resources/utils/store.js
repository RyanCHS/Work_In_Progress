import { reactive } from "vue";

// interface StateStore {
//     open: boolean;
//     active: boolean;
//     settings: boolean;
//     onChange: () => void;
//     activeBtn: () => void;
//     settingsBtn: () => void;
// }

const stateStore = reactive({
    open: false,
    settings: false,
    active: false,
    onChange() {
        stateStore.open = !stateStore.open;
    },
    settingsBtn() {
        stateStore.settings = !stateStore.settings;
    },
    activeBtn() {
        stateStore.active = !stateStore.active;
    },
});

export default stateStore;
