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
    const products = await prisma.product.findMany({ orderBy: { type: "asc" } });
    res.json(products);
  })
);

const priceSchema = z.object({
  basePricePerLiter: z.number().positive(),
});

router.patch(
  "/:id/price",
  requireRole("ADMIN"),
  asyncHandler(async (req, res) => {
    const id = Number(req.params.id);
    const { basePricePerLiter } = priceSchema.parse(req.body);
    const product = await prisma.product.update({
      where: { id },
      data: { basePricePerLiter },
    });
    res.json(product);
  })
);

module.exports = router;
