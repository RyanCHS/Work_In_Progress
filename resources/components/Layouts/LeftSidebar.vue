<template>
  <div
    :class="[
      'sidebar-area bg-white box-shadow',
      { active: stateStoreInstance.open },
    ]"
    id="SidebarTheme"
  >
    <div class="logo bg-white">
      <RouterLink to="/admin" class="d-flex flex-column align-items-center">
        <span class="fw-bold text-black position-relative logo-title">WIP</span>
        <span class="fw-bold text-black position-relative logo-subtitle"
          >Work In Progress</span
        >
      </RouterLink>
    </div>
    <div
      :class="['burger-menu', { active: stateStoreInstance.open }]"
      @click="stateStoreInstance.onChange"
    >
      <span class="top-bar"></span>
      <span class="middle-bar"></span>
      <span class="bottom-bar"></span>
    </div>
    <div class="virtual-scroll mt-3">
      <div class="sidebar-inner">
        <div class="sidebar-menu">
          <AccordionSlot>
            <template v-for="item in menuItems" :key="item.id">
              <RouterLink
                v-if="item.type === 'single'"
                :to="item.link"
                class="sidebar-menu-link"
              >
                <span class="material-symbols-outlined">{{ item.icon }}</span>
                <span class="title">{{ item.title }}</span>
                <template v-if="item.badge">
                  <span class="trezo-badge d-inline-block position-relative">
                    {{ item.badge }}
                  </span>
                </template>
              </RouterLink>
            </template>
          </AccordionSlot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import stateStore from "../../utils/store";
import AccordionSlot from "./AccordionSlot.vue";
import { useAuth } from "@composables/useAuth";
import { RouterLink } from "vue-router";
import {
  menuItemsAdvisorSafety,
  menuItemsPengawasK3,
  menuItemsAdministrator,
} from "../../data/menuData";
const { user } = useAuth();

defineOptions({
  name: "LeftSidebar",
});

const menuItems = ref([]);

watch(
  user,
  (val) => {
    if (!val) return;

    if (val.UserGroup === "Safety Advisor") {
      menuItems.value = menuItemsAdvisorSafety;
    } else if (val.UserGroup === "Pengawas K3") {
      menuItems.value = menuItemsPengawasK3;
    } else if (val.UserGroup === "Administrator") {
      menuItems.value = menuItemsAdministrator;
    }
  },
  { immediate: true }
);
const stateStoreInstance = stateStore;
</script>

