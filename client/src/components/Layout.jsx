import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

const staffLinks = [
  { to: "/stock", label: "Stock Inventory" },
  { to: "/deliveries/new", label: "Tanker Delivery Intake" },
  { to: "/daily-entry", label: "Daily Transaction Entry" },
];

const adminLinks = [
  { to: "/admin/reports", label: "Daily Reports" },
  { to: "/admin/profit-loss", label: "Profit & Loss" },
  { to: "/admin/deliveries", label: "Tanker Deliveries" },
  { to: "/admin/config", label: "Tanks, Pumps & Prices" },
  { to: "/admin/users", label: "User Management" },
];

export default function Layout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const links = user?.role === "ADMIN" ? adminLinks : staffLinks;

  return (
    <div className="bm-app-shell">
      <aside className="bm-sidebar">
        <div className="bm-sidebar__brand">
          <img src="/logo.png" alt="Bestman Merchandise logo" onError={(e) => (e.target.style.display = "none")} />
          <div className="bm-sidebar__brand-text">
            BESTMAN MERCHANDISE
            <div style={{ fontWeight: 400, opacity: 0.75 }}>{user?.role === "ADMIN" ? "Admin Dashboard" : "Staff Portal"}</div>
          </div>
        </div>
        <nav>
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              className={({ isActive }) => "bm-nav-link" + (isActive ? " active" : "")}
            >
              {link.label}
            </NavLink>
          ))}
        </nav>
      </aside>
      <div className="bm-main">
        <header className="bm-topbar">
          <h1>Fuel & Tanker Delivery Tracking System</h1>
          <div style={{ display: "flex", alignItems: "center", gap: 12 }}>
            <span style={{ fontSize: 14 }}>
              {user?.name} <span style={{ color: "#8a93a3" }}>({user?.role})</span>
            </span>
            <button
              className="bm-btn secondary"
              onClick={() => {
                logout();
                navigate("/login");
              }}
            >
              Logout
            </button>
          </div>
        </header>
        <main className="bm-content">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
