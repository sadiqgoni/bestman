const { PrismaClient } = require("@prisma/client");
const bcrypt = require("bcryptjs");

const prisma = new PrismaClient();

async function main() {
  const adminPassword = await bcrypt.hash("Admin@123", 10);
  const staffPassword = await bcrypt.hash("Staff@123", 10);

  await prisma.user.upsert({
    where: { username: "admin" },
    update: {},
    create: { name: "System Admin", username: "admin", password: adminPassword, role: "ADMIN" },
  });

  await prisma.user.upsert({
    where: { username: "staff1" },
    update: {},
    create: { name: "Station Attendant", username: "staff1", password: staffPassword, role: "STAFF" },
  });

  const pms = await prisma.product.upsert({
    where: { type: "PMS" },
    update: {},
    create: { type: "PMS", basePricePerLiter: 617.0, buyingPricePerLiter: 580.0 },
  });

  const diesel = await prisma.product.upsert({
    where: { type: "DIESEL" },
    update: {},
    create: { type: "DIESEL", basePricePerLiter: 1150.0, buyingPricePerLiter: 1080.0 },
  });

  const kerosene = await prisma.product.upsert({
    where: { type: "KEROSENE" },
    update: {},
    create: { type: "KEROSENE", basePricePerLiter: 1200.0, buyingPricePerLiter: 1100.0 },
  });

  const tank1 = await prisma.tank.create({
    data: { name: "PMS Tank 1", productId: pms.id, capacityLiters: 33000, currentStockLiters: 20000 },
  });
  const tank2 = await prisma.tank.create({
    data: { name: "PMS Tank 2", productId: pms.id, capacityLiters: 33000, currentStockLiters: 30000 },
  });
  const dieselTank = await prisma.tank.create({
    data: { name: "Diesel Tank 1", productId: diesel.id, capacityLiters: 20000, currentStockLiters: 12000 },
  });
  const keroseneTank = await prisma.tank.create({
    data: { name: "Kerosene Tank 1", productId: kerosene.id, capacityLiters: 10000, currentStockLiters: 5000 },
  });

  await prisma.pump.createMany({
    data: [
      { name: "Pump 1 (PMS)", productId: pms.id, tankId: tank1.id },
      { name: "Pump 2 (PMS)", productId: pms.id, tankId: tank2.id },
      { name: "Pump 3 (Diesel)", productId: diesel.id, tankId: dieselTank.id },
      { name: "Pump 4 (Kerosene)", productId: kerosene.id, tankId: keroseneTank.id },
    ],
  });

  console.log("Seed complete. Admin login: admin / Admin@123, Staff login: staff1 / Staff@123");
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
