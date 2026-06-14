# سند (Sanad) — Demo Script for Graduation Defense

**Total Time:** ~10 minutes  
**Presenter:** [Your Name]  
**Environment:** XAMPP/WAMP on local machine, Google Chrome, mobile view for responsiveness demo

---

## 1. Opening (1 minute)

> *(Open browser to http://localhost/sanad/ — show homepage)*

**Presenter:**

"في اليمن، ملايين الأشخاص يحتاجون أجهزة طبية — أجهزة تنفس، أسرة مستشفيات، كراسي متحركة — لكنهم لا يستطيعون شراءها. وفي الوقت نفسه، آلاف الأسر لديها هذه الأجهزة خاملة في منازلها بعد أن لم تعد بحاجة لها.

هنا يأتي دور **سند** — منصة تكافل طبي إلكترونية تربط بين المتبرعين والمحتاجين. تسمح للمتبرع بعرض جهازه الطبي، وتسمح للمحتاج بتقديم طلب مع تقرير طبي، ويدير المشرف العملية برمتها لضمان النزاهة.

اسمي [Your Name]، وهذا مشروع تخرجي."

---

## 2. Platform Walkthrough (5 minutes)

### 2.1 Guest View — Homepage & Marketplace (1 min)

> *(Point to hero section, stats bar, how-it-works)*

"تبدأ الرحلة من الصفحة الرئيسية. نرى هنا شعار المنصة، إحصائيات سريعة عن الأجهزة المتاحة والعائلات المستفيدة، وشرح بسيط لكيفية العمل في ثلاث خطوات."

> *(Click "تصفح الأجهزة" or navigate to marketplace.php)*

"ننتقل إلى سوق الأجهزة الطبية. هنا يمكن للزائر — حتى بدون تسجيل الدخول — تصفح جميع الأجهزة المتاحة."

> *(Demonstrate filters: select a governorate, pick a category, type in search)*

"يمكن تصفية النتائج حسب المحافظة، الفئة الطبية، نوع العرض، وحالة الجهاز. التصفية تعمل ديناميكياً بدون إعادة تحميل الصفحة."

### 2.2 Register as Donor → Add Device (1 min)

> *(Click on register.php, select role=متبرع)*

"أسجل الآن كمتبرع — أختار دور 'متبرع'، أدخل الاسم، رقم الهاتف اليمني، البريد الإلكتروني، المحافظة والمديرية، ثم كلمة المرور."

> *(Login with the new donor account)*

"بعد التسجيل، أسجل الدخول وأنتقل إلى لوحة المتبرع."

> *(Navigate to add-device.php)*

"أضغط على 'إضافة جهاز جديد' — أختار اسم الجهاز، الفئة الطبية (مثلاً: أجهزة تنفسية)، الحالة التشغيلية، وأكتب وصفاً مفصلاً."

> *(Fill form, demonstrate conditional loan duration field appearing when loan is selected)*

"عند اختيار 'إعارة مؤقتة'، يظهر حقل المدة تلقائياً."

> *(Upload a test photo, demonstrate pin placement on map)*

"أرفع صوراً للجهاز — يتم تغيير اسم الملف إلى UUID للحماية. ثم أحدد موقع الجهاز على الخريطة. عند الضغط على الخريطة، تُحفظ الإحداثيات."

> *(Submit)*

"يُرفع الطلب بحالة 'بانتظار المراجعة'."

### 2.3 Admin Approves Device (1 min)

> *(Logout, login as admin)*

"أسجل الدخول كمشرف — [show admin login]. أنتقل إلى لوحة التحكم."

> *(Show admin/index.php — dashboard stats with counts)*

"نرى هنا لوحة الإحصائيات: عدد المستخدمين، الأجهزة، الطلبات المعلقة."

> *(Navigate to admin/listings.php)*

"في صفحة مراجعة الإعلانات، أرى الجهاز الذي أضافه المتبرع — مع التفاصيل الكاملة والصور."

> *(Click "موافقة")*

"بعد الموافقة، يتغير حالة الجهاز إلى 'نشط' ويظهر فوراً في السوق."

> *(Go back to marketplace as guest or beneficiary — show the device now with status "متاح" / "active")*

### 2.4 Beneficiary Requests Device (1 min)

> *(Logout admin, register as beneficiary, login)*

"أسجل الآن كمستفيد — أختار دور 'مستفيد'، وأكمل بياناتي."

> *(Browse marketplace, find the approved device, click on it)*

"أجد الجهاز في السوق، أضغط عليه لرؤية التفاصيل."

> *(Show device.php — photo gallery, description, map, request button)*

"في صفحة التفاصيل، أرى معرض الصور، الوصف، موقع الجهاز على الخريطة، وزر 'طلب الجهاز'."

> *(Click "طلب الجهاز" — modal appears)*

"يظهر مودال زجاجي (Glassmorphism) يحتوي على حقل لوصف الحالة الطبية ورفع التقرير الطبي."

> *(Fill case description, upload medical report PDF, submit)*

"بعد الإرسال، يتغير حالة الجهاز إلى 'قيد المراجعة' ويختفي من نتائج البحث للمستخدمين الآخرين — ضمان عدم وجود تضارب."

### 2.5 Admin Approves Request → Communication (1 min)

> *(Logout, login as admin)*

"المشرف يرى طلباً جديداً في admin/requests.php مع إمكانية عرض التقرير الطبي."

> *(Click approve on the request)*

"عند الموافقة: حالة الجهاز تصبح 'معار'، ويظهر للمستفيد زر واتساب وزر اتصال مباشر برقم المتبرع."

> *(Switch to beneficiary dashboard — show WhatsApp and Call buttons)*

"المستفيد الآن يستطيع التواصل مع المتبرع عبر واتساب برسالة جاهزة مكتوبة مسبقاً، أو الاتصال المباشر."

> *(Switch to donor dashboard — show beneficiary name and governorate)*

"المتبرع يرى اسم المستفيد ومحافظته — معلومات كافية للثقة، لكن خصوصية الرقم محفوظة."

---

## 3. Technical Highlights (2 minutes)

> *(Keep browser open, switch to code or stay on pages as reference)*

**Presenter:**

"سند بنيت بأسلوب تقني نظيف ومتعمد. دعوني أشرح أبرز النقاط:"

| الميزة | التفاصيل |
|--------|----------|
| **PHP خالص** | لا Laravel ولا Symfony — برهان على إتقان أساسيات backend: sessions, PDO, file handling |
| **Vanilla JS** | لا jQuery ولا React/Vue — DOM manipulation خالص، ES6+ |
| **CSS أصلي** | Tailwind عبر CDN مع style.css مخصص — Grid, Flexbox, Custom Properties |
| **تصميم RTL** | `dir="rtl" lang="ar"` في كل الصفحات، خط Tajawal يدعم العربية والإنجليزية |
| **Glassmorphism** | مودالات شفافة بتأثير `backdrop-filter: blur()` |
| **PDO Prepared Statements** | كل استعلامات قاعدة البيانات — حماية تامة من SQL Injection |
| **CSRF Tokens** | كل نماذج POST تحتوي على توكن للتحقق |
| **رفع الملفات بأمان** | UUID v4 للتسمية، `finfo_file()` لفحص MIME الحقيقي، extension whitelist |
| **حماية التقارير الطبية** | `.htaccess` يمنع الوصول المباشر، تُعرض فقط عبر endpoint مصادق |
| **استجابة كاملة** | 3 أعمدة → 2 عمود → عمود واحد (1200px, 768px, 480px) |
| **لمس-friendly** | أزرار لا تقل عن 44×44 بكسل كما تنص مواصفات Apple HIG |

---

## 4. Risk Mitigation (1 minute)

**Presenter:**

"أمن المعلومات كان أولوية في كل سطر من الكود. إليكم كيف تعاملنا مع المخاطر:"

| التهديد | الإجراء |
|---------|---------|
| **SQL Injection** | PDO Prepared Statements — لا يوجد استعلام بسيط concat |
| **XSS** | `htmlspecialchars()` على كل مخرج، `strip_tags()` على كل مدخل نصي |
| **CSRF** | توكن عشوائي لكل جلسة، يُتحقق منه قبل معالجة أي POST |
| **وصول غير مصرح به** | دالة `requireRole()` في أعلى كل صفحة محمية — تعيد التوجيه مع رسالة خطأ |
| **فقدان البيانات** | MySQL InnoDB مع FK Constraints — عمليات حساسة ضمن transactions |
| **تسريبات التقارير الطبية** | المجلد محمي بـ .htaccess، الوصول فقط عبر PHP authenticated |
| **API Key مفقودة** | صورة خريطة static fallback عند غياب Google Maps API Key |

---

## 5. Q&A Preparation (1 minute)

**Prepare answers to these likely questions:**

### Q: "لماذا لم تستخدم Laravel أو أي Framework؟"
**A:** "التطبيق متعمد أن يكون خفيفاً — يمكن نشره على أي استضافة مشتركة بدون متطلبات Composer خاصة. استخدام PHP الخالص يظهر فهماً للأسس: آلية sessions، PDO، معالجة الملفات، التحكم في الوصول. Laravel يخفي هذه التفاصيل، لكن هنا نثبت أننا نفهمها."

### Q: "كيف تتعامل مع التواصل المباشر؟ أليس هناك نظام محادثة داخلي؟"
**A:** "طبقنا مبدأ YAGNI — 'You Aren't Gonna Need It'. بدلاً من بناء نظام محادثة معقد، استخدمنا روابط WhatsApp التي يستخدمها 90% من اليمنيين يومياً. الزر يفتح واتساب برسالة جاهزة — حل بسيط، عملي، بدون تعقيد تقني أو استهلاك موارد خادم."

### Q: "كيف تحمي خصوصية المستخدمين؟"
**A:** "رقم الهاتف لا يُكشف إلا بعد موافقة المشرف على الطلب. قبل ذلك، يرى المتبرع اسم المستفيد ومحافظته فقط. التقارير الطبية محمية ولا يمكن الوصول إليها مباشرة عبر الرابط."

### Q: "ماذا عن قابلية التوسع؟"
**A:** "أعمدة `status` و `role` و `governorate` مفهرسة — استعلامات سريعة. Prepared Statements تمنع N+1 problem. PHP الخالص يعني footprint صغير على الخادم. عند الحاجة للتوسع، نضيف caching layer."

### Q: "هل يمكن لأي شخص أن يصبح مشرفاً؟"
**A:** "لا. دور المشرف يُمنح يدوياً من قاعدة البيانات فقط — لا يوجد self-register للمشرفين. هذا يضمن أن التحكم في المنصة يبقى بيد جهة موثوقة."

### Q: "ماذا لو رفض المشرف طلب مستفيد؟"
**A:** "المشرف يكتب سبب الرفض. يعود الجهاز إلى حالة 'متاح' في السوق. المستفيد يرى سبب الرفض ويستطيع إعادة التقديم بوثائق مصححة. كل رفض مسجل بـ ID المشرف وتاريخ ووقت."

---

## Preparation Checklist

- [ ] XAMPP/WAMP running (Apache + MySQL)
- [ ] php.ini: upload_max_filesize = 10M, post_max_size = 12M
- [ ] Database: automates on first request — no manual import
- [ ] Test accounts prepared:
  - Admin: admin@sanad.test / password (MANUALLY set role='admin' in DB)
  - Donor: donor@sanad.test / password
  - Beneficiary: beneficiary@sanad.test / password
- [ ] Sample device photos ready in assets/images/demo/
- [ ] Sample medical report PDF ready in assets/images/demo/
- [ ] Google Maps API key set (or fallback image verified)
- [ ] Browser DevTools mobile mode tested
- [ ] Presentation remote/clicker working
- [ ] Internet connection (for Tailwind CDN) — or download CDN asset locally as backup
- [ ] Second monitor/extended display for presenter notes

---

## Key URLs Quick Reference

| Page | URL |
|------|-----|
| Homepage | `http://localhost/sanad/` |
| Marketplace | `http://localhost/sanad/marketplace.php` |
| Device Detail | `http://localhost/sanad/device.php?id=1` |
| Register | `http://localhost/sanad/register.php` |
| Login | `http://localhost/sanad/login.php` |
| Add Device | `http://localhost/sanad/add-device.php` |
| Donor Dashboard | `http://localhost/sanad/dashboard-donor.php` |
| Beneficiary Dashboard | `http://localhost/sanad/dashboard-beneficiary.php` |
| Admin Dashboard | `http://localhost/sanad/admin/index.php` |
| Admin Listings | `http://localhost/sanad/admin/listings.php` |
| Admin Requests | `http://localhost/sanad/admin/requests.php` |
| Admin Users | `http://localhost/sanad/admin/users.php` |
| Setup Notes | `http://localhost/sanad/setup-notes.txt` |

---

> *End of Demo Script — Good luck with your defense! 🎓*
