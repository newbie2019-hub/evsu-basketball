import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/Authenticated.vue";
import { useAuthStore } from "../stores/authentication";
import Cookies from 'js-cookie'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
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
          path: "performance",
          name: "Performance",
          component: () => import("../views/AthletePerformance.vue"),
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
  ],
});

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth && (!localStorage.getItem("user_data") && !Cookies.get('access_token'))) {
    next({ name: "Login" });
  } else if (!to.meta.requiresAuth && localStorage.getItem("user_data")) {
    next({ name: "Dashboard" });
  } else {
    next();
  }
});

export default router;
