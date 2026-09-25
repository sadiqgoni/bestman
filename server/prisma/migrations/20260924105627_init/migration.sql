-- CreateTable
CREATE TABLE `users` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL,
    `username` VARCHAR(191) NOT NULL,
    `password` VARCHAR(191) NOT NULL,
    `role` ENUM('ADMIN', 'STAFF') NOT NULL,
    `active` BOOLEAN NOT NULL DEFAULT true,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `users_username_key`(`username`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `products` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `type` ENUM('PMS', 'DIESEL', 'KEROSENE') NOT NULL,
    `basePricePerLiter` DECIMAL(10, 2) NOT NULL,
    `buyingPricePerLiter` DECIMAL(10, 2) NOT NULL DEFAULT 0,
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `products_type_key`(`type`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `tanks` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL,
    `productId` INTEGER NOT NULL,
    `capacityLiters` DECIMAL(12, 2) NOT NULL,
    `currentStockLiters` DECIMAL(12, 2) NOT NULL DEFAULT 0,
    `active` BOOLEAN NOT NULL DEFAULT true,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `pumps` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL,
    `productId` INTEGER NOT NULL,
    `tankId` INTEGER NOT NULL,
    `active` BOOLEAN NOT NULL DEFAULT true,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `tanker_deliveries` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `tankerPlateNumber` VARCHAR(191) NOT NULL,
    `supplierName` VARCHAR(191) NOT NULL,
    `invoiceNumber` VARCHAR(191) NOT NULL,
    `productId` INTEGER NOT NULL,
    `tankId` INTEGER NOT NULL,
    `waybillLiters` DECIMAL(12, 2) NOT NULL,
    `receivedLiters` DECIMAL(12, 2) NOT NULL,
    `varianceLiters` DECIMAL(12, 2) NOT NULL,
    `buyingPricePerLiter` DECIMAL(10, 2) NULL,
    `totalCost` DECIMAL(14, 2) NULL,
    `driverNotes` VARCHAR(191) NULL,
    `status` ENUM('PENDING', 'CONFIRMED', 'REJECTED') NOT NULL DEFAULT 'PENDING',
    `createdById` INTEGER NOT NULL,
    `confirmedById` INTEGER NULL,
    `confirmedAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `daily_entries` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `entryDate` DATE NOT NULL,
    `staffId` INTEGER NOT NULL,
    `status` ENUM('DRAFT', 'SUBMITTED') NOT NULL DEFAULT 'SUBMITTED',
    `fuelGrandTotalLiters` DECIMAL(12, 2) NOT NULL DEFAULT 0,
    `fuelGrandTotalAmount` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `cashAmount` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `posAmount` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `bankDepositAmount` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `totalExpenses` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `netCashRevenue` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `grossProfit` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `netProfit` DECIMAL(14, 2) NOT NULL DEFAULT 0,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `daily_entries_staffId_entryDate_key`(`staffId`, `entryDate`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `pump_readings` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dailyEntryId` INTEGER NOT NULL,
    `pumpId` INTEGER NOT NULL,
    `openingReading` DECIMAL(12, 2) NOT NULL,
    `closingReading` DECIMAL(12, 2) NOT NULL,
    `litersSold` DECIMAL(12, 2) NOT NULL,
    `unitSellingPrice` DECIMAL(10, 2) NOT NULL,
    `totalAmount` DECIMAL(14, 2) NOT NULL,
    `basePriceAtEntry` DECIMAL(10, 2) NOT NULL,
    `priceVariance` BOOLEAN NOT NULL DEFAULT false,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `expenses` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `dailyEntryId` INTEGER NOT NULL,
    `description` VARCHAR(191) NOT NULL,
    `category` ENUM('MAINTENANCE', 'SALARY', 'LOGISTICS', 'UTILITIES', 'MISCELLANEOUS', 'OTHER') NOT NULL DEFAULT 'OTHER',
    `amount` DECIMAL(14, 2) NOT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `tanks` ADD CONSTRAINT `tanks_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `pumps` ADD CONSTRAINT `pumps_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `pumps` ADD CONSTRAINT `pumps_tankId_fkey` FOREIGN KEY (`tankId`) REFERENCES `tanks`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `tanker_deliveries` ADD CONSTRAINT `tanker_deliveries_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `tanker_deliveries` ADD CONSTRAINT `tanker_deliveries_tankId_fkey` FOREIGN KEY (`tankId`) REFERENCES `tanks`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `tanker_deliveries` ADD CONSTRAINT `tanker_deliveries_createdById_fkey` FOREIGN KEY (`createdById`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `tanker_deliveries` ADD CONSTRAINT `tanker_deliveries_confirmedById_fkey` FOREIGN KEY (`confirmedById`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `daily_entries` ADD CONSTRAINT `daily_entries_staffId_fkey` FOREIGN KEY (`staffId`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `pump_readings` ADD CONSTRAINT `pump_readings_dailyEntryId_fkey` FOREIGN KEY (`dailyEntryId`) REFERENCES `daily_entries`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `pump_readings` ADD CONSTRAINT `pump_readings_pumpId_fkey` FOREIGN KEY (`pumpId`) REFERENCES `pumps`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `expenses` ADD CONSTRAINT `expenses_dailyEntryId_fkey` FOREIGN KEY (`dailyEntryId`) REFERENCES `daily_entries`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
