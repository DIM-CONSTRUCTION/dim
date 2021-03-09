/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : dim_db

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 17/06/2020 09:14:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for areas
-- ----------------------------
DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of areas
-- ----------------------------
BEGIN;
INSERT INTO `areas` VALUES (1, 'Area 1', 1, NULL, NULL, '2020-04-02 07:31:09', '2020-04-02 07:31:09');
INSERT INTO `areas` VALUES (2, 'Block A', 1, 1, NULL, '2020-04-02 07:31:25', '2020-04-02 07:31:25');
INSERT INTO `areas` VALUES (3, 'Block B', 1, 1, NULL, '2020-04-02 07:31:41', '2020-05-15 08:26:35');
INSERT INTO `areas` VALUES (4, 'Floor A-1', 1, 2, NULL, '2020-04-02 07:32:00', '2020-04-02 07:32:00');
INSERT INTO `areas` VALUES (5, 'Floor A-2', 1, 2, NULL, '2020-04-02 07:32:21', '2020-05-15 08:26:29');
INSERT INTO `areas` VALUES (6, 'Apartment A-1-1', 1, 4, NULL, '2020-04-02 07:32:46', '2020-04-16 09:00:45');
INSERT INTO `areas` VALUES (8, 'Apartment A-1-2', 1, 4, NULL, '2020-04-16 09:02:49', '2020-04-20 08:05:35');
COMMIT;

-- ----------------------------
-- Table structure for benchmark_details
-- ----------------------------
DROP TABLE IF EXISTS `benchmark_details`;
CREATE TABLE `benchmark_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `benchmark_id` int(11) NOT NULL,
  `project_division_id` int(11) NOT NULL,
  `quantity` double DEFAULT NULL,
  `hours_unit` double DEFAULT NULL,
  `unit_labor_hour` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_material_rate` double DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of benchmark_details
-- ----------------------------
BEGIN;
INSERT INTO `benchmark_details` VALUES (1, 1, 4, 400, 2, 2000, '2020-04-02 08:18:50', '2020-05-14 14:05:28', 12000, 6);
INSERT INTO `benchmark_details` VALUES (6, 1, 4, NULL, 2, 2000, '2020-04-17 08:13:46', '2020-05-14 14:05:28', 12000, 8);
INSERT INTO `benchmark_details` VALUES (22, 1, 5, 12, 123, 10000, '2020-04-20 09:31:37', '2020-05-14 14:05:28', 10, 6);
INSERT INTO `benchmark_details` VALUES (23, 1, 5, NULL, 123, 10000, '2020-04-20 09:31:37', '2020-05-14 14:05:28', 10, 8);
INSERT INTO `benchmark_details` VALUES (24, 1, 5, NULL, 11, 10000, '2020-04-20 09:31:37', '2020-04-20 11:46:22', 10, 5);
INSERT INTO `benchmark_details` VALUES (25, 1, 4, NULL, 2, 2000, '2020-04-20 10:32:14', '2020-05-14 14:05:28', 12000, 3);
INSERT INTO `benchmark_details` VALUES (57, 10, 4, 400, 2, 2000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 12000, 6);
INSERT INTO `benchmark_details` VALUES (58, 10, 4, NULL, 2, 2000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 12000, 8);
INSERT INTO `benchmark_details` VALUES (59, 10, 5, 12, 123, 10000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 10, 6);
INSERT INTO `benchmark_details` VALUES (60, 10, 5, NULL, 123, 10000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 10, 8);
INSERT INTO `benchmark_details` VALUES (61, 10, 5, NULL, 123, 10000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 10, 5);
INSERT INTO `benchmark_details` VALUES (62, 10, 4, NULL, 2, 2000, '2020-04-24 12:09:58', '2020-06-09 09:24:28', 12000, 3);
INSERT INTO `benchmark_details` VALUES (63, 10, 6, 400, 0.5, 2000, '2020-06-09 09:04:52', '2020-06-09 09:24:28', 3175, 6);
INSERT INTO `benchmark_details` VALUES (64, 10, 6, NULL, 0.5, 2000, '2020-06-09 09:04:52', '2020-06-09 09:24:28', 3175, 8);
COMMIT;

-- ----------------------------
-- Table structure for benchmarks
-- ----------------------------
DROP TABLE IF EXISTS `benchmarks`;
CREATE TABLE `benchmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locked` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of benchmarks
-- ----------------------------
BEGIN;
INSERT INTO `benchmarks` VALUES (1, 'Oceanna Benchmark 1', 1, 0, '2020-04-02', '2020-04-02 08:02:00', '2020-06-09 09:22:12', 0);
INSERT INTO `benchmarks` VALUES (10, 'Oceanna Benchmark 10', 1, 1, '2020-04-02', '2020-04-24 12:09:00', '2020-06-09 09:22:04', 0);
COMMIT;

-- ----------------------------
-- Table structure for company_infos
-- ----------------------------
DROP TABLE IF EXISTS `company_infos`;
CREATE TABLE `company_infos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(3000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `zip_postal_code` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinates` point DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of company_infos
-- ----------------------------
BEGIN;
INSERT INTO `company_infos` VALUES (1, 'Lambert Electromec', 'company-infos/April2020/tuzoAWqowGaoBDJs4lEF.png', 'info@lambertelectromec.com', '+234 1 462 82 90 / 91 / 92', NULL, '+234 0 1 462 92 93', '1682 Sanusi Fafunwa St.', 'Victoria Island', 'Lagos', 145, '60096, Ikoyi', ST_GeomFromText('POINT(-117.161 32.7157)'), NULL, '2020-04-02 07:09:09', '2020-04-02 07:09:09');
COMMIT;

