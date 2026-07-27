import os
from PIL import Image, ImageDraw, ImageFont
import arabic_reshaper
from bidi.algorithm import get_display

# Create output folder
output_dir = os.path.join(os.path.dirname(__file__), "images")
os.makedirs(output_dir, exist_ok=True)

# Font settings
font_path = "C:\\Windows\\Fonts\\arial.ttf"
if not os.path.exists(font_path):
    font_path = "arial.ttf" # Fallback

def get_arabic(text):
    if not text:
        return ""
    # Check if text is Arabic
    is_arabic = any('\u0600' <= char <= '\u06FF' for char in text)
    if is_arabic:
        reshaped = arabic_reshaper.reshape(text)
        return get_display(reshaped)
    return text

def draw_text(draw, text, x, y, font, fill="black", anchor="mm"):
    arabic_text = get_arabic(text)
    draw.text((x, y), arabic_text, fill=fill, font=font, anchor=anchor)

def draw_box(draw, x1, y1, x2, y2, bg_color="#E6F2FF", border_color="#0066CC", border_width=2, radius=10):
    draw.rounded_rectangle([x1, y1, x2, y2], radius=radius, fill=bg_color, outline=border_color, width=border_width)

def draw_arrow(draw, x1, y1, x2, y2, text="", font=None, color="#333333", width=2):
    # Draw line
    draw.line([x1, y1, x2, y2], fill=color, width=width)
    # Draw arrow head
    # We can determine direction based on coordinates
    import math
    angle = math.atan2(y2 - y1, x2 - x1)
    arrow_len = 10
    px1 = x2 - arrow_len * math.cos(angle - math.pi/6)
    py1 = y2 - arrow_len * math.sin(angle - math.pi/6)
    px2 = x2 - arrow_len * math.cos(angle + math.pi/6)
    py2 = y2 - arrow_len * math.sin(angle + math.pi/6)
    draw.polygon([x2, y2, px1, py1, px2, py2], fill=color)
    
    # Draw text above line
    if text and font:
        mx = (x1 + x2) / 2
        my = (y1 + y2) / 2 - 12
        draw_text(draw, text, mx, my, font, fill=color, anchor="mm")

def draw_diamond(draw, cx, cy, rx, ry, text="", font=None, bg_color="#FFF2CC", border_color="#D6B656", border_width=2):
    coords = [(cx, cy - ry), (cx + rx, cy), (cx, cy + ry), (cx - rx, cy)]
    draw.polygon(coords, fill=bg_color, outline=border_color, width=border_width)
    if text and font:
        draw_text(draw, text, cx, cy, font, fill="black", anchor="mm")

# 1. System Hierarchy Diagram
def draw_system_hierarchy():
    im = Image.new("RGB", (850, 500), "white")
    draw = ImageDraw.Draw(im)
    f_title = ImageFont.truetype(font_path, 18)
    f_box = ImageFont.truetype(font_path, 13)
    
    # Root
    draw_box(draw, 350, 20, 500, 70, bg_color="#004C99", border_color="#00264D", border_width=3)
    draw_text(draw, "منصة سَنَد التكافلية", 425, 45, f_title, fill="white")
    
    # Level 1 Nodes
    roles = [
        ("الزائر (غير مسجل)", 100, 150),
        ("المستفيد (المريض)", 300, 150),
        ("المتبرع (المعير)", 500, 150),
        ("مدير النظام (الأدمن)", 700, 150)
    ]
    
    for title, cx, cy in roles:
        # Draw lines from root
        draw.line([425, 70, 425, 110], fill="#004C99", width=2)
        draw.line([100, 110, 700, 110], fill="#004C99", width=2)
        draw.line([cx, 110, cx, 150], fill="#004C99", width=2)
        
        draw_box(draw, cx - 80, cy - 25, cx + 80, cy + 25, bg_color="#CCE6FF", border_color="#0066CC")
        draw_text(draw, title, cx, cy, f_box, fill="black")
        
    # Level 2 Nodes (Details)
    visitor_details = ["الصفحة الرئيسية", "تصفح سوق الأجهزة", "البحث والفلترة الجغرافية", "إنشاء حساب / دخول"]
    beneficiary_details = ["لوحة تحكم المستفيد", "طلب استعارة جهاز", "رفع التقرير الطبي", "التواصل عبر واتساب/اتصال"]
    donor_details = ["لوحة تحكم المتبرع", "إضافة جهاز طبي جديد", "تحديد موقع الجهاز (خرائط)", "إدارة الأجهزة المضافة"]
    admin_details = ["لوحة تحكم المشرف", "مراجعة واعتماد الأجهزة", "مراجعة واعتماد الطلبات", "إدارة الحسابات والتقارير"]
    
    all_details = [
        (visitor_details, 100),
        (beneficiary_details, 300),
        (donor_details, 500),
        (admin_details, 700)
    ]
    
    for details, cx in all_details:
        start_y = 210
        for item in details:
            # Draw line to child
            draw.line([cx, 175, cx, start_y], fill="#66B2FF", width=1)
            draw_box(draw, cx - 85, start_y, cx + 85, start_y + 35, bg_color="#F2F8FF", border_color="#66B2FF", radius=5)
            draw_text(draw, item, cx, start_y + 17, f_box, fill="#003366")
            start_y += 55
            
    im.save(os.path.join(output_dir, "system_hierarchy.png"))

