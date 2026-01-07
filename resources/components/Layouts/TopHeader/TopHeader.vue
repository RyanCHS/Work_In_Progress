<template>
  <header
    id="HeaderTheme"
    :class="[
      'header-area bg-white',
      { sticky: isSticky },
      { active: stateStoreInstance.open },
    ]"
  >
    <div class="d-md-flex align-content-center flex-wrap justify-space-between">
      <div class="header-left-side d-flex align-items-center">
        <div
          :class="['burger-menu', { active: stateStoreInstance.open }]"
          @click="stateStoreInstance.onChange"
        >
          <span class="material-symbols-outlined">menu</span>
        </div>
      </div>
      <ul
        class="header-right-side d-flex align-items-center m-0 p-0 list-unstyled"
      >
        <li>
          <DarkSwtichBtn />
        </li>
        <li>
          <ToggleFullscreenBtn />
        </li>
        <li v-if="!hasRole('Administrator')">
          <NotificationsLists />
        </li>
        <li>
          <AdminProfile />
        </li>
      </ul>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from "vue";
import stateStore from "../../../utils/store";
import { useAuth } from "@composables/useAuth";
import DarkSwtichBtn from "./DarkSwtichBtn.vue";
import ToggleFullscreenBtn from "./ToggleFullscreenBtn.vue";
import NotificationsLists from "./NotificationsLists.vue";
import AdminProfile from "./AdminProfile.vue";

defineOptions({
  name: "TopHeader",
});
const { hasRole } = useAuth();
const stateStoreInstance = stateStore;
const isSticky = ref(false);

onMounted(() => {
  window.addEventListener("scroll", () => {
    isSticky.value = window.scrollY >= 100;
  });
});
</script>

