import { useEffect, useMemo, useState } from "react";
import api from "../../api/client";

const EXPENSE_CATEGORIES = ["MAINTENANCE", "SALARY", "LOGISTICS", "UTILITIES", "MISCELLANEOUS", "OTHER"];

function todayISO() {
  return new Date().toISOString().slice(0, 10);
}

export default function DailyTransactionPage() {
  const [pumps, setPumps] = useState([]);
  const [entryDate, setEntryDate] = useState(todayISO());
  const [readings, setReadings] = useState({}); // pumpId -> { opening, closing, price }
  const [expenses, setExpenses] = useState([]);
  const [payments, setPayments] = useState({ cash: "", pos: "", bank: "" });
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    api.get("/pumps").then((res) => {
      setPumps(res.data);
      const initial = {};
      res.data.forEach((p) => {
        initial[p.id] = { opening: "", closing: "", price: String(p.product.basePricePerLiter) };
      });
      setReadings(initial);
    });
  }, []);

  function updateReading(pumpId, field, value) {
    setReadings((r) => ({ ...r, [pumpId]: { ...r[pumpId], [field]: value } }));
  }

  const rows = useMemo(
    () =>
      pumps.map((p) => {
        const r = readings[p.id] || { opening: "", closing: "", price: "" };
        const opening = Number(r.opening) || 0;
        const closing = Number(r.closing) || 0;
        const price = Number(r.price) || 0;
        const litersSold = closing > opening ? closing - opening : 0;
        const totalAmount = litersSold * price;
        const basePrice = Number(p.product.basePricePerLiter);
        const priceVariance = price > basePrice;
        return { pump: p, opening, closing, price, litersSold, totalAmount, basePrice, priceVariance };
      }),
    [pumps, readings]
  );

  const fuelGrandTotalLiters = rows.reduce((sum, r) => sum + r.litersSold, 0);
  const fuelGrandTotalAmount = rows.reduce((sum, r) => sum + r.totalAmount, 0);

  const totalExpenses = expenses.reduce((sum, e) => sum + (Number(e.amount) || 0), 0);
  const netCashRevenue = fuelGrandTotalAmount - totalExpenses;

  const paymentSum = (Number(payments.cash) || 0) + (Number(payments.pos) || 0) + (Number(payments.bank) || 0);
  const paymentMismatch = Math.abs(paymentSum - fuelGrandTotalAmount) > 0.01;

  function addExpense() {
    setExpenses((e) => [...e, { description: "", category: "OTHER", amount: "" }]);
  }

  function updateExpense(idx, field, value) {
    setExpenses((e) => e.map((exp, i) => (i === idx ? { ...exp, [field]: value } : exp)));
  }

  function removeExpense(idx) {
    setExpenses((e) => e.filter((_, i) => i !== idx));
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (paymentMismatch) {
      setError(
        `Payment breakdown (₦${paymentSum.toFixed(2)}) must equal Fuel Grand Total (₦${fuelGrandTotalAmount.toFixed(2)}) before submission.`
      );
      return;
    }

    const pumpReadings = rows
      .filter((r) => r.opening || r.closing)
      .map((r) => ({
        pumpId: r.pump.id,
        openingReading: r.opening,
        closingReading: r.closing,
        unitSellingPrice: r.price,
      }));

    if (pumpReadings.length === 0) {
      setError("Enter at least one pump reading.");
      return;
    }

    setSubmitting(true);
    try {
      await api.post("/daily-entries", {
        entryDate,
        pumpReadings,
        expenses: expenses
          .filter((exp) => exp.description && exp.amount)
          .map((exp) => ({ ...exp, amount: Number(exp.amount) })),
        cashAmount: Number(payments.cash) || 0,
        posAmount: Number(payments.pos) || 0,
        bankDepositAmount: Number(payments.bank) || 0,
      });
      setSuccess("Daily transaction submitted successfully.");
      setExpenses([]);
      setPayments({ cash: "", pos: "", bank: "" });
      setReadings((r) => {
        const reset = {};
        Object.keys(r).forEach((k) => (reset[k] = { ...r[k], opening: "", closing: "" }));
        return reset;
      });
    } catch (err) {
      setError(err.response?.data?.message || "Failed to submit daily entry");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <h2>Daily Transaction Entry</h2>
      {error && <div className="bm-alert error">{error}</div>}
      {success && <div className="bm-alert success">{success}</div>}

      <div className="bm-card">
        <div className="bm-field" style={{ maxWidth: 220, marginBottom: 16 }}>
          <label>Entry Date</label>
          <input type="date" value={entryDate} onChange={(e) => setEntryDate(e.target.value)} required />
        </div>

        <h3>1. Pump Meter Readings</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Pump</th>
              <th>Opening</th>
              <th>Closing</th>
              <th>Liters Sold</th>
              <th>Unit Price</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((r) => (
              <tr key={r.pump.id}>
                <td>{r.pump.name}</td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style={{ width: 100 }}
                    value={readings[r.pump.id]?.opening || ""}
                    onChange={(e) => updateReading(r.pump.id, "opening", e.target.value)}
                  />
                </td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style={{ width: 100 }}
                    value={readings[r.pump.id]?.closing || ""}
                    onChange={(e) => updateReading(r.pump.id, "closing", e.target.value)}
                  />
                </td>
                <td>{r.litersSold.toFixed(2)}</td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    style={{ width: 100 }}
                    value={readings[r.pump.id]?.price || ""}
                    onChange={(e) => updateReading(r.pump.id, "price", e.target.value)}
                  />
                  {r.priceVariance && <div className="bm-tag red" style={{ marginTop: 4 }}>Excess Price</div>}
                </td>
                <td>₦{r.totalAmount.toFixed(2)}</td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="bm-grid" style={{ marginTop: 16 }}>
          <div className="bm-stat">
            <div className="label">Fuel Grand Total (Liters)</div>
            <div className="value">{fuelGrandTotalLiters.toFixed(2)} L</div>
          </div>
          <div className="bm-stat">
            <div className="label">Fuel Grand Total (Revenue)</div>
            <div className="value">₦{fuelGrandTotalAmount.toFixed(2)}</div>
          </div>
        </div>
      </div>

      <div className="bm-card">
        <h3>2. Payment Breakdown</h3>
        <div className="bm-form-row">
          <div className="bm-field">
            <label>Cash Amount</label>
            <input type="number" step="0.01" value={payments.cash} onChange={(e) => setPayments((p) => ({ ...p, cash: e.target.value }))} />
          </div>
          <div className="bm-field">
            <label>POS / Bank Transfer Amount</label>
            <input type="number" step="0.01" value={payments.pos} onChange={(e) => setPayments((p) => ({ ...p, pos: e.target.value }))} />
          </div>
          <div className="bm-field">
            <label>Direct Bank Deposit Amount</label>
            <input type="number" step="0.01" value={payments.bank} onChange={(e) => setPayments((p) => ({ ...p, bank: e.target.value }))} />
          </div>
        </div>
        {paymentMismatch && paymentSum > 0 && (
          <div className="bm-alert warning">
            Payment total ₦{paymentSum.toFixed(2)} does not match Fuel Grand Total ₦{fuelGrandTotalAmount.toFixed(2)}.
          </div>
        )}
      </div>

      <div className="bm-card">
        <h3>3. Daily Expenses</h3>
        <table className="bm-table">
          <thead>
            <tr>
              <th>Item Description</th>
              <th>Category</th>
              <th>Amount</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {expenses.map((exp, idx) => (
              <tr key={idx}>
                <td>
                  <input value={exp.description} onChange={(e) => updateExpense(idx, "description", e.target.value)} />
                </td>
                <td>
                  <select value={exp.category} onChange={(e) => updateExpense(idx, "category", e.target.value)}>
                    {EXPENSE_CATEGORIES.map((c) => (
                      <option key={c} value={c}>
                        {c}
                      </option>
                    ))}
                  </select>
                </td>
                <td>
                  <input type="number" step="0.01" style={{ width: 120 }} value={exp.amount} onChange={(e) => updateExpense(idx, "amount", e.target.value)} />
                </td>
                <td>
                  <button type="button" className="bm-btn danger" onClick={() => removeExpense(idx)}>
                    Remove
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        <button type="button" className="bm-btn secondary" onClick={addExpense} style={{ marginTop: 10 }}>
          + Add Expense
        </button>
        <div className="bm-stat" style={{ marginTop: 16, maxWidth: 260 }}>
          <div className="label">Expense Total</div>
          <div className="value">₦{totalExpenses.toFixed(2)}</div>
        </div>
      </div>

      <div className="bm-card">
        <h3>4. Net Daily Reconciliation</h3>
        <div className="bm-stat" style={{ maxWidth: 320 }}>
          <div className="label">Actual Net Cash Revenue (Fuel Total − Expenses)</div>
          <div className={netCashRevenue < 0 ? "value bm-loss" : "value bm-profit"}>
            ₦{netCashRevenue.toFixed(2)}
          </div>
        </div>
      </div>

      <button className="bm-btn" type="submit" disabled={submitting}>
        {submitting ? "Submitting..." : "Submit Daily Transaction"}
      </button>
    </form>
  );
}