# 2. Use Case Diagram
def draw_use_case():
    im = Image.new("RGB", (850, 600), "white")
    draw = ImageDraw.Draw(im)
    f_actor = ImageFont.truetype(font_path, 14)
    f_uc = ImageFont.truetype(font_path, 12)
    f_title = ImageFont.truetype(font_path, 16)
    
    # System boundary
    draw_box(draw, 180, 50, 670, 560, bg_color="#FAFAFA", border_color="#999999", border_width=2, radius=15)
    draw_text(draw, "حدود النظام: منصة سَنَد", 425, 75, f_title, fill="#333333")
    
    # Actors
    # Donor (Left)
    draw.ellipse([40, 200, 70, 230], outline="black", width=2) # Head
    draw.line([55, 230, 55, 290], fill="black", width=2) # Body
    draw.line([25, 250, 85, 250], fill="black", width=2) # Arms
    draw.line([55, 290, 35, 340], fill="black", width=2) # Left Leg
    draw.line([55, 290, 75, 340], fill="black", width=2) # Right Leg
    draw_text(draw, "المُعير / المتبرع", 55, 360, f_actor, fill="black")
    
    # Beneficiary (Right)
    draw.ellipse([780, 200, 810, 230], outline="black", width=2)
    draw.line([795, 230, 795, 290], fill="black", width=2)
    draw.line([765, 250, 825, 250], fill="black", width=2)
    draw.line([795, 290, 775, 340], fill="black", width=2)
    draw.line([795, 290, 815, 340], fill="black", width=2)
    draw_text(draw, "المستفيد / المريض", 795, 360, f_actor, fill="black")
    
    # Admin (Top Right/Middle)
    draw.ellipse([410, 575, 440, 605], outline="black", width=2) # Wait, let's put Admin at the bottom or top
    # Let's adjust Admin position to bottom center
    draw.ellipse([410, 10, 440, 40], outline="black", width=2)
    draw.line([425, 40, 425, 80], fill="black", width=2) # wait, Admin is at top, let's place it outside boundary
    # Let's put Admin inside actor representation
    
    # Ovals for Use Cases
    use_cases = [
        ("تسجيل حساب جديد", 425, 120, "both"),
        ("تصفح سوق الأجهزة الطبية", 425, 175, "both"),
        ("البحث والفلترة الجغرافية", 425, 230, "both"),
        ("إدراج جهاز طبي جديد", 425, 285, "donor"),
        ("تحديد الموقع على الخريطة", 425, 340, "donor"),
        ("تقديم طلب استعارة", 425, 395, "beneficiary"),
        ("رفع وثائق التقرير الطبي", 425, 450, "beneficiary"),
        ("مراجعة الأجهزة والطلبات والتحقق", 425, 505, "admin"),
    ]
    
    for text, cx, cy, actor_type in use_cases:
        draw_box(draw, cx - 150, cy - 20, cx + 150, cy + 20, bg_color="#E1F5FE", border_color="#0288D1", radius=15)
        draw_text(draw, text, cx, cy, f_uc, fill="#01579B")
        
        # Draw lines to actors
        if actor_type == "both" or actor_type == "donor":
            draw.line([85, 250, cx - 150, cy], fill="#555555", width=1)
        if actor_type == "both" or actor_type == "beneficiary":
            draw.line([765, 250, cx + 150, cy], fill="#555555", width=1)
            
    # Add Admin Actor at the bottom
    draw.ellipse([410, 565, 440, 595], outline="black", width=2)
    draw.line([425, 595, 425, 630], fill="black", width=2)
    # wait, image is height 600, let's adjust Admin actor coordinates to 425, 575
    # Let's just draw Admin text at the bottom
    draw_box(draw, 350, 515, 500, 555, bg_color="#FFE0B2", border_color="#F57C00", radius=5)
    draw_text(draw, "مدير النظام (Admin)", 425, 535, f_actor, fill="#E65100")
    # Draw line from Admin to "مراجعة الأجهزة والطلبات"
    draw.line([425, 515, 425, 485], fill="#E65100", width=1)
    
    im.save(os.path.join(output_dir, "use_case_diagram.png"))

