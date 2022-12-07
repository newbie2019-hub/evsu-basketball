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
        path: "teams",
        name: "Teams",
        component: () => import("../views/GameTeams.vue"),
      },
      {
        path: "player-evaluation",
        name: "Player Evaluation",
        component: () => import("../views/PlayerEvaluation.vue"),
      },
      {
        path: "schedules",
        name: "Schedules",
        component: () => import("../views/GameSchedule.vue"),
      },
      {
        path: "statistics",
        name: "Player Statistics",
        component: () => import("../views/PlayerStatistics.vue"),
      },
      {
        path: "evaluations",
        name: "Evaluations",
        component: () => import("../views/EvaluationData.vue"),
      },
      {
        path: "performance-new",
        name: "New Performance",
        component: () => import("../views/NewPerformance.vue"),
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
