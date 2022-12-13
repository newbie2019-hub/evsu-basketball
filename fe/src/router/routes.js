import AuthenticatedLayout from "../layouts/AuthenticatedLayout.vue";
import { useAuthStore } from "src/stores/authentication";

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
        meta: {
          isPlayer: true,
        },
      },
      {
        path: "athletes",
        name: "Athletes",
        component: () => import("../views/UserAthletes.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "athletes/:id",
        name: "Athlete Info",
        component: () => import("../views/AthleteInfo.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "teams",
        name: "Teams",
        component: () => import("../views/GameTeams.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "coaches",
        name: "Coaches",
        component: () => import("../views/AssistantCoach.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach") return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "player-evaluation",
        name: "Player Evaluation",
        component: () => import("../views/PlayerEvaluation.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "player-evaluation/:id",
        name: "Player Evaluation Information",
        component: () => import("../views/ViewPlayerEvaluation.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
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
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "evaluations",
        name: "Evaluations",
        component: () => import("../views/EvaluationData.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "performance-new",
        name: "New Performance",
        component: () => import("../views/NewPerformance.vue"),
        beforeEnter(to, from, next) {
          const { user } = useAuthStore();
          if (user.position == "Coach" || user.position == "Assistant-Coach")
            return next();
          return next({ name: "Dashboard" });
        },
      },
      {
        path: "drills",
        name: "Drills",
        component: () => import("../views/GameDrills.vue"),
        meta: {
          isPlayer: true,
        },
      },
      {
        path: "settings",
        name: "Settings",
        component: () => import("../views/UserSettings.vue"),
        meta: {
          isPlayer: true,
        },
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