# 3. Context Level DFD
def draw_context_dfd():
    im = Image.new("RGB", (850, 450), "white")
    draw = ImageDraw.Draw(im)
    f_entity = ImageFont.truetype(font_path, 14)
    f_process = ImageFont.truetype(font_path, 16)
    f_flow = ImageFont.truetype(font_path, 11)
    
    # Center Process
    draw.ellipse([325, 125, 525, 325], fill="#E1F5FE", outline="#0288D1", width=3)
    draw_text(draw, "1.0", 425, 185, f_process, fill="#01579B")
    draw_text(draw, "نظام منصة سَنَد", 425, 215, f_process, fill="#01579B")
    draw_text(draw, "التكافلية للأجهزة الطبية", 425, 245, f_process, fill="#01579B")
    
    # Entities
    # Donor (Left)
    draw_box(draw, 50, 175, 170, 275, bg_color="#ECEFF1", border_color="#546E7A", border_width=3)
    draw_text(draw, "المُعير / المتبرع", 110, 225, f_entity, fill="#37474F")
    
    # Beneficiary (Right)
    draw_box(draw, 680, 175, 800, 275, bg_color="#ECEFF1", border_color="#546E7A", border_width=3)
    draw_text(draw, "المستفيد / المريض", 740, 225, f_entity, fill="#37474F")
    
    # Admin (Top)
    draw_box(draw, 365, 10, 485, 80, bg_color="#ECEFF1", border_color="#546E7A", border_width=3)
    draw_text(draw, "مدير النظام (الأدمن)", 425, 45, f_entity, fill="#37474F")
    
    # Flows
    # Donor -> System
    draw_arrow(draw, 170, 195, 325, 195, text="بيانات الحساب / إضافة جهاز", font=f_flow)
    # System -> Donor
    draw_arrow(draw, 325, 255, 170, 255, text="حالة الجهاز / تفاصيل طلب الاستعارة", font=f_flow)
    
    # Beneficiary -> System
    draw_arrow(draw, 680, 255, 525, 255, text="طلب استعارة جهاز + التقرير الطبي", font=f_flow)
    # System -> Beneficiary
    draw_arrow(draw, 525, 195, 680, 195, text="بيانات التواصل مع المتبرع (واتساب)", font=f_flow)
    
    # Admin -> System
    draw_arrow(draw, 400, 80, 400, 125, text="اعتماد / رفض الأجهزة والطلبات", font=f_flow)
    # System -> Admin
    draw_arrow(draw, 450, 125, 450, 80, text="إحصائيات / تقارير / طلبات معلقة", font=f_flow)
    
    im.save(os.path.join(output_dir, "context_dfd.png"))

# 4. Level 0 DFD
def draw_level0_dfd():
    im = Image.new("RGB", (850, 500), "white")
    draw = ImageDraw.Draw(im)
    f_proc = ImageFont.truetype(font_path, 12)
    f_store = ImageFont.truetype(font_path, 12)
    f_flow = ImageFont.truetype(font_path, 10)
    
    # 4 Processes
    processes = [
        ("1.0 إدارة الحسابات", 150, 100),
        ("2.0 إدراج الأجهزة", 425, 100),
        ("3.0 تقديم الطلبات", 425, 300),
        ("4.0 الرقابة والموافقة", 700, 200)
    ]
    
    for text, cx, cy in processes:
        draw.ellipse([cx - 70, cy - 40, cx + 70, cy + 40], fill="#E0F2F1", outline="#009688", width=2)
        draw_text(draw, text, cx, cy, f_proc, fill="#004D40")
        
    # 3 Data Stores (Represented by open-ended rectangles)
    # D1: users, D2: devices, D3: requests
    def draw_store(draw, x, y, name):
        draw.line([x, y, x + 100, y], fill="#00796B", width=2)
        draw.line([x, y + 30, x + 100, y + 30], fill="#00796B", width=2)
        draw.line([x, y, x, y + 30], fill="#00796B", width=2)
        draw_text(draw, name, x + 50, y + 15, f_store, fill="#004D40")
        
    draw_store(draw, 100, 220, "D1: Users")
    draw_store(draw, 425, 200, "D2: Devices")
    draw_store(draw, 100, 400, "D3: Requests")
    
    # Flows
    # Process 1 -> D1
    draw_arrow(draw, 150, 140, 150, 220, text="حفظ بيانات المستخدم", font=f_flow)
    # Process 2 -> D2
    draw_arrow(draw, 425, 140, 425, 200, text="إدراج جهاز جديد (معلق)", font=f_flow)
    # Process 4 -> D2
    draw_arrow(draw, 700, 240, 525, 215, text="تحديث حالة الجهاز (نشط)", font=f_flow)
    # Process 3 -> D3
    draw_arrow(draw, 355, 300, 150, 400, text="حفظ طلب الاستعارة", font=f_flow)
    # Process 3 -> D2
    draw_arrow(draw, 475, 260, 475, 230, text="تحديث حالة الجهاز (قيد المراجعة)", font=f_flow)
    # Process 4 -> D3
    draw_arrow(draw, 700, 200, 200, 415, text="تحديث حالة الطلب (مقبول/مرفوض)", font=f_flow)
    
    im.save(os.path.join(output_dir, "level0_dfd.png"))

