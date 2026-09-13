<?php
session_start();
if (!isset($_SESSION['user_logged']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

// جلب جميع الهواتف
$sql = "SELECT * FROM phones ORDER BY id DESC";
$stmt = $conn->query($sql);
$phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - Spectra</title>
    <!-- استدعاء بوتستراب 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- خطوط عصرية -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: #131825;
            --accent-color: #00e5bc;
            --text-main: #ffffff;
            --text-muted: #8c92a4;
            --border-color: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            padding: 20px 0 90px 0;
        }

        .dashboard-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
        }

        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* الأزرار المودرن */
        .btn-custom-accent {
            background-color: var(--accent-color);
            color: #0b0f19;
            border: none;
            border-radius: 50px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-custom-accent:hover {
            background-color: #00c4a1;
            color: #0b0f19;
            box-shadow: 0 0 15px rgba(0, 229, 188, 0.4);
        }

        .btn-outline-danger-custom {
            background: transparent;
            color: #ff4d4d;
            border: 1px solid rgba(255, 77, 77, 0.3);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-danger-custom:hover {
            background: rgba(255, 77, 77, 0.1);
            border-color: #ff4d4d;
            color: #ff4d4d;
        }

        .btn-outline-warning-custom {
            background: transparent;
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-warning-custom:hover {
            background: rgba(255, 193, 7, 0.1);
            border-color: #ffc107;
            color: #ffc107;
        }

        /* حاوية البيانات وتصميم الكاردات للموبايل */
        .table-container {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
        }

        .table {
            color: var(--text-main) !important;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table thead th {
            background-color: rgba(0, 229, 188, 0.05);
            color: var(--accent-color);
            border-bottom: 1px solid var(--border-color);
            padding: 16px;
            font-size: 15px;
        }

        .table tbody td {
            background-color: transparent !important;
            color: var(--text-main) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 14px;
        }

        .table-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .price-tag {
            color: var(--accent-color);
            font-weight: 700;
        }

        /* تصميم خاص للموبايل (Cards View) عشان يمنع التداخل نهائياً */
        @media (max-width: 768px) {
            .desktop-table {
                display: none !important;
            }
            .mobile-cards {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            .phone-card-item {
                background: var(--card-bg);
                border: 1px solid var(--border-color);
                border-radius: 16px;
                padding: 15px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            }
            .phone-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                border-bottom: 1px solid var(--border-color);
                padding-bottom: 10px;
            }
            .phone-card-info {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                font-size: 13px;
                color: var(--text-muted);
            }
            .phone-card-info span {
                color: var(--text-main);
                font-weight: 600;
            }
            .phone-card-actions {
                display: flex;
                gap: 8px;
                justify-content: flex-end;
                border-top: 1px solid var(--border-color);
                padding-top: 10px;
            }
        }

        @media (min-width: 769px) {
            .mobile-cards {
                display: none !important;
            }
        }

        /* شريط التنقل السفلي */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(19, 24, 37, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
        }

        .bottom-nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .bottom-nav a.active, .bottom-nav a:hover {
            color: var(--accent-color);
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- الهيدر الخاص باللوحة -->
        <div class="dashboard-header">
            <div>
                <div class="brand-logo">Spectra Dashboard</div>
                <p class="text-muted mb-0" style="font-size: 13px;">أهلاً بك يا أدمن، إدارتك للمتجر أصبحت أسهل.</p>
            </div>
            <div class="header-actions">
                <a href="add-phone.php" class="btn-custom-accent">+ إضافة هاتف جديد</a>
                <a href="logout.php" class="btn-outline-danger-custom">تسجيل الخروج</a>
            </div>
        </div>

        <?php if (count($phones) > 0): ?>
            
            <!-- عرض الجدول للكمبيوتر والشاشات الكبيرة -->
            <div class="table-responsive table-container desktop-table">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>اسم الهاتف</th>
                            <th>السعر</th>
                            <th>الرام / المساحة</th>
                            <th>المعالج</th>
                            <th>التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phones as $phone): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($phone['image'])): ?>
                                        <img src="<?php echo $phone['image']; ?>" class="table-img" alt="phone">
                                    <?php else: ?>
                                        <span class="text-muted">بدون</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo $phone['name']; ?></td>
                                <td><span class="price-tag"><?php echo $phone['price']; ?> جنيه</span></td>
                                <td style="font-size: 14px; color: var(--text-muted);"><?php echo $phone['ram']; ?> / <?php echo $phone['storage']; ?></td>
                                <td style="font-size: 14px;"><?php echo !empty($phone['processor']) ? $phone['processor'] : '-'; ?></td>
                                <td>
                                    <a href="edit-phone.php?id=<?php echo $phone['id']; ?>" class="btn-outline-warning-custom">تعديل</a>
                                    <a href="delete-phone.php?id=<?php echo $phone['id']; ?>" class="btn-outline-danger-custom ms-1" onclick="return confirm('هل أنت متأكد من حذف هذا الهاتف؟');">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- عرض كاردات مرتبة ومنظمة للموبايل الشاشات الصغيرة -->
            <div class="mobile-cards">
                <?php foreach ($phones as $phone): ?>
                    <div class="phone-card-item">
                        <div class="phone-card-header">
                            <?php if (!empty($phone['image'])): ?>
                                <img src="<?php echo $phone['image']; ?>" class="table-img" alt="phone">
                            <?php else: ?>
                                <div class="table-img d-flex align-items-center justify-content: center text-muted bg-dark" style="font-size:10px;">بدون</div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="font-size: 15px;"><?php echo $phone['name']; ?></h6>
                                <span class="price-tag" style="font-size: 14px;"><?php echo $phone['price']; ?> جنيه</span>
                            </div>
                        </div>
                        <div class="phone-card-info">
                            <div>الرام/المساحة: <span><?php echo $phone['ram']; ?> / <?php echo $phone['storage']; ?></span></div>
                            <div>المعالج: <span><?php echo !empty($phone['processor']) ? $phone['processor'] : '-'; ?></span></div>
                        </div>
                        <div class="phone-card-actions">
                            <a href="edit-phone.php?id=<?php echo $phone['id']; ?>" class="btn-outline-warning-custom">تعديل</a>
                            <a href="delete-phone.php?id=<?php echo $phone['id']; ?>" class="btn-outline-danger-custom" onclick="return confirm('هل أنت متأكد من حذف هذا الهاتف؟');">حذف</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="text-center py-5 text-muted table-container">
                <p class="mb-0">لا توجد هواتف مسجلة حالياً.</p>
            </div>
        <?php endif; ?>

    </div>

    <!-- شريط التنقل السفلي -->
    <div class="bottom-nav">
        <a href="index.php">🏠 الرئيسية</a>
        <a href="compare.php">⚖️ المقارنة</a>
        <a href="admin-dashboard.php" class="active">⚙️ لوحة التحكم</a>
    </div>

</body>
</html>