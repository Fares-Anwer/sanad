-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 28 يوليو 2026 الساعة 23:23
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sanad_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `devices`
--

CREATE TABLE `devices` (
  `id` int(10) UNSIGNED NOT NULL,
  `donor_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` enum('respiratory','mobility','beds_clinical','diagnostic') NOT NULL,
  `condition_rating` enum('excellent','good','acceptable') NOT NULL,
  `description` text NOT NULL,
  `offer_type` enum('donation','loan') NOT NULL,
  `loan_duration` varchar(50) DEFAULT NULL,
  `governorate` varchar(50) NOT NULL,
  `district` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('pending_review','active','under_request_review','loaned','rejected') NOT NULL DEFAULT 'pending_review',
  `rejection_reason` text DEFAULT NULL,
  `admin_reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `admin_reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `devices`
--

INSERT INTO `devices` (`id`, `donor_id`, `name`, `category`, `condition_rating`, `description`, `offer_type`, `loan_duration`, `governorate`, `district`, `latitude`, `longitude`, `status`, `rejection_reason`, `admin_reviewed_by`, `admin_reviewed_at`, `created_at`) VALUES
(1, 2, 'جهاز أكسجين منزلي طراز Philips EverFlo', 'respiratory', 'good', 'جهاز توليد أكسجين مستمر بقدرة 5 لتر في الدقيقة، مناسب للاستخدام المنزلي. تم استخدامه لمدة 6 أشهر فقط ويعمل بكفاءة عالية. يشمل أنبوب الأكسجين والترآب المائي.', 'donation', NULL, 'amanat_al_asimah', 'التحرير', 15.36940000, 44.19100000, 'pending_review', NULL, NULL, NULL, '2025-03-01 06:00:00'),
(2, 4, 'ساعة قياس الأكسجين في الدم Masimo', 'diagnostic', 'excellent', 'جهاز قياس تشبع الأكسجين في الدم (BPM/SpO2) من نوع Masimo. جديد غير مستعمل، في علسته الأصلية مع الضمان. دقيق جداً ويُستخدم في المستشفيات والعيادات.', 'donation', NULL, 'taiz', 'القاهرة', 13.57890000, 44.02190000, 'active', NULL, NULL, NULL, '2025-03-02 07:15:00'),
(3, 3, 'كرسي متحرك يدوي قابل للطي', 'mobility', 'good', 'كرسي متحرك معدني خفيف الوزن، قابل للطي وسهل التخزين. المقعد والظهر من القماش المقسّى. مناسب للتنقل اليومي.', 'donation', NULL, 'aden', 'المعلا', 12.77940000, 45.01860000, 'active', NULL, 1, '2025-03-03 11:00:00', '2025-02-10 05:00:00'),
(4, 5, 'جهاز تنفس صناعي متنقل ResMed', 'respiratory', 'excellent', 'جهاز ضغط الهواء الإيجابي المستمر (CPAP) ResMed AirSense 10. يستخدم لعلاج اضطرابات النوم واضطرابات التنفس. يحتوي على كيس تخزين وكتيب التعليمات بالعربية.', 'loan', '3_months', 'lahij', 'الحوطة', 13.05800000, 44.88300000, 'active', NULL, 1, '2025-03-03 11:30:00', '2025-02-11 06:30:00'),
(5, 6, 'سرير طبي يدوي للعناية المنزلية', 'beds_clinical', 'acceptable', 'سرير طبي يدوي بارتفاع قابل للتعديل. المعدن لا يزال سليماً لكن القماش يحتاج تنظيف. مناسب للمرضى الذين يحتاجون للاستلقاء لفترة مديدة في المنزل.', 'donation', NULL, 'ibb', 'السياني', 13.96077300, 44.17223800, 'active', NULL, 1, '2025-03-04 09:00:00', '2025-02-12 07:00:00'),
(6, 2, 'جهاز قياس ضغط الدم الأوتوماتيكي Omron', 'diagnostic', 'excellent', 'جهاز قياس ضغط الدم رقمي من شرطة Omron طراز M7. جديد في العلبة. يقيس ضغط الدم الشرياني ونبض القلب بدقة عالية. مناسب للاستخدام المنزلي.', 'donation', NULL, 'amanat_al_asimah', 'الثورة', 15.38000000, 44.20000000, 'active', NULL, 1, '2025-03-04 10:00:00', '2025-02-13 08:15:00'),
(7, 8, 'جهاز أكسجين مركزي Hospital Grade', 'respiratory', 'good', 'جهاز أكسجين مركزية قوي بقدرة 10 لتر في الدقيقة. مناسب للمستوصفات والعيادات الصغيرة. يحتاج إلى توصيل بالكهرباء فقط.', 'loan', '1_month', 'al_hudaydah', 'الحالي', 14.79690000, 42.98140000, 'active', NULL, 1, '2025-03-05 08:30:00', '2025-02-14 09:00:00'),
(8, 9, 'جهاز فحص السمع الرقمي', 'diagnostic', 'excellent', 'جهاز فحص سمع أوتوماتيكي لتقييم حدة السمع. مناسب للعيادات السمعية. يوفر تقريراً مطبوعاً بالنتائج.', 'donation', NULL, 'hadramawt', 'السيئون', 15.95600000, 48.78200000, 'active', NULL, 1, '2025-03-05 09:00:00', '2025-02-15 10:30:00'),
(10, 6, 'ساعة قياس نبض الطراز Beurer', 'diagnostic', 'good', 'جهاز قياس نبض وتشبع الأكسجين في الدم. خفيف وسهل الاستخدام. يعمل بالبطارية ولا يحتاج توصيل بالكهرباء.', 'donation', NULL, 'ibb', 'الرضمة', 13.95000000, 44.20000000, 'under_request_review', NULL, 1, '2025-03-06 11:00:00', '2025-02-17 06:00:00'),
(11, 3, 'جهاز غسيل كلى متنقل DaVita', 'beds_clinical', 'good', 'جهاز غسيل كلى متنقل بقدرة عالية. تم صيانته مؤخراً ويحتاج متابعة دورية. مناسب للمرضى الذين لا يستطيعون الذهاب للمستشفى بانتظام.', 'loan', '3 أشهر', 'aden', 'كريتر', 12.79000000, 45.03000000, 'under_request_review', NULL, 1, '2025-03-07 08:00:00', '2025-02-18 07:30:00'),
(12, 5, 'جهاز تنفس نابضي Nebulizer طراز Omron', 'respiratory', 'excellent', 'جهاز البخاخ الطبي (Nebulizer) لتحويل الدواء إلى رذاذ للاستنشاق. مناسب لمرضى الربو والتهاب الشعب الهوائية. هادئ وسريع.', 'loan', '3 أشهر', 'lahij', 'تبن', 13.02000000, 44.85000000, 'loaned', NULL, 1, '2025-02-20 10:00:00', '2025-02-05 08:00:00'),
(13, 8, 'كرسي متحرك كهربائي مع ميزان', 'mobility', 'good', 'كرسي متحرك كهربائي بالكامل مع تحكم يدوي وميزان. مناسب للمرضى الذين لا يستطيعون استخدام الكرسي اليدوي. يحتاج بطاريتين.', 'loan', '6 أشهر', 'al_hudaydah', 'الخوخة', 14.75000000, 42.90000000, 'loaned', NULL, 1, '2025-02-22 11:00:00', '2025-02-08 09:30:00'),
(14, 6, 'سرير رعاية موتى مع رف للأجهزة', 'beds_clinical', 'excellent', 'سرير طبي كهربائي بـ 3 محركات لتعديل الرأس والقدمين والارتفاع. مع رف معدني للأجهزة الطبية. جديد تقريباً.', 'donation', NULL, 'ibb', 'يريم', 13.90000000, 44.30000000, 'loaned', NULL, 1, '2025-02-25 14:00:00', '2025-02-10 11:00:00'),
(15, 4, 'سماعة طبيب تقليدية قديمة', 'diagnostic', 'acceptable', 'سماعة طبيب تقليدية من النحاس. قديمة لكنها لا تزال تعمل. تحتاج تنظيفاً دقيقاً وتعقيمها قبل الاستخدام.', 'donation', NULL, 'taiz', 'الكعكة', 13.58000000, 44.01000000, 'rejected', 'الجهاز قديم جداً وقد لا يلبي المعايير الطبية المطلوبة. يُنصح باستخدام أجهزة رقمية حديثة.', 1, '2025-03-01 10:00:00', '2025-02-19 08:00:00'),
(16, 7, 'حقيبة إسعافات أولية مجهزة', 'beds_clinical', 'acceptable', 'حقيبة إسعافات أولية تحتوي على مستلزمات طبية أساسية. بعض المنتجات منتهية الصلاحية.', 'donation', NULL, 'marib', 'حريب', 15.40000000, 45.30000000, 'rejected', 'العديد من المستلزمات داخل الحقيبة منتهية الصلاحية أو قريبة الانتهاء. لا يمكن قبولها في هذه الحالة.', 1, '2025-03-02 09:00:00', '2025-02-20 05:30:00'),
(17, 9, 'جهاز تسخين كهربائي طبي قديم', 'respiratory', 'acceptable', 'جهاز تسخين هواء للتنفس يستخدم في علاج الربو. قديم لكنه يعمل. يحتاج إلى فحص كهربائي أولاً.', 'donation', NULL, 'hadramawt', 'تريم', 15.92000000, 49.00000000, 'rejected', 'الجهاز قديم جداً وقد يشكل خطراً كهربائياً. لا يتوافق مع معايير السلامة المطلوبة.', 1, '2025-03-03 08:00:00', '2025-02-21 06:45:00'),
(18, 2, 'جهاز قياس حرارة غير رقمي', 'diagnostic', 'acceptable', 'جهاز قياس حرارة الزئبق التقليدي. دقيق لكنه غير عملي للاستخدام السريري اليومي.', 'donation', NULL, 'amanat_al_asimah', 'شعبوب', 15.39000000, 44.21000000, 'rejected', 'الأجهزة الزئبقية غير مدعومة نظراً للمخاطر البيئية. يُطلب استبدالها بأجهزة رقمية.', 1, '2025-03-04 09:30:00', '2025-02-22 07:00:00'),
(19, 3, 'عربة نقالة للمستلزمات الطبية', 'beds_clinical', 'good', 'عربة معدنية متحركة بـ 3 طبقات لنقل المستلزمات الطبية في العيادات. سهلة التنظيف.', 'donation', NULL, 'aden', 'المنصورة', 12.78000000, 45.02000000, 'active', NULL, 1, '2026-07-27 23:33:10', '2025-03-01 05:00:00'),
(20, 5, 'جهاز ضغط الهواء النابضي PulmoAide', 'respiratory', 'good', 'جهاز ضغط هواء نابضي للرئة. يُستخدم لتوسيع الشعب الهوائية في علاج الربو والتهابات الرئة. يعمل بالكهرباء.', 'loan', '3 أشهر', 'lahij', 'المضاربة', 13.01000000, 44.87000000, 'pending_review', NULL, NULL, NULL, '2025-03-15 06:00:00'),
(21, 3, 'جهاز تكثيف الأكسجين Yuwell 10L', 'respiratory', 'excellent', 'جهاز مكثف أكسجين طبي ذو تدفق عالٍ يصل إلى 10 لتر في الدقيقة. ممتاز جداً للحالات التي تعاني من نقص حاد في الأكسجين أو فشل تنفسي. الجهاز نظيف جداً وتم استخدامه لـ 100 ساعة فقط. يشتمل على فلاتر إضافية جديدة وأنابيب استنشاق معقمة.', 'loan', '6 أشهر', 'aden', 'خور مكسر', 12.80210000, 45.02980000, 'active', NULL, NULL, NULL, '2026-07-24 18:31:28'),
(22, 5, 'كرسي متحرك كهربائي ذكي خفيف الوزن', 'mobility', 'good', 'كرسي متحرك يعمل بالبطارية الكهربائية، قابل للطي وخفيف الوزن يسهل وضعه في السيارة. تحكم مريح عبر قبضة القيادة الذكية (Joystick). بطارية الليثيوم تدوم لـ 15 كيلومتر شحن كامل. مناسب لكبار السن وذوي الإعاقة الحركية.', 'donation', NULL, 'lahij', 'الحوطة', 13.06100000, 44.88120000, 'active', NULL, NULL, NULL, '2026-07-24 18:31:28'),
(23, 7, 'سرير طبي كهربائي 3 حركات مع مرتبة هوائية', 'beds_clinical', 'excellent', 'سرير طبي متكامل يعمل بالكهرباء بريموت تحكم للتحكم في ارتفاع الرأس والقدمين وكامل السرير. يأتي مع مرتبة طبية لمنع تقرحات الفراش (مرتبة هوائية متطورة مع منفاخ صامت). مناسب جداً لمرضى الشلل النصفي أو المرضى طريحي الفراش لفترات طويلة.', 'loan', '12 شهر', 'marib', 'مدينة مأرب', 15.46120000, 45.32450000, 'active', NULL, NULL, NULL, '2026-07-24 18:31:28'),
(24, 8, 'جهاز قياس السكر والضغط المتكامل Beurer', 'diagnostic', 'excellent', 'حقيبة طبية متكاملة تحتوي على جهاز قياس الضغط الإلكتروني من المعصم وجهاز قياس نسبة السكر في الدم مع علبة أشرطة فحص جديدة (50 شريط) ووخاز إبر. الأجهزة دقيقة جداً ومصنوعة بجودة ألمانية ممتازة وسهلة الاستخدام لكبار السن.', 'donation', NULL, 'al_hudaydah', 'الحالي', 14.79900000, 42.97900000, 'active', NULL, NULL, NULL, '2026-07-24 18:31:28');

-- --------------------------------------------------------

--
-- بنية الجدول `device_photos`
--

CREATE TABLE `device_photos` (
  `id` int(10) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `device_photos`
--

INSERT INTO `device_photos` (`id`, `device_id`, `file_path`, `is_primary`, `uploaded_at`) VALUES
(1, 1, 'uploads/devices/device_1_main.jpg', 1, '2025-03-01 06:01:00'),
(3, 2, 'uploads/devices/device_2_main.jpg', 1, '2025-03-02 07:16:00'),
(15, 10, 'uploads/devices/device_10_main.jpg', 1, '2025-02-17 06:01:00'),
(16, 11, 'uploads/devices/device_11_main.jpg', 1, '2025-02-18 07:31:00'),
(17, 11, 'uploads/devices/device_11_tube.jpg', 0, '2025-02-18 07:31:00'),
(18, 12, 'uploads/devices/device_12_main.jpg', 1, '2025-02-05 08:01:00'),
(19, 13, 'uploads/devices/device_13_main.jpg', 1, '2025-02-08 09:31:00'),
(20, 13, 'uploads/devices/device_13_battery.jpg', 0, '2025-02-08 09:31:00'),
(21, 14, 'uploads/devices/device_14_main.jpg', 1, '2025-02-10 11:01:00'),
(22, 15, 'uploads/devices/device_15_main.jpg', 1, '2025-02-19 08:01:00'),
(23, 16, 'uploads/devices/device_16_main.jpg', 1, '2025-02-20 05:31:00'),
(24, 17, 'uploads/devices/device_17_main.jpg', 1, '2025-02-21 06:46:00'),
(25, 18, 'uploads/devices/device_18_main.jpg', 1, '2025-02-22 07:01:00'),
(27, 20, 'uploads/devices/device_20_main.jpg', 1, '2025-03-15 06:01:00'),
(28, 20, 'uploads/devices/device_20_tube.jpg', 0, '2025-03-15 06:01:00'),
(29, 21, 'uploads/devices/device_21_main.jpg', 1, '2026-07-24 18:31:28'),
(30, 21, 'uploads/devices/device_21_details.jpg', 0, '2026-07-24 18:31:28'),
(31, 22, 'uploads/devices/device_22_main.jpg', 1, '2026-07-24 18:31:28'),
(32, 23, 'uploads/devices/device_23_main.jpg', 1, '2026-07-24 18:31:28'),
(33, 23, 'uploads/devices/device_23_side.jpg', 0, '2026-07-24 18:31:28'),
(34, 24, 'uploads/devices/device_24_main.jpg', 1, '2026-07-24 18:31:28'),
(35, 19, 'uploads/devices/cd207f36-3816-49cc-b07c-aa3eee179b4a.jpg', 1, '2026-07-27 20:28:37'),
(36, 8, 'uploads/devices/5feb1b82-cb0c-4829-b1c4-fdc1f5da5c1b.webp', 1, '2026-07-28 21:10:57'),
(37, 7, 'uploads/devices/9b39aa69-074c-4c37-8ba1-fb26aa612275.jpeg', 1, '2026-07-28 21:12:24'),
(38, 6, 'uploads/devices/6180f57f-af79-46f1-8499-b94fdf1cfccf.webp', 1, '2026-07-28 21:14:05'),
(39, 3, 'uploads/devices/420d9a3d-4ffb-4811-b333-ec07d66c1774.jpg', 1, '2026-07-28 21:14:58'),
(40, 4, 'uploads/devices/b917ce58-d700-41f8-9643-dd251e52f8dc.jpg', 1, '2026-07-28 21:16:39'),
(41, 5, 'uploads/devices/77d5a9d7-4f30-4055-8e91-c3174b9db68a.jpg', 1, '2026-07-28 21:18:02');

-- --------------------------------------------------------

--
-- بنية الجدول `requests`
--

CREATE TABLE `requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `beneficiary_id` int(10) UNSIGNED NOT NULL,
  `case_description` text NOT NULL,
  `medical_doc_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `admin_reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `admin_reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `requests`
--

INSERT INTO `requests` (`id`, `device_id`, `beneficiary_id`, `case_description`, `medical_doc_path`, `status`, `rejection_reason`, `admin_reviewed_by`, `admin_reviewed_at`, `created_at`) VALUES
(1, 12, 10, 'المريض يعاني من التهاب الشعب الهوائية المزمن منذ ثلاث سنوات ويتطلب بخاخات مستمرة. تم تشخيص الحالة بمرض الربو الشديد من قِبل طبيب أمراض الصدر في مستشفى ذمار المركزي. يحتاج المريض لجهاز نبولايزر للاستنشاق اليومي.', 'uploads/medical-reports/report_1.pdf', 'approved', NULL, 1, '2025-02-20 10:00:00', '2025-02-18 05:00:00'),
(2, 13, 11, 'المريض يعاني من إعاقة حركية في الطرفين السفليين بسبب حادث مروري أدى إلى كسر في الحوض. لا يستطيع المشي ويحتاج إلى كرسي متحرك للتنقل اليومي. تم تأكيد التشخيص من قِبل جراح العظام في مستشفى عتق المركزي.', 'uploads/medical-reports/report_2.pdf', 'approved', NULL, 1, '2025-02-22 11:00:00', '2025-02-20 06:00:00'),
(3, 10, 13, 'المريض يعاني من انخفاض تشبع الأكسجين في الدم بنسبة 88% أثناء النوم. أظهرت الفحوصات إصابته باضطراب التنفس أثناء النوم. يحتاج إلى جهاز قياس متابعة يومي لرصد الحالة.', 'uploads/medical-reports/report_3.pdf', 'approved', NULL, 1, '2025-03-06 11:00:00', '2025-03-04 07:00:00'),
(4, 14, 14, 'المرضى في مركز رعاية مسنين يحتاجون إلى سرير طبي لرعاية أحد المرضى المقيمين الذين يعانون من تيبّس المفاصل وعدم القدرة على الحركة. السرير الحالي في المركز قدّم ولا يصلح للاستخدام.', 'uploads/medical-reports/report_4.pdf', 'approved', NULL, 1, '2025-02-25 14:00:00', '2025-02-23 08:00:00'),
(5, 3, 12, 'المريضة تعاني من كسر في ساق اليمنى بعد سقوطها من الدرج. لا تستطيع المشي لمدة 8 أسابيع حسب تقرير الطبيب المعالج. تحتاج إلى كرسي متحرك للاستقلالية أثناء فترة التعافي.', 'uploads/medical-reports/report_5.pdf', 'pending', NULL, NULL, NULL, '2025-03-08 05:30:00'),
(6, 5, 15, 'المريض يعاني من شلل نصفي بعد تعرضه لسكتة دماغية قبل شهرين. يستطيع الجلوس لكنه لا يستطيع الوقوف بشكل مستقل. يحتاج إلى سرير طبي مناسب لحالته في المنزل.', 'uploads/medical-reports/report_6.pdf', 'pending', NULL, NULL, NULL, '2025-03-09 06:00:00'),
(7, 19, 16, 'العيادة تحتاج إلى عربة نقالة لنقل المستلزمات الطبية بين غرف الفحص. العيادة تخدم أكثر من 200 مريض يومياً والنقل اليدوي يستغرق وقتاً طويلاً.', 'uploads/medical-reports/report_7.pdf', 'pending', NULL, NULL, NULL, '2025-03-10 07:00:00'),
(9, 8, 10, 'المريض يطلب جهاز فحص السمع لأنه يعاني من صعوبة في السمع.', 'uploads/medical-reports/report_9.pdf', 'rejected', 'التقرير الطبي غير كافٍ ولا يحتوي على نتائج فحص السمع المفصلة. يُطلب تقديم تقرير من أخصائي أنف وأذن وحنجرة.', 1, '2025-03-13 10:00:00', '2025-03-11 09:00:00'),
(10, 11, 12, 'المريضة تريد جهاز غسيل كلى متنقل للاستخدام في المنزل بدلاً من الذهاب المستمر للمستشفى.', 'uploads/medical-reports/report_10.pdf', 'rejected', 'جهاز غسيل الكلى يتطلب متابعة طبية مستمرة ولا يمكن استخدامه في المنزل بدون إشراف طبي مباشر. يُنصح بالاستمرار في غسيل الكلى في المستشفى.', 1, '2025-03-14 11:00:00', '2025-03-12 11:00:00'),
(21, 21, 12, 'المريضة تعاني من تليف رئوي حاد وقصور مزمن في وظائف التنفس، وبحاجة مستمرة للأكسجين المنزلي لـ 16 ساعة يومياً. تعيش في ظروف مادية صعبة ولا تستطيع شراء أو استئجار جهاز تكثيف الأكسجين. يرجى قبول الطلب لتسهيل رعاية المريضة منزلياً.', 'uploads/medical-reports/report_21.pdf', 'pending', NULL, NULL, NULL, '2026-07-24 18:31:28'),
(22, 22, 15, 'المريض وائل يعاني من شلل في الأطراف السفلية منذ الولادة. يدرس في الجامعة ويحتاج إلى هذا الكرسي الكهربائي ليتمكن من الحركة والتنقل بين قاعات المحاضرات دون الحاجة لمساعدة مستمرة. التقرير الطبي المرفق يوضح حالته الصحية والاجتماعية بالتفصيل.', 'uploads/medical-reports/report_22.pdf', 'pending', NULL, NULL, NULL, '2026-07-24 18:31:28'),
(23, 23, 11, 'والد المريض تعرض لجلطة دماغية شديدة أدت إلى شلل شقي كامل وعدم القدرة على الحركة نهائياً. هو الآن طريح الفراش وبدأ يعاني من تقرحات جلدية مؤلمة. نحن بحاجة ماسة للسرير الطبي الكهربائي مع المرتبة الهوائية لتسهيل ركبته وتغذيته وتقليبه لمنع التقرحات.', 'uploads/medical-reports/report_23.pdf', 'approved', NULL, 1, '2026-07-24 21:30:00', '2026-07-24 18:31:28');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('beneficiary','donor','admin') NOT NULL DEFAULT 'beneficiary',
  `governorate` varchar(50) NOT NULL,
  `district` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `full_name`, `phone`, `email`, `password_hash`, `role`, `governorate`, `district`, `is_active`, `created_at`) VALUES
(1, 'مدير النظام', '967770000001', 'admin@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'admin', 'amanat_al_asimah', 'الصافية', 1, '2024-12-31 21:00:00'),
(2, 'أحمد عبد الله العمري', '967771000001', 'ahmed.omari@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'amanat_al_asimah', 'التحرير', 1, '2025-01-15 05:30:00'),
(3, 'خالد سعيد الحبيبي', '967771000002', 'khaled.habibi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'aden', 'خور مكسر', 1, '2025-01-16 06:00:00'),
(4, 'محمد ناصر القبيسي', '967771000003', 'mohammed.qabisi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'taiz', 'المظفر', 1, '2025-01-17 07:15:00'),
(5, 'عبد الرحمن حسين المقطري', '967771000004', 'abdulrahman@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'lahij', 'الحوطة', 1, '2025-01-18 08:00:00'),
(6, 'فاطمة علي الزبيدي', '967771000005', 'fatima.zubaidi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'ibb', 'مدينة إب', 1, '2025-01-19 09:30:00'),
(7, 'يوسف أحمد النهمي', '967771000006', 'yusuf.nahmi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'marib', 'مدينة مأرب', 1, '2025-01-20 10:45:00'),
(8, 'عبد الملك شايع المحرمي', '967771000007', 'abdulmalik@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'al_hudaydah', 'الحالي', 1, '2025-01-21 11:00:00'),
(9, 'عمر مختار البركاني', '967771000008', 'omar.barkani@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'donor', 'hadramawt', 'المكلا', 1, '2025-01-22 12:20:00'),
(10, 'حسين علي الجملي', '967772000001', 'hussain.jamli@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'dhamar', 'مدينة ذمار', 1, '2025-02-01 05:00:00'),
(11, 'سمير عبد الواحد الورافي', '967772000002', 'samir.warafi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'shabwah', 'عتق', 1, '2025-02-02 06:10:00'),
(12, 'منى صالح العولقي', '967772000003', 'mona.awlaqi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'saada', 'مدينة صعدة', 1, '2025-02-03 07:20:00'),
(13, 'أشرف محمد الحمادي', '967772000004', 'ashraf.hamadi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'al_hudaydah', 'الميناء', 1, '2025-02-04 08:30:00'),
(14, 'رنا أحمد lesbienne', '967772000005', 'reem.haj@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'hajjah', 'مدينة حجة', 1, '2025-02-05 09:40:00'),
(15, 'وائل عادل القدسي', '967772000006', 'wael.qudsi@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'ad_dali', 'مدينة الضالع', 1, '2025-02-06 10:50:00'),
(16, 'هدى ناصر المنصوري', '967772000007', 'hoda.mansouri@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'amran', 'مدينة عمران', 1, '2025-02-07 11:00:00'),
(17, 'ياسر عمر الطيري', '967772000008', 'yasser.tayri@sanad.ye', '$2y$10$tguxV6lK.bN85J5xXNxw5uLypZY9kV9OgxrRQHZNpaKi52vQgLoPu', 'beneficiary', 'al_bayda', 'مدينة البيضاء', 1, '2025-02-08 12:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `admin_reviewed_by` (`admin_reviewed_by`);

--
-- Indexes for table `device_photos`
--
ALTER TABLE `device_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `admin_reviewed_by` (`admin_reviewed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `device_photos`
--
ALTER TABLE `device_photos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `device_photos`
--
ALTER TABLE `device_photos`
  ADD CONSTRAINT `device_photos_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`beneficiary_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