# 5. Entity-Relationship Diagram (ERD)
def draw_erd():
    im = Image.new("RGB", (850, 520), "white")
    draw = ImageDraw.Draw(im)
    f_ent = ImageFont.truetype(font_path, 13)
    f_rel = ImageFont.truetype(font_path, 11)
    f_attr = ImageFont.truetype(font_path, 10)
    
    # 4 Entities
    entities = [
        ("users", 120, 150),
        ("devices", 425, 150),
        ("requests", 425, 380),
        ("device_photos", 730, 150)
    ]
    
    for name, cx, cy in entities:
        draw_box(draw, cx - 65, cy - 25, cx + 65, cy + 25, bg_color="#E8E8E8", border_color="#333333", border_width=2, radius=5)
        draw_text(draw, name, cx, cy, f_ent, fill="black")
        
    # Relationships (Diamonds)
    # users -- lists -- devices
    draw_diamond(draw, 272, 150, 45, 25, text="lists (1:N)", font=f_rel)
    draw.line([185, 150, 227, 150], fill="black", width=2)
    draw.line([317, 150, 360, 150], fill="black", width=2)
    
    # devices -- has -- device_photos
    draw_diamond(draw, 577, 150, 45, 25, text="has (1:N)", font=f_rel)
    draw.line([490, 150, 532, 150], fill="black", width=2)
    draw.line([622, 150, 665, 150], fill="black", width=2)
    
    # devices -- requested -- requests
    draw_diamond(draw, 425, 265, 50, 25, text="requested (1:N)", font=f_rel)
    draw.line([425, 175, 425, 240], fill="black", width=2)
    draw.line([425, 290, 425, 355], fill="black", width=2)
    
    # users -- makes -- requests
    draw_diamond(draw, 272, 380, 45, 25, text="makes (1:N)", font=f_rel)
    draw.line([120, 175, 120, 380], fill="black", width=2)
    draw.line([120, 380, 227, 380], fill="black", width=2)
    draw.line([317, 380, 360, 380], fill="black", width=2)
    
    # Attributes for entities
    # users
    u_attrs = ["id (PK)", "full_name", "phone", "email", "role", "governorate"]
    for i, attr in enumerate(u_attrs):
        cx = 60
        cy = 220 + i * 35
        draw.ellipse([cx - 45, cy - 13, cx + 45, cy + 13], fill="white", outline="black")
        draw_text(draw, attr, cx, cy, f_attr, fill="black")
        draw.line([120, 175, cx, cy - 13], fill="gray", width=1)
        
    # devices
    d_attrs = ["id (PK)", "name", "category", "offer_type", "status", "governorate"]
    for i, attr in enumerate(d_attrs):
        cx = 320 if i < 3 else 530
        cy = 50 + (i % 3) * 35
        draw.ellipse([cx - 45, cy - 13, cx + 45, cy + 13], fill="white", outline="black")
        draw_text(draw, attr, cx, cy, f_attr, fill="black")
        draw.line([425, 150, cx, cy], fill="gray", width=1)
        
    # requests
    r_attrs = ["id (PK)", "case_desc", "status", "medical_doc_path", "created_at"]
    for i, attr in enumerate(r_attrs):
        cx = 425 + (i - 2) * 100
        cy = 460
        draw.ellipse([cx - 48, cy - 13, cx + 48, cy + 13], fill="white", outline="black")
        draw_text(draw, attr, cx, cy, f_attr, fill="black")
        draw.line([425, 405, cx, cy - 13], fill="gray", width=1)
        
    im.save(os.path.join(output_dir, "erd_diagram.png"))

