import { useEffect, useState } from "react";
import api from "../../api/client";

export default function StockView() {
  const [stock, setStock] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    api
      .get("/reports/stock")
      .then((res) => setStock(res.data))
      .catch((err) => setError(err.response?.data?.message || "Failed to load stock"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p>Loading stock inventory...</p>;

  return (
    <div>
      <h2 style={{ marginBottom: 16 }}>Current Stock Inventory</h2>
      {error && <div className="bm-alert error">{error}</div>}
      <div className="bm-grid">
        {stock.map((t) => (
          <div className="bm-card" key={t.tankId}>
            <h3>{t.tankName}</h3>
            <p style={{ color: "#5b6472", fontSize: 13, marginTop: -8 }}>{t.product}</p>
            <div className="bm-stat" style={{ marginBottom: 10 }}>
              <div className="label">Remaining Stock</div>
              <div className="value">{t.remainingStockLiters.toLocaleString()} L</div>
            </div>
            <div className="bm-stat" style={{ marginBottom: 10 }}>
              <div className="label">Selling Price / Liter</div>
              <div className="value">₦{t.basePricePerLiter.toFixed(2)}</div>
            </div>
            <div className="bm-stat">
              <div className="label">Expected Total Value</div>
              <div className="value">₦{t.expectedSellingValue.toLocaleString(undefined, { maximumFractionDigits: 2 })}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
