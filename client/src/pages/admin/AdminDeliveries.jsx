import { useEffect, useState } from "react";
import api from "../../api/client";

export default function AdminDeliveries() {
  const [deliveries, setDeliveries] = useState([]);
  const [buyingPrices, setBuyingPrices] = useState({});
  const [error, setError] = useState("");

  function load() {
    api.get("/deliveries").then((res) => setDeliveries(res.data));
  }

  useEffect(load, []);

  async function confirm(id) {
    const price = buyingPrices[id];
    if (!price) {
      setError("Enter a buying price per liter before confirming.");
      return;
    }
    setError("");
    try {
      await api.post(`/deliveries/${id}/confirm`, { buyingPricePerLiter: Number(price) });
      load();
    } catch (err) {
      setError(err.response?.data?.message || "Failed to confirm delivery");
    }
  }

  async function reject(id) {
    await api.post(`/deliveries/${id}/reject`);
    load();
  }

  return (
    <div>
      <h2>Tanker Delivery & Purchasing Management</h2>
      {error && <div className="bm-alert error">{error}</div>}
      <div className="bm-card">
        <table className="bm-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Plate No.</th>
              <th>Supplier</th>
              <th>Invoice</th>
              <th>Product</th>
              <th>Tank</th>
              <th>Waybill (L)</th>
              <th>Received (L)</th>
              <th>Variance</th>
              <th>Status</th>
              <th>Buying Price / L</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {deliveries.map((d) => (
              <tr key={d.id}>
                <td>{new Date(d.createdAt).toLocaleDateString()}</td>
                <td>{d.tankerPlateNumber}</td>
                <td>{d.supplierName}</td>
                <td>{d.invoiceNumber}</td>
                <td>{d.product.type}</td>
                <td>{d.tank.name}</td>
                <td>{Number(d.waybillLiters).toLocaleString()}</td>
                <td>{Number(d.receivedLiters).toLocaleString()}</td>
                <td>
                  {Number(d.varianceLiters) !== 0 ? (
                    <span className="bm-tag red">{Number(d.varianceLiters).toFixed(2)} L short</span>
                  ) : (
                    <span className="bm-tag green">OK</span>
                  )}
                </td>
                <td>{d.status}</td>
                <td>
                  {d.status === "PENDING" ? (
                    <input
                      type="number"
                      step="0.01"
                      style={{ width: 100 }}
                      value={buyingPrices[d.id] || ""}
                      onChange={(e) => setBuyingPrices((b) => ({ ...b, [d.id]: e.target.value }))}
                    />
                  ) : (
                    d.buyingPricePerLiter && `₦${Number(d.buyingPricePerLiter).toFixed(2)}`
                  )}
                </td>
                <td>
                  {d.status === "PENDING" && (
                    <>
                      <button className="bm-btn" onClick={() => confirm(d.id)}>
                        Confirm
                      </button>{" "}
                      <button className="bm-btn danger" onClick={() => reject(d.id)}>
                        Reject
                      </button>
                    </>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
