import axios from "axios";

const api = axios.create({ baseURL: "/api" });

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("bestman_token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      localStorage.removeItem("bestman_token");
      localStorage.removeItem("bestman_user");
      if (!window.location.pathname.includes("/login")) {
        window.location.href = "/staff/login";
      }
    }
    return Promise.reject(err);
  }
);

export default api;