<style lang="scss">
.header-area {
  border-radius: 0 0 15px 15px;
  padding: 14px 25px;
  position: fixed;
  left: 285px;
  right: 25px;
  z-index: 5;
  top: 0;

  .header-left-side {
    .burger-menu {
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      top: 1px;
      margin-right: 23px;

      .material-symbols-outlined {
        font-size: 20px;
        color: var(--bodyColor);
        font-weight: 600;
      }
    }
    .v-btn {
      &.v-btn--density-default {
        height: auto;
      }
    }
    .search-box {
      width: 260px;

      .input-search {
        background-color: #f6f7f9;
        border: 1px solid #f6f7f9;
        color: var(--blackColor);
        padding: 11px 16px 12px;
        border-radius: 7px;
        display: block;
        height: 44px;
        width: 100%;
        font-size: var(--fontSize);
        font-family: var(--fontFamily);

        &::placeholder {
          color: var(--blackColor);
          transition: var(--transition);
        }
        &:focus-visible {
          outline: 0;
        }
      }
      button {
        top: 50%;
        padding: 0;
        right: 15px;
        border: none;
        line-height: 1;
        cursor: pointer;
        position: absolute;
        font-size: 22px !important;
        transform: translateY(-40%);
        color: var(--primaryColor);
        background-color: transparent;
        transition: var(--transition);

        &:hover {
          color: var(--blackColor);
        }
        i {
          font-size: 20px;
          color: var(--primaryColor);
          font-weight: 600;
        }
      }
    }
  }
  .header-right-side {
    padding-left: 0;
    list-style-type: none;

    li {
      margin-left: 12px;
      margin-right: 12px;

      &:first-child {
        margin-left: 0;
      }

      .language-menu-btn {
        top: 1px;
        height: auto;
        line-height: 1;
        min-width: auto;
        padding: 0 12px 0 0;
        display: inline-block;
        color: var(--bodyColor);

        i {
          font-size: 22px;
        }
        &::after {
          top: 50%;
          right: -3px;
          margin-top: -1px;
          content: "\ea4e";
          position: absolute;
          color: var(--bodyColor);
          transform: translateY(-50%);
          font-family: remixicon !important;
        }
      }
      .dark-switch-btn {
        top: 1px;
        padding: 0;
        height: auto;
        line-height: 1;
        min-width: auto;
        color: #fe7a36;
        position: relative;
        display: inline-block;
        i {
          font-size: 22px;
        }
      }
      .fullscreen-btn {
        top: 1px;
        padding: 0;
        height: auto;
        line-height: 1;
        min-width: auto;
        position: relative;
        display: inline-block;
        color: var(--bodyColor);
        i {
          font-size: 24px;
        }
      }
      .notification-btn {
        top: 1px;
        padding: 0;
        height: auto;
        line-height: 1;
        min-width: auto;
        position: relative;
        display: inline-block;
        color: var(--bodyColor);
        i {
          font-size: 24px;
        }
        &::before {
          content: "";
          background: #fd5812;
          width: 7px;
          height: 7px;
          border-radius: 50%;
          position: absolute;
          top: 2px;
          right: 4px;
          z-index: 1;
        }
        &::after {
          content: "";
          top: 2px;
          z-index: 1;
          width: 7px;
          right: 4px;
          height: 7px;
          border-radius: 50%;
          position: absolute;
          background: var(--whiteColor);
          animation: ripple 1.2s ease-out infinite;
        }
      }
      .profile-btn {
        height: auto;
        min-width: auto;
        position: relative;
        padding: 0 14px 0 0;
        display: inline-block;
        margin-left: -5px;
        margin-right: -5px;
        display: flex;

        .v-responsive {
          position: initial;
        }
        .v-img__img {
          width: 42px !important;
          height: 42px !important;
          margin-right: 8px;
          border: 2px solid #c2cdff;
          border-radius: 50%;
        }
        &:after {
          top: 50%;
          right: -3px;
          margin-top: 1px;
          content: "\ea4e";
          position: absolute;
          color: var(--bodyColor);
          transform: translateY(-50%);
          font-size: 15px;
          font-family: remixicon !important;
        }
      }
      .settings-btn {
        top: 1px;
        padding: 0;
        height: auto;
        line-height: 1;
        min-width: auto;
        position: relative;
        display: inline-block;
        color: var(--bodyColor);
        cursor: pointer;
      }
      &:last-child {
        margin-right: 0;
      }
    }
  }
  &.sticky {
    top: 0;
    right: 25px;
    left: 285px;
    z-index: 22;
    position: fixed !important;
    animation: 500ms ease-in-out 0s normal none 1 running fadeInDown;
    box-shadow: #959da51f 0 8px 24px;
  }
  &.active {
    left: 85px;
  }
}
@keyframes fadeInDown {
  0% {
    opacity: 0;
    transform: translate3d(0, -100%, 0);
  }
  to {
    opacity: 1;
    transform: translateZ(0);
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .header-area {
    position: inherit !important;
    padding: 15px;
    margin-bottom: 25px;

    .header-left-side {
      justify-content: center;

      .burger-menu {
        margin-right: 13px;

        span {
          width: 20px;
          margin: 5px 0;
        }
      }
      .search-box {
        width: 250px;

        .input-search {
          padding: 11px 13px 12px;
          font-size: 13px;
        }
        button {
          right: 13px;
          font-size: 18px !important;
        }
      }
    }
    .header-right-side {
      justify-content: center;
      margin-top: 13px !important;

      li {
        margin: {
          left: 8px;
          right: 8px;
        }
        &:first-child {
          margin-left: 0;
        }
        .dark-swtich-btn {
          i {
            font-size: 20px;
          }
        }
        .profile-btn {
          padding: 0 15px 0 0;
          margin-left: -5px;
          margin-right: -5px;
          .v-img__img {
            width: 35px !important;
            height: 35px !important;
            margin-right: 0;
          }
          span {
            font-size: 0 !important;
          }
        }
        .settings-btn {
          i {
            font-size: 22px;
          }
        }
      }
    }
    &.sticky {
      right: 15px;
      left: 15px;

      &.active {
        left: 15px;
      }
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
  .header-area {
    position: inherit !important;
    margin-bottom: 25px;
    .header-left-side {
      .burger-menu {
        margin-right: 15px;
      }
      .menu-btn {
        width: 40px;
        height: 40px;
        margin-right: 15px;
        font-size: 15px !important;
      }
      .search-box {
        width: 260px;
      }
    }
    .header-right-side {
      li {
        margin: {
          left: 7px;
          right: 7px;
        }
        .btn {
          width: 40px;
          height: 40px;
          font-size: 17px !important;

          &.notification-btn {
            &::before {
              top: 9px;
              right: 13px;
            }
            &::after {
              top: 9px;
              right: 13px;
            }
          }
        }
        .profile-btn {
          font-size: 13px !important;
          padding: 0 18px 0 50px;

          .v-img__img {
            width: 40px;
            height: 40px;
          }
          span {
            font-size: 14px;
            margin-top: 3px;
          }
          &::before {
            display: block;
            bottom: 1px;
          }
        }
      }
    }
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .header-area {
    position: inherit !important;
    padding: 20px;
    margin-bottom: 25px;

    .header-right-side {
      li {
        margin-right: 7px;
        margin-left: 7px;

        .profile-btn {
          padding: 2px 20px 2px 0;

          .v-img__img {
            width: 40px;
            height: 40px;
          }
          span {
            margin-top: 4px;
            font-size: 15px;
          }
        }
      }
    }
    &.sticky {
      right: 25px;
      left: 25px;

      &.active {
        left: 25px;
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .header-area {
    position: inherit !important;
    margin-bottom: 25px;
    .header-left-side {
      .burger-menu {
        margin-right: 15px;
      }
      .menu-btn {
        margin-right: 15px;
      }
    }
    &.sticky {
      right: 25px;
      left: 25px;

      &.active {
        left: 25px;
      }
    }
  }
}
</style>
