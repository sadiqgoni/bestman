const { Router } = require("express");
const { z } = require("zod");
const prisma = require("../prismaClient");
const asyncHandler = require("../utils/asyncHandler");
const { authenticate, requireRole } = require("../middleware/auth");

const router = Router();
router.use(authenticate);

router.get(
  "/",
  asyncHandler(async (req, res) => {
    const tanks = await prisma.tank.findMany({
      include: { product: true },
      orderBy: { id: "asc" },
    });
    res.json(tanks);
  })
);

const createSchema = z.object({
  name: z.string().min(1),
  productId: z.number().int(),
  capacityLiters: z.number().positive(),
  currentStockLiters: z.number().nonnegative().default(0),
});

const updateSchema = z.object({
  name: z.string().min(1).optional(),
  capacityLiters: z.number().positive().optional(),
  active: z.boolean().optional(),
});

router.post(
  "/",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const data = createSchema.parse(req.body);
    const tank = await prisma.tank.create({ data, include: { product: true } });
    res.status(201).json(tank);
  })
);

router.patch(
  "/:id",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const data = updateSchema.parse(req.body);
    const tank = await prisma.tank.update({ where: { id }, data, include: { product: true } });
    res.json(tank);
  })
);

router.post(
  "/:id/dip",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const { measuredLiters } = z.object({ measuredLiters: z.number().nonnegative() }).parse(req.body);
    const tank = await prisma.tank.update({
      where: { id },
      data: { currentStockLiters: measuredLiters },
      include: { product: true },
    });
    res.json(tank);
  })
);

module.exports = router;
