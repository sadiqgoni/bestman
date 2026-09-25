import { useEffect, useState } from "react";
import api from "../../api/client";

export default function AdminProfitLoss() {
  const [rows, setRows] = useState([]);
  const [totals, setTotals] = useState(null);
  const [from, setFrom] = useState("");
  const [to, setTo] = useState("");

  function load() {
    const params = {};
    if (from) params.from = from;
    if (to) params.to = to;
    api.get("/reports/profit-loss", { params }).then((res) => {
      setRows(res.data.rows);
      setTotals(res.data.totals);
    });
  }

  useEffect(load, []);

  return (
    <div>
      <h2>Profit & Loss Tracking</h2>

      <div className="bm-card">
        <div className="bm-form-row" style={{ maxWidth: 500 }}>
          <div className="bm-field">
            <label>From</label>
            <input type="date" value={from} onChange={(e) => setFrom(e.target.value)} />
          </div>
          <div className="bm-field">
            <label>To</label>
            <input type="date" value={to} onChange={(e) => setTo(e.target.value)} />
          </div>
          <div style={{ alignSelf: "end" }}>
            <button className="bm-btn" onClick={load}>
              Filter
            </button>
          </div>
        </div>
      </div>

      {totals && (
        <div className="bm-grid">
          <div className="bm-stat">
            <div className="label">Total Revenue</div>
            <div className="value">₦{totals.fuelGrandTotalAmount.toFixed(2)}</div>
          </div>
          <div className="bm-stat">
            <div className="label">Total Expenses</div>
            <div className="value">₦{totals.totalExpenses.toFixed(2)}</div>
          </div>
          <div className="bm-stat">
            <div className="label">Gross Profit</div>
            <div className={totals.grossProfit < 0 ? "value bm-loss" : "value bm-profit"}>
              ₦{totals.grossProfit.toFixed(2)}
            </div>
          </div>
          <div className="bm-stat">
            <div className="label">Net Profit / Loss</div>
            <div className={totals.netProfit < 0 ? "value bm-loss" : "value bm-profit"}>
              ₦{totals.netProfit.toFixed(2)}
            </div>
          </div>
        </div>
      )}

      <div className="bm-card">
        <table className="bm-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Staff</th>
              <th>Revenue</th>
              <th>Expenses</th>
              <th>Gross Profit</th>
              <th>Net Profit / Loss</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r) => (
              <tr key={r.id}>
                <td>{new Date(r.entryDate).toLocaleDateString()}</td>
                <td>{r.staff}</td>
                <td>₦{r.fuelGrandTotalAmount.toFixed(2)}</td>
                <td>₦{r.totalExpenses.toFixed(2)}</td>
                <td className={r.grossProfit < 0 ? "bm-loss" : "bm-profit"}>₦{r.grossProfit.toFixed(2)}</td>
                <td className={r.isLoss ? "bm-loss" : "bm-profit"}>
                  {r.isLoss ? `-₦${Math.abs(r.netProfit).toFixed(2)}` : `₦${r.netProfit.toFixed(2)}`}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
