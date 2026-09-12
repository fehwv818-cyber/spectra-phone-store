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
            padding: 30px 0 80px 0;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* الأزرار المودرن */
        .btn-custom-accent {
            background-color: var(--accent-color);
            color: #0b0f19;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 14px;
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
            padding: 5px 15px;
            font-size: 13px;
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
            padding: 5px 15px;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-warning-custom:hover {
            background: rgba(255, 193, 7, 0.1);
            border-color: #ffc107;
            color: #ffc107;
        }

















        
        /* جدول البيانات الفخم */
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
            padding: 18px;
            font-size: 16px;
        }

        .table tbody td {
            background-color: transparent !important;
            color: var(--text-main) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
        }

        .table-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .price-tag {
            color: var(--accent-color);
            font-weight: 700;
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

        .bottom-nav a.active,
        .bottom-nav a:hover {
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
                <p class="text-muted mb-0" style="font-size: 14px;">أهلاً بك يا أدمن، إدارتك للمتجر أصبحت أسهل.</p>
            </div>
            <div>
                <a href="add-phone.php" class="btn-custom-accent">+ إضافة هاتف جديد</a>
                <a href="logout.php" class="btn-outline-danger-custom ms-2">تسجيل الخروج</a>
            </div>
        </div>

        <!-- جدول المنتجات -->
        <div class="table-responsive table-container">
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
                    <?php if (count($phones) > 0): ?>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-5 text-muted">لا توجد هواتف مسجلة حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- شريط التنقل السفلي -->
    <div class="bottom-nav">
        <a href="index.php">🏠 الرئيسية</a>
        <a href="compare.php">⚖️ المقارنة</a>
        <a href="admin-dashboard.php" class="active">🛠️ لوحة التحكم</a>
    </div>

</body>

</html>