import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "@composables/useAuth";
import SignInPage from "@pages/Authentication/SignInPage.vue";
import RegisterPage from "@pages/Authentication/RegisterPage.vue";
import DashboardPage from "@pages/Dashboard/DashboardPage.vue";
import ErrorPage from "@pages/Error/ErrorPage.vue";
import InternalErrorPage from "@pages/Error/InternalErrorPage.vue";
import LogoutPage from "@pages/Authentication/LogoutPage.vue";
import InspectionPage from "@pages/Inspection/InspectionPage.vue";
import WorkInProgressPage from "@pages/WorkInProgress/WorkInProgressPage.vue";
import WorkingPermitPage from "@pages/WorkingPermit/WorkingPermitPage.vue";
import MyProfilePage from "@pages/MyProfile/MyProfilePage.vue";
import TemplateInspectionPage from "@pages/TemplateInspection/TemplateInspectionPage.vue";
import UserManagementPage from "@pages/UserManagement/UserManagementPage.vue";
import NotificationsPage from "@pages/Notifications/NotificationsPage.vue";

const routes = [
  { path: "/:pathMatch(.*)*", name: "ErrorPage", component: ErrorPage },
  {
    path: "/errors/internal-error",
    name: "InternalErrorPage",
    component: InternalErrorPage,
  },
  {
    path: "/",
    redirect: "/authentication/sign-in",
  },
  {
    path: "/authentication/sign-in",
    name: "SignInPage",
    component: SignInPage,
  },
  {
    path: "/logout",
    name: "LogoutPage",
    component: LogoutPage,
  },
  {
    path: "/authentication/register",
    name: "RegisterPage",
    component: RegisterPage,
  },
  {
    path: "/admin",
    redirect: "/admin/dashboard",
  },
  {
    path: "/admin/dashboard",
    name: "DashboardPage",
    component: DashboardPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/inspection",
    name: "InspectionPage",
    component: InspectionPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/work-in-progress",
    name: "WorkInProgressPage",
    component: WorkInProgressPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/working-permit",
    name: "WorkingPermitPage",
    component: WorkingPermitPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/myprofile",
    name: "MyProfilePage",
    component: MyProfilePage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/template-inspection",
    name: "TemplateInspectionPage",
    component: TemplateInspectionPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/user-management",
    name: "UserManagementPage",
    component: UserManagementPage,
    meta: { requiresAuth: true },
  },
  {
    path: "/admin/notifications",
    name: "NotificationsPage",
    component: NotificationsPage,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  linkExactActiveClass: "active",
  scrollBehavior() {
    return { top: 0, behavior: "smooth" };
  },
});

router.beforeEach((to, from, next) => {
  const { isAuthenticated } = useAuth();

  if (to.meta.requiresAuth && !isAuthenticated()) {
    return next("/authentication/sign-in");
  }

  next();
});
export default router;
