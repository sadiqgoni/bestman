const { Router } = require("express");
const { z } = require("zod");
const prisma = require("../prismaClient");
const asyncHandler = require("../utils/asyncHandler");
const { authenticate, requireRole } = require("../middleware/auth");

const router = Router();
router.use(authenticate);

const pumpReadingSchema = z.object({
  pumpId: z.number().int(),
  openingReading: z.number().nonnegative(),
  closingReading: z.number().nonnegative(),
  unitSellingPrice: z.number().positive(),
});

const expenseSchema = z.object({
  description: z.string().min(1),
  category: z
    .enum(["MAINTENANCE", "SALARY", "LOGISTICS", "UTILITIES", "MISCELLANEOUS", "OTHER"])
    .default("OTHER"),
  amount: z.number().nonnegative(),
});

const createSchema = z.object({
  entryDate: z.string(), // ISO date
  pumpReadings: z.array(pumpReadingSchema).min(1),
  expenses: z.array(expenseSchema).default([]),
  cashAmount: z.number().nonnegative(),
  posAmount: z.number().nonnegative(),
  bankDepositAmount: z.number().nonnegative(),
});

router.get(
  "/",
  asyncHandler(async (req, res) => {
    const { staffId, from, to } = req.query;
    const where = {};
    if (staffId) where.staffId = Number(staffId);
    if (req.user.role === "STAFF") where.staffId = req.user.id;
    if (from || to) {
      where.entryDate = {};
      if (from) where.entryDate.gte = new Date(String(from));
      if (to) where.entryDate.lte = new Date(String(to));
    }

    const entries = await prisma.dailyEntry.findMany({
      where,
      include: {
        staff: { select: { id: true, name: true, username: true } },
        pumpReadings: { include: { pump: { include: { product: true } } } },
        expenses: true,
      },
      orderBy: { entryDate: "desc" },
    });
    res.json(entries);
  })
);

router.get(
  "/:id",
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const entry = await prisma.dailyEntry.findUnique({
      where: { id },
      include: {
        staff: { select: { id: true, name: true, username: true } },
        pumpReadings: { include: { pump: { include: { product: true, tank: true } } } },
        expenses: true,
      },
    });
    if (!entry) return res.status(404).json({ message: "Daily entry not found" });
    res.json(entry);
  })
);

router.post(
  "/",
  asyncHandler(async (req, res) => {
    const data = createSchema.parse(req.body);

    // Validate readings and compute totals
    const pumps = await prisma.pump.findMany({
      where: { id: { in: data.pumpReadings.map((r) => r.pumpId) } },
      include: { product: true, tank: true },
    });
    const pumpMap = new Map(pumps.map((p) => [p.id, p]));

    let fuelGrandTotalLiters = 0;
    let fuelGrandTotalAmount = 0;
    let grossProfit = 0;
    const tankDecrements = new Map(); // tankId -> liters

    const readingsData = data.pumpReadings.map((r) => {
      const pump = pumpMap.get(r.pumpId);
      if (!pump) throw Object.assign(new Error(`Pump ${r.pumpId} not found`), { status: 400 });
      if (r.closingReading < r.openingReading) {
        throw Object.assign(
          new Error(`Closing reading must be >= opening reading for pump ${pump.name}`),
          { status: 400 }
        );
      }
      const litersSold = r.closingReading - r.openingReading;
      const totalAmount = litersSold * r.unitSellingPrice;
      const basePrice = Number(pump.product.basePricePerLiter);
      const priceVariance = r.unitSellingPrice > basePrice;

      fuelGrandTotalLiters += litersSold;
      fuelGrandTotalAmount += totalAmount;
      grossProfit += (r.unitSellingPrice - Number(pump.product.buyingPricePerLiter)) * litersSold;

      tankDecrements.set(pump.tankId, (tankDecrements.get(pump.tankId) || 0) + litersSold);

      return {
        pumpId: r.pumpId,
        openingReading: r.openingReading,
        closingReading: r.closingReading,
        litersSold,
        unitSellingPrice: r.unitSellingPrice,
        totalAmount,
        basePriceAtEntry: basePrice,
        priceVariance,
      };
    });

    const totalExpenses = data.expenses.reduce((sum, e) => sum + e.amount, 0);

    const paymentSum = data.cashAmount + data.posAmount + data.bankDepositAmount;
    if (Math.abs(paymentSum - fuelGrandTotalAmount) > 0.01) {
      return res.status(400).json({
        message: `Payment breakdown (${paymentSum.toFixed(2)}) must equal Fuel Grand Total (${fuelGrandTotalAmount.toFixed(2)})`,
      });
    }

    const netCashRevenue = fuelGrandTotalAmount - totalExpenses;
    const netProfit = grossProfit - totalExpenses;

    const entry = await prisma.$transaction(async (tx) => {
      const created = await tx.dailyEntry.create({
        data: {
          entryDate: new Date(data.entryDate),
          staffId: req.user.id,
          fuelGrandTotalLiters,
          fuelGrandTotalAmount,
          cashAmount: data.cashAmount,
          posAmount: data.posAmount,
          bankDepositAmount: data.bankDepositAmount,
          totalExpenses,
          netCashRevenue,
          grossProfit,
          netProfit,
          pumpReadings: { create: readingsData },
          expenses: { create: data.expenses },
        },
        include: { pumpReadings: true, expenses: true },
      });

      for (const [tankId, liters] of tankDecrements.entries()) {
        await tx.tank.update({
          where: { id: tankId },
          data: { currentStockLiters: { decrement: liters } },
        });
      }

      return created;
    });

    res.status(201).json(entry);
  })
);

module.exports = router;
