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
    const pumps = await prisma.pump.findMany({
      where: { active: true },
      include: { product: true, tank: true },
      orderBy: { id: "asc" },
    });
    res.json(pumps);
  })
);

const createSchema = z.object({
  name: z.string().min(1),
  productId: z.number().int(),
  tankId: z.number().int(),
});

const updateSchema = z.object({
  name: z.string().min(1).optional(),
  active: z.boolean().optional(),
});

router.post(
  "/",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const data = createSchema.parse(req.body);
    const pump = await prisma.pump.create({ data, include: { product: true, tank: true } });
    res.status(201).json(pump);
  })
);

router.patch(
  "/:id",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const data = updateSchema.parse(req.body);
    const pump = await prisma.pump.update({ where: { id }, data, include: { product: true, tank: true } });
    res.json(pump);
  })
);

module.exports = router;