<style lang="scss">
.sidebar-area {
  transition: var(--transition);
  border-radius: 0 10px 10px 0;
  overflow: hidden;
  position: fixed;
  height: 100vh;
  width: 260px;
  z-index: 222;
  left: 0;
  top: 0;

  .virtual-scroll {
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;

    &::-webkit-scrollbar {
      -webkit-appearance: none;
    }
    &::-webkit-scrollbar:vertical {
      width: 5px;
    }
    &::-webkit-scrollbar:horizontal {
      height: 5px;
    }
    &::-webkit-scrollbar-thumb {
      border-radius: 50px;
      background-color: rgba(0, 0, 0, 0.2);
    }
    &::-webkit-scrollbar-track {
      background: var(--whiteColor);
    }
  }
  .logo {
    border-bottom: 1px solid var(--borderColor);
    padding: 15px 25px 19px;
    position: absolute;
    z-index: 2;
    right: 0;
    left: 0;
    top: 0;
    a {
      transition: unset;
      span {
        margin-left: 8px;
        font-size: 24px;
        top: 1px;
      }
    }
    .logo-title {
      font-size: 24px;
    }
    .logo-subtitle {
      font-size: 16px;
    }
  }
  .burger-menu {
    top: 25px;
    z-index: 3;
    opacity: 0;
    right: 15px;
    cursor: pointer;
    position: absolute;
    visibility: hidden;
    transition: var(--transition);

    span {
      height: 1px;
      width: 25px;
      margin: 6px 0;
      display: block;
      background: var(--blackColor);
      transition: var(--transition);
    }
    &.active {
      opacity: 1;
      visibility: visible;

      span {
        &.top-bar {
          transform: rotate(45deg);
          transform-origin: 10% 10%;
        }
        &.middle-bar {
          opacity: 0;
        }
        &.bottom-bar {
          transform: rotate(-45deg);
          transform-origin: 10% 90%;
          margin-top: 5px;
        }
      }
    }
  }
  .sidebar-inner {
    padding: 90px 20px 20px 25px;
    .sidebar-menu {
      .sub-title {
        display: block;
        font-size: 12px;
        margin-bottom: 10px;
        text-transform: uppercase;
        color: #8695aa;
        &:not(:first-child) {
          margin-top: 10px;
        }
      }
    }
  }
  .accordion {
    .accordion__item {
      color: var(--blackColor);
      background-color: transparent;
      list-style-type: none;
      margin-bottom: 5px;

      .title-header {
        transition: var(--transition);
        background-color: transparent;
        color: var(--blackColor);
        border-radius: 7px;
        position: relative;
        display: block;
        height: auto;
        font-size: var(--fontSize);
        font-weight: 500;
        padding: 8px 20px 8px 40px;
        cursor: pointer;
        i,
        .material-symbols-outlined {
          transition: var(--transition);
          transform: translateY(-50%);
          color: var(--bodyColor);
          position: absolute;
          font-size: 22px;
          line-height: 1;
          left: 12px;
          top: 50%;

          &.arrow-right {
            left: auto;
            right: 5px;
            font-size: 19px;
            margin-top: 0;
            color: rgba(0, 0, 0, 0.54);
            line-height: 0.8;
          }
        }
        .badge {
          top: 50%;
          right: 34px;
          width: 20px;
          height: 20px;
          font-size: 12px;
          color: #fd5812;
          line-height: 20px;
          position: absolute;
          transform: translateY(-50%);
          background-color: #fff5ed;
          padding: 0 6px;
          &.two {
            color: #00b69b;
            background: #00b69b12;
          }
          &.three {
            color: #ee368c;
            background: #ee368c1a;
          }
        }
        &:hover {
          color: var(--blackColor);
          background-color: #f6f7f9;
        }
      }
      .accordion__trigger {
        &.accordion__trigger_active {
          background-color: #f5f5f9;
          border-radius: 5px;

          .title-header {
            i,
            .material-symbols-outlined {
              color: var(--blackColor);
              &.arrow-right {
                transform: rotate(90deg);
                top: 27%;
              }
            }
          }
        }
      }
      .accordion__content {
        padding: 4px 0 0;

        .sidebar-sub-menu {
          padding-left: 0;
          list-style-type: none;
          margin: {
            top: 0;
            bottom: 0;
          }
          .sidemenu-item {
            margin-bottom: 4px;
            .sidemenu-link {
              display: block;
              position: relative;
              border-radius: 7px;
              color: var(--bodyColor);
              transition: var(--transition);
              font-size: var(--fontSize);
              font-weight: 500;
              padding: 8px 10px 8px 38px;
              &::after {
                top: 50%;
                left: 18px;
                width: 10px;
                content: "";
                height: 10px;
                transition: 0.3s;
                border-radius: 50%;
                position: absolute;
                transform: translateY(-50%);
                border: 1px solid var(--bodyColor);
              }
              .trezo-badge {
                top: -1px;
                color: #fd5812;
                padding: 2px 6px;
                margin-left: 5px;
                border-radius: 3px;
                line-height: initial;
                background-color: #ffe8d4;
                font-size: 10px;
                font-weight: 400;
                &:before {
                  top: 50%;
                  left: -3px;
                  width: 6px;
                  height: 6px;
                  content: "";
                  position: absolute;
                  background: #ffe8d4;
                  transform: translateY(-50%) rotate(45deg);
                }
                &.style-two {
                  color: #25b003;
                  background-color: #d8ffc8;
                  &:before {
                    background: #d8ffc8;
                  }
                }
              }
              &:hover,
              &.active,
              &.router-link-active {
                background-color: #ecf0ff;
                color: var(--primaryColor);

                .badge {
                  background: var(--whiteColor);
                }
                &::after {
                  border-color: var(--primaryColor);
                }
              }
              .badge {
                top: -1px;
                color: #fd5812;
                padding: 2px 6px;
                margin-left: 5px;
                border-radius: 3px;
                line-height: initial;
                background-color: #ffe8d4;
                font-size: 10px;
                font-weight: 400;
              }
            }
            .accordion {
              .accordion__item {
                .title-header {
                  &::after {
                    top: 50%;
                    left: 18px;
                    width: 10px;
                    content: "";
                    height: 10px;
                    transition: 0.3s;
                    border-radius: 50%;
                    position: absolute;
                    transform: translateY(-50%);
                    border: 1px solid var(--bodyColor);
                  }
                }
                .accordion__trigger {
                  &.accordion__trigger_active {
                    background-color: #e7effd !important;
                    .title-header {
                      color: var(--primaryColor) !important;
                    }
                  }
                }
                .accordion__content {
                  padding-left: 20px;
                }
              }
            }
          }
        }
      }
    }
    .sidebar-menu-link {
      transition: var(--transition);
      color: var(--blackColor);
      margin-bottom: 5px;
      position: relative;
      border-radius: 7px;
      display: block;
      padding: 11.5px 30px 11.5px 40px;
      font-size: var(--fontSize);
      font-weight: 500;
      i,
      .trezo-badge {
        top: -1px;
        color: #fd5812;
        padding: 2px 6px;
        margin-left: 5px;
        border-radius: 3px;
        line-height: initial;
        background-color: #ffe8d4;
        font-size: 10px;
        font-weight: 400;
        position: relative;
      }
      .material-symbols-outlined {
        transition: var(--transition);
        transform: translateY(-50%);
        color: var(--bodyColor);
        position: absolute;
        font-size: 22px;
        line-height: 1;
        left: 12px;
        top: 50%;
      }
      &:hover,
      &.router-link-active {
        color: var(--primaryColor);
        background-color: #f6f7f9;
        i,
        .material-symbols-outlined {
          color: var(--primaryColor);
        }
      }
    }
  }
  &.active {
    width: 60px;

    .logo {
      padding: {
        left: 12px;
        right: 12px;
      }
      a {
        span {
          display: none;
        }
      }
    }
    .sidebar-inner {
      padding: {
        top: 80px;
        left: 12px;
        right: 12px;
      }
    }
    .sidebar-menu {
      .sub-title {
        display: none;
      }
    }
    .accordion {
      .accordion__item {
        margin: {
          top: 3px;
          bottom: 3px;
        }
        .title-header {
          padding: 0;
          width: 36px;
          height: 36px;

          .arrow-right {
            display: none;
          }
          .title {
            display: none;
          }
          .badge {
            display: none;
          }
          .material-symbols-outlined {
            left: 4px;
          }
        }
        .accordion__content {
          display: none;
        }
      }
      .sidebar-menu-link {
        padding: 0;
        width: 36px;
        height: 36px;

        i,
        .material-symbols-outlined {
          left: 4px;
        }
        .title {
          display: none;
        }
      }
    }
    .burger-menu {
      opacity: 0;
      visibility: hidden;
    }
    &:hover {
      width: 260px;

      .logo {
        padding: {
          left: 15px;
          right: 15px;
        }
        a {
          span {
            display: inline-block;
          }
        }
      }
      .burger-menu {
        opacity: 1;
        visibility: visible;
      }
      .sidebar-inner {
        padding: {
          top: 100px;
          left: 15px;
          right: 15px;
        }
      }
      .sidebar-menu {
        .sub-title {
          display: block;
        }
      }
      .accordion {
        .accordion__item {
          margin: {
            top: 0;
            bottom: 0;
          }
          .title-header {
            width: auto;
            height: auto;
            padding: {
              bottom: 14px;
              right: 60px;
              left: 37px;
              top: 14px;
            }
            .arrow-right {
              display: block;
            }
            .title {
              display: block;
            }
            .badge {
              display: inline-block;
            }
          }
          .accordion__content {
            display: block;
          }
        }
        .sidebar-menu-link {
          width: auto;
          height: auto;
          padding: {
            bottom: 14px;
            right: 60px;
            left: 37px;
            top: 14px;
          }
          i {
            left: 15px;
            right: auto;
            text-align: unset;
          }
          .title {
            display: block;
          }
        }
      }
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .sidebar-area {
    width: 250px;
    left: -100%;

    .logo {
      padding: {
        top: 15px;
        bottom: 15px;
      }
      a {
        span {
          margin-left: 15px;
        }
      }
    }
    .sidebar-menu {
      .sub-title {
        font-size: 13px;
      }
    }
    .burger-menu {
      top: 25px;
      right: 12px;
    }
    .sidebar-inner {
      padding-top: 75px;
    }
    .accordion {
      .accordion__item {
        .title-header {
          font-size: 13px;
          padding: 8px 25px 8px 37px;
          .material-symbols-outlined {
            font-size: 18px;
            left: 12px !important;
          }
          .title {
            font-size: 13px;
          }
          .badge {
            right: 28px;
            width: 18px;
            height: 18px;
            font-size: 11px;
            line-height: 18px;
          }
        }
        .accordion__content {
          .sidebar-sub-menu {
            .sidemenu-item {
              .sidemenu-link {
                font-size: 13px;
                padding: 7px 10px 7px 35px;
                &::after {
                  left: 15px;
                  width: 5px;
                  height: 5px;
                }
              }
            }
          }
        }
      }
      .sidebar-menu-link {
        font-size: 13px;
        padding: 8px 25px 8px 37px;
        i {
          font-size: 14px;
          left: 12px;
        }
      }
    }
    &.active {
      width: 250px;
      left: 0;

      .logo {
        padding: {
          left: 12px;
          right: 12px;
        }
        a {
          span {
            display: inline-block;
          }
        }
      }
      .burger-menu {
        opacity: 1;
        visibility: visible;
      }
      .sidebar-inner {
        padding: {
          top: 75px;
          left: 12px;
          right: 12px;
        }
      }
      .sidebar-menu {
        .sub-title {
          display: block;
        }
      }
      .accordion {
        .accordion__item {
          .title-header {
            width: auto;
            height: auto;
            padding: {
              bottom: 13px;
              right: 50px;
              left: 34px;
              top: 13px;
            }
            display: block;
            .title {
              display: block !important;
            }
            .badge {
              display: block !important;
              right: 35px;
            }
            .arrow-right {
              display: block !important;
            }
            .accordion__content {
              width: auto;
              height: auto;
              display: block !important;
              padding: {
                bottom: 13px;
                right: 50px;
                left: 34px;
                top: 13px;
              }
              i {
                left: 12px;
                right: auto;
                text-align: unset;
              }
              .badge {
                display: inline-block !important;
              }
            }
          }
          .accordion__content {
            display: block;
          }
        }
        .sidebar-menu-link {
          width: auto;
          height: auto;
          padding: {
            bottom: 13px;
            right: 50px;
            left: 34px;
            top: 13px;
          }
          i {
            left: 12px;
            right: auto;
            text-align: unset;
          }
          .title {
            display: block;
          }
        }
      }
      &:hover {
        width: 250px;

        .logo {
          padding: {
            left: 12px;
            right: 12px;
          }
          a {
            span {
              display: inline-block;
            }
          }
        }
        .burger-menu {
          opacity: 1;
          visibility: visible;
        }
        .sidebar-inner {
          padding: {
            top: 75px;
            left: 12px;
            right: 12px;
          }
        }
        .sidebar-menu {
          .sub-title {
            display: block;
          }
        }
        .accordion {
          .accordion__item {
            margin: {
              top: 0;
              bottom: 0;
            }
            .title-header {
              width: auto;
              height: auto;
              padding: {
                bottom: 13px;
                right: 50px;
                left: 34px;
                top: 13px;
              }
              .title {
                display: block;
              }
              .badge {
                display: inline-block;
                right: 35px;
              }
            }
            .accordion__content {
              display: block;
            }
          }
          .sidebar-menu-link {
            width: auto;
            height: auto;
            padding: {
              bottom: 13px;
              right: 50px;
              left: 34px;
              top: 13px;
            }
            i {
              left: 12px;
              right: auto;
              text-align: unset;
            }
            .title {
              display: block;
            }
          }
        }
      }
    }
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .sidebar-area {
    left: -100%;

    .sidebar-inner {
      padding-top: 85px;
    }
    &.active {
      width: 260px;
      left: 0;

      .logo {
        padding: {
          left: 15px;
          right: 15px;
        }
        a {
          span {
            display: inline-block;
          }
        }
      }
      .burger-menu {
        opacity: 1;
        visibility: visible;
      }
      .sidebar-inner {
        padding: {
          top: 80px;
          left: 15px;
          right: 15px;
        }
      }
      .sidebar-menu {
        .sub-title {
          display: block;
        }
      }
      .accordion {
        .accordion__item {
          margin: {
            top: 0;
            bottom: 0;
          }
          .title-header {
            width: auto;
            height: auto;
            padding: {
              bottom: 14px;
              right: 60px;
              left: 37px;
              top: 14px;
            }
            .arrow-right {
              display: block;
            }
            .title {
              display: block;
            }
            .badge {
              display: inline-block;
            }
          }
          .accordion__content {
            display: block;
          }
        }
        .sidebar-menu-link {
          width: auto;
          height: auto;
          padding: {
            bottom: 14px;
            right: 60px;
            left: 37px;
            top: 14px;
          }
          i {
            left: 15px;
            right: auto;
            text-align: unset;
          }
          .title {
            display: block;
          }
        }
      }
      &:hover {
        width: 260px;

        .sidebar-inner {
          padding-top: 80px;
        }
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .sidebar-area {
    left: -100%;

    &.active {
      width: 260px;
      left: 0;

      .logo {
        padding: {
          left: 15px;
          right: 15px;
        }
        a {
          span {
            display: inline-block;
          }
        }
      }
      .burger-menu {
        opacity: 1;
        visibility: visible;
      }
      .sidebar-inner {
        padding: {
          top: 80px;
          left: 15px;
          right: 15px;
        }
      }
      .sidebar-menu {
        .sub-title {
          display: block;
        }
      }
      .accordion {
        .accordion__item {
          margin: {
            top: 0;
            bottom: 0;
          }
          .title-header {
            width: auto;
            height: auto;
            padding: {
              bottom: 14px;
              right: 60px;
              left: 37px;
              top: 14px;
            }
            .arrow-right {
              display: block;
            }
            .title {
              display: block;
            }
            .badge {
              display: inline-block;
            }
          }
          .accordion__content {
            display: block;
          }
        }
        .sidebar-menu-link {
          width: auto;
          height: auto;
          padding: {
            bottom: 14px;
            right: 60px;
            left: 37px;
            top: 14px;
          }
          i {
            left: 15px;
            right: auto;
            text-align: unset;
          }
          .title {
            display: block;
          }
        }
      }
      &:hover {
        width: 260px;

        .sidebar-inner {
          padding-top: 80px;
        }
      }
    }
  }
}
</style>