-- ----------------------------
-- Table structure for countries
-- ----------------------------
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of countries
-- ----------------------------
BEGIN;
INSERT INTO `countries` VALUES (1, 'Afghanistan', 'AFG', NULL, '+93');
INSERT INTO `countries` VALUES (2, 'Albania', 'ALB', NULL, '+355');
INSERT INTO `countries` VALUES (3, 'Algeria', 'DZA', NULL, '+213');
INSERT INTO `countries` VALUES (4, 'American Samoa', 'ASM', NULL, '+1684');
INSERT INTO `countries` VALUES (5, 'Andorra', 'AND', NULL, '+376');
INSERT INTO `countries` VALUES (6, 'Angola', 'AGO', NULL, '+244');
INSERT INTO `countries` VALUES (7, 'Anguilla', 'AIA', NULL, '+1264');
INSERT INTO `countries` VALUES (8, 'Antarctica', 'ATA', NULL, '+672');
INSERT INTO `countries` VALUES (9, 'Argentina', 'ARG', NULL, '+54');
INSERT INTO `countries` VALUES (10, 'Armenia', 'ARM', NULL, '+374');
INSERT INTO `countries` VALUES (11, 'Aruba', 'ABW', NULL, '+297');
INSERT INTO `countries` VALUES (12, 'Australia', 'AUS', NULL, '+61');
INSERT INTO `countries` VALUES (13, 'Austria', 'AUT', NULL, '+43');
INSERT INTO `countries` VALUES (14, 'Azerbaijan', 'AZE', NULL, '+994');
INSERT INTO `countries` VALUES (15, 'Bahamas', 'BHS', NULL, '+1');
INSERT INTO `countries` VALUES (16, 'Bahrain', 'BHR', NULL, '+973');
INSERT INTO `countries` VALUES (17, 'Bangladesh', 'BGD', NULL, '+880');
INSERT INTO `countries` VALUES (18, 'Barbados', 'BRB', NULL, '+1');
INSERT INTO `countries` VALUES (19, 'Belarus', 'BLR', NULL, '+375');
INSERT INTO `countries` VALUES (20, 'Belgium', 'BEL', NULL, '+32');
INSERT INTO `countries` VALUES (21, 'Belize', 'BLZ', NULL, '+501');
INSERT INTO `countries` VALUES (22, 'Benin', 'BEN', NULL, '+229');
INSERT INTO `countries` VALUES (23, 'Bermuda', 'BMU', NULL, '+1');
INSERT INTO `countries` VALUES (24, 'Bhutan', 'BTN', NULL, '+975');
INSERT INTO `countries` VALUES (25, 'Bolivia', 'BOL', NULL, '+591');
INSERT INTO `countries` VALUES (26, 'Bosnia and Herzegovina', 'BIH', NULL, '+387');
INSERT INTO `countries` VALUES (27, 'Botswana', 'BWA', NULL, '+267');
INSERT INTO `countries` VALUES (28, 'Brazil', 'BRA', NULL, '+55');
INSERT INTO `countries` VALUES (29, 'British Virgin Islands', 'VGB', NULL, '+1284');
INSERT INTO `countries` VALUES (30, 'Brunei', 'BRN', NULL, '+673');
INSERT INTO `countries` VALUES (31, 'Bulgaria', 'BGR', NULL, '+359');
INSERT INTO `countries` VALUES (32, 'Burkina Faso', 'BFA', NULL, '+226');
INSERT INTO `countries` VALUES (33, 'Burundi', 'BDI', NULL, '+257');
INSERT INTO `countries` VALUES (34, 'Cambodia', 'KHM', NULL, '+855');
INSERT INTO `countries` VALUES (35, 'Cameroon', 'CMR', NULL, '+237');
INSERT INTO `countries` VALUES (36, 'Canada', 'CAN', NULL, '+1');
INSERT INTO `countries` VALUES (37, 'Cape Verde', 'CPV', NULL, '+238');
INSERT INTO `countries` VALUES (38, 'Cayman Islands', 'CYM', NULL, '+1345');
INSERT INTO `countries` VALUES (39, 'Central African Republic', 'CAF', NULL, '+236');
INSERT INTO `countries` VALUES (40, 'Chile', 'CHL', NULL, '+56');
INSERT INTO `countries` VALUES (41, 'China', 'CHN', NULL, '+86');
INSERT INTO `countries` VALUES (42, 'Colombia', 'COL', NULL, '+57');
INSERT INTO `countries` VALUES (43, 'Comoros', 'COM', NULL, '+269');
INSERT INTO `countries` VALUES (44, 'Cook Islands', 'COK', NULL, '+682');
INSERT INTO `countries` VALUES (45, 'Costa Rica', 'CRI', NULL, '+506');
INSERT INTO `countries` VALUES (46, 'Croatia', 'HRV', NULL, '+385');
INSERT INTO `countries` VALUES (47, 'Cuba', 'CUB', NULL, '+53');
INSERT INTO `countries` VALUES (48, 'Curacao', 'CUW', NULL, '+599');
INSERT INTO `countries` VALUES (49, 'Cyprus', 'CYP', NULL, '+357');
INSERT INTO `countries` VALUES (50, 'Czech Republic', 'CZE', NULL, '+420');
INSERT INTO `countries` VALUES (51, 'Democratic Republic of Congo', 'COD', NULL, '+243');
INSERT INTO `countries` VALUES (52, 'Denmark', 'DNK', NULL, '+45');
INSERT INTO `countries` VALUES (53, 'Djibouti', 'DJI', NULL, '+253');
INSERT INTO `countries` VALUES (54, 'Dominica', 'DMA', NULL, '+1');
INSERT INTO `countries` VALUES (55, 'Dominican Republic', 'DOM', NULL, '+1');
INSERT INTO `countries` VALUES (56, 'East Timor', 'TLS', NULL, '+670');
INSERT INTO `countries` VALUES (57, 'Ecuador', 'ECU', NULL, '+593');
INSERT INTO `countries` VALUES (58, 'Egypt', 'EGY', NULL, '+20');
INSERT INTO `countries` VALUES (59, 'El Salvador', 'SLV', NULL, '+503');
INSERT INTO `countries` VALUES (60, 'Equatorial Guinea', 'GNQ', NULL, '+240');
INSERT INTO `countries` VALUES (61, 'Eritrea', 'ERI', NULL, '+291');
INSERT INTO `countries` VALUES (62, 'Estonia', 'EST', NULL, '+372');
INSERT INTO `countries` VALUES (63, 'Ethiopia', 'ETH', NULL, '+251');
INSERT INTO `countries` VALUES (64, 'Falkland Islands', 'FLK', NULL, '+500');
INSERT INTO `countries` VALUES (65, 'Faroe Islands', 'FRO', NULL, '+298');
INSERT INTO `countries` VALUES (66, 'Fiji', 'FJI', NULL, '+679');
INSERT INTO `countries` VALUES (67, 'Finland', 'FIN', NULL, '+358');
INSERT INTO `countries` VALUES (68, 'France', 'FRA', NULL, '+33');
INSERT INTO `countries` VALUES (69, 'French Polynesia', 'PYF', NULL, '+689');
INSERT INTO `countries` VALUES (70, 'Gabon', 'GAB', NULL, '+241');
INSERT INTO `countries` VALUES (71, 'Gambia', 'GMB', NULL, '+220');
INSERT INTO `countries` VALUES (72, 'Georgia', 'GEO', NULL, '+995');
INSERT INTO `countries` VALUES (73, 'Germany', 'DEU', NULL, '+49');
INSERT INTO `countries` VALUES (74, 'Ghana', 'GHA', NULL, '+233');
INSERT INTO `countries` VALUES (75, 'Gibraltar', 'GIB', NULL, '+350');
INSERT INTO `countries` VALUES (76, 'Greece', 'GRC', NULL, '+30');
INSERT INTO `countries` VALUES (77, 'Greenland', 'GRL', NULL, '+299');
INSERT INTO `countries` VALUES (78, 'Guadeloupe', 'GLP', NULL, '+590');
INSERT INTO `countries` VALUES (79, 'Guam', 'GUM', NULL, '+1671');
INSERT INTO `countries` VALUES (80, 'Guatemala', 'GTM', NULL, '+502');
INSERT INTO `countries` VALUES (81, 'Guinea', 'GIN', NULL, '+224');
INSERT INTO `countries` VALUES (82, 'Guinea-Bissau', 'GNB', NULL, '+245');
INSERT INTO `countries` VALUES (83, 'Guyana', 'GUY', NULL, '+592');
INSERT INTO `countries` VALUES (84, 'Haiti', 'HTI', NULL, '+509');
INSERT INTO `countries` VALUES (85, 'Honduras', 'HND', NULL, '+504');
INSERT INTO `countries` VALUES (86, 'Hong Kong', 'HKG', NULL, '+852');
INSERT INTO `countries` VALUES (87, 'Hungary', 'HUN', NULL, '+36');
INSERT INTO `countries` VALUES (88, 'Iceland', 'ISL', NULL, '+354');
INSERT INTO `countries` VALUES (89, 'India', 'IND', NULL, '+91');
INSERT INTO `countries` VALUES (90, 'Indonesia', 'IDN', NULL, '+62');
INSERT INTO `countries` VALUES (91, 'Iran', 'IRN', NULL, '+98');
INSERT INTO `countries` VALUES (92, 'Iraq', 'IRQ', NULL, '+964');
INSERT INTO `countries` VALUES (93, 'Ireland', 'IRL', NULL, '+353');
INSERT INTO `countries` VALUES (94, 'Isle of Man', 'IMN', NULL, '+44');
INSERT INTO `countries` VALUES (95, 'Israel', 'ISR', NULL, '+972');
INSERT INTO `countries` VALUES (96, 'Italy', 'ITA', NULL, '+39');
INSERT INTO `countries` VALUES (97, 'Ivory Coast', 'CIV', NULL, '+225');
INSERT INTO `countries` VALUES (98, 'Jamaica', 'JAM', NULL, '+1');
INSERT INTO `countries` VALUES (99, 'Japan', 'JPN', NULL, '+81');
INSERT INTO `countries` VALUES (100, 'Jordan', 'JOR', NULL, '+962');
INSERT INTO `countries` VALUES (101, 'Kazakhstan', 'KAZ', NULL, '+7');
INSERT INTO `countries` VALUES (102, 'Kenya', 'KEN', NULL, '+254');
INSERT INTO `countries` VALUES (103, 'Kiribati', 'KIR', NULL, '+686');
INSERT INTO `countries` VALUES (104, 'Kosovo', 'XKX', NULL, '+381');
INSERT INTO `countries` VALUES (105, 'Kuwait', 'KWT', NULL, '+965');
INSERT INTO `countries` VALUES (106, 'Kyrgyzstan', 'KGZ', NULL, '+996');
INSERT INTO `countries` VALUES (107, 'Laos', 'LAO', NULL, '+856');
INSERT INTO `countries` VALUES (108, 'Latvia', 'LVA', NULL, '+371');
INSERT INTO `countries` VALUES (109, 'Lebanon', 'LBN', NULL, '+961');
INSERT INTO `countries` VALUES (110, 'Lesotho', 'LSO', NULL, '+266');
INSERT INTO `countries` VALUES (111, 'Liberia', 'LBR', NULL, '+231');
INSERT INTO `countries` VALUES (112, 'Libya', 'LBY', NULL, '+218');
INSERT INTO `countries` VALUES (113, 'Liechtenstein', 'LIE', NULL, '+423');
INSERT INTO `countries` VALUES (114, 'Lithuania', 'LTU', NULL, '+370');
INSERT INTO `countries` VALUES (115, 'Luxembourg', 'LUX', NULL, '+352');
INSERT INTO `countries` VALUES (116, 'Macau', 'MAC', NULL, '+853');
INSERT INTO `countries` VALUES (117, 'Macedonia', 'MKD', NULL, '+389');
INSERT INTO `countries` VALUES (118, 'Madagascar', 'MDG', NULL, '+261');
INSERT INTO `countries` VALUES (119, 'Malawi', 'MWI', NULL, '+265');
INSERT INTO `countries` VALUES (120, 'Malaysia', 'MYS', NULL, '+60');
INSERT INTO `countries` VALUES (121, 'Maldives', 'MDV', NULL, '+960');
INSERT INTO `countries` VALUES (122, 'Mali', 'MLI', NULL, '+223');
INSERT INTO `countries` VALUES (123, 'Malta', 'MLT', NULL, '+356');
INSERT INTO `countries` VALUES (124, 'Marshall Islands', 'MHL', NULL, '+692');
INSERT INTO `countries` VALUES (125, 'Mauritania', 'MRT', NULL, '+222');
INSERT INTO `countries` VALUES (126, 'Mauritius', 'MUS', NULL, '+230');
INSERT INTO `countries` VALUES (127, 'Mexico', 'MEX', NULL, '+52');
INSERT INTO `countries` VALUES (128, 'Micronesia', 'FSM', NULL, '+691');
INSERT INTO `countries` VALUES (129, 'Moldova', 'MDA', NULL, '+373');
INSERT INTO `countries` VALUES (130, 'Monaco', 'MCO', NULL, '+377');
INSERT INTO `countries` VALUES (131, 'Mongolia', 'MNG', NULL, '+976');
INSERT INTO `countries` VALUES (132, 'Montenegro', 'MNE', NULL, '+382');
INSERT INTO `countries` VALUES (133, 'Montserrat', 'MSR', NULL, '+1664');
INSERT INTO `countries` VALUES (134, 'Morocco', 'MAR', NULL, '+212');
INSERT INTO `countries` VALUES (135, 'Mozambique', 'MOZ', NULL, '+258');
INSERT INTO `countries` VALUES (136, 'Myanmar [Burma]', 'MMR', NULL, '+95');
INSERT INTO `countries` VALUES (137, 'Namibia', 'NAM', NULL, '+264');
INSERT INTO `countries` VALUES (138, 'Nauru', 'NRU', NULL, '+674');
INSERT INTO `countries` VALUES (139, 'Nepal', 'NPL', NULL, '+977');
INSERT INTO `countries` VALUES (140, 'Netherlands', 'NLD', NULL, '+31');
INSERT INTO `countries` VALUES (141, 'New Caledonia', 'NCL', NULL, '+687');
INSERT INTO `countries` VALUES (142, 'New Zealand', 'NZL', NULL, '+64');
INSERT INTO `countries` VALUES (143, 'Nicaragua', 'NIC', NULL, '+505');
INSERT INTO `countries` VALUES (144, 'Niger', 'NER', NULL, '+227');
INSERT INTO `countries` VALUES (145, 'Nigeria', 'NGA', NULL, '+234');
INSERT INTO `countries` VALUES (146, 'Niue', 'NIU', NULL, '+683');
INSERT INTO `countries` VALUES (147, 'Norfolk Island', 'NFK', NULL, '+672');
INSERT INTO `countries` VALUES (148, 'North Korea', 'PRK', NULL, '+850');
INSERT INTO `countries` VALUES (149, 'Northern Mariana Islands', 'MNP', NULL, '+1670');
INSERT INTO `countries` VALUES (150, 'Norway', 'NOR', NULL, '+47');
INSERT INTO `countries` VALUES (151, 'Oman', 'OMN', NULL, '+968');
INSERT INTO `countries` VALUES (152, 'Pakistan', 'PAK', NULL, '+92');
INSERT INTO `countries` VALUES (153, 'Palau', 'PLW', NULL, '+680');
INSERT INTO `countries` VALUES (154, 'Panama', 'PAN', NULL, '+507');
INSERT INTO `countries` VALUES (155, 'Papua New Guinea', 'PNG', NULL, '+675');
INSERT INTO `countries` VALUES (156, 'Paraguay', 'PRY', NULL, '+595');
INSERT INTO `countries` VALUES (157, 'Peru', 'PER', NULL, '+51');
INSERT INTO `countries` VALUES (158, 'Philippines', 'PHL', NULL, '+63');
INSERT INTO `countries` VALUES (159, 'Pitcairn Islands', 'PCN', NULL, '+870');
INSERT INTO `countries` VALUES (160, 'Poland', 'POL', NULL, '+48');
INSERT INTO `countries` VALUES (161, 'Portugal', 'PRT', NULL, '+351');
INSERT INTO `countries` VALUES (162, 'Puerto Rico', 'PRI', NULL, '+1');
INSERT INTO `countries` VALUES (163, 'Qatar', 'QAT', NULL, '+974');
INSERT INTO `countries` VALUES (164, 'Republic of the Congo', 'COG', NULL, '+242');
INSERT INTO `countries` VALUES (165, 'Reunion', 'REU', NULL, '+262');
INSERT INTO `countries` VALUES (166, 'Romania', 'ROU', NULL, '+40');
INSERT INTO `countries` VALUES (167, 'Russia', 'RUS', NULL, '+7');
INSERT INTO `countries` VALUES (168, 'Rwanda', 'RWA', NULL, '+250');
INSERT INTO `countries` VALUES (169, 'Saint Barthélemy', 'BLM', NULL, '+590');
INSERT INTO `countries` VALUES (170, 'Saint Helena', 'SHN', NULL, '+290');
INSERT INTO `countries` VALUES (171, 'Saint Kitts and Nevis', 'KNA', NULL, '+1');
INSERT INTO `countries` VALUES (172, 'Saint Lucia', 'LCA', NULL, '+1');
INSERT INTO `countries` VALUES (173, 'Saint Martin', 'MAF', NULL, '+1599');
INSERT INTO `countries` VALUES (174, 'Saint Pierre and Miquelon', 'SPM', NULL, '+508');
INSERT INTO `countries` VALUES (175, 'Saint Vincent and the Grenadines', 'VCT', NULL, '+1');
INSERT INTO `countries` VALUES (176, 'Samoa', 'WSM', NULL, '+685');
INSERT INTO `countries` VALUES (177, 'San Marino', 'SMR', NULL, '+378');
INSERT INTO `countries` VALUES (178, 'Sao Tome and Principe', 'STP', NULL, '+239');
INSERT INTO `countries` VALUES (179, 'Saudi Arabia', 'SAU', NULL, '+966');
INSERT INTO `countries` VALUES (180, 'Senegal', 'SEN', NULL, '+221');
INSERT INTO `countries` VALUES (181, 'Serbia', 'SRB', NULL, '+381');
INSERT INTO `countries` VALUES (182, 'Seychelles', 'SYC', NULL, '+248');
INSERT INTO `countries` VALUES (183, 'Sierra Leone', 'SLE', NULL, '+232');
INSERT INTO `countries` VALUES (184, 'Singapore', 'SGP', NULL, '+65');
INSERT INTO `countries` VALUES (185, 'Slovakia', 'SVK', NULL, '+421');
INSERT INTO `countries` VALUES (186, 'Slovenia', 'SVN', NULL, '+386');
INSERT INTO `countries` VALUES (187, 'Solomon Islands', 'SLB', NULL, '+677');
INSERT INTO `countries` VALUES (188, 'Somalia', 'SOM', NULL, '+252');
INSERT INTO `countries` VALUES (189, 'South Africa', 'ZAF', NULL, '+27');
INSERT INTO `countries` VALUES (190, 'South Korea', 'KOR', NULL, '+82');
INSERT INTO `countries` VALUES (191, 'South Sudan', 'SSD', NULL, '+211');
INSERT INTO `countries` VALUES (192, 'Spain', 'ESP', NULL, '+34');
INSERT INTO `countries` VALUES (193, 'Sri Lanka', 'LKA', NULL, '+94');
INSERT INTO `countries` VALUES (194, 'Sudan', 'SDN', NULL, '+249');
INSERT INTO `countries` VALUES (195, 'Suriname', 'SUR', NULL, '+597');
INSERT INTO `countries` VALUES (196, 'Swaziland', 'SWZ', NULL, '+268');
INSERT INTO `countries` VALUES (197, 'Sweden', 'SWE', NULL, '+46');
INSERT INTO `countries` VALUES (198, 'Switzerland', 'CHE', NULL, '+41');
INSERT INTO `countries` VALUES (199, 'Syria', 'SYR', NULL, '+963');
INSERT INTO `countries` VALUES (200, 'Taiwan', 'TWN', NULL, '+886');
INSERT INTO `countries` VALUES (201, 'Tajikistan', 'TJK', NULL, '+992');
INSERT INTO `countries` VALUES (202, 'Tanzania', 'TZA', NULL, '+180');
INSERT INTO `countries` VALUES (203, 'Thailand', 'THA', NULL, '+66');
INSERT INTO `countries` VALUES (204, 'Togo', 'TGO', NULL, '+228');
INSERT INTO `countries` VALUES (205, 'Tokelau', 'TKL', NULL, '+690');
INSERT INTO `countries` VALUES (206, 'Trinidad and Tobago', 'TTO', NULL, '+1');
INSERT INTO `countries` VALUES (207, 'Tunisia', 'TUN', NULL, '+216');
INSERT INTO `countries` VALUES (208, 'Turkey', 'TUR', NULL, '+90');
INSERT INTO `countries` VALUES (209, 'Turkmenistan', 'TKM', NULL, '+993');
INSERT INTO `countries` VALUES (210, 'Tuvalu', 'TUV', NULL, '+688');
INSERT INTO `countries` VALUES (211, 'Uganda', 'UGA', NULL, '+256');
INSERT INTO `countries` VALUES (212, 'Ukraine', 'UKR', NULL, '+380');
INSERT INTO `countries` VALUES (213, 'United Arab Emirates', 'ARE', NULL, '+971');
INSERT INTO `countries` VALUES (214, 'United Kingdom', 'GBR', NULL, '+44');
INSERT INTO `countries` VALUES (215, 'United States', 'USA', NULL, '+1');
INSERT INTO `countries` VALUES (216, 'Uruguay', 'URY', NULL, '+598');
INSERT INTO `countries` VALUES (217, 'Uzbekistan', 'UZB', NULL, '+998');
INSERT INTO `countries` VALUES (218, 'Vanuatu', 'VUT', NULL, '+678');
INSERT INTO `countries` VALUES (219, 'Vatican', 'VAT', NULL, '+39');
INSERT INTO `countries` VALUES (220, 'Venezuela', 'VEN', NULL, '+58');
INSERT INTO `countries` VALUES (221, 'Vietnam', 'VNM', NULL, '+84');
INSERT INTO `countries` VALUES (222, 'Western Sahara', 'ESH', NULL, '+212');
INSERT INTO `countries` VALUES (223, 'Yemen', 'YEM', NULL, '+967');
INSERT INTO `countries` VALUES (224, 'Zambia', 'ZMB', NULL, '+260');
INSERT INTO `countries` VALUES (225, 'Zimbabwe', 'ZWE', NULL, '+263');
COMMIT;

