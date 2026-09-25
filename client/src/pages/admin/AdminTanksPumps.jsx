import { useEffect, useState } from "react";
import api from "../../api/client";

export default function AdminTanksPumps() {
  const [products, setProducts] = useState([]);
  const [tanks, setTanks] = useState([]);
  const [pumps, setPumps] = useState([]);
  const [tankForm, setTankForm] = useState({ name: "", productId: "", capacityLiters: "", currentStockLiters: "" });
  const [pumpForm, setPumpForm] = useState({ name: "", productId: "", tankId: "" });
  const [dipValues, setDipValues] = useState({});
  const [error, setError] = useState("");

  function loadAll() {
    api.get("/products").then((res) => setProducts(res.data));
    api.get("/tanks").then((res) => setTanks(res.data));
    api.get("/pumps").then((res) => setPumps(res.data));
  }

  useEffect(loadAll, []);

  async function updatePrice(productId, value) {
    await api.patch(`/products/${productId}/price`, { basePricePerLiter: Number(value) });
    loadAll();
  }

  async function createTank(e) {
    e.preventDefault();
    setError("");
    try {
      await api.post("/tanks", {
        name: tankForm.name,
        productId: Number(tankForm.productId),
        capacityLiters: Number(tankForm.capacityLiters),
        currentStockLiters: Number(tankForm.currentStockLiters) || 0,
      });
      setTankForm({ name: "", productId: "", capacityLiters: "", currentStockLiters: "" });
      loadAll();
    } catch (err) {
      setError(err.response?.data?.message || "Failed to create tank");
    }
  }

  async function createPump(e) {
    e.preventDefault();
    setError("");
    try {
      await api.post("/pumps", {
        name: pumpForm.name,
        productId: Number(pumpForm.productId),
        tankId: Number(pumpForm.tankId),
      });
      setPumpForm({ name: "", productId: "", tankId: "" });
      loadAll();
    } catch (err) {
      setError(err.response?.data?.message || "Failed to create pump");
    }
  }

  async function submitDip(tankId) {
    const value = dipValues[tankId];
    if (value === undefined || value === "") return;
    await api.post(`/tanks/${tankId}/dip`, { measuredLiters: Number(value) });
    setDipValues((d) => ({ ...d, [tankId]: "" }));
    loadAll();
  }

  return (
    <div>
      <h2>Tanks, Pumps & Base Prices</h2>
      {error && <div className="bm-alert error">{error}</div>}

      <div className="bm-card">
        <h3>Product Base Selling Prices</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Base Selling Price / Liter</th>
              <th>Current Buying Price / Liter</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {products.map((p) => (
              <ProductPriceRow key={p.id} product={p} onSave={updatePrice} />
            ))}
          </tbody>
        </table>
      </div>

      <div className="bm-card">
        <h3>Tanks</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Product</th>
              <th>Capacity (L)</th>
              <th>Current Stock (L)</th>
              <th>Dip / Measurement Correction</th>
            </tr>
          </thead>
          <tbody>
            {tanks.map((t) => (
              <tr key={t.id}>
                <td>{t.name}</td>
                <td>{t.product.type}</td>
                <td>{Number(t.capacityLiters).toLocaleString()}</td>
                <td>{Number(t.currentStockLiters).toLocaleString()}</td>
                <td>
                  <input
                    type="number"
                    style={{ width: 100 }}
                    value={dipValues[t.id] || ""}
                    onChange={(e) => setDipValues((d) => ({ ...d, [t.id]: e.target.value }))}
                  />
                  <button className="bm-btn secondary" style={{ marginLeft: 6 }} onClick={() => submitDip(t.id)}>
                    Update
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        <h4>Add Tank</h4>
        <form onSubmit={createTank} className="bm-form-row">
          <div className="bm-field">
            <label>Name</label>
            <input value={tankForm.name} onChange={(e) => setTankForm((f) => ({ ...f, name: e.target.value }))} required />
          </div>
          <div className="bm-field">
            <label>Product</label>
            <select value={tankForm.productId} onChange={(e) => setTankForm((f) => ({ ...f, productId: e.target.value }))} required>
              <option value="">Select</option>
              {products.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.type}
                </option>
              ))}
            </select>
          </div>
          <div className="bm-field">
            <label>Capacity (L)</label>
            <input type="number" value={tankForm.capacityLiters} onChange={(e) => setTankForm((f) => ({ ...f, capacityLiters: e.target.value }))} required />
          </div>
          <div className="bm-field">
            <label>Initial Stock (L)</label>
            <input type="number" value={tankForm.currentStockLiters} onChange={(e) => setTankForm((f) => ({ ...f, currentStockLiters: e.target.value }))} />
          </div>
          <div style={{ alignSelf: "end" }}>
            <button className="bm-btn" type="submit">
              Add Tank
            </button>
          </div>
        </form>
      </div>

      <div className="bm-card">
        <h3>Pumps</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Product</th>
              <th>Tank</th>
            </tr>
          </thead>
          <tbody>
            {pumps.map((p) => (
              <tr key={p.id}>
                <td>{p.name}</td>
                <td>{p.product.type}</td>
                <td>{p.tank.name}</td>
              </tr>
            ))}
          </tbody>
        </table>
        <h4>Add Pump</h4>
        <form onSubmit={createPump} className="bm-form-row">
          <div className="bm-field">
            <label>Name</label>
            <input value={pumpForm.name} onChange={(e) => setPumpForm((f) => ({ ...f, name: e.target.value }))} required />
          </div>
          <div className="bm-field">
            <label>Product</label>
            <select value={pumpForm.productId} onChange={(e) => setPumpForm((f) => ({ ...f, productId: e.target.value }))} required>
              <option value="">Select</option>
              {products.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.type}
                </option>
              ))}
            </select>
          </div>
          <div className="bm-field">
            <label>Tank</label>
            <select value={pumpForm.tankId} onChange={(e) => setPumpForm((f) => ({ ...f, tankId: e.target.value }))} required>
              <option value="">Select</option>
              {tanks
                .filter((t) => String(t.productId) === String(pumpForm.productId))
                .map((t) => (
                  <option key={t.id} value={t.id}>
                    {t.name}
                  </option>
                ))}
            </select>
          </div>
          <div style={{ alignSelf: "end" }}>
            <button className="bm-btn" type="submit">
              Add Pump
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

function ProductPriceRow({ product, onSave }) {
  const [price, setPrice] = useState(String(product.basePricePerLiter));

  return (
    <tr>
      <td>{product.type}</td>
      <td>
        <input type="number" step="0.01" style={{ width: 120 }} value={price} onChange={(e) => setPrice(e.target.value)} />
      </td>
      <td>₦{Number(product.buyingPricePerLiter).toFixed(2)}</td>
      <td>
        <button className="bm-btn secondary" onClick={() => onSave(product.id, price)}>
          Save
        </button>
      </td>
    </tr>
  );
}
