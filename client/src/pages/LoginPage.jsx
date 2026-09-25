import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function LoginPage({ expectedRole }) {
  const { login, logout } = useAuth();
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      const user = await login(username, password);
      if (user.role !== expectedRole) {
        logout();
        throw new Error(
          expectedRole === "ADMIN"
            ? "This is the admin login. Use a staff account on the staff login page."
            : "This is the staff login. Use an admin account on the admin login page."
        );
      }
      navigate(user.role === "ADMIN" ? "/admin/reports" : "/stock");
    } catch (err) {
      setError(err.response?.data?.message || err.message || "Login failed");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="bm-login-page">
      <form className="bm-login-card" onSubmit={handleSubmit}>
        <img src="/logo.png" alt="Bestman Merchandise" onError={(e) => (e.target.style.display = "none")} />
        <h1>BESTMAN MERCHANDISE NIG. LTD.</h1>
        <h2>{expectedRole === "ADMIN" ? "Admin Portal Login" : "Staff Portal Login"}</h2>
        <p className="bm-login-help">
          {expectedRole === "ADMIN" ? "Manage reports, deliveries, users and prices." : "Record stock, deliveries and daily transactions."}
        </p>
        {error && <div className="bm-alert error">{error}</div>}
        <div className="bm-field" style={{ marginBottom: 14 }}>
          <label>Username</label>
          <input value={username} onChange={(e) => setUsername(e.target.value)} required autoFocus />
        </div>
        <div className="bm-field" style={{ marginBottom: 20 }}>
          <label>Password</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        <button className="bm-btn" type="submit" disabled={loading} style={{ width: "100%" }}>
          {loading ? "Signing in..." : "Login"}
        </button>
        <a className="bm-login-switch" href={expectedRole === "ADMIN" ? "/staff/login" : "/admin/login"}>
          Go to {expectedRole === "ADMIN" ? "staff" : "admin"} login
        </a>
      </form>
    </div>
  );
}