-- ----------------------------
-- Table structure for currencies
-- ----------------------------
DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of currencies
-- ----------------------------
BEGIN;
INSERT INTO `currencies` VALUES (1, 'US Dollars', '$', 'USD', 1, '2020-04-02 05:28:57', '2020-04-02 05:28:57');
COMMIT;

-- ----------------------------
-- Table structure for data_rows
-- ----------------------------
DROP TABLE IF EXISTS `data_rows`;
CREATE TABLE `data_rows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data_type_id` int(10) unsigned NOT NULL,
  `field` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `browse` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `details` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `data_rows_data_type_id_foreign` (`data_type_id`),
  CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of data_rows
-- ----------------------------
BEGIN;
INSERT INTO `data_rows` VALUES (1, 1, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1);
INSERT INTO `data_rows` VALUES (2, 1, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2);
INSERT INTO `data_rows` VALUES (3, 1, 'email', 'text', 'Email', 1, 1, 1, 1, 1, 1, NULL, 3);
INSERT INTO `data_rows` VALUES (4, 1, 'password', 'password', 'Password', 1, 0, 0, 1, 1, 0, NULL, 4);
INSERT INTO `data_rows` VALUES (5, 1, 'remember_token', 'text', 'Remember Token', 0, 0, 0, 0, 0, 0, NULL, 5);
INSERT INTO `data_rows` VALUES (6, 1, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 0, 0, 0, NULL, 6);
INSERT INTO `data_rows` VALUES (7, 1, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 7);
INSERT INTO `data_rows` VALUES (8, 1, 'avatar', 'image', 'Avatar', 0, 1, 1, 1, 1, 1, NULL, 8);
INSERT INTO `data_rows` VALUES (9, 1, 'user_belongsto_role_relationship', 'relationship', 'Role', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsTo\",\"column\":\"role_id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"roles\",\"pivot\":0}', 10);
INSERT INTO `data_rows` VALUES (10, 1, 'user_belongstomany_role_relationship', 'relationship', 'Roles', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"user_roles\",\"pivot\":\"1\",\"taggable\":\"0\"}', 11);
INSERT INTO `data_rows` VALUES (11, 1, 'settings', 'hidden', 'Settings', 0, 0, 0, 0, 0, 0, NULL, 12);
INSERT INTO `data_rows` VALUES (12, 2, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1);
INSERT INTO `data_rows` VALUES (13, 2, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2);
INSERT INTO `data_rows` VALUES (14, 2, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3);
INSERT INTO `data_rows` VALUES (15, 2, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4);
INSERT INTO `data_rows` VALUES (16, 3, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1);
INSERT INTO `data_rows` VALUES (17, 3, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2);
INSERT INTO `data_rows` VALUES (18, 3, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3);
INSERT INTO `data_rows` VALUES (19, 3, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4);
INSERT INTO `data_rows` VALUES (20, 3, 'display_name', 'text', 'Display Name', 1, 1, 1, 1, 1, 1, NULL, 5);
INSERT INTO `data_rows` VALUES (21, 1, 'role_id', 'text', 'Role', 1, 1, 1, 1, 1, 1, NULL, 9);
INSERT INTO `data_rows` VALUES (22, 4, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (23, 4, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 2);
INSERT INTO `data_rows` VALUES (24, 4, 'location', 'text', 'Location', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 4);
INSERT INTO `data_rows` VALUES (25, 4, 'coordinates', 'coordinates', 'Coordinates', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 5);
INSERT INTO `data_rows` VALUES (26, 4, 'reference', 'text', 'Reference', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 3);
INSERT INTO `data_rows` VALUES (27, 4, 'owner', 'text', 'Owner', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 7);
INSERT INTO `data_rows` VALUES (28, 4, 'architect', 'text', 'Architect', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 8);
INSERT INTO `data_rows` VALUES (29, 4, 'start_date', 'date', 'Start Date', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 9);
INSERT INTO `data_rows` VALUES (30, 4, 'estimated_delivery_date', 'date', 'Estimated Delivery Date', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 10);
INSERT INTO `data_rows` VALUES (31, 4, 'total_budget', 'number', 'Total Budget', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 11);
INSERT INTO `data_rows` VALUES (32, 4, 'currency_id', 'text', 'Currency Id', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 13);
INSERT INTO `data_rows` VALUES (33, 4, 'datetime_format', 'text', 'Datetime Format', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 14);
INSERT INTO `data_rows` VALUES (34, 4, 'notes', 'text_area', 'Notes', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 17);
INSERT INTO `data_rows` VALUES (35, 4, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 1, 0, 1, '{}', 18);
INSERT INTO `data_rows` VALUES (36, 4, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 19);
INSERT INTO `data_rows` VALUES (37, 5, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (38, 5, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 2);
INSERT INTO `data_rows` VALUES (39, 5, 'symbol', 'text', 'Symbol', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 3);
INSERT INTO `data_rows` VALUES (40, 5, 'code', 'text', 'Code', 1, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 4);
INSERT INTO `data_rows` VALUES (41, 5, 'base', 'checkbox', 'Is Base Currency', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"on\":\"Yes\",\"off\":\"No\",\"checked\":false}', 5);
INSERT INTO `data_rows` VALUES (42, 5, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 1, 0, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (43, 5, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7);
INSERT INTO `data_rows` VALUES (44, 4, 'project_belongsto_currency_relationship', 'relationship', 'Currency', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Currency\",\"table\":\"currencies\",\"type\":\"belongsTo\",\"column\":\"currency_id\",\"key\":\"id\",\"label\":\"symbol\",\"pivot_table\":\"currencies\",\"pivot\":\"0\",\"taggable\":\"0\"}', 12);
INSERT INTO `data_rows` VALUES (45, 4, 'image', 'image', 'Image', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 6);
INSERT INTO `data_rows` VALUES (46, 6, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (47, 6, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 2);
INSERT INTO `data_rows` VALUES (48, 6, 'project_id', 'text', 'Project Id', 1, 0, 1, 1, 1, 1, '{}', 4);
INSERT INTO `data_rows` VALUES (49, 6, 'parent_id', 'text', 'Parent Id', 0, 0, 1, 1, 1, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (50, 6, 'description', 'text_area', 'Description', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 7);
INSERT INTO `data_rows` VALUES (51, 6, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 1, 0, 1, '{}', 8);
INSERT INTO `data_rows` VALUES (52, 6, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 9);
INSERT INTO `data_rows` VALUES (53, 6, 'area_belongsto_area_relationship', 'relationship', 'Parent Area', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Area\",\"table\":\"areas\",\"type\":\"belongsTo\",\"column\":\"parent_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 5);
INSERT INTO `data_rows` VALUES (54, 6, 'area_belongsto_project_relationship', 'relationship', 'Project', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsTo\",\"column\":\"project_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3);
INSERT INTO `data_rows` VALUES (55, 7, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (56, 7, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 2);
INSERT INTO `data_rows` VALUES (57, 7, 'project_id', 'text', 'Project Id', 1, 1, 1, 1, 1, 1, '{}', 4);
INSERT INTO `data_rows` VALUES (58, 7, 'revision', 'number', 'Revision', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 5);
INSERT INTO `data_rows` VALUES (59, 7, 'start_date', 'date', 'Start Date', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 6);
INSERT INTO `data_rows` VALUES (60, 7, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 1, 0, 1, '{}', 7);
INSERT INTO `data_rows` VALUES (61, 7, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 8);
INSERT INTO `data_rows` VALUES (62, 7, 'benchmark_belongsto_project_relationship', 'relationship', 'Project', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsTo\",\"column\":\"project_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3);
INSERT INTO `data_rows` VALUES (63, 8, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (64, 8, 'title', 'text', 'Title', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 2);
INSERT INTO `data_rows` VALUES (65, 8, 'reference', 'text', 'Reference', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 3);
INSERT INTO `data_rows` VALUES (66, 8, 'project_id', 'text', 'Project Id', 1, 1, 1, 1, 1, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (67, 8, 'parent_id', 'text', 'Parent Id', 0, 1, 1, 1, 1, 1, '{}', 8);
INSERT INTO `data_rows` VALUES (68, 8, 'description', 'text_area', 'Description', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 9);
INSERT INTO `data_rows` VALUES (69, 8, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 10);
INSERT INTO `data_rows` VALUES (70, 8, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 11);
INSERT INTO `data_rows` VALUES (71, 8, 'project_division_belongsto_project_relationship', 'relationship', 'Project', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsTo\",\"column\":\"project_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 5);
INSERT INTO `data_rows` VALUES (72, 8, 'project_division_belongsto_project_division_relationship', 'relationship', 'Parent Division', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\ProjectDivision\",\"table\":\"project_divisions\",\"type\":\"belongsTo\",\"column\":\"parent_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 7);
INSERT INTO `data_rows` VALUES (73, 9, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (74, 9, 'benchmark_id', 'text', 'Benchmark Id', 1, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (75, 9, 'project_division_id', 'text', 'Project Division Id', 1, 1, 1, 1, 1, 1, '{}', 5);
INSERT INTO `data_rows` VALUES (77, 9, 'quantity', 'text', 'Quantity', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"2\"}}', 7);
INSERT INTO `data_rows` VALUES (82, 9, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 12);
INSERT INTO `data_rows` VALUES (83, 9, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 13);
INSERT INTO `data_rows` VALUES (84, 9, 'benchmark_detail_belongsto_benchmark_relationship', 'relationship', 'Benchmark', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\Benchmark\",\"table\":\"benchmarks\",\"type\":\"belongsTo\",\"column\":\"benchmark_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2);
INSERT INTO `data_rows` VALUES (85, 9, 'benchmark_detail_belongsto_project_division_relationship', 'relationship', 'Project Division', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\ProjectDivision\",\"table\":\"project_divisions\",\"type\":\"belongsTo\",\"column\":\"project_division_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 4);
INSERT INTO `data_rows` VALUES (86, 10, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (87, 10, 'title', 'text', 'Title', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 2);
INSERT INTO `data_rows` VALUES (88, 10, 'project_id', 'text', 'Project Id', 1, 1, 1, 1, 1, 1, '{}', 4);
INSERT INTO `data_rows` VALUES (89, 10, 'date', 'date', 'Date', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 5);
INSERT INTO `data_rows` VALUES (90, 10, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 6);
INSERT INTO `data_rows` VALUES (91, 10, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7);
INSERT INTO `data_rows` VALUES (92, 10, 'form_belongsto_project_relationship', 'relationship', 'Project', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsTo\",\"column\":\"project_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3);
INSERT INTO `data_rows` VALUES (93, 11, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (94, 11, 'division_id', 'text', 'Division Id', 1, 1, 1, 1, 1, 1, '{}', 4);
INSERT INTO `data_rows` VALUES (95, 11, 'area_id', 'text', 'Area Id', 1, 1, 1, 1, 1, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (96, 11, 'quantity', 'number', 'Quantity', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 7);
INSERT INTO `data_rows` VALUES (97, 11, 'percentage_completed', 'number', 'Percentage Completed (%)', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 8);
INSERT INTO `data_rows` VALUES (98, 11, 'images', 'multiple_images', 'Images', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 9);
INSERT INTO `data_rows` VALUES (99, 11, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 11);
INSERT INTO `data_rows` VALUES (100, 11, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 12);
INSERT INTO `data_rows` VALUES (101, 11, 'form_detail_belongsto_area_relationship', 'relationship', 'Area Of Work', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"model\":\"App\\\\Area\",\"table\":\"areas\",\"type\":\"belongsTo\",\"column\":\"area_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 5);
INSERT INTO `data_rows` VALUES (102, 11, 'form_detail_belongsto_project_division_relationship', 'relationship', 'Project Division', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"model\":\"App\\\\ProjectDivision\",\"table\":\"project_divisions\",\"type\":\"belongsTo\",\"column\":\"division_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3);
INSERT INTO `data_rows` VALUES (103, 12, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (104, 12, 'full_name', 'text', 'Full Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 2);
INSERT INTO `data_rows` VALUES (105, 12, 'reference', 'text', 'Reference', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 3);
INSERT INTO `data_rows` VALUES (106, 12, 'country_id', 'text', 'Nationality', 0, 1, 1, 1, 1, 1, '{}', 5);
INSERT INTO `data_rows` VALUES (107, 12, 'specialty_id', 'text', 'Specialty Id', 0, 1, 1, 1, 1, 1, '{}', 7);
INSERT INTO `data_rows` VALUES (108, 12, 'phone', 'text', 'Phone', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 11);
INSERT INTO `data_rows` VALUES (109, 12, 'mobile', 'text', 'Mobile', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 12);
INSERT INTO `data_rows` VALUES (110, 12, 'address', 'text_area', 'Address', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 13);
INSERT INTO `data_rows` VALUES (111, 12, 'year_of_employment', 'number', 'Year Of Employment', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 8);
INSERT INTO `data_rows` VALUES (112, 12, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 14);
INSERT INTO `data_rows` VALUES (113, 12, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 15);
INSERT INTO `data_rows` VALUES (114, 12, 'labor_belongstomany_project_relationship', 'relationship', 'Assigned Projects', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"9\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"labors_projects\",\"pivot\":\"1\",\"taggable\":\"on\"}', 9);
INSERT INTO `data_rows` VALUES (116, 13, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (117, 13, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2);
INSERT INTO `data_rows` VALUES (118, 13, 'image', 'image', 'Image', 0, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (119, 13, 'description', 'text_area', 'Description', 0, 1, 1, 1, 1, 1, '{}', 4);
INSERT INTO `data_rows` VALUES (120, 14, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (121, 14, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 2);
INSERT INTO `data_rows` VALUES (122, 14, 'abbreviation', 'text', 'Abbreviation', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 3);
INSERT INTO `data_rows` VALUES (123, 14, 'image', 'image', 'Image', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 5);
INSERT INTO `data_rows` VALUES (124, 14, 'code', 'text', 'Code', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 4);
INSERT INTO `data_rows` VALUES (125, 12, 'labor_belongsto_specialty_relationship', 'relationship', 'Specialty', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\Specialty\",\"table\":\"specialties\",\"type\":\"belongsTo\",\"column\":\"specialty_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 6);
INSERT INTO `data_rows` VALUES (126, 12, 'labor_belongsto_country_relationship', 'relationship', 'Nationality', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\Country\",\"table\":\"countries\",\"type\":\"belongsTo\",\"column\":\"country_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 4);
INSERT INTO `data_rows` VALUES (127, 17, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (128, 17, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 2);
INSERT INTO `data_rows` VALUES (129, 17, 'reference', 'text', 'Reference', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 3);
INSERT INTO `data_rows` VALUES (130, 17, 'type_id', 'text', 'Type Id', 1, 1, 1, 1, 1, 1, '{}', 5);
INSERT INTO `data_rows` VALUES (131, 17, 'image', 'image', 'Image', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 6);
INSERT INTO `data_rows` VALUES (132, 17, 'description', 'text_area', 'Description', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 8);
INSERT INTO `data_rows` VALUES (133, 17, 'equip_belongstomany_project_relationship', 'relationship', 'Assigned Projects', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"},\"model\":\"App\\\\Project\",\"table\":\"projects\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"equipments_projects\",\"pivot\":\"1\",\"taggable\":\"on\"}', 7);
INSERT INTO `data_rows` VALUES (134, 18, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (135, 18, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2);
INSERT INTO `data_rows` VALUES (136, 18, 'description', 'text_area', 'Description', 0, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (137, 17, 'equip_belongsto_equipment_type_relationship', 'relationship', 'Equipment Type', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"model\":\"App\\\\EquipmentType\",\"table\":\"equipment_types\",\"type\":\"belongsTo\",\"column\":\"type_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 4);
INSERT INTO `data_rows` VALUES (138, 20, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (139, 20, 'form_details_id', 'text', 'Form Details Id', 1, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (140, 20, 'equip_id', 'text', 'Equip Id', 1, 1, 1, 1, 1, 1, '{}', 5);
INSERT INTO `data_rows` VALUES (141, 20, 'hours_of_use', 'number', 'Hours Of Use', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 6);
INSERT INTO `data_rows` VALUES (142, 20, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 7);
INSERT INTO `data_rows` VALUES (143, 20, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 8);
INSERT INTO `data_rows` VALUES (144, 20, 'fd_equip_belongsto_form_detail_relationship', 'relationship', 'Form Details', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\FormDetail\",\"table\":\"form_details\",\"type\":\"belongsTo\",\"column\":\"form_details_id\",\"key\":\"id\",\"label\":\"division_id\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2);
INSERT INTO `data_rows` VALUES (145, 20, 'fd_equip_belongsto_equip_relationship', 'relationship', 'Equipment Used', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Equip\",\"table\":\"equips\",\"type\":\"belongsTo\",\"column\":\"equip_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 4);
INSERT INTO `data_rows` VALUES (146, 21, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (147, 21, 'form_details_id', 'text', 'Form Details Id', 1, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (148, 21, 'labor_id', 'text', 'Labor Id', 1, 1, 1, 1, 1, 1, '{}', 5);
INSERT INTO `data_rows` VALUES (149, 21, 'hours_of_work', 'number', 'Hours Of Work', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"}}', 6);
INSERT INTO `data_rows` VALUES (150, 21, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 9);
INSERT INTO `data_rows` VALUES (151, 21, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 10);
INSERT INTO `data_rows` VALUES (152, 21, 'fd_labor_belongsto_form_detail_relationship', 'relationship', 'Form Details', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"model\":\"App\\\\FormDetail\",\"table\":\"form_details\",\"type\":\"belongsTo\",\"column\":\"form_details_id\",\"key\":\"id\",\"label\":\"division_id\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2);
INSERT INTO `data_rows` VALUES (153, 21, 'fd_labor_belongsto_labor_relationship', 'relationship', 'Assigned Labor', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"model\":\"App\\\\Labor\",\"table\":\"labors\",\"type\":\"belongsTo\",\"column\":\"labor_id\",\"key\":\"id\",\"label\":\"full_name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 4);
INSERT INTO `data_rows` VALUES (154, 22, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (155, 22, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 2);
INSERT INTO `data_rows` VALUES (156, 22, 'logo', 'image', 'Logo', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 3);
INSERT INTO `data_rows` VALUES (157, 22, 'email', 'text', 'Email', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 4);
INSERT INTO `data_rows` VALUES (158, 22, 'phone', 'text', 'Phone', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 5);
INSERT INTO `data_rows` VALUES (159, 22, 'mobile', 'text', 'Mobile', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 6);
INSERT INTO `data_rows` VALUES (160, 22, 'fax', 'text', 'Fax', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 7);
INSERT INTO `data_rows` VALUES (161, 22, 'address_line_1', 'text', 'Address Line 1', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 8);
INSERT INTO `data_rows` VALUES (162, 22, 'address_line_2', 'text', 'Address Line 2', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 9);
INSERT INTO `data_rows` VALUES (163, 22, 'city', 'text', 'City', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 10);
INSERT INTO `data_rows` VALUES (164, 22, 'country_id', 'text', 'Country Id', 0, 1, 1, 1, 1, 1, '{}', 12);
INSERT INTO `data_rows` VALUES (165, 22, 'zip_postal_code', 'text', 'Zip / Postal Code', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 13);
INSERT INTO `data_rows` VALUES (166, 22, 'coordinates', 'coordinates', 'Coordinates', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 14);
INSERT INTO `data_rows` VALUES (167, 22, 'description', 'rich_text_box', 'Description', 0, 0, 1, 1, 1, 1, '{\"display\":{\"width\":\"12\"}}', 15);
INSERT INTO `data_rows` VALUES (168, 22, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 16);
INSERT INTO `data_rows` VALUES (169, 22, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 17);
INSERT INTO `data_rows` VALUES (170, 22, 'company_info_belongsto_country_relationship', 'relationship', 'Country', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"},\"model\":\"App\\\\Country\",\"table\":\"countries\",\"type\":\"belongsTo\",\"column\":\"country_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 11);
INSERT INTO `data_rows` VALUES (171, 4, 'delivery_date', 'date', 'Actual Delivery Date', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"}}', 15);
INSERT INTO `data_rows` VALUES (172, 4, 'status', 'select_dropdown', 'Status', 1, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"6\"},\"default\":\"running\",\"options\":{\"running\":\"Running\",\"paused\":\"Paused\",\"complete\":\"Complete\"}}', 16);
INSERT INTO `data_rows` VALUES (173, 13, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 5);
INSERT INTO `data_rows` VALUES (174, 13, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 6);
INSERT INTO `data_rows` VALUES (175, 8, 'unit_of_measure', 'text', 'Unit Of Measure', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"4\"}}', 4);
INSERT INTO `data_rows` VALUES (176, 18, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 4);
INSERT INTO `data_rows` VALUES (177, 18, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5);
INSERT INTO `data_rows` VALUES (178, 17, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 9);
INSERT INTO `data_rows` VALUES (179, 17, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 10);
INSERT INTO `data_rows` VALUES (180, 23, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1);
INSERT INTO `data_rows` VALUES (181, 23, 'name', 'text', 'Type of Work', 1, 1, 1, 1, 1, 1, 'null', 2);
INSERT INTO `data_rows` VALUES (182, 23, 'description', 'text_area', 'Description', 0, 1, 1, 1, 1, 1, '{}', 3);
INSERT INTO `data_rows` VALUES (183, 23, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, '{}', 4);
INSERT INTO `data_rows` VALUES (184, 23, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5);
INSERT INTO `data_rows` VALUES (185, 21, 'fd_labor_belongsto_labor_work_type_relationship', 'relationship', 'labor_work_types', 0, 1, 1, 1, 1, 1, '{\"display\":{\"width\":\"3\"},\"model\":\"App\\\\LaborWorkType\",\"table\":\"labor_work_types\",\"type\":\"belongsTo\",\"column\":\"labor_work_type_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 7);
INSERT INTO `data_rows` VALUES (186, 21, 'labor_work_type_id', 'text', 'Labor Work Type Id', 1, 1, 1, 1, 1, 1, '{}', 8);
INSERT INTO `data_rows` VALUES (187, 9, 'benchmark_detail_belongsto_area_relationship', 'relationship', 'areas', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Area\",\"table\":\"areas\",\"type\":\"belongsTo\",\"column\":\"area_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 14);
INSERT INTO `data_rows` VALUES (188, 9, 'hours_unit', 'text', 'Hours Unit', 0, 1, 1, 1, 1, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (190, 9, 'unit_material_rate', 'text', 'Unit Material Rate', 0, 1, 1, 1, 1, 1, '{}', 10);
INSERT INTO `data_rows` VALUES (191, 9, 'area_id', 'text', 'Area Id', 0, 1, 1, 1, 1, 1, '{}', 11);
INSERT INTO `data_rows` VALUES (192, 8, 'benchmarking_type', 'text', 'Benchmarking Type', 0, 1, 1, 1, 1, 1, '{}', 10);
INSERT INTO `data_rows` VALUES (193, 9, 'unit_labor_hour', 'text', 'Unit Labor Hour', 0, 1, 1, 1, 1, 1, '{}', 6);
INSERT INTO `data_rows` VALUES (194, 7, 'locked', 'checkbox', 'Locked', 0, 1, 1, 1, 1, 1, '{}', 8);
INSERT INTO `data_rows` VALUES (195, 11, 'form_detail_belongsto_form_relationship', 'relationship', 'forms', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Form\",\"table\":\"forms\",\"type\":\"belongsTo\",\"column\":\"form_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"areas\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2);
INSERT INTO `data_rows` VALUES (196, 11, 'form_id', 'text', 'Form Id', 1, 1, 1, 1, 1, 1, '{}', 10);
INSERT INTO `data_rows` VALUES (197, 21, 'extra_hours_of_work', 'text', 'Extra Hours Of Work', 0, 1, 1, 1, 1, 1, '{}', 8);
INSERT INTO `data_rows` VALUES (198, 4, 'use_percentage', 'checkbox', 'Use Percentage', 0, 1, 1, 1, 1, 1, '{}', 19);
COMMIT;

-- ----------------------------
-- Table structure for data_types
-- ----------------------------
DROP TABLE IF EXISTS `data_types`;
CREATE TABLE `data_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_singular` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_plural` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_name` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_name` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT '0',
  `server_side` tinyint(4) NOT NULL DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_types_name_unique` (`name`),
  UNIQUE KEY `data_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of data_types
-- ----------------------------
BEGIN;
INSERT INTO `data_types` VALUES (1, 'users', 'users', 'User', 'Users', 'voyager-person', 'TCG\\Voyager\\Models\\User', 'TCG\\Voyager\\Policies\\UserPolicy', 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController', '', 1, 0, NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `data_types` VALUES (2, 'menus', 'menus', 'Menu', 'Menus', 'voyager-list', 'TCG\\Voyager\\Models\\Menu', NULL, '', '', 1, 0, NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `data_types` VALUES (3, 'roles', 'roles', 'Role', 'Roles', 'voyager-lock', 'TCG\\Voyager\\Models\\Role', NULL, 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController', '', 1, 0, NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `data_types` VALUES (4, 'projects', 'projects', 'Project', 'Projects', 'voyager-lighthouse', 'App\\Project', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 08:52:02', '2020-04-30 13:29:30');
INSERT INTO `data_types` VALUES (5, 'currencies', 'currencies', 'Currency', 'Currencies', 'voyager-dollar', 'App\\Currency', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 09:06:21', '2020-04-02 04:59:29');
INSERT INTO `data_types` VALUES (6, 'areas', 'areas', 'Area', 'Areas', 'voyager-categories', 'App\\Area', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 09:41:05', '2020-04-01 09:43:30');
INSERT INTO `data_types` VALUES (7, 'benchmarks', 'benchmarks', 'Benchmark', 'Benchmarks', 'voyager-check', 'App\\Benchmark', NULL, '\\TCG\\Voyager\\Http\\Controllers\\VoyagerBenchmarkController', NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 10:09:09', '2020-04-23 08:11:00');
INSERT INTO `data_types` VALUES (8, 'project_divisions', 'project-divisions', 'Project Division', 'Project Divisions', 'voyager-list', 'App\\ProjectDivision', NULL, NULL, NULL, 1, 1, '{\"order_column\":\"reference\",\"order_display_column\":\"title\",\"order_direction\":\"asc\",\"default_search_key\":\"title\",\"scope\":null}', '2020-04-01 10:26:00', '2020-04-20 12:18:51');
INSERT INTO `data_types` VALUES (9, 'benchmark_details', 'benchmark-details', 'Benchmark Detail', 'Benchmark Details', 'voyager-list-add', 'App\\BenchmarkDetail', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-01 11:43:15', '2020-04-22 12:56:24');
INSERT INTO `data_types` VALUES (10, 'forms', 'forms', 'Form', 'Forms', 'voyager-browser', 'App\\Form', NULL, '\\App\\Http\\Controllers\\Voyager\\VoyagerFormController', NULL, 1, 1, '{\"order_column\":\"date\",\"order_display_column\":\"title\",\"order_direction\":\"desc\",\"default_search_key\":\"title\",\"scope\":null}', '2020-04-01 15:22:24', '2020-04-29 09:41:57');
INSERT INTO `data_types` VALUES (11, 'form_details', 'form-details', 'Form Detail', 'Form Details', 'voyager-window-list', 'App\\FormDetail', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-01 15:28:00', '2020-04-29 07:20:55');
INSERT INTO `data_types` VALUES (12, 'labors', 'labors', 'Labor', 'Labors', 'voyager-people', 'App\\Labor', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"full_name\",\"scope\":null}', '2020-04-01 15:56:43', '2020-04-01 17:12:25');
INSERT INTO `data_types` VALUES (13, 'specialties', 'specialties', 'Specialty', 'Specialties', 'voyager-brush', 'App\\Specialty', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-01 17:04:12', '2020-04-02 07:13:28');
INSERT INTO `data_types` VALUES (14, 'countries', 'countries', 'Country', 'Countries', 'voyager-world', 'App\\Country', NULL, NULL, NULL, 1, 1, '{\"order_column\":\"name\",\"order_display_column\":\"name\",\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-01 17:06:58', '2020-04-02 05:28:10');
INSERT INTO `data_types` VALUES (17, 'equips', 'equips', 'Equipment', 'Equipments', 'voyager-truck', 'App\\Equip', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 17:47:21', '2020-04-02 07:48:07');
INSERT INTO `data_types` VALUES (18, 'equipment_types', 'equipment-types', 'Equipment Type', 'Equipment Types', 'voyager-hammer', 'App\\EquipmentType', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2020-04-01 17:54:41', '2020-04-02 07:43:39');
INSERT INTO `data_types` VALUES (20, 'fd_equips', 'fd-equips', 'Form Details Equipment', 'Form Details Equipments', 'voyager-paint-bucket', 'App\\FdEquip', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-02 03:43:28', '2020-04-02 03:45:37');
INSERT INTO `data_types` VALUES (21, 'fd_labors', 'fd-labors', 'Form Details Labor', 'Form Details Labors', 'voyager-group', 'App\\FdLabor', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-02 03:49:49', '2020-04-30 09:13:52');
INSERT INTO `data_types` VALUES (22, 'company_infos', 'company-infos', 'Company Info', 'Company Infos', 'voyager-info-circled', 'App\\CompanyInfo', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2020-04-02 04:07:12', '2020-04-02 04:19:27');
INSERT INTO `data_types` VALUES (23, 'labor_work_types', 'labor-work-types', 'Labor Work Type', 'Labor Work Types', 'voyager-pirate-swords', 'App\\LaborWorkType', NULL, NULL, NULL, 1, 1, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\"}', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
COMMIT;

-- ----------------------------
-- Table structure for equipment_types
-- ----------------------------
DROP TABLE IF EXISTS `equipment_types`;
CREATE TABLE `equipment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of equipment_types
-- ----------------------------
BEGIN;
INSERT INTO `equipment_types` VALUES (1, 'Machinery', NULL, '2020-04-02 07:44:02', '2020-04-02 07:44:02');
INSERT INTO `equipment_types` VALUES (2, 'Tools', NULL, '2020-04-02 07:44:08', '2020-04-02 07:44:08');
COMMIT;

-- ----------------------------
-- Table structure for equipments_projects
-- ----------------------------
DROP TABLE IF EXISTS `equipments_projects`;
CREATE TABLE `equipments_projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `equip_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of equipments_projects
-- ----------------------------
BEGIN;
INSERT INTO `equipments_projects` VALUES (1, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for equips
-- ----------------------------
DROP TABLE IF EXISTS `equips`;
CREATE TABLE `equips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(3000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` int(11) NOT NULL,
  `image` varchar(3000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of equips
-- ----------------------------
BEGIN;
INSERT INTO `equips` VALUES (1, 'Drill', 'DR80', 2, NULL, NULL, '2020-04-02 07:48:30', '2020-04-02 07:48:30');
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for fd_equips
-- ----------------------------
DROP TABLE IF EXISTS `fd_equips`;
CREATE TABLE `fd_equips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_details_id` int(11) NOT NULL,
  `equip_id` int(11) NOT NULL,
  `hours_of_use` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fd_equips
-- ----------------------------
BEGIN;
INSERT INTO `fd_equips` VALUES (4, 1, 1, 4, '2020-04-30 13:04:46', NULL);
INSERT INTO `fd_equips` VALUES (5, 6, 1, 7, '2020-04-30 14:12:25', '2020-04-30 14:12:37');
COMMIT;

-- ----------------------------
-- Table structure for fd_labors
-- ----------------------------
DROP TABLE IF EXISTS `fd_labors`;
CREATE TABLE `fd_labors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_details_id` int(11) NOT NULL,
  `labor_id` int(11) NOT NULL,
  `hours_of_work` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `labor_work_type_id` int(11) NOT NULL,
  `extra_hours_of_work` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fd_labors
-- ----------------------------
BEGIN;
INSERT INTO `fd_labors` VALUES (30, 1, 1, 4, '2020-04-30 11:02:53', '2020-05-15 09:17:08', 1, 0);
INSERT INTO `fd_labors` VALUES (31, 1, 2, 8, '2020-04-30 11:03:09', '2020-05-19 07:23:30', 1, 0);
INSERT INTO `fd_labors` VALUES (32, 7, 1, 1, '2020-04-30 12:31:53', NULL, 2, 1);
INSERT INTO `fd_labors` VALUES (33, 7, 2, 1, '2020-04-30 12:44:38', NULL, 2, 2);
INSERT INTO `fd_labors` VALUES (34, 6, 1, 3, '2020-05-15 08:40:31', NULL, 2, 0);
INSERT INTO `fd_labors` VALUES (35, 6, 2, 4, '2020-05-15 08:40:45', NULL, 1, 0);
INSERT INTO `fd_labors` VALUES (36, 8, 1, 8, '2020-06-09 04:18:18', NULL, 1, 0);
INSERT INTO `fd_labors` VALUES (37, 9, 2, 4, '2020-06-09 04:18:28', NULL, 2, 0);
INSERT INTO `fd_labors` VALUES (38, 10, 1, 5, '2020-06-09 04:19:48', NULL, 2, 0);
INSERT INTO `fd_labors` VALUES (39, 11, 1, 7, '2020-06-09 04:20:16', NULL, 1, 0);
INSERT INTO `fd_labors` VALUES (40, 12, 2, 8, '2020-06-09 04:21:16', NULL, 2, 0);
INSERT INTO `fd_labors` VALUES (41, 13, 1, 5, '2020-06-09 04:21:44', NULL, 2, 0);
COMMIT;

-- ----------------------------
-- Table structure for form_details
-- ----------------------------
DROP TABLE IF EXISTS `form_details`;
CREATE TABLE `form_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `percentage_completed` double NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `form_id` int(11) NOT NULL,
  `working_hours` double NOT NULL,
  `extra_hours` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of form_details
-- ----------------------------
BEGIN;
INSERT INTO `form_details` VALUES (1, 4, 6, 4, 0.75, NULL, '2020-04-02 07:41:54', '2020-06-09 09:05:56', 1, 12, 0);
INSERT INTO `form_details` VALUES (7, 6, 6, 4, 0.8, NULL, NULL, '2020-06-09 09:06:26', 1, 6, 0);
INSERT INTO `form_details` VALUES (8, 4, 6, 4, 0.75, NULL, NULL, '2020-06-09 09:09:26', 2, 16, 0);
INSERT INTO `form_details` VALUES (9, 6, 6, 4, 1, NULL, NULL, '2020-06-09 09:09:36', 2, 10, 0);
INSERT INTO `form_details` VALUES (10, 4, 5, 8, 1.25, NULL, NULL, '2020-06-09 09:08:52', 3, 12, 0);
INSERT INTO `form_details` VALUES (11, 6, 5, 5, 1, NULL, NULL, '2020-06-09 09:09:06', 3, 8, 0);
INSERT INTO `form_details` VALUES (12, 4, 5, 6, 1, NULL, NULL, '2020-06-09 09:08:14', 4, 14, 0);
INSERT INTO `form_details` VALUES (13, 6, 5, 3, 0.7, NULL, NULL, '2020-06-09 09:08:34', 4, 6, 0);
COMMIT;

-- ----------------------------
-- Table structure for forms
-- ----------------------------
DROP TABLE IF EXISTS `forms`;
CREATE TABLE `forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of forms
-- ----------------------------
BEGIN;
INSERT INTO `forms` VALUES (1, 'Daily Report 2/4/2020', 1, '2020-04-02', '2020-04-02 07:35:48', '2020-04-02 07:35:48');
INSERT INTO `forms` VALUES (2, 'Daily Report 9/6/2020', 1, '2020-06-09', '2020-06-09 04:16:53', '2020-06-09 04:16:53');
INSERT INTO `forms` VALUES (3, 'Daily Report 6/6/2020', 1, '2020-06-06', '2020-06-09 04:19:14', '2020-06-09 04:19:14');
INSERT INTO `forms` VALUES (4, 'Daily Report 5/6/2020', 1, '2020-06-05', '2020-06-09 04:20:47', '2020-06-09 04:20:47');
COMMIT;

-- ----------------------------
-- Table structure for labor_work_types
-- ----------------------------
DROP TABLE IF EXISTS `labor_work_types`;
CREATE TABLE `labor_work_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of labor_work_types
-- ----------------------------
BEGIN;
INSERT INTO `labor_work_types` VALUES (1, 'Helper', NULL, '2020-04-02 08:00:46', '2020-04-02 08:00:46');
INSERT INTO `labor_work_types` VALUES (2, 'Electrical Technician', NULL, '2020-04-02 08:00:58', '2020-04-02 08:00:58');
COMMIT;

-- ----------------------------
-- Table structure for labors
-- ----------------------------
DROP TABLE IF EXISTS `labors`;
CREATE TABLE `labors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `specialty_id` int(11) DEFAULT NULL,
  `phone` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci,
  `year_of_employment` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of labors
-- ----------------------------
BEGIN;
INSERT INTO `labors` VALUES (1, 'Gang 2', 'L-1', 199, 4, NULL, NULL, NULL, 2019, '2020-04-02 07:16:36', '2020-05-15 08:54:53');
INSERT INTO `labors` VALUES (2, 'Gang 1', 'L-2', 145, 3, NULL, NULL, NULL, 2010, '2020-04-02 07:19:33', '2020-05-15 08:54:46');
COMMIT;

-- ----------------------------
-- Table structure for labors_projects
-- ----------------------------
DROP TABLE IF EXISTS `labors_projects`;
CREATE TABLE `labors_projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `labor_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for menu_items
-- ----------------------------
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `icon_class` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menu_items
-- ----------------------------
BEGIN;
INSERT INTO `menu_items` VALUES (1, 1, 'Dashboard', '', '_self', 'voyager-boat', NULL, NULL, 1, '2020-04-01 08:40:00', '2020-04-01 08:40:00', 'voyager.dashboard', NULL);
INSERT INTO `menu_items` VALUES (2, 1, 'Media', '', '_self', 'voyager-images', NULL, 5, 3, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.media.index', NULL);
INSERT INTO `menu_items` VALUES (3, 1, 'Users', '', '_self', 'voyager-person', NULL, 5, 2, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.users.index', NULL);
INSERT INTO `menu_items` VALUES (4, 1, 'Roles', '', '_self', 'voyager-lock', NULL, 5, 1, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.roles.index', NULL);
INSERT INTO `menu_items` VALUES (5, 1, 'Tools', '', '_self', 'voyager-tools', NULL, NULL, 2, '2020-04-01 08:40:00', '2020-04-01 14:37:13', NULL, NULL);
INSERT INTO `menu_items` VALUES (6, 1, 'Menu Builder', '', '_self', 'voyager-list', NULL, 5, 4, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.menus.index', NULL);
INSERT INTO `menu_items` VALUES (7, 1, 'Database', '', '_self', 'voyager-data', NULL, 5, 5, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.database.index', NULL);
INSERT INTO `menu_items` VALUES (8, 1, 'Compass', '', '_self', 'voyager-compass', NULL, 5, 6, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.compass.index', NULL);
INSERT INTO `menu_items` VALUES (9, 1, 'BREAD', '', '_self', 'voyager-bread', NULL, 5, 7, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.bread.index', NULL);
INSERT INTO `menu_items` VALUES (10, 1, 'Settings', '', '_self', 'voyager-settings', NULL, 5, 9, '2020-04-01 08:40:00', '2020-04-01 14:37:18', 'voyager.settings.index', NULL);
INSERT INTO `menu_items` VALUES (11, 1, 'Hooks', '', '_self', 'voyager-hook', NULL, 5, 8, '2020-04-01 08:40:00', '2020-04-01 14:37:13', 'voyager.hooks', NULL);
INSERT INTO `menu_items` VALUES (12, 1, 'Projects', '', '_self', 'voyager-lighthouse', '#000000', 18, 1, '2020-04-01 08:52:02', '2020-04-02 03:28:14', 'voyager.projects.index', 'null');
INSERT INTO `menu_items` VALUES (13, 1, 'Currencies', '', '_self', 'voyager-dollar', NULL, 26, 1, '2020-04-01 09:06:21', '2020-04-01 17:20:00', 'voyager.currencies.index', NULL);
INSERT INTO `menu_items` VALUES (14, 1, 'Areas', '', '_self', 'voyager-categories', NULL, 18, 3, '2020-04-01 09:41:05', '2020-04-01 14:38:56', 'voyager.areas.index', NULL);
INSERT INTO `menu_items` VALUES (15, 1, 'Benchmarks', '', '_self', 'voyager-check', NULL, 19, 1, '2020-04-01 10:09:09', '2020-04-01 14:39:45', 'voyager.benchmarks.index', NULL);
INSERT INTO `menu_items` VALUES (16, 1, 'Project Divisions', '', '_self', 'voyager-list', NULL, 18, 2, '2020-04-01 10:26:00', '2020-04-01 14:38:56', 'voyager.project-divisions.index', NULL);
INSERT INTO `menu_items` VALUES (17, 1, 'Benchmark Details', '', '_self', 'voyager-list-add', NULL, 19, 2, '2020-04-01 11:43:15', '2020-04-01 14:39:48', 'voyager.benchmark-details.index', NULL);
INSERT INTO `menu_items` VALUES (18, 1, 'Project Settings', '', '_self', 'voyager-tree', '#000000', NULL, 4, '2020-04-01 14:37:48', '2020-04-02 03:25:29', NULL, '');
INSERT INTO `menu_items` VALUES (19, 1, 'Benchmark Settings', '', '_self', 'voyager-tag', '#000000', NULL, 5, '2020-04-01 14:39:38', '2020-04-02 03:25:29', NULL, '');
INSERT INTO `menu_items` VALUES (20, 1, 'Forms', '', '_self', 'voyager-browser', NULL, 22, 1, '2020-04-01 15:22:24', '2020-04-01 15:32:32', 'voyager.forms.index', NULL);
INSERT INTO `menu_items` VALUES (21, 1, 'Form Details', '', '_self', 'voyager-window-list', NULL, 22, 2, '2020-04-01 15:28:00', '2020-04-01 15:32:34', 'voyager.form-details.index', NULL);
INSERT INTO `menu_items` VALUES (22, 1, 'Forms', '', '_self', 'voyager-documentation', '#000000', NULL, 6, '2020-04-01 15:32:26', '2020-04-02 03:25:29', NULL, '');
INSERT INTO `menu_items` VALUES (23, 1, 'Labors', '', '_self', 'voyager-people', NULL, 30, 2, '2020-04-01 15:56:43', '2020-04-02 04:19:39', 'voyager.labors.index', NULL);
INSERT INTO `menu_items` VALUES (24, 1, 'Specialties', '', '_self', 'voyager-brush', '#000000', 26, 3, '2020-04-01 17:04:12', '2020-04-02 03:29:16', 'voyager.specialties.index', 'null');
INSERT INTO `menu_items` VALUES (25, 1, 'Countries', '', '_self', 'voyager-world', '#000000', 26, 2, '2020-04-01 17:06:58', '2020-04-01 17:20:00', 'voyager.countries.index', 'null');
INSERT INTO `menu_items` VALUES (26, 1, 'General Settings', '', '_self', 'voyager-settings', '#000000', NULL, 7, '2020-04-01 17:19:31', '2020-04-02 03:25:29', NULL, '');
INSERT INTO `menu_items` VALUES (28, 1, 'Equipments', '', '_self', 'voyager-truck', NULL, 30, 3, '2020-04-01 17:47:21', '2020-04-02 04:19:39', 'voyager.equips.index', NULL);
INSERT INTO `menu_items` VALUES (29, 1, 'Equipment Types', '', '_self', 'voyager-hammer', NULL, 26, 4, '2020-04-01 17:54:41', '2020-04-02 03:25:08', 'voyager.equipment-types.index', NULL);
INSERT INTO `menu_items` VALUES (30, 1, 'Company', '', '_self', 'voyager-company', '#000000', NULL, 3, '2020-04-02 03:24:51', '2020-04-02 03:25:29', NULL, '');
INSERT INTO `menu_items` VALUES (32, 1, 'Form Details Equipments', '', '_self', 'voyager-paint-bucket', NULL, 22, 3, '2020-04-02 03:43:28', '2020-04-02 03:46:03', 'voyager.fd-equips.index', NULL);
INSERT INTO `menu_items` VALUES (33, 1, 'Form Details Labors', '', '_self', 'voyager-group', NULL, 22, 4, '2020-04-02 03:49:49', '2020-04-02 03:51:44', 'voyager.fd-labors.index', NULL);
INSERT INTO `menu_items` VALUES (34, 1, 'Company Infos', '', '_self', 'voyager-info-circled', NULL, 30, 1, '2020-04-02 04:07:12', '2020-04-02 04:19:39', 'voyager.company-infos.index', NULL);
INSERT INTO `menu_items` VALUES (35, 1, 'Labor Work Types', '', '_self', 'voyager-pirate-swords', NULL, 26, 5, '2020-04-02 07:55:14', '2020-04-02 07:55:29', 'voyager.labor-work-types.index', NULL);
INSERT INTO `menu_items` VALUES (36, 1, 'Instant Analysis', '', '_self', 'voyager-pie-chart', '#000000', NULL, 8, '2020-05-12 07:22:41', '2020-05-12 07:22:41', 'voyager.instantanalysis.index', NULL);
INSERT INTO `menu_items` VALUES (37, 1, 'Charts Analysis', '', '_self', 'voyager-pie-chart', '#000000', NULL, 9, '2020-05-12 07:22:41', '2020-05-12 07:22:41', 'voyager.chartsanalysis.index', NULL);
COMMIT;

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menus
-- ----------------------------
BEGIN;
INSERT INTO `menus` VALUES (1, 'admin', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2016_01_01_000000_add_voyager_user_fields', 1);
INSERT INTO `migrations` VALUES (3, '2016_01_01_000000_create_data_types_table', 1);
INSERT INTO `migrations` VALUES (4, '2016_05_19_173453_create_menu_table', 1);
INSERT INTO `migrations` VALUES (5, '2016_10_21_190000_create_roles_table', 1);
INSERT INTO `migrations` VALUES (6, '2016_10_21_190000_create_settings_table', 1);
INSERT INTO `migrations` VALUES (7, '2016_11_30_135954_create_permission_table', 1);
INSERT INTO `migrations` VALUES (8, '2016_11_30_141208_create_permission_role_table', 1);
INSERT INTO `migrations` VALUES (9, '2016_12_26_201236_data_types__add__server_side', 1);
INSERT INTO `migrations` VALUES (10, '2017_01_13_000000_add_route_to_menu_items_table', 1);
INSERT INTO `migrations` VALUES (11, '2017_01_14_005015_create_translations_table', 1);
INSERT INTO `migrations` VALUES (12, '2017_01_15_000000_make_table_name_nullable_in_permissions_table', 1);
INSERT INTO `migrations` VALUES (13, '2017_03_06_000000_add_controller_to_data_types_table', 1);
INSERT INTO `migrations` VALUES (14, '2017_04_21_000000_add_order_to_data_rows_table', 1);
INSERT INTO `migrations` VALUES (15, '2017_07_05_210000_add_policyname_to_data_types_table', 1);
INSERT INTO `migrations` VALUES (16, '2017_08_05_000000_add_group_to_settings_table', 1);
INSERT INTO `migrations` VALUES (17, '2017_11_26_013050_add_user_role_relationship', 1);
INSERT INTO `migrations` VALUES (18, '2017_11_26_015000_create_user_roles_table', 1);
INSERT INTO `migrations` VALUES (19, '2018_03_11_000000_add_user_settings', 1);
INSERT INTO `migrations` VALUES (20, '2018_03_14_000000_add_details_to_data_types_table', 1);
INSERT INTO `migrations` VALUES (21, '2018_03_16_000000_make_settings_value_nullable', 1);
INSERT INTO `migrations` VALUES (22, '2019_08_19_000000_create_failed_jobs_table', 1);
COMMIT;

-- ----------------------------
-- Table structure for permission_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permission_role
-- ----------------------------
BEGIN;
INSERT INTO `permission_role` VALUES (1, 1);
INSERT INTO `permission_role` VALUES (2, 1);
INSERT INTO `permission_role` VALUES (3, 1);
INSERT INTO `permission_role` VALUES (4, 1);
INSERT INTO `permission_role` VALUES (5, 1);
INSERT INTO `permission_role` VALUES (6, 1);
INSERT INTO `permission_role` VALUES (7, 1);
INSERT INTO `permission_role` VALUES (8, 1);
INSERT INTO `permission_role` VALUES (9, 1);
INSERT INTO `permission_role` VALUES (10, 1);
INSERT INTO `permission_role` VALUES (11, 1);
INSERT INTO `permission_role` VALUES (12, 1);
INSERT INTO `permission_role` VALUES (13, 1);
INSERT INTO `permission_role` VALUES (14, 1);
INSERT INTO `permission_role` VALUES (15, 1);
INSERT INTO `permission_role` VALUES (16, 1);
INSERT INTO `permission_role` VALUES (17, 1);
INSERT INTO `permission_role` VALUES (18, 1);
INSERT INTO `permission_role` VALUES (19, 1);
INSERT INTO `permission_role` VALUES (20, 1);
INSERT INTO `permission_role` VALUES (21, 1);
INSERT INTO `permission_role` VALUES (22, 1);
INSERT INTO `permission_role` VALUES (23, 1);
INSERT INTO `permission_role` VALUES (24, 1);
INSERT INTO `permission_role` VALUES (25, 1);
INSERT INTO `permission_role` VALUES (26, 1);
INSERT INTO `permission_role` VALUES (27, 1);
INSERT INTO `permission_role` VALUES (28, 1);
INSERT INTO `permission_role` VALUES (29, 1);
INSERT INTO `permission_role` VALUES (30, 1);
INSERT INTO `permission_role` VALUES (31, 1);
INSERT INTO `permission_role` VALUES (32, 1);
INSERT INTO `permission_role` VALUES (33, 1);
INSERT INTO `permission_role` VALUES (34, 1);
INSERT INTO `permission_role` VALUES (35, 1);
INSERT INTO `permission_role` VALUES (36, 1);
INSERT INTO `permission_role` VALUES (37, 1);
INSERT INTO `permission_role` VALUES (38, 1);
INSERT INTO `permission_role` VALUES (39, 1);
INSERT INTO `permission_role` VALUES (40, 1);
INSERT INTO `permission_role` VALUES (41, 1);
INSERT INTO `permission_role` VALUES (42, 1);
INSERT INTO `permission_role` VALUES (43, 1);
INSERT INTO `permission_role` VALUES (44, 1);
INSERT INTO `permission_role` VALUES (45, 1);
INSERT INTO `permission_role` VALUES (46, 1);
INSERT INTO `permission_role` VALUES (47, 1);
INSERT INTO `permission_role` VALUES (48, 1);
INSERT INTO `permission_role` VALUES (49, 1);
INSERT INTO `permission_role` VALUES (50, 1);
INSERT INTO `permission_role` VALUES (51, 1);
INSERT INTO `permission_role` VALUES (52, 1);
INSERT INTO `permission_role` VALUES (53, 1);
INSERT INTO `permission_role` VALUES (54, 1);
INSERT INTO `permission_role` VALUES (55, 1);
INSERT INTO `permission_role` VALUES (56, 1);
INSERT INTO `permission_role` VALUES (57, 1);
INSERT INTO `permission_role` VALUES (58, 1);
INSERT INTO `permission_role` VALUES (59, 1);
INSERT INTO `permission_role` VALUES (60, 1);
INSERT INTO `permission_role` VALUES (61, 1);
INSERT INTO `permission_role` VALUES (62, 1);
INSERT INTO `permission_role` VALUES (63, 1);
INSERT INTO `permission_role` VALUES (64, 1);
INSERT INTO `permission_role` VALUES (65, 1);
INSERT INTO `permission_role` VALUES (66, 1);
INSERT INTO `permission_role` VALUES (67, 1);
INSERT INTO `permission_role` VALUES (68, 1);
INSERT INTO `permission_role` VALUES (69, 1);
INSERT INTO `permission_role` VALUES (70, 1);
INSERT INTO `permission_role` VALUES (71, 1);
INSERT INTO `permission_role` VALUES (72, 1);
INSERT INTO `permission_role` VALUES (73, 1);
INSERT INTO `permission_role` VALUES (74, 1);
INSERT INTO `permission_role` VALUES (75, 1);
INSERT INTO `permission_role` VALUES (76, 1);
INSERT INTO `permission_role` VALUES (77, 1);
INSERT INTO `permission_role` VALUES (78, 1);
INSERT INTO `permission_role` VALUES (79, 1);
INSERT INTO `permission_role` VALUES (80, 1);
INSERT INTO `permission_role` VALUES (81, 1);
INSERT INTO `permission_role` VALUES (87, 1);
INSERT INTO `permission_role` VALUES (88, 1);
INSERT INTO `permission_role` VALUES (89, 1);
INSERT INTO `permission_role` VALUES (90, 1);
INSERT INTO `permission_role` VALUES (91, 1);
INSERT INTO `permission_role` VALUES (92, 1);
INSERT INTO `permission_role` VALUES (93, 1);
INSERT INTO `permission_role` VALUES (94, 1);
INSERT INTO `permission_role` VALUES (95, 1);
INSERT INTO `permission_role` VALUES (96, 1);
INSERT INTO `permission_role` VALUES (102, 1);
INSERT INTO `permission_role` VALUES (103, 1);
INSERT INTO `permission_role` VALUES (104, 1);
INSERT INTO `permission_role` VALUES (105, 1);
INSERT INTO `permission_role` VALUES (106, 1);
INSERT INTO `permission_role` VALUES (107, 1);
INSERT INTO `permission_role` VALUES (108, 1);
INSERT INTO `permission_role` VALUES (109, 1);
INSERT INTO `permission_role` VALUES (110, 1);
INSERT INTO `permission_role` VALUES (111, 1);
INSERT INTO `permission_role` VALUES (112, 1);
INSERT INTO `permission_role` VALUES (113, 1);
INSERT INTO `permission_role` VALUES (114, 1);
INSERT INTO `permission_role` VALUES (115, 1);
INSERT INTO `permission_role` VALUES (116, 1);
INSERT INTO `permission_role` VALUES (117, 1);
INSERT INTO `permission_role` VALUES (118, 1);
INSERT INTO `permission_role` VALUES (119, 1);
INSERT INTO `permission_role` VALUES (120, 1);
INSERT INTO `permission_role` VALUES (121, 1);
COMMIT;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES (1, 'browse_admin', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (2, 'browse_bread', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (3, 'browse_database', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (4, 'browse_media', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (5, 'browse_compass', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (6, 'browse_menus', 'menus', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (7, 'read_menus', 'menus', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (8, 'edit_menus', 'menus', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (9, 'add_menus', 'menus', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (10, 'delete_menus', 'menus', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (11, 'browse_roles', 'roles', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (12, 'read_roles', 'roles', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (13, 'edit_roles', 'roles', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (14, 'add_roles', 'roles', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (15, 'delete_roles', 'roles', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (16, 'browse_users', 'users', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (17, 'read_users', 'users', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (18, 'edit_users', 'users', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (19, 'add_users', 'users', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (20, 'delete_users', 'users', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (21, 'browse_settings', 'settings', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (22, 'read_settings', 'settings', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (23, 'edit_settings', 'settings', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (24, 'add_settings', 'settings', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (25, 'delete_settings', 'settings', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (26, 'browse_hooks', NULL, '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `permissions` VALUES (27, 'browse_projects', 'projects', '2020-04-01 08:52:02', '2020-04-01 08:52:02');
INSERT INTO `permissions` VALUES (28, 'read_projects', 'projects', '2020-04-01 08:52:02', '2020-04-01 08:52:02');
INSERT INTO `permissions` VALUES (29, 'edit_projects', 'projects', '2020-04-01 08:52:02', '2020-04-01 08:52:02');
INSERT INTO `permissions` VALUES (30, 'add_projects', 'projects', '2020-04-01 08:52:02', '2020-04-01 08:52:02');
INSERT INTO `permissions` VALUES (31, 'delete_projects', 'projects', '2020-04-01 08:52:02', '2020-04-01 08:52:02');
INSERT INTO `permissions` VALUES (32, 'browse_currencies', 'currencies', '2020-04-01 09:06:21', '2020-04-01 09:06:21');
INSERT INTO `permissions` VALUES (33, 'read_currencies', 'currencies', '2020-04-01 09:06:21', '2020-04-01 09:06:21');
INSERT INTO `permissions` VALUES (34, 'edit_currencies', 'currencies', '2020-04-01 09:06:21', '2020-04-01 09:06:21');
INSERT INTO `permissions` VALUES (35, 'add_currencies', 'currencies', '2020-04-01 09:06:21', '2020-04-01 09:06:21');
INSERT INTO `permissions` VALUES (36, 'delete_currencies', 'currencies', '2020-04-01 09:06:21', '2020-04-01 09:06:21');
INSERT INTO `permissions` VALUES (37, 'browse_areas', 'areas', '2020-04-01 09:41:05', '2020-04-01 09:41:05');
INSERT INTO `permissions` VALUES (38, 'read_areas', 'areas', '2020-04-01 09:41:05', '2020-04-01 09:41:05');
INSERT INTO `permissions` VALUES (39, 'edit_areas', 'areas', '2020-04-01 09:41:05', '2020-04-01 09:41:05');
INSERT INTO `permissions` VALUES (40, 'add_areas', 'areas', '2020-04-01 09:41:05', '2020-04-01 09:41:05');
INSERT INTO `permissions` VALUES (41, 'delete_areas', 'areas', '2020-04-01 09:41:05', '2020-04-01 09:41:05');
INSERT INTO `permissions` VALUES (42, 'browse_benchmarks', 'benchmarks', '2020-04-01 10:09:09', '2020-04-01 10:09:09');
INSERT INTO `permissions` VALUES (43, 'read_benchmarks', 'benchmarks', '2020-04-01 10:09:09', '2020-04-01 10:09:09');
INSERT INTO `permissions` VALUES (44, 'edit_benchmarks', 'benchmarks', '2020-04-01 10:09:09', '2020-04-01 10:09:09');
INSERT INTO `permissions` VALUES (45, 'add_benchmarks', 'benchmarks', '2020-04-01 10:09:09', '2020-04-01 10:09:09');
INSERT INTO `permissions` VALUES (46, 'delete_benchmarks', 'benchmarks', '2020-04-01 10:09:09', '2020-04-01 10:09:09');
INSERT INTO `permissions` VALUES (47, 'browse_project_divisions', 'project_divisions', '2020-04-01 10:26:00', '2020-04-01 10:26:00');
INSERT INTO `permissions` VALUES (48, 'read_project_divisions', 'project_divisions', '2020-04-01 10:26:00', '2020-04-01 10:26:00');
INSERT INTO `permissions` VALUES (49, 'edit_project_divisions', 'project_divisions', '2020-04-01 10:26:00', '2020-04-01 10:26:00');
INSERT INTO `permissions` VALUES (50, 'add_project_divisions', 'project_divisions', '2020-04-01 10:26:00', '2020-04-01 10:26:00');
INSERT INTO `permissions` VALUES (51, 'delete_project_divisions', 'project_divisions', '2020-04-01 10:26:00', '2020-04-01 10:26:00');
INSERT INTO `permissions` VALUES (52, 'browse_benchmark_details', 'benchmark_details', '2020-04-01 11:43:15', '2020-04-01 11:43:15');
INSERT INTO `permissions` VALUES (53, 'read_benchmark_details', 'benchmark_details', '2020-04-01 11:43:15', '2020-04-01 11:43:15');
INSERT INTO `permissions` VALUES (54, 'edit_benchmark_details', 'benchmark_details', '2020-04-01 11:43:15', '2020-04-01 11:43:15');
INSERT INTO `permissions` VALUES (55, 'add_benchmark_details', 'benchmark_details', '2020-04-01 11:43:15', '2020-04-01 11:43:15');
INSERT INTO `permissions` VALUES (56, 'delete_benchmark_details', 'benchmark_details', '2020-04-01 11:43:15', '2020-04-01 11:43:15');
INSERT INTO `permissions` VALUES (57, 'browse_forms', 'forms', '2020-04-01 15:22:24', '2020-04-01 15:22:24');
INSERT INTO `permissions` VALUES (58, 'read_forms', 'forms', '2020-04-01 15:22:24', '2020-04-01 15:22:24');
INSERT INTO `permissions` VALUES (59, 'edit_forms', 'forms', '2020-04-01 15:22:24', '2020-04-01 15:22:24');
INSERT INTO `permissions` VALUES (60, 'add_forms', 'forms', '2020-04-01 15:22:24', '2020-04-01 15:22:24');
INSERT INTO `permissions` VALUES (61, 'delete_forms', 'forms', '2020-04-01 15:22:24', '2020-04-01 15:22:24');
INSERT INTO `permissions` VALUES (62, 'browse_form_details', 'form_details', '2020-04-01 15:28:00', '2020-04-01 15:28:00');
INSERT INTO `permissions` VALUES (63, 'read_form_details', 'form_details', '2020-04-01 15:28:00', '2020-04-01 15:28:00');
INSERT INTO `permissions` VALUES (64, 'edit_form_details', 'form_details', '2020-04-01 15:28:00', '2020-04-01 15:28:00');
INSERT INTO `permissions` VALUES (65, 'add_form_details', 'form_details', '2020-04-01 15:28:00', '2020-04-01 15:28:00');
INSERT INTO `permissions` VALUES (66, 'delete_form_details', 'form_details', '2020-04-01 15:28:00', '2020-04-01 15:28:00');
INSERT INTO `permissions` VALUES (67, 'browse_labors', 'labors', '2020-04-01 15:56:43', '2020-04-01 15:56:43');
INSERT INTO `permissions` VALUES (68, 'read_labors', 'labors', '2020-04-01 15:56:43', '2020-04-01 15:56:43');
INSERT INTO `permissions` VALUES (69, 'edit_labors', 'labors', '2020-04-01 15:56:43', '2020-04-01 15:56:43');
INSERT INTO `permissions` VALUES (70, 'add_labors', 'labors', '2020-04-01 15:56:43', '2020-04-01 15:56:43');
INSERT INTO `permissions` VALUES (71, 'delete_labors', 'labors', '2020-04-01 15:56:43', '2020-04-01 15:56:43');
INSERT INTO `permissions` VALUES (72, 'browse_specialties', 'specialties', '2020-04-01 17:04:12', '2020-04-01 17:04:12');
INSERT INTO `permissions` VALUES (73, 'read_specialties', 'specialties', '2020-04-01 17:04:12', '2020-04-01 17:04:12');
INSERT INTO `permissions` VALUES (74, 'edit_specialties', 'specialties', '2020-04-01 17:04:12', '2020-04-01 17:04:12');
INSERT INTO `permissions` VALUES (75, 'add_specialties', 'specialties', '2020-04-01 17:04:12', '2020-04-01 17:04:12');
INSERT INTO `permissions` VALUES (76, 'delete_specialties', 'specialties', '2020-04-01 17:04:12', '2020-04-01 17:04:12');
INSERT INTO `permissions` VALUES (77, 'browse_countries', 'countries', '2020-04-01 17:06:58', '2020-04-01 17:06:58');
INSERT INTO `permissions` VALUES (78, 'read_countries', 'countries', '2020-04-01 17:06:58', '2020-04-01 17:06:58');
INSERT INTO `permissions` VALUES (79, 'edit_countries', 'countries', '2020-04-01 17:06:58', '2020-04-01 17:06:58');
INSERT INTO `permissions` VALUES (80, 'add_countries', 'countries', '2020-04-01 17:06:58', '2020-04-01 17:06:58');
INSERT INTO `permissions` VALUES (81, 'delete_countries', 'countries', '2020-04-01 17:06:58', '2020-04-01 17:06:58');
INSERT INTO `permissions` VALUES (87, 'browse_equips', 'equips', '2020-04-01 17:47:21', '2020-04-01 17:47:21');
INSERT INTO `permissions` VALUES (88, 'read_equips', 'equips', '2020-04-01 17:47:21', '2020-04-01 17:47:21');
INSERT INTO `permissions` VALUES (89, 'edit_equips', 'equips', '2020-04-01 17:47:21', '2020-04-01 17:47:21');
INSERT INTO `permissions` VALUES (90, 'add_equips', 'equips', '2020-04-01 17:47:21', '2020-04-01 17:47:21');
INSERT INTO `permissions` VALUES (91, 'delete_equips', 'equips', '2020-04-01 17:47:21', '2020-04-01 17:47:21');
INSERT INTO `permissions` VALUES (92, 'browse_equipment_types', 'equipment_types', '2020-04-01 17:54:41', '2020-04-01 17:54:41');
INSERT INTO `permissions` VALUES (93, 'read_equipment_types', 'equipment_types', '2020-04-01 17:54:41', '2020-04-01 17:54:41');
INSERT INTO `permissions` VALUES (94, 'edit_equipment_types', 'equipment_types', '2020-04-01 17:54:41', '2020-04-01 17:54:41');
INSERT INTO `permissions` VALUES (95, 'add_equipment_types', 'equipment_types', '2020-04-01 17:54:41', '2020-04-01 17:54:41');
INSERT INTO `permissions` VALUES (96, 'delete_equipment_types', 'equipment_types', '2020-04-01 17:54:41', '2020-04-01 17:54:41');
INSERT INTO `permissions` VALUES (102, 'browse_fd_equips', 'fd_equips', '2020-04-02 03:43:28', '2020-04-02 03:43:28');
INSERT INTO `permissions` VALUES (103, 'read_fd_equips', 'fd_equips', '2020-04-02 03:43:28', '2020-04-02 03:43:28');
INSERT INTO `permissions` VALUES (104, 'edit_fd_equips', 'fd_equips', '2020-04-02 03:43:28', '2020-04-02 03:43:28');
INSERT INTO `permissions` VALUES (105, 'add_fd_equips', 'fd_equips', '2020-04-02 03:43:28', '2020-04-02 03:43:28');
INSERT INTO `permissions` VALUES (106, 'delete_fd_equips', 'fd_equips', '2020-04-02 03:43:28', '2020-04-02 03:43:28');
INSERT INTO `permissions` VALUES (107, 'browse_fd_labors', 'fd_labors', '2020-04-02 03:49:49', '2020-04-02 03:49:49');
INSERT INTO `permissions` VALUES (108, 'read_fd_labors', 'fd_labors', '2020-04-02 03:49:49', '2020-04-02 03:49:49');
INSERT INTO `permissions` VALUES (109, 'edit_fd_labors', 'fd_labors', '2020-04-02 03:49:49', '2020-04-02 03:49:49');
INSERT INTO `permissions` VALUES (110, 'add_fd_labors', 'fd_labors', '2020-04-02 03:49:49', '2020-04-02 03:49:49');
INSERT INTO `permissions` VALUES (111, 'delete_fd_labors', 'fd_labors', '2020-04-02 03:49:49', '2020-04-02 03:49:49');
INSERT INTO `permissions` VALUES (112, 'browse_company_infos', 'company_infos', '2020-04-02 04:07:12', '2020-04-02 04:07:12');
INSERT INTO `permissions` VALUES (113, 'read_company_infos', 'company_infos', '2020-04-02 04:07:12', '2020-04-02 04:07:12');
INSERT INTO `permissions` VALUES (114, 'edit_company_infos', 'company_infos', '2020-04-02 04:07:12', '2020-04-02 04:07:12');
INSERT INTO `permissions` VALUES (115, 'add_company_infos', 'company_infos', '2020-04-02 04:07:12', '2020-04-02 04:07:12');
INSERT INTO `permissions` VALUES (116, 'delete_company_infos', 'company_infos', '2020-04-02 04:07:12', '2020-04-02 04:07:12');
INSERT INTO `permissions` VALUES (117, 'browse_labor_work_types', 'labor_work_types', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
INSERT INTO `permissions` VALUES (118, 'read_labor_work_types', 'labor_work_types', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
INSERT INTO `permissions` VALUES (119, 'edit_labor_work_types', 'labor_work_types', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
INSERT INTO `permissions` VALUES (120, 'add_labor_work_types', 'labor_work_types', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
INSERT INTO `permissions` VALUES (121, 'delete_labor_work_types', 'labor_work_types', '2020-04-02 07:55:14', '2020-04-02 07:55:14');
COMMIT;

-- ----------------------------
-- Table structure for project_divisions
-- ----------------------------
DROP TABLE IF EXISTS `project_divisions`;
CREATE TABLE `project_divisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_of_measure` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `benchmarking_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of project_divisions
-- ----------------------------
BEGIN;
INSERT INTO `project_divisions` VALUES (1, 'Electrical', '1', 1, NULL, NULL, '2020-04-02 07:25:42', '2020-04-02 07:29:08', NULL, NULL);
INSERT INTO `project_divisions` VALUES (2, 'Cable Trays', '1.1', 1, 1, NULL, '2020-04-02 07:26:05', '2020-04-17 07:43:17', NULL, NULL);
INSERT INTO `project_divisions` VALUES (3, 'Cable Trays for Power Systems', '1.1.1', 1, 2, NULL, '2020-04-02 07:26:42', '2020-04-17 09:23:10', NULL, NULL);
INSERT INTO `project_divisions` VALUES (4, 'Cable Tray for Power Systems 60x50mm', '1.1.1.1', 1, 3, NULL, '2020-04-02 07:27:27', '2020-04-17 07:43:31', 'Linear Meter', NULL);
INSERT INTO `project_divisions` VALUES (5, 'Cable Tray for Power Systems 80x30mm', '1.1.1.2', 1, 3, NULL, '2020-04-16 07:13:19', '2020-04-21 08:28:31', 'Linear Meter', NULL);
INSERT INTO `project_divisions` VALUES (6, 'Marking & Support', '1.1.1.3', 1, 3, NULL, '2020-04-27 07:48:50', '2020-04-27 07:49:08', 'N/A', NULL);
COMMIT;

-- ----------------------------
-- Table structure for projects
-- ----------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinates` point DEFAULT NULL,
  `reference` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `architect` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `estimated_delivery_date` date DEFAULT NULL,
  `total_budget` bigint(20) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `datetime_format` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `use_percentage` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of projects
-- ----------------------------
BEGIN;
INSERT INTO `projects` VALUES (1, 'Oceanna', 'Lagos, Nigeria', ST_GeomFromText('POINT(-116.904 32.6949)'), 'OC-2020', NULL, NULL, '2019-07-01', '2022-07-01', 12000000, 1, NULL, NULL, '2020-04-02 07:22:53', '2020-04-02 07:22:53', 'projects/April2020/OP3vHRm9DMcRlCM9QWC4.jpg', NULL, 'running', NULL);
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES (1, 'admin', 'Administrator', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
INSERT INTO `roles` VALUES (2, 'user', 'Normal User', '2020-04-01 08:40:00', '2020-04-01 08:40:00');
COMMIT;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `details` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `group` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of settings
-- ----------------------------
BEGIN;
INSERT INTO `settings` VALUES (1, 'site.title', 'Site Title', 'Site Title', '', 'text', 1, 'Site');
INSERT INTO `settings` VALUES (2, 'site.description', 'Site Description', 'Site Description', '', 'text', 2, 'Site');
INSERT INTO `settings` VALUES (3, 'site.logo', 'Site Logo', 'settings/April2020/ZHY8aKOs4s2rL4FroLBC.png', '', 'image', 3, 'Site');
INSERT INTO `settings` VALUES (4, 'site.google_analytics_tracking_id', 'Google Analytics Tracking ID', NULL, '', 'text', 4, 'Site');
INSERT INTO `settings` VALUES (5, 'admin.bg_image', 'Admin Background Image', 'settings/April2020/jm8Zjr3cN8OstUDQVqE7.jpg', '', 'image', 5, 'Admin');
INSERT INTO `settings` VALUES (6, 'admin.title', 'Admin Title', 'DIM', '', 'text', 1, 'Admin');
INSERT INTO `settings` VALUES (7, 'admin.description', 'Admin Description', 'Digital Industrial Module', '', 'text', 2, 'Admin');
INSERT INTO `settings` VALUES (8, 'admin.loader', 'Admin Loader', '', '', 'image', 3, 'Admin');
INSERT INTO `settings` VALUES (9, 'admin.icon_image', 'Admin Icon Image', 'settings/May2020/0vT6inJ8IBHmkMFogbiV.png', '', 'image', 4, 'Admin');
INSERT INTO `settings` VALUES (10, 'admin.google_analytics_client_id', 'Google Analytics Client ID (used for admin dashboard)', NULL, '', 'text', 1, 'Admin');
COMMIT;

-- ----------------------------
-- Table structure for specialties
-- ----------------------------
DROP TABLE IF EXISTS `specialties`;
CREATE TABLE `specialties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(3000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of specialties
-- ----------------------------
BEGIN;
INSERT INTO `specialties` VALUES (1, 'Superintendent Electrical', NULL, NULL, '2020-04-02 07:13:37', '2020-04-02 07:14:02');
INSERT INTO `specialties` VALUES (2, 'Superintendent Mechanical', NULL, NULL, '2020-04-02 07:14:13', '2020-04-02 07:14:13');
INSERT INTO `specialties` VALUES (3, 'Foreman', NULL, NULL, '2020-04-02 07:14:27', '2020-04-02 07:14:27');
INSERT INTO `specialties` VALUES (4, 'Gang', NULL, NULL, '2020-04-02 07:14:37', '2020-04-02 07:14:50');
COMMIT;

-- ----------------------------
-- Table structure for translations
-- ----------------------------
DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_key` int(10) unsigned NOT NULL,
  `locale` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_table_name_column_name_foreign_key_locale_unique` (`table_name`,`column_name`,`foreign_key`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_user_id_index` (`user_id`),
  KEY `user_roles_role_id_index` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT 'users/default.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES (1, 1, 'admin', 'souheil.jabbour@in2uitions.com', 'users/default.png', NULL, '$2y$10$7Itqe2hjcHPZfmYbyCP2kO.KEI3dfCqaebU5L1tolKO5OeIvXLXD2', NULL, NULL, '2020-04-01 08:41:02', '2020-04-01 08:41:02');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
