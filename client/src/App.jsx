import { Navigate, Route, Routes } from "react-router-dom";
import { useAuth } from "./context/AuthContext";
import Layout from "./components/Layout";
import LoginPage from "./pages/LoginPage";
import StockView from "./pages/staff/StockView";
import TankerDeliveryForm from "./pages/staff/TankerDeliveryForm";
import DailyTransactionPage from "./pages/staff/DailyTransactionPage";
import AdminUsers from "./pages/admin/AdminUsers";
import AdminTanksPumps from "./pages/admin/AdminTanksPumps";
import AdminDeliveries from "./pages/admin/AdminDeliveries";
import AdminDailyReports from "./pages/admin/AdminDailyReports";
import AdminProfitLoss from "./pages/admin/AdminProfitLoss";

function ProtectedRoute({ children, roles }) {
  const { user, loading } = useAuth();
  if (loading) return <div className="bm-content">Loading...</div>;
  if (!user) return <Navigate to="/login" replace />;
  if (roles && !roles.includes(user.role)) return <Navigate to="/" replace />;
  return children;
}

export default function App() {
  const { user } = useAuth();

  return (
    <Routes>
      <Route path="/login" element={<Navigate to="/staff/login" replace />} />
      <Route path="/admin/login" element={<LoginPage expectedRole="ADMIN" />} />
      <Route path="/staff/login" element={<LoginPage expectedRole="STAFF" />} />
      <Route
        path="/*"
        element={
          <ProtectedRoute>
            <Layout />
          </ProtectedRoute>
        }
      >
        <Route
          index
          element={<Navigate to={user?.role === "ADMIN" ? "/admin/reports" : "/stock"} replace />}
        />

        {/* Staff Portal */}
        <Route
          path="stock"
          element={
            <ProtectedRoute roles={["STAFF", "ADMIN"]}>
              <StockView />
            </ProtectedRoute>
          }
        />
        <Route
          path="deliveries/new"
          element={
            <ProtectedRoute roles={["STAFF", "ADMIN"]}>
              <TankerDeliveryForm />
            </ProtectedRoute>
          }
        />
        <Route
          path="daily-entry"
          element={
            <ProtectedRoute roles={["STAFF", "ADMIN"]}>
              <DailyTransactionPage />
            </ProtectedRoute>
          }
        />

        {/* Admin Dashboard */}
        <Route
          path="admin/users"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <AdminUsers />
            </ProtectedRoute>
          }
        />
        <Route
          path="admin/config"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <AdminTanksPumps />
            </ProtectedRoute>
          }
        />
        <Route
          path="admin/deliveries"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <AdminDeliveries />
            </ProtectedRoute>
          }
        />
        <Route
          path="admin/reports"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <AdminDailyReports />
            </ProtectedRoute>
          }
        />
        <Route
          path="admin/profit-loss"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <AdminProfitLoss />
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  );
}
