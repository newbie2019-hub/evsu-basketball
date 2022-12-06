import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const config = {
  position: "top-right",
  timeout: 3517,
  closeOnClick: true,
  pauseOnFocusLoss: false,
  pauseOnHover: false,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: true,
  closeButton: "button",
  icon: true,
  rtl: false,
};

export default ({ app }) => {
  app.use(Toast, config);
};
