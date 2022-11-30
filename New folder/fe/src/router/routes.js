import AuthenticatedLayout from "../layouts/AuthenticatedLayout.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: AuthenticatedLayout,
    redirect: { name: "Dashboard" },
    children: [
      {
        path: "dashboard",
        name: "Dashboard",
        component: () => import("../views/UserDashboard.vue"),
      },
      {
        path: "athletes",
        name: "Athletes",
        component: () => import("../views/UserAthletes.vue"),
      },
      {
        path: "performance",
        name: "Performance",
        component: () => import("../views/AthletePerformance.vue"),
      },
      {
        path: "schedules",
        name: "Schedule",
        component: () => import("../views/GameSchedule.vue"),
      },
      {
        path: "drills",
        name: "Drills",
        component: () => import("../views/GameDrills.vue"),
      },
      {
        path: "settings",
        name: "Settings",
        component: () => import("../views/UserSettings.vue"),
      },
    ],
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("../pages/UserLogin.vue"),
    meta: {
      requiresAuth: false,
    },
  },
  {
    path: "/register",
    name: "register",
    component: () => import("../pages/UserRegister.vue"),
    meta: {
      requiresAuth: false,
    },
  },
  {
    path: "/forgot-password",
    name: "reset",
    component: () => import("../pages/ForgotPassword.vue"),
    meta: {
      requiresAuth: false,
    },
  },
];

export default routes;