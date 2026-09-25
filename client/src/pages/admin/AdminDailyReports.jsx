import { useEffect, useState } from "react";
import api from "../../api/client";

export default function AdminDailyReports() {
  const [entries, setEntries] = useState([]);
  const [selected, setSelected] = useState(null);
  const [priceAlerts, setPriceAlerts] = useState([]);
  const [deliveryAlerts, setDeliveryAlerts] = useState([]);

  useEffect(() => {
    api.get("/daily-entries").then((res) => setEntries(res.data));
    api.get("/reports/price-alerts").then((res) => setPriceAlerts(res.data));
    api.get("/reports/delivery-alerts").then((res) => setDeliveryAlerts(res.data));
  }, []);

  return (
    <div>
      <h2>Daily Report View</h2>

      {(priceAlerts.length > 0 || deliveryAlerts.length > 0) && (
        <div className="bm-card">
          <h3>Active Alerts</h3>
          {priceAlerts.map((a) => (
            <div className="bm-alert warning" key={`p-${a.id}`}>
              Price Variance: {a.dailyEntry.staff.name} sold {a.pump.name} at ₦{Number(a.unitSellingPrice).toFixed(2)}
              , above base price ₦{Number(a.basePriceAtEntry).toFixed(2)} on{" "}
              {new Date(a.dailyEntry.entryDate).toLocaleDateString()}.
            </div>
          ))}
          {deliveryAlerts.map((d) => (
            <div className="bm-alert error" key={`d-${d.id}`}>
              Delivery Shortage: {d.tankerPlateNumber} ({d.supplierName}) short by{" "}
              {Number(d.varianceLiters).toFixed(2)} L on {new Date(d.createdAt).toLocaleDateString()}.
            </div>
          ))}
        </div>
      )}

      <div className="bm-card">
        <table className="bm-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Staff</th>
              <th>Liters Sold</th>
              <th>Fuel Revenue</th>
              <th>Expenses</th>
              <th>Net Cash Revenue</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {entries.map((e) => (
              <tr key={e.id}>
                <td>{new Date(e.entryDate).toLocaleDateString()}</td>
                <td>{e.staff.name}</td>
                <td>{Number(e.fuelGrandTotalLiters).toFixed(2)}</td>
                <td>₦{Number(e.fuelGrandTotalAmount).toFixed(2)}</td>
                <td>₦{Number(e.totalExpenses).toFixed(2)}</td>
                <td className={Number(e.netCashRevenue) < 0 ? "bm-loss" : "bm-profit"}>
                  ₦{Number(e.netCashRevenue).toFixed(2)}
                </td>
                <td>
                  <button className="bm-btn secondary" onClick={() => setSelected(e)}>
                    View
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selected && <EntryDetail entry={selected} onClose={() => setSelected(null)} />}
    </div>
  );
}

function EntryDetail({ entry, onClose }) {
  return (
    <div className="bm-card">
      <h3>
        Entry Detail — {entry.staff.name} on {new Date(entry.entryDate).toLocaleDateString()}
      </h3>
      <h4>Pump Readings</h4>
      <table className="bm-table">
        <thead>
          <tr>
            <th>Pump</th>
            <th>Opening</th>
            <th>Closing</th>
            <th>Liters Sold</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Flag</th>
          </tr>
        </thead>
        <tbody>
          {entry.pumpReadings.map((r) => (
            <tr key={r.id}>
              <td>{r.pump.name}</td>
              <td>{Number(r.openingReading).toFixed(2)}</td>
              <td>{Number(r.closingReading).toFixed(2)}</td>
              <td>{Number(r.litersSold).toFixed(2)}</td>
              <td>₦{Number(r.unitSellingPrice).toFixed(2)}</td>
              <td>₦{Number(r.totalAmount).toFixed(2)}</td>
              <td>{r.priceVariance && <span className="bm-tag red">Excess Price</span>}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <h4>Payment Breakdown</h4>
      <div className="bm-grid">
        <div className="bm-stat">
          <div className="label">Cash</div>
          <div className="value">₦{Number(entry.cashAmount).toFixed(2)}</div>
        </div>
        <div className="bm-stat">
          <div className="label">POS / Transfer</div>
          <div className="value">₦{Number(entry.posAmount).toFixed(2)}</div>
        </div>
        <div className="bm-stat">
          <div className="label">Bank Deposit</div>
          <div className="value">₦{Number(entry.bankDepositAmount).toFixed(2)}</div>
        </div>
      </div>

      <h4>Expenses</h4>
      <table className="bm-table">
        <thead>
          <tr>
            <th>Description</th>
            <th>Category</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          {entry.expenses.map((exp) => (
            <tr key={exp.id}>
              <td>{exp.description}</td>
              <td>{exp.category}</td>
              <td>₦{Number(exp.amount).toFixed(2)}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <div className="bm-grid" style={{ marginTop: 16 }}>
        <div className="bm-stat">
          <div className="label">Gross Profit</div>
          <div className={Number(entry.grossProfit) < 0 ? "value bm-loss" : "value bm-profit"}>
            ₦{Number(entry.grossProfit).toFixed(2)}
          </div>
        </div>
        <div className="bm-stat">
          <div className="label">Net Profit / Loss</div>
          <div className={Number(entry.netProfit) < 0 ? "value bm-loss" : "value bm-profit"}>
            ₦{Number(entry.netProfit).toFixed(2)}
          </div>
        </div>
      </div>

      <button className="bm-btn secondary" style={{ marginTop: 16 }} onClick={onClose}>
        Close
      </button>
    </div>
  );
}
