<template>
  <v-menu @update:modelValue="onMenuToggle">
    <template v-slot:activator="{ props }">
      <button
        :class="hasNotification ? 'notification-btn' : 'notification'"
        v-bind="props"
      >
        <i class="material-symbols-outlined">notifications</i>
        <div class="dot rounded-circle"></div>
      </button>
    </template>
    <v-list class="notification-list">
      <div class="title d-flex align-items-center">
        <span class="fw-semibold text-black">
          Notifications
          <span class="text-body fw-normal"> ({{ notifications.length }})</span>
        </span>
      </div>
      <ul class="menu-body p-0 mt-0 list-unstyled">
        <li v-for="item in notifications" class="position-relative">
          <div
            class="icon rounded-circle d-flex align-items-center justify-content-center position-absolute text-center transition"
          >
            <i class="material-symbols-outlined"> {{ icon(item.Category) }} </i>
          </div>
          <span class="sub-title d-block text-black">
            {{ item.Title }}
          </span>
          <span class="time d-block"> {{ timeAgo(item.CreatedAt) }} </span>
        </li>
      </ul>
      <div class="menu-footer text-center">
        <RouterLink
          to="/admin/notifications"
          class="d-inline-block fw-medium position-relative text-primary"
        >
          See All Notifications <i class="flaticon-chevron"></i>
        </RouterLink>
      </div>
    </v-list>
  </v-menu>
</template>

<script setup>
import { onMounted, ref, onBeforeUnmount } from "vue";
import { useAuth } from "@composables/useAuth";
import apiClient from "@utils/axios";
import { timeAgo } from "@utils/date";
const { user } = useAuth();

defineOptions({
  name: "NotificationsLists",
});

const hasNotification = ref(false);
const notifications = ref([]);
const echoChannel = ref(null);

onMounted(() => {
  echoChannel.value = window.Echo.channel("announcements").listen(
    "SendAnnouncement",
    async (e) => {
      if (e.message === "All" || e.message === user.value.UserId) {
        await loadData();
        hasNotification.value = true;
      }
    }
  );

  if (window.Echo.connector?.socket) {
    window.Echo.connector.socket.on("connect", () => {
      console.log("✅ WebSocket connected");
    });
  }
});

onMounted(async () => {
  await loadData();
});

onBeforeUnmount(() => {
  if (echoChannel.value) {
    window.Echo.leave("announcements");
  }
});

const loadData = async () => {
  const response = await apiClient.get("/notifications", {
    params: {
      limit: 3,
      mode: "limit",
    },
  });
  if (!response.data.success) {
    return;
  }
  notifications.value = response.data.data;
};

const onMenuToggle = (val) => {
  if (val) {
    hasNotification.value = false;
  }
};

const icon = (category) => {
  switch (category) {
    case "Working Permit":
      return "carry_on_bag_checked";
    case "Inspection":
      return "policy_alert";

    default:
      return "sms";
  }
};
</script>
