const { Router } = require("express");
const prisma = require("../prismaClient");
const asyncHandler = require("../utils/asyncHandler");
const { authenticate, requireRole } = require("../middleware/auth");

const router = Router();
router.use(authenticate);

// Current stock & value snapshot per tank
router.get(
  "/stock",
  asyncHandler(async (req, res) => {
    const tanks = await prisma.tank.findMany({ include: { product: true }, orderBy: { id: "asc" } });
    const stock = tanks.map((t) => {
      const remainingStock = Number(t.currentStockLiters);
      const buyingPrice = Number(t.product.buyingPricePerLiter);
      const sellingPrice = Number(t.product.basePricePerLiter);
      return {
        tankId: t.id,
        tankName: t.name,
        product: t.product.type,
        capacityLiters: Number(t.capacityLiters),
        remainingStockLiters: remainingStock,
        basePricePerLiter: sellingPrice,
        buyingPricePerLiter: buyingPrice,
        expectedSellingValue: remainingStock * sellingPrice,
        remainingValueAtCost: remainingStock * buyingPrice,
      };
    });
    res.json(stock);
  })
);

// Delivery shortage/variance alerts for Admin
router.get(
  "/delivery-alerts",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const deliveries = await prisma.tankerDelivery.findMany({
      where: { varianceLiters: { not: 0 } },
      include: { product: true, tank: true, createdBy: { select: { id: true, name: true } } },
      orderBy: { createdAt: "desc" },
    });
    res.json(deliveries);
  })
);

// Price variance alerts for Admin
router.get(
  "/price-alerts",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const readings = await prisma.pumpReading.findMany({
      where: { priceVariance: true },
      include: {
        pump: { include: { product: true } },
        dailyEntry: { include: { staff: { select: { id: true, name: true } } } },
      },
      orderBy: { createdAt: "desc" },
    });
    res.json(readings);
  })
);

// Daily profit & loss report, optionally filtered by date range
router.get(
  "/profit-loss",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const { from, to } = req.query;
    const where = {};
    if (from || to) {
      where.entryDate = {};
      if (from) where.entryDate.gte = new Date(String(from));
      if (to) where.entryDate.lte = new Date(String(to));
    }

    const entries = await prisma.dailyEntry.findMany({
      where,
      include: { staff: { select: { id: true, name: true } } },
      orderBy: { entryDate: "desc" },
    });

    const rows = entries.map((e) => ({
      id: e.id,
      entryDate: e.entryDate,
      staff: e.staff.name,
      fuelGrandTotalAmount: Number(e.fuelGrandTotalAmount),
      totalExpenses: Number(e.totalExpenses),
      grossProfit: Number(e.grossProfit),
      netProfit: Number(e.netProfit),
      isLoss: Number(e.netProfit) < 0,
    }));

    const totals = rows.reduce(
      (acc, r) => ({
        fuelGrandTotalAmount: acc.fuelGrandTotalAmount + r.fuelGrandTotalAmount,
        totalExpenses: acc.totalExpenses + r.totalExpenses,
        grossProfit: acc.grossProfit + r.grossProfit,
        netProfit: acc.netProfit + r.netProfit,
      }),
      { fuelGrandTotalAmount: 0, totalExpenses: 0, grossProfit: 0, netProfit: 0 }
    );

    res.json({ rows, totals });
  })
);

module.exports = router;
