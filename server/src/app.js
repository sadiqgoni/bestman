const express = require("express");
const cors = require("cors");
const morgan = require("morgan");
const errorHandler = require("./middleware/errorHandler");

const authRoutes = require("./routes/auth.routes");
const usersRoutes = require("./routes/users.routes");
const productsRoutes = require("./routes/products.routes");
const tanksRoutes = require("./routes/tanks.routes");
const pumpsRoutes = require("./routes/pumps.routes");
const deliveriesRoutes = require("./routes/deliveries.routes");
const dailyEntriesRoutes = require("./routes/dailyEntries.routes");
const reportsRoutes = require("./routes/reports.routes");

const app = express();

app.use(cors({ origin: process.env.CLIENT_ORIGIN || "*" }));
app.use(express.json());
app.use(morgan("dev"));

app.get("/api/health", (req, res) => res.json({ status: "ok" }));

app.use("/api/auth", authRoutes);
app.use("/api/users", usersRoutes);
app.use("/api/products", productsRoutes);
app.use("/api/tanks", tanksRoutes);
app.use("/api/pumps", pumpsRoutes);
app.use("/api/deliveries", deliveriesRoutes);
app.use("/api/daily-entries", dailyEntriesRoutes);
app.use("/api/reports", reportsRoutes);

app.use((req, res) => res.status(404).json({ message: "Not found" }));
app.use(errorHandler);

module.exports = app;
