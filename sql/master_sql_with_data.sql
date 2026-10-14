-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.21.0.7344
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table best_green_solar_corp.accesses: ~8 rows (approximately)
DELETE FROM `accesses`;
INSERT INTO `accesses` (`id`, `role_id`, `department_id`, `page_id`, `status`, `created_at`) VALUES
	(1, 1, 1, 1, 1, '2026-10-13 08:53:09'),
	(2, 1, 1, 2, 1, '2026-10-13 08:53:30'),
	(3, 1, 1, 3, 1, '2026-10-13 08:53:40'),
	(4, 1, 1, 4, 1, '2026-10-13 08:53:53'),
	(5, 1, 1, 5, 1, '2026-10-13 08:54:20'),
	(6, 1, 1, 6, 1, '2026-10-13 08:54:32'),
	(7, 1, 1, 7, 1, '2026-10-13 08:55:52'),
	(8, 1, 1, 8, 1, '2026-10-13 08:56:01'),
	(9, 1, 1, 9, 1, '2026-10-13 08:56:10'),
	(10, 1, 1, 10, 1, '2026-10-13 08:56:20');

-- Dumping data for table best_green_solar_corp.audit_logs: ~0 rows (approximately)
DELETE FROM `audit_logs`;

-- Dumping data for table best_green_solar_corp.departments: ~6 rows (approximately)
DELETE FROM `departments`;
INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
	(1, 'Executive', '2026-10-08 00:00:00'),
	(2, 'Human Resources', '2026-10-08 11:15:09'),
	(3, 'Finance', '2026-10-08 11:15:22'),
	(4, 'Support', '2026-10-08 11:15:36'),
	(5, 'Sales & Marketing', '2026-10-08 11:15:50'),
	(6, 'Operations', '2026-10-08 11:16:00');

-- Dumping data for table best_green_solar_corp.inventory_in_changes: ~6 rows (approximately)
DELETE FROM `inventory_in_changes`;
INSERT INTO `inventory_in_changes` (`id`, `inventory_in_id`, `old_value`, `new_value`, `user_id`, `changed_at`, `created_at`) VALUES
	(1, 5, 'No item', 'Added item #8: Product #4, Qty 5, Cost 3000', 1, '2026-10-11 05:34:42', '2026-10-11 11:34:42'),
	(2, 5, 'Removed item #7: Product #5, Qty 4, Cost 300.00', 'Item removed', 1, '2026-10-11 05:34:42', '2026-10-11 11:34:42'),
	(3, 6, 'No item', 'Added item #10: Product #3, Qty 3, Cost 4000', 1, '2026-10-11 05:51:28', '2026-10-11 11:51:28'),
	(4, 6, 'Status: Draft', 'Status: Posted', 1, '2026-10-11 05:51:33', '2026-10-11 11:51:33'),
	(5, 7, 'Status: Draft', 'Status: Posted', 1, '2026-10-11 05:52:50', '2026-10-11 11:52:50'),
	(6, 8, 'Status: Draft', 'Status: Posted', 1, '2026-10-11 06:46:21', '2026-10-11 12:46:21'),
	(7, 11, 'Status: Draft', 'Status: Posted', 1, '2026-10-11 06:54:57', '2026-10-11 12:54:57');

-- Dumping data for table best_green_solar_corp.inventory_in_items: ~11 rows (approximately)
DELETE FROM `inventory_in_items`;
INSERT INTO `inventory_in_items` (`id`, `inventory_in_id`, `product_id`, `supplier_product_id`, `amount`, `total_amount`, `qty`, `remaining_qty`, `remarks`, `created_at`) VALUES
	(1, 1, 3, 1, 200.00, 400.00, 2, 0, NULL, '2026-10-10 08:21:51'),
	(2, 2, 1, 2, 1000.00, 3000.00, 3, 0, NULL, '2026-10-10 10:27:54'),
	(3, 2, 5, 3, 200.00, 200.00, 1, 0, NULL, '2026-10-10 10:27:54'),
	(4, 3, 1, NULL, 250.00, 25000.00, 100, 0, NULL, '2026-10-11 10:37:55'),
	(5, 4, 1, NULL, 275.00, 13750.00, 50, 0, NULL, '2026-10-11 10:37:55'),
	(6, 5, 3, 4, 200.00, 400.00, 2, 0, NULL, '2026-10-11 11:12:52'),
	(8, 5, 4, 6, 3000.00, 15000.00, 5, 0, NULL, '2026-10-11 11:34:42'),
	(9, 6, 5, 7, 200.00, 200.00, 1, 0, NULL, '2026-10-11 11:51:18'),
	(10, 6, 3, 8, 4000.00, 12000.00, 3, 0, NULL, '2026-10-11 11:51:28'),
	(11, 7, 3, 1, 400.00, 800.00, 2, 0, NULL, '2026-10-11 11:52:45'),
	(12, 8, 3, 1, 200.00, 600.00, 3, 0, NULL, '2026-10-11 12:43:22'),
	(15, 11, 3, 1, 1000.00, 4000.00, 4, 0, NULL, '2026-10-11 12:52:31');

