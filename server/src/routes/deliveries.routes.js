const { Router } = require("express");
const { z } = require("zod");
const prisma = require("../prismaClient");
const asyncHandler = require("../utils/asyncHandler");
const { authenticate, requireRole } = require("../middleware/auth");

const router = Router();
router.use(authenticate);

const createSchema = z.object({
  tankerPlateNumber: z.string().min(1),
  supplierName: z.string().min(1),
  invoiceNumber: z.string().min(1),
  productId: z.number().int(),
  tankId: z.number().int(),
  waybillLiters: z.number().positive(),
  receivedLiters: z.number().positive(),
  driverNotes: z.string().optional(),
});

router.get(
  "/",
  asyncHandler(async (req, res) => {
    const { status } = req.query;
    const deliveries = await prisma.tankerDelivery.findMany({
      where: status ? { status: String(status).toUpperCase() } : undefined,
      include: {
        product: true,
        tank: true,
        createdBy: { select: { id: true, name: true, username: true } },
        confirmedBy: { select: { id: true, name: true, username: true } },
      },
      orderBy: { createdAt: "desc" },
    });
    res.json(deliveries);
  })
);

router.post(
  "/",
  asyncHandler(async (req, res) => {
    const data = createSchema.parse(req.body);
    const varianceLiters = data.waybillLiters - data.receivedLiters;

    const delivery = await prisma.tankerDelivery.create({
      data: {
        ...data,
        varianceLiters,
        createdById: req.user.id,
      },
      include: { product: true, tank: true },
    });

    res.status(201).json({
      ...delivery,
      varianceFlagged: varianceLiters !== 0,
    });
  })
);

const confirmSchema = z.object({
  buyingPricePerLiter: z.number().positive(),
});

router.post(
  "/:id/confirm",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const { buyingPricePerLiter } = confirmSchema.parse(req.body);

    const delivery = await prisma.tankerDelivery.findUnique({ where: { id } });
    if (!delivery) return res.status(404).json({ message: "Delivery not found" });
    if (delivery.status !== "PENDING") {
      return res.status(400).json({ message: "Delivery already processed" });
    }

    const totalCost = Number(delivery.receivedLiters) * buyingPricePerLiter;

    const [updatedDelivery] = await prisma.$transaction([
      prisma.tankerDelivery.update({
        where: { id },
        data: {
          status: "CONFIRMED",
          buyingPricePerLiter,
          totalCost,
          confirmedById: req.user.id,
          confirmedAt: new Date(),
        },
        include: { product: true, tank: true },
      }),
      prisma.tank.update({
        where: { id: delivery.tankId },
        data: { currentStockLiters: { increment: delivery.receivedLiters } },
      }),
      prisma.product.update({
        where: { id: delivery.productId },
        data: { buyingPricePerLiter },
      }),
    ]);

    res.json(updatedDelivery);
  })
);

router.post(
  "/:id/reject",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const delivery = await prisma.tankerDelivery.update({
      where: { id },
      data: { status: "REJECTED", confirmedById: req.user.id, confirmedAt: new Date() },
    });
    res.json(delivery);
  })
);

module.exports = router;