# 6. Sequence Diagram for User Registration
def draw_sequence_register():
    im = Image.new("RGB", (850, 480), "white")
    draw = ImageDraw.Draw(im)
    f_actor = ImageFont.truetype(font_path, 13)
    f_arrow = ImageFont.truetype(font_path, 11)
    
    # Columns
    x_user = 150
    x_ui = 425
    x_db = 700
    
    # Lifelines
    draw_box(draw, x_user - 50, 20, x_user + 50, 60, bg_color="#E0F7FA", border_color="#00ACC1")
    draw_text(draw, "المستخدم", x_user, 40, f_actor)
    draw.line([x_user, 60, x_user, 440], fill="black", width=2, joint="miter")
    
    draw_box(draw, x_ui - 60, 20, x_ui + 60, 60, bg_color="#FFF9C4", border_color="#FBC02D")
    draw_text(draw, "واجهة التسجيل", x_ui, 40, f_actor)
    draw.line([x_ui, 60, x_ui, 440], fill="black", width=2)
    
    draw_box(draw, x_db - 50, 20, x_db + 50, 60, bg_color="#C8E6C9", border_color="#388E3C")
    draw_text(draw, "قاعدة البيانات", x_db, 40, f_actor)
    draw.line([x_db, 60, x_db, 440], fill="black", width=2)
    
    # Sequence lines
    # 1. User fills register form
    draw_arrow(draw, x_user, 110, x_ui, 110, text="1. إرسال نموذج التسجيل (الاسم، الهاتف، المحافظة...)", font=f_arrow)
    # 2. UI validates data
    draw.line([x_ui, 140, x_ui + 40, 140], fill="black", width=2)
    draw.line([x_ui + 40, 140, x_ui + 40, 170], fill="black", width=2)
    draw_arrow(draw, x_ui + 40, 170, x_ui, 170, text="2. التحقق من المدخلات وكلمة المرور", font=f_arrow)
    
    # 3. UI checks if email/phone exists
    draw_arrow(draw, x_ui, 210, x_db, 210, text="3. استعلام عن البريد الإلكتروني ورقم الهاتف", font=f_arrow)
    # 4. DB returns result
    draw.line([x_db, 240, x_ui, 240], fill="black", width=1) # Dotted back
    draw_text(draw, "4. لا توجد حسابات مكررة", x_ui + 130, 225, f_arrow)
    
    # 5. UI inserts user
    draw_arrow(draw, x_ui, 280, x_db, 280, text="5. حفظ المستخدم الجديد (تشفير كلمة المرور)", font=f_arrow)
    # 6. DB success
    draw.line([x_db, 310, x_ui, 310], fill="black", width=1)
    draw_text(draw, "6. تم الحفظ بنجاح والحصول على المعرف ID", x_ui + 130, 295, f_arrow)
    
    # 7. UI success response
    draw_arrow(draw, x_ui, 360, x_user, 360, text="7. إنشاء جلسة تسجيل الدخول وتوجيه المستخدم للوحة التحكم", font=f_arrow)
    
    im.save(os.path.join(output_dir, "sequence_register.png"))

# 7. Sequence Diagram for Listing Device
def draw_sequence_add_device():
    im = Image.new("RGB", (850, 480), "white")
    draw = ImageDraw.Draw(im)
    f_actor = ImageFont.truetype(font_path, 13)
    f_arrow = ImageFont.truetype(font_path, 11)
    
    x_donor = 120
    x_ui = 350
    x_db = 580
    x_fs = 760
    
    # Lifelines
    draw_box(draw, x_donor - 50, 20, x_donor + 50, 60, bg_color="#E0F7FA", border_color="#00ACC1")
    draw_text(draw, "المتبرع", x_donor, 40, f_actor)
    draw.line([x_donor, 60, x_donor, 440], fill="black", width=2)
    
    draw_box(draw, x_ui - 60, 20, x_ui + 60, 60, bg_color="#FFF9C4", border_color="#FBC02D")
    draw_text(draw, "واجهة إضافة جهاز", x_ui, 40, f_actor)
    draw.line([x_ui, 60, x_ui, 440], fill="black", width=2)
    
    draw_box(draw, x_db - 50, 20, x_db + 50, 60, bg_color="#C8E6C9", border_color="#388E3C")
    draw_text(draw, "قاعدة البيانات", x_db, 40, f_actor)
    draw.line([x_db, 60, x_db, 440], fill="black", width=2)
    
    draw_box(draw, x_fs - 50, 20, x_fs + 50, 60, bg_color="#FFCDD2", border_color="#E53935")
    draw_text(draw, "نظام الملفات (السيرفر)", x_fs, 40, f_actor)
    draw.line([x_fs, 60, x_fs, 440], fill="black", width=2)
    
    # 1. Donor enters device info and uploads photos
    draw_arrow(draw, x_donor, 100, x_ui, 100, text="1. إدخال بيانات الجهاز + رفع الصور + الموقع", font=f_arrow)
    # 2. UI uploads photos to File System
    draw_arrow(draw, x_ui, 150, x_fs, 150, text="2. فحص وتسمية الصور وتخزينها", font=f_arrow)
    # 3. File System returns file paths
    draw.line([x_fs, 180, x_ui, 180], fill="black", width=1)
    draw_text(draw, "3. مسار حفظ الصور المؤرشفة", x_ui + 150, 165, f_arrow)
    
    # 4. UI saves device info in DB (status = pending_review)
    draw_arrow(draw, x_ui, 230, x_db, 230, text="4. حفظ تفاصيل الجهاز في جدول devices", font=f_arrow)
    # 5. DB returns success
    draw.line([x_db, 260, x_ui, 260], fill="black", width=1)
    draw_text(draw, "5. تم حفظ الجهاز بنجاح", x_ui + 100, 245, f_arrow)
    
    # 6. UI saves photos paths
    draw_arrow(draw, x_ui, 300, x_db, 300, text="6. إدراج مسارات الصور في جدول device_photos", font=f_arrow)
    draw.line([x_db, 330, x_ui, 330], fill="black", width=1)
    
    # 7. UI alerts donor
    draw_arrow(draw, x_ui, 380, x_donor, 380, text="7. إظهار رسالة 'تم الإضافة وبانتظار مراجعة الإدارة'", font=f_arrow)
    
    im.save(os.path.join(output_dir, "sequence_add_device.png"))

