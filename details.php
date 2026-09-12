<?php
session_start();
include 'db.php';

// التحقق من وجود معرف الهاتف في رابط الـ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$phone_id = intval($_GET['id']);

// جلب تفاصيل الهاتف المحدد من قاعدة البيانات
$stmt = $conn->prepare("SELECT * FROM phones WHERE id = ?");
$stmt->execute([$phone_id]);
$phone = $stmt->fetch(PDO::FETCH_ASSOC);

// لو الهاتف غير مشهور أو مش موجود في القاعدة يرجع للرئيسية
if (!$phone) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spectra - <?php echo htmlspecialchars($phone['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: #12141c;
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-purple: #a855f7;
            --accent-glow: rgba(168, 85, 247, 0.15);
            --accent-green: #00e5bc;
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 30px 20px 90px 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 700;
        }

        .bento-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .phone-img-large {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.02);
            padding: 15px;
            border: 1px solid var(--card-border);
        }

        .spec-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .spec-label {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
        }

        .spec-value {
            color: var(--text-main);
            font-size: 16px;
            font-weight: 700;
        }

        .price-tag {
            color: var(--accent-purple);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: var(--accent-purple);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
            text-align: center;
        }

        .btn-custom:hover {
            background-color: #9333ea;
            box-shadow: 0 0 15px var(--accent-glow);
            color: #fff;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--card-border);
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
            text-align: center;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-purple);
            color: var(--accent-purple);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(18, 20, 28, 0.9);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--card-border);
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
            color: var(--accent-purple);
        }
    </style>
</head>

<body>

    <div class="container" style="max-width: 1000px;">

        <!-- الهيدر -->
        <div class="dashboard-header">
            <div>
                <div class="brand-title">Spectra Store 📱</div>
                <p class="text-muted mb-0" style="font-size: 13px;">تفاصيل ومواصفات الجهاز بالكامل</p>
            </div>
            <div>
                <a href="index.php" class="btn-outline-custom" style="padding: 8px 20px; font-size: 13px;">← العودة للمتجر</a>
            </div>
        </div>

        <!-- محتوى تفاصيل الهاتف (Bento Grid Layout) -->
        <div class="bento-card">
            <div class="row g-4 align-items: center;">

                <!-- صورة الموبايل -->
                <div class="col-md-5 text-center">
                    <?php if (!empty($phone['image'])): ?>
                        <img src="<?php echo htmlspecialchars($phone['image']); ?>" class="phone-img-large" alt="<?php echo htmlspecialchars($phone['name']); ?>">
                    <?php else: ?>
                        <div class="py-5 text-muted">لا توجد صورة متاحة</div>
                    <?php endif; ?>
                </div>

                <!-- المواصفات والتفاصيل -->
                <div class="col-md-7">
                    <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($phone['name']); ?></h2>
                    <div class="price-tag"><?php echo htmlspecialchars($phone['price']); ?> جنيه</div>

                    <div class="specs-list mb-4">
                        <div class="spec-item">
                            <span class="spec-label">الرام (RAM)</span>
                            <span class="spec-value"><?php echo !empty($phone['ram']) ? htmlspecialchars($phone['ram']) : 'غير متاح'; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">المساحة التخزينية</span>
                            <span class="spec-value"><?php echo !empty($phone['storage']) ? htmlspecialchars($phone['storage']) : '-'; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">المعالج (Processor)</span>
                            <span class="spec-value"><?php echo !empty($phone['processor']) ? htmlspecialchars($phone['processor']) : '-'; ?></span>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="compare.php?add=<?php echo $phone['id']; ?>" class="btn-custom">⚖️ أضف للمقارنة</a>
                        <a href="index.php" class="btn-outline-custom">تصفح هواتف أخرى</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- شريط التنقل السفلي -->
    <div class="bottom-nav">
        <a href="index.php" class="active">🏠 الرئيسية</a>
        <a href="compare.php">⚖️ المقارنة</a>
        <a href="login.php">🛠️ لوحة التحكم</a>
    </div>

</body>

</html>