-- Dumping data for table best_green_solar_corp.inventory_ins: ~9 rows (approximately)
DELETE FROM `inventory_ins`;
INSERT INTO `inventory_ins` (`id`, `supplier_id`, `transaction_date`, `status`, `inventory_period_id`, `user_id`, `created_at`) VALUES
	(1, 1, '2026-10-10', 'posted', 1, 1, '2026-10-10 02:21:51'),
	(2, 4, '2026-10-10', 'posted', 1, 1, '2026-10-10 04:27:54'),
	(3, 1, '2026-09-05', 'posted', 2, 1, '2026-09-05 09:00:00'),
	(4, 1, '2026-09-12', 'posted', 2, 1, '2026-09-12 10:00:00'),
	(5, 5, '2026-10-11', 'posted', 1, 1, '2026-10-11 05:12:52'),
	(6, 6, '2026-10-11', 'posted', 1, 1, '2026-10-11 05:51:18'),
	(7, 1, '2026-10-11', 'posted', 1, 1, '2026-10-11 05:52:45'),
	(8, 1, '2026-10-11', 'posted', 1, 1, '2026-10-11 06:43:22'),
	(11, 1, '2026-10-11', 'posted', 1, 1, '2026-10-11 06:52:31');

-- Dumping data for table best_green_solar_corp.inventory_out_changes: ~2 rows (approximately)
DELETE FROM `inventory_out_changes`;
INSERT INTO `inventory_out_changes` (`id`, `inventory_out_id`, `old_value`, `new_value`, `user_id`, `changed_at`, `created_at`) VALUES
	(1, 1, 'Removed: Test product A (Qty: 1)', 'Product: Not included', 1, '2026-10-10 05:09:43', '2026-10-10 11:09:43'),
	(2, 1, 'Product: Not included', 'Added: GROUNDING LUGS (Qty: 1)', 1, '2026-10-10 05:09:58', '2026-10-10 11:09:58'),
	(3, 1, 'draft', 'posted', 1, '2026-10-10 05:10:03', '2026-10-10 11:10:03'),
	(4, 2, 'draft', 'posted', 1, '2026-10-11 04:30:04', '2026-10-11 10:30:04');

-- Dumping data for table best_green_solar_corp.inventory_out_item_allocations: ~4 rows (approximately)
DELETE FROM `inventory_out_item_allocations`;
INSERT INTO `inventory_out_item_allocations` (`id`, `inventory_out_item_id`, `inventory_in_item_id`, `qty`, `amount`, `total_amount`, `created_at`) VALUES
	(2, 7, 1, 1.000, 200.00, 200.00, '2026-10-10 11:10:03'),
	(3, 8, 3, 1.000, 200.00, 200.00, '2026-10-10 11:10:03'),
	(4, 9, 2, 2.000, 1000.00, 2000.00, '2026-10-11 10:30:03'),
	(5, 10, 4, 30.000, 250.00, 7500.00, '2026-09-18 14:00:00');

-- Dumping data for table best_green_solar_corp.inventory_out_items: ~4 rows (approximately)
DELETE FROM `inventory_out_items`;
INSERT INTO `inventory_out_items` (`id`, `inventory_out_id`, `product_id`, `qty`, `remarks`, `created_at`, `amount`, `total_amount`) VALUES
	(7, 1, 3, 1, '1aa', '2026-10-10 11:09:58', 0.00, 0.00),
	(8, 1, 5, 1, 'aaaa', '2026-10-10 11:09:58', 0.00, 0.00),
	(9, 2, 1, 2, 'test', '2026-10-11 10:29:45', 0.00, 0.00),
	(10, 3, 1, 30, 'September FIFO test transaction', '2026-09-18 14:00:00', 0.00, 0.00);

-- Dumping data for table best_green_solar_corp.inventory_outs: ~3 rows (approximately)
DELETE FROM `inventory_outs`;
INSERT INTO `inventory_outs` (`id`, `date_and_time`, `project_id`, `transaction_date`, `status`, `inventory_period_id`, `user_id`, `created_at`) VALUES
	(1, '2026-10-10 04:50:13', 1, '2026-10-10', 'posted', 1, 1, '2026-10-10 10:50:13'),
	(2, '2026-10-11 04:29:45', 1, '2026-10-11', 'posted', 1, 1, '2026-10-11 10:29:45'),
	(3, '2026-09-18 14:00:00', 1, '2026-09-18', 'posted', 2, 1, '2026-09-18 14:00:00');

-- Dumping data for table best_green_solar_corp.inventory_period_product_balances: ~6 rows (approximately)
DELETE FROM `inventory_period_product_balances`;
INSERT INTO `inventory_period_product_balances` (`id`, `inventory_period_id`, `product_id`, `opening_qty`, `in_qty`, `out_qty`, `closing_qty`, `opening_value`, `in_value`, `out_value`, `closing_value`, `created_at`, `updated_at`) VALUES
	(7, 2, 1, 0.000, 150.000, 30.000, 120.000, 0.00, 38750.00, 7500.00, 31250.00, '2026-10-11 10:49:11', NULL),
	(8, 2, 2, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 0.00, 0.00, '2026-10-11 10:49:11', NULL),
	(9, 2, 3, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 0.00, 0.00, '2026-10-11 10:49:11', NULL),
	(10, 2, 4, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 0.00, 0.00, '2026-10-11 10:49:11', NULL),
	(11, 2, 5, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 0.00, 0.00, '2026-10-11 10:49:11', NULL),
	(12, 2, 6, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 0.00, 0.00, '2026-10-11 10:49:11', NULL);

-- Dumping data for table best_green_solar_corp.inventory_periods: ~0 rows (approximately)
DELETE FROM `inventory_periods`;
INSERT INTO `inventory_periods` (`id`, `year`, `month`, `status`, `closed_by`, `closed_at`) VALUES
	(1, 2026, 10, 'open', NULL, '2026-10-10 08:03:22'),
	(2, 2026, 9, 'closed', 1, '2026-10-11 04:49:11');

-- Dumping data for table best_green_solar_corp.pages: ~10 rows (approximately)
DELETE FROM `pages`;
INSERT INTO `pages` (`id`, `name`, `model`, `status`, `created_at`) VALUES
	(1, 'Dashboard', 'Dashboard', 1, '2026-10-13 08:21:51'),
	(2, 'Products', 'Products', 1, '2026-10-13 08:21:51'),
	(3, 'Suppliers', 'Suppliers', 1, '2026-10-13 08:21:51'),
	(4, 'Inventory IN', 'InventoryIns', 1, '2026-10-13 08:21:51'),
	(5, 'Inventory OUT', 'InventoryOuts', 1, '2026-10-13 08:21:51'),
	(6, 'Inventory Periods', 'InventoryPeriods', 1, '2026-10-13 08:21:51'),
	(7, 'Projects', 'Projects', 1, '2026-10-13 08:21:51'),
	(8, 'Users', 'User', 1, '2026-10-13 08:21:51'),
	(9, 'Roles', 'Roles', 1, '2026-10-13 08:21:51'),
	(10, 'Departments', 'Departments', 1, '2026-10-13 08:21:51');

-- Dumping data for table best_green_solar_corp.product_price_changes: ~0 rows (approximately)
DELETE FROM `product_price_changes`;
INSERT INTO `product_price_changes` (`id`, `product_id`, `supplier_product_id`, `old_price`, `new_price`, `user_id`, `changed_at`, `created_at`) VALUES
	(1, 3, 1, '200.00', '1000.00', 1, '2026-10-11 06:54:57', '2026-10-11 12:54:57');

-- Dumping data for table best_green_solar_corp.product_types: ~4 rows (approximately)
DELETE FROM `product_types`;
INSERT INTO `product_types` (`id`, `name`, `created_at`) VALUES
	(1, 'SOLAR PANELS (PV MODULES)', '2026-10-08 11:37:24'),
	(2, 'SOLAR BATTERY', '2026-10-08 11:37:38'),
	(3, 'SOLAR PANEL INVERTERS (PV INVERTERS)', '2026-10-08 11:37:49'),
	(4, 'SOLAR WATER PUMP', '2026-10-08 11:37:57'),
	(5, 'ACCESSORIES AND ETC.', '2026-10-08 11:38:04');

-- Dumping data for table best_green_solar_corp.products: ~5 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `name`, `description`, `model`, `product_type_id`, `unit_id`, `installation_price`, `created_at`) VALUES
	(1, 'OSDA SOLAR PANEL 625 WATTS', 'OSDA SOLAR PANEL 625 WATTS', 'ODA625-33V-MHDRZ', 1, 1, 5000.00, '2026-10-08 11:45:09'),
	(2, 'GOODWE GRID-TIED INVERTER THREE-PHASE 50KW', 'GOODWE GRID-TIED INVERTER THREE-PHASE 50KW', 'GW50K-SMT-L-G10', 3, 2, 180000.00, '2026-10-08 11:47:32'),
	(3, 'GOODWE GRID-TIED INVERTER  SINGLE PHASE 10KW', 'GOODWE GRID-TIED INVERTER  SINGLE PHASE 10KW', '1 PHASE 3MPPTS GRID TIED W/ WIFI & DC SWITCH (MS-30)', 3, 2, 51000.00, '2026-10-08 11:48:17'),
	(4, 'SOLAR SUBMERSIBLE PUMP I-VORTEX - NO. 12 (1HP DC)', 'SOLAR SUBMERSIBLE PUMP I-VORTEX - NO. 12 (1HP DC)', 'iV3SC4.8-95-96-750', 4, 2, 16000.00, '2026-10-08 11:49:15'),
	(5, 'GROUNDING LUGS', 'GROUNDING LUGS', NULL, 5, 4, 70.00, '2026-10-08 11:49:55'),
	(6, 'Test product A', 'Test product A', 'Model 123', 1, 1, 2000.00, '2026-10-09 10:35:49');

-- Dumping data for table best_green_solar_corp.projects: ~0 rows (approximately)
DELETE FROM `projects`;
INSERT INTO `projects` (`id`, `name`, `created_at`) VALUES
	(1, 'Test Project', '2026-10-09 10:45:51');

-- Dumping data for table best_green_solar_corp.roles: ~4 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `created_at`) VALUES
	(1, 'Super Admin', '2026-10-08 11:13:23'),
	(2, 'Manager', '2026-10-08 11:13:53'),
	(3, 'Employee', '2026-10-08 11:14:15'),
	(4, 'Viewer ', '2026-10-08 11:14:32');

-- Dumping data for table best_green_solar_corp.supplier_products: ~8 rows (approximately)
DELETE FROM `supplier_products`;
INSERT INTO `supplier_products` (`id`, `supplier_id`, `product_id`, `supplier_price`, `supplier_product_code`, `created_at`) VALUES
	(1, 1, 3, 1000.00, NULL, '2026-10-10 08:21:51'),
	(2, 4, 1, 1000.00, NULL, '2026-10-10 10:27:54'),
	(3, 4, 5, 200.00, NULL, '2026-10-10 10:27:54'),
	(4, 5, 3, 200.00, NULL, '2026-10-11 11:12:52'),
	(5, 5, 5, 300.00, NULL, '2026-10-11 11:12:52'),
	(6, 5, 4, 3000.00, NULL, '2026-10-11 11:46:42'),
	(7, 6, 5, 200.00, NULL, '2026-10-11 11:51:18'),
	(8, 6, 3, 4000.00, NULL, '2026-10-11 11:51:33');

-- Dumping data for table best_green_solar_corp.suppliers: ~6 rows (approximately)
DELETE FROM `suppliers`;
INSERT INTO `suppliers` (`id`, `name`, `created_at`) VALUES
	(1, 'A.S.A.-R.E', '2026-10-08 11:43:50'),
	(2, 'UNLI SOLAR', '2026-10-08 11:43:57'),
	(3, 'RIMSOO ELECTRICAL', '2026-10-08 11:44:02'),
	(4, 'AGC PRIME', '2026-10-08 11:44:09'),
	(5, 'GOODWELL', '2026-10-08 11:44:14'),
	(6, 'LEI\'S HARDWARE', '2026-10-08 11:44:19'),
	(7, 'SHOPEE', '2026-10-08 11:44:28'),
	(8, 'Test Supplier', '2026-10-09 10:40:50');

-- Dumping data for table best_green_solar_corp.units: ~8 rows (approximately)
DELETE FROM `units`;
INSERT INTO `units` (`id`, `name`, `created_at`) VALUES
	(1, 'PANEL', '2026-10-08 11:38:29'),
	(2, 'UNIT', '2026-10-08 11:38:37'),
	(3, 'SET', '2026-10-08 11:38:49'),
	(4, 'PC(s)', '2026-10-08 11:38:59'),
	(5, 'METER(s)', '2026-10-08 11:39:06'),
	(6, 'ROLL', '2026-10-08 11:39:13'),
	(7, 'PAIR', '2026-10-08 11:39:20'),
	(8, 'TUBE(s)', '2026-10-08 11:39:37'),
	(9, 'BAG(s)\r\n', '2026-10-08 11:39:50');

-- Dumping data for table best_green_solar_corp.user: ~0 rows (approximately)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `first_name`, `last_name`, `pin_code`, `email`, `role_id`, `department_id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
	(1, 'Super', 'Admin', '$2y$13$n/rZfIfPGqVQUwok3vr41Oyk7m3A/UvLU5Eh9oDJs0ZkvV6LStEQK', 'admin@solar.local', 1, 1, 'admin', 'Yii2DefaultAuthKey1234567890abcd', '$2y$13$QyRaSGGJgLWR2NqWfeyT.uhnoY/5EwUXICloXYWdhjU/FGyhYfwXy', NULL, 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