# 8. Sequence Diagram for Request Device
def draw_sequence_request_device():
    im = Image.new("RGB", (850, 500), "white")
    draw = ImageDraw.Draw(im)
    f_actor = ImageFont.truetype(font_path, 13)
    f_arrow = ImageFont.truetype(font_path, 11)
    
    x_ben = 100
    x_ui = 300
    x_db = 500
    x_adm = 720
    
    # Lifelines
    draw_box(draw, x_ben - 50, 20, x_ben + 50, 60, bg_color="#E0F7FA", border_color="#00ACC1")
    draw_text(draw, "المستفيد", x_ben, 40, f_actor)
    draw.line([x_ben, 60, x_ben, 460], fill="black", width=2)
    
    draw_box(draw, x_ui - 60, 20, x_ui + 60, 60, bg_color="#FFF9C4", border_color="#FBC02D")
    draw_text(draw, "واجهة الطلب", x_ui, 40, f_actor)
    draw.line([x_ui, 60, x_ui, 460], fill="black", width=2)
    
    draw_box(draw, x_db - 50, 20, x_db + 50, 60, bg_color="#C8E6C9", border_color="#388E3C")
    draw_text(draw, "قاعدة البيانات", x_db, 40, f_actor)
    draw.line([x_db, 60, x_db, 460], fill="black", width=2)
    
    draw_box(draw, x_adm - 50, 20, x_adm + 50, 60, bg_color="#FFE0B2", border_color="#F57C00")
    draw_text(draw, "المشرف (Admin)", x_adm, 40, f_actor)
    draw.line([x_adm, 60, x_adm, 460], fill="black", width=2)
    
    # 1. Beneficiary requests device and uploads medical doc
    draw_arrow(draw, x_ben, 100, x_ui, 100, text="1. إرسال طلب استعارة (وصف الحالة + التقرير الطبي)", font=f_arrow)
    # 2. UI inserts request in requests table & updates device status to 'under_request_review'
    draw_arrow(draw, x_ui, 140, x_db, 140, text="2. حفظ الطلب في requests وتحديث حالة الجهاز", font=f_arrow)
    
    # 3. Admin reviews request
    draw_arrow(draw, x_db, 190, x_adm, 190, text="3. استعراض الطلب والتقرير الطبي للمريض", font=f_arrow)
    # 4. Admin decides (Approve)
    draw_arrow(draw, x_adm, 240, x_ui, 240, text="4. اتخاذ قرار الموافقة على الطلب", font=f_arrow)
    
    # 5. UI updates request to 'approved' and device to 'loaned' in DB
    draw_arrow(draw, x_ui, 290, x_db, 290, text="5. تحديث حالة الطلب إلى approved وحالة الجهاز إلى loaned", font=f_arrow)
    draw.line([x_db, 320, x_ui, 320], fill="black", width=1)
    
    # 6. UI notifies Beneficiary
    draw_arrow(draw, x_ui, 360, x_ben, 360, text="6. إظهار بيانات التواصل (رقم الهاتف وزر واتساب)", font=f_arrow)
    # 7. Beneficiary contacts Donor via WhatsApp
    draw.line([x_ben, 400, x_ben - 40, 400], fill="black", width=2)
    draw.line([x_ben - 40, 400, x_ben - 40, 430], fill="black", width=2)
    draw_arrow(draw, x_ben - 40, 430, x_ben, 430, text="7. تواصل مباشر مع المتبرع لتسليم الجهاز", font=f_arrow)
    
    im.save(os.path.join(output_dir, "sequence_request_device.png"))

