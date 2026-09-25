import { useEffect, useMemo, useState } from "react";
import api from "../../api/client";

const emptyForm = {
  tankerPlateNumber: "",
  supplierName: "",
  invoiceNumber: "",
  productId: "",
  tankId: "",
  waybillLiters: "",
  receivedLiters: "",
  driverNotes: "",
};

export default function TankerDeliveryForm() {
  const [products, setProducts] = useState([]);
  const [tanks, setTanks] = useState([]);
  const [form, setForm] = useState(emptyForm);
  const [message, setMessage] = useState(null);
  const [error, setError] = useState("");
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    api.get("/products").then((res) => setProducts(res.data));
    api.get("/tanks").then((res) => setTanks(res.data));
  }, []);

  const filteredTanks = useMemo(
    () => tanks.filter((t) => String(t.productId) === String(form.productId)),
    [tanks, form.productId]
  );

  const variance = useMemo(() => {
    const waybill = Number(form.waybillLiters);
    const received = Number(form.receivedLiters);
    if (!waybill || !received) return null;
    return waybill - received;
  }, [form.waybillLiters, form.receivedLiters]);

  function update(field, value) {
    setForm((f) => ({ ...f, [field]: value }));
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setMessage(null);
    setSubmitting(true);
    try {
      const res = await api.post("/deliveries", {
        tankerPlateNumber: form.tankerPlateNumber,
        supplierName: form.supplierName,
        invoiceNumber: form.invoiceNumber,
        productId: Number(form.productId),
        tankId: Number(form.tankId),
        waybillLiters: Number(form.waybillLiters),
        receivedLiters: Number(form.receivedLiters),
        driverNotes: form.driverNotes || undefined,
      });
      setMessage(
        res.data.varianceFlagged
          ? `Delivery logged. Variance of ${res.data.varianceLiters}L flagged to Admin.`
          : "Delivery logged successfully with no variance."
      );
      setForm(emptyForm);
    } catch (err) {
      setError(err.response?.data?.message || "Failed to log delivery");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div>
      <h2>Tanker Delivery Intake</h2>
      <div className="bm-card">
        {message && <div className="bm-alert success">{message}</div>}
        {error && <div className="bm-alert error">{error}</div>}
        {variance !== null && variance !== 0 && (
          <div className="bm-alert warning">
            Variance Alert: Waybill Liters − Received Liters = {variance.toFixed(2)} L discrepancy will be
            flagged to Admin.
          </div>
        )}
        <form onSubmit={handleSubmit}>
          <div className="bm-form-row">
            <div className="bm-field">
              <label>Tanker Plate Number</label>
              <input value={form.tankerPlateNumber} onChange={(e) => update("tankerPlateNumber", e.target.value)} required />
            </div>
            <div className="bm-field">
              <label>Supplier Name</label>
              <input value={form.supplierName} onChange={(e) => update("supplierName", e.target.value)} required />
            </div>
            <div className="bm-field">
              <label>Invoice / Waybill Number</label>
              <input value={form.invoiceNumber} onChange={(e) => update("invoiceNumber", e.target.value)} required />
            </div>
          </div>
          <div className="bm-form-row">
            <div className="bm-field">
              <label>Product Type</label>
              <select value={form.productId} onChange={(e) => update("productId", e.target.value)} required>
                <option value="">Select product</option>
                {products.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.type}
                  </option>
                ))}
              </select>
            </div>
            <div className="bm-field">
              <label>Destination Tank</label>
              <select value={form.tankId} onChange={(e) => update("tankId", e.target.value)} required>
                <option value="">Select tank</option>
                {filteredTanks.map((t) => (
                  <option key={t.id} value={t.id}>
                    {t.name}
                  </option>
                ))}
              </select>
            </div>
          </div>
          <div className="bm-form-row">
            <div className="bm-field">
              <label>Delivered Liters (Waybill)</label>
              <input type="number" step="0.01" value={form.waybillLiters} onChange={(e) => update("waybillLiters", e.target.value)} required />
            </div>
            <div className="bm-field">
              <label>Discharged / Received Liters (Dipping)</label>
              <input type="number" step="0.01" value={form.receivedLiters} onChange={(e) => update("receivedLiters", e.target.value)} required />
            </div>
          </div>
          <div className="bm-field" style={{ marginBottom: 16 }}>
            <label>Driver Notes (optional)</label>
            <textarea rows={2} value={form.driverNotes} onChange={(e) => update("driverNotes", e.target.value)} />
          </div>
          <button className="bm-btn" type="submit" disabled={submitting}>
            {submitting ? "Submitting..." : "Log Delivery"}
          </button>
        </form>
      </div>
    </div>
  );
}
