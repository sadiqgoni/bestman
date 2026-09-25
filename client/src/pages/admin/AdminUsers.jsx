import { useEffect, useState } from "react";
import api from "../../api/client";

const emptyForm = { name: "", username: "", password: "", role: "STAFF" };

export default function AdminUsers() {
  const [users, setUsers] = useState([]);
  const [form, setForm] = useState(emptyForm);
  const [error, setError] = useState("");
  const [submitting, setSubmitting] = useState(false);

  function load() {
    api.get("/users").then((res) => setUsers(res.data));
  }

  useEffect(load, []);

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setSubmitting(true);
    try {
      await api.post("/users", form);
      setForm(emptyForm);
      load();
    } catch (err) {
      setError(err.response?.data?.message || "Failed to create user");
    } finally {
      setSubmitting(false);
    }
  }

  async function toggleActive(user) {
    await api.patch(`/users/${user.id}`, { active: !user.active });
    load();
  }

  return (
    <div>
      <h2>User Management</h2>
      <div className="bm-card">
        <h3>Create Staff / Admin Account</h3>
        {error && <div className="bm-alert error">{error}</div>}
        <form onSubmit={handleSubmit}>
          <div className="bm-form-row">
            <div className="bm-field">
              <label>Full Name</label>
              <input value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} required />
            </div>
            <div className="bm-field">
              <label>Username</label>
              <input value={form.username} onChange={(e) => setForm((f) => ({ ...f, username: e.target.value }))} required />
            </div>
            <div className="bm-field">
              <label>Password</label>
              <input type="password" value={form.password} onChange={(e) => setForm((f) => ({ ...f, password: e.target.value }))} required />
            </div>
            <div className="bm-field">
              <label>Role</label>
              <select value={form.role} onChange={(e) => setForm((f) => ({ ...f, role: e.target.value }))}>
                <option value="STAFF">Staff / Manager</option>
                <option value="ADMIN">Admin</option>
              </select>
            </div>
          </div>
          <button className="bm-btn" type="submit" disabled={submitting}>
            {submitting ? "Creating..." : "Create Account"}
          </button>
        </form>
      </div>

      <div className="bm-card">
        <h3>Existing Accounts</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {users.map((u) => (
              <tr key={u.id}>
                <td>{u.name}</td>
                <td>{u.username}</td>
                <td>{u.role}</td>
                <td>
                  <span className={`bm-tag ${u.active ? "green" : "red"}`}>{u.active ? "Active" : "Inactive"}</span>
                </td>
                <td>
                  <button className="bm-btn secondary" onClick={() => toggleActive(u)}>
                    {u.active ? "Deactivate" : "Activate"}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