# 9. Flowchart for Donor (إضافة جهاز)
def draw_flowchart_donor():
    im = Image.new("RGB", (850, 520), "white")
    draw = ImageDraw.Draw(im)
    f_box = ImageFont.truetype(font_path, 12)
    f_title = ImageFont.truetype(font_path, 16)
    
    draw_text(draw, "مخطط انسيابي: إضافة جهاز طبي جديد (المتبرع)", 425, 25, f_title, fill="black")
    
    # 1. Start (Oval)
    draw_box(draw, 375, 60, 475, 100, bg_color="#E0F2F1", border_color="#009688", radius=20)
    draw_text(draw, "البداية", 425, 80, f_box, fill="black")
    draw_arrow(draw, 425, 100, 425, 140)
    
    # 2. Enter Info
    draw_box(draw, 340, 140, 510, 190, bg_color="#E1F5FE", border_color="#0288D1", radius=5)
    draw_text(draw, "إدخال بيانات الجهاز والصور والموقع", 425, 165, f_box, fill="black")
    draw_arrow(draw, 425, 190, 425, 230)
    
    # 3. Decision: Valid?
    draw_diamond(draw, 425, 260, 75, 30, text="هل البيانات صالحة؟", font=f_box)
    
    # No branch
    draw_arrow(draw, 350, 260, 250, 260)
    draw_box(draw, 120, 235, 250, 285, bg_color="#FFEBEE", border_color="#E53935", radius=5)
    draw_text(draw, "إظهار أخطاء التحقق", 185, 260, f_box, fill="black")
    # arrow back to enter info
    draw.line([185, 235, 185, 165], fill="black", width=2)
    draw_arrow(draw, 185, 165, 340, 165)
    
    # Yes branch
    draw_arrow(draw, 425, 290, 425, 330)
    # Save as pending
    draw_box(draw, 340, 330, 510, 380, bg_color="#E8F5E9", border_color="#43A047", radius=5)
    draw_text(draw, "حفظ الجهاز بحالة (معلق) في قاعدة البيانات", 425, 355, f_box, fill="black")
    draw_arrow(draw, 425, 380, 425, 420)
    
    # Notification
    draw_box(draw, 345, 420, 505, 460, bg_color="#E0F7FA", border_color="#00ACC1", radius=5)
    draw_text(draw, "إعلام المتبرع بانتظار مراجعة الإدارة", 425, 440, f_box, fill="black")
    draw_arrow(draw, 425, 460, 425, 490)
    
    # End
    draw_box(draw, 385, 490, 465, 515, bg_color="#ECEFF1", border_color="#607D8B", radius=10)
    draw_text(draw, "النهاية", 425, 502, f_box, fill="black")
    
    im.save(os.path.join(output_dir, "flowchart_donor.png"))

# 10. Flowchart for Beneficiary (طلب استعارة)
def draw_flowchart_beneficiary():
    im = Image.new("RGB", (850, 520), "white")
    draw = ImageDraw.Draw(im)
    f_box = ImageFont.truetype(font_path, 12)
    f_title = ImageFont.truetype(font_path, 16)
    
    draw_text(draw, "مخطط انسيابي: طلب استعارة جهاز طبي (المستفيد)", 425, 25, f_title, fill="black")
    
    # Start
    draw_box(draw, 375, 60, 475, 100, bg_color="#E0F2F1", border_color="#009688", radius=20)
    draw_text(draw, "البداية", 425, 80, f_box, fill="black")
    draw_arrow(draw, 425, 100, 425, 130)
    
    # Search
    draw_box(draw, 340, 130, 510, 175, bg_color="#E1F5FE", border_color="#0288D1", radius=5)
    draw_text(draw, "تصفح السوق والبحث والفلترة الجغرافية", 425, 152, f_box, fill="black")
    draw_arrow(draw, 425, 175, 425, 210)
    
    # Choose Device & Request
    draw_box(draw, 330, 210, 520, 255, bg_color="#E1F5FE", border_color="#0288D1", radius=5)
    draw_text(draw, "اختيار جهاز متاح والضغط على 'طلب استعارة'", 425, 232, f_box, fill="black")
    draw_arrow(draw, 425, 255, 425, 290)
    
    # Input case details and upload medical document
    draw_box(draw, 310, 290, 540, 335, bg_color="#E1F5FE", border_color="#0288D1", radius=5)
    draw_text(draw, "إدخال وصف الحالة الطبية ورفع التقرير الطبي المعتمد", 425, 312, f_box, fill="black")
    draw_arrow(draw, 425, 335, 425, 370)
    
    # Save request
    draw_box(draw, 315, 370, 535, 415, bg_color="#E8F5E9", border_color="#43A047", radius=5)
    draw_text(draw, "حفظ الطلب وتحديث حالة الجهاز إلى (قيد المراجعة)", 425, 392, f_box, fill="black")
    draw_arrow(draw, 425, 415, 425, 450)
    
    # Show status pending review
    draw_box(draw, 330, 450, 520, 490, bg_color="#E0F7FA", border_color="#00ACC1", radius=5)
    draw_text(draw, "عرض حالة الطلب (معلق) بانتظار المشرف", 425, 470, f_box, fill="black")
    draw_arrow(draw, 425, 490, 425, 510)
    
    # End
    draw_box(draw, 385, 510, 465, 535, bg_color="#ECEFF1", border_color="#607D8B", radius=10)
    draw_text(draw, "النهاية", 425, 522, f_box, fill="black")
    
    im.save(os.path.join(output_dir, "flowchart_beneficiary.png"))

# 11. Flowchart for Admin Approval
def draw_flowchart_admin():
    im = Image.new("RGB", (850, 550), "white")
    draw = ImageDraw.Draw(im)
    f_box = ImageFont.truetype(font_path, 12)
    f_title = ImageFont.truetype(font_path, 16)
    
    draw_text(draw, "مخطط انسيابي: معالجة الطلبات والتحقق (المشرف)", 425, 25, f_title, fill="black")
    
    # Start
    draw_box(draw, 375, 60, 475, 100, bg_color="#E0F2F1", border_color="#009688", radius=20)
    draw_text(draw, "البداية", 425, 80, f_box, fill="black")
    draw_arrow(draw, 425, 100, 425, 140)
    
    # Review Listings / Requests
    draw_box(draw, 330, 140, 520, 190, bg_color="#FFE0B2", border_color="#F57C00", radius=5)
    draw_text(draw, "فحص الإعلانات المعلقة أو طلبات الاستعارة", 425, 165, f_box, fill="black")
    draw_arrow(draw, 425, 190, 425, 230)
    
    # Decision: Valid?
    draw_diamond(draw, 425, 260, 80, 30, text="هل المستندات/البيانات سليمة؟", font=f_box)
    
    # No branch (Reject)
    draw_arrow(draw, 345, 260, 220, 260)
    draw_box(draw, 80, 235, 220, 285, bg_color="#FFEBEE", border_color="#E53935", radius=5)
    draw_text(draw, "رفض الطلب وكتابة السبب", 150, 260, f_box, fill="black")
    draw_arrow(draw, 150, 285, 150, 350)
    
    # Process reject update
    draw_box(draw, 60, 350, 240, 400, bg_color="#FFEBEE", border_color="#E53935", radius=5)
    draw_text(draw, "إرجاع حالة الجهاز (نشط) وإشعار المريض", 150, 375, f_box, fill="black")
    draw.line([150, 400, 150, 450], fill="black", width=2)
    draw.line([150, 450, 425, 450], fill="black", width=2) # Line to merge
    
    # Yes branch (Approve)
    draw_arrow(draw, 425, 290, 425, 350)
    draw_box(draw, 310, 350, 540, 400, bg_color="#E8F5E9", border_color="#43A047", radius=5)
    draw_text(draw, "الموافقة على الطلب وتنشيط أزرار الاتصال", 425, 375, f_box, fill="black")
    draw_arrow(draw, 425, 400, 425, 480) # Arrow to end section
    
    # Merge line from reject
    draw.line([425, 450, 425, 480], fill="black", width=2)
    
    # End
    draw_box(draw, 385, 480, 465, 515, bg_color="#ECEFF1", border_color="#607D8B", radius=10)
    draw_text(draw, "النهاية", 425, 497, f_box, fill="black")
    
    im.save(os.path.join(output_dir, "flowchart_admin.png"))

def generate_all():
    print("Generating diagrams...")
    draw_system_hierarchy()
    draw_use_case()
    draw_context_dfd()
    draw_level0_dfd()
    draw_erd()
    draw_sequence_register()
    draw_sequence_add_device()
    draw_sequence_request_device()
    draw_flowchart_donor()
    draw_flowchart_beneficiary()
    draw_flowchart_admin()
    print("Done generating diagrams!")

if __name__ == "__main__":
    generate_all()
