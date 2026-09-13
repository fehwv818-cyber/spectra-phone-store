<?php
session_start();
include 'db.php';

// إضافة هاتف للمقارنة عن طريق الـ URL
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    if (!isset($_SESSION['compare'])) {
        $_SESSION['compare'] = [];
    }
    // حد أقصى 4 هواتف للمقارنة عشان شكل الجدول يفضل شغال تمام
    if (!in_array($id, $_SESSION['compare']) && count($_SESSION['compare']) < 4) {
        $_SESSION['compare'][] = $id;
    }
    header("Location: compare.php");
    exit();
}

// إزالة هاتف من المقارنة
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (($key = array_search($id, $_SESSION['compare'])) !== false) {
        unset($_SESSION['compare'][$key]);
        $_SESSION['compare'] = array_values($_SESSION['compare']); // إعادة ترتيب المفاتيح
    }
    header("Location: compare.php");
    exit();
}

$compare_ids = $_SESSION['compare'] ?? [];
$phones = [];

if (!empty($compare_ids)) {
    // تجهيز وتنظيف المعرفات وجعلها مصفوفة رقمية متسلسلة 100%
    $clean_ids = array_values(array_unique(array_map('intval', $compare_ids)));

    if (!empty($clean_ids)) {
        $placeholders = implode(',', array_fill(0, count($clean_ids), '?'));

        $stmt = $conn->prepare("SELECT * FROM phones WHERE id IN ($placeholders)");
        $stmt->execute($clean_ids);
        $phones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// -- منطق التحليل الذكي (Smart Analysis) --
$best_price_phone = null;
$best_specs_phone = null;

if (count($phones) > 0) {
    $best_price_phone = $phones[0];
    $best_specs_phone = $phones[0];

    foreach ($phones as $p) {
        if ((float)$p['price'] < (float)$best_price_phone['price']) {
            $best_price_phone = $p;
        }
        if ((int)$p['ram'] >= (int)$best_specs_phone['ram']) {
            $best_specs_phone = $p;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spectra - المقارنة الذكية</title>
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
            padding: 25px 15px 90px 15px;
        }

        .dashboard-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 700;
        }

        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .bento-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .analysis-box {
            background: rgba(168, 85, 247, 0.05);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .analysis-box h5 {
            color: var(--accent-purple);
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .table-custom {
            color: var(--text-main);
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .table-custom th,
        .table-custom td {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
            border-color: var(--card-border) !important;
            padding: 15px;
        }

        .table-custom th {
            color: var(--accent-purple) !important;
            font-weight: 700;
        }

        .phone-thumb {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--card-border);
        }

        .badge-best {
            background-color: var(--accent-green);
            color: #0b0c10;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--card-border);
            border-radius: 50px;
            padding: 7px 18px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-purple);
            color: var(--accent-purple);
        }

        .btn-add-compare {
            background: rgba(168, 85, 247, 0.1);
            color: var(--accent-purple);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 50px;
            padding: 7px 18px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-add-compare:hover {
            background: var(--accent-purple);
            color: #fff;
        }

        .btn-remove {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-remove:hover {
            background: #ef4444;
            color: #fff;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(18, 20, 28, 0.95);
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

    <div class="container" style="max-width: 1100px;">

        <!-- الهيدر المظبوط والمرن -->
        <div class="dashboard-header">
            <div>
                <div class="brand-title">Spectra Smart Comparison ⚖️</div>
                <p class="text-muted mb-0" style="font-size: 12px;">تحليل ذكي ومقارنة فورية بين مواصفات الهواتف</p>
            </div>
            <div class="header-actions">
                <a href="index.php" class="btn-add-compare">+ إضافة هاتف للمقارنة</a>
                <a href="index.php" class="btn-outline-custom">← العودة للمتجر</a>
            </div>
        </div>

        <?php if (count($phones) > 0): ?>

            <!-- صندوق التحليل الذكي (Smart Analysis Box) -->
            <div class="analysis-box">
                <h5>🤖 تقرير التحليل الذكي من Spectra:</h5>
                <p class="mb-2">
                    ✨ الهاتف الأقوى من حيث المواصفات والرام هو: <strong style="color: var(--accent-green);"><?php echo htmlspecialchars($best_specs_phone['name']); ?></strong>.
                </p>
                <p class="mb-0">
                    💰 الخيار الاقتصادي الأوفر من حيث السعر هو: <strong style="color: var(--accent-purple);"><?php echo htmlspecialchars($best_price_phone['name']); ?></strong> بسعر <?php echo htmlspecialchars($best_price_phone['price']); ?> جنيه.
                </p>
            </div>

            <!-- جدول المقارنة -->
            <div class="bento-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>صورة الجهاز</th>
                                <th>اسم الهاتف</th>
                                <th>السعر</th>
                                <th>الرام (RAM)</th>
                                <th>المساحة التخزينية</th>
                                <th>المعالج (Processor)</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($phones as $phone): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($phone['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($phone['image']); ?>" class="phone-thumb" alt="phone">
                                        <?php else: ?>
                                            <span>-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: 700;">
                                        <?php echo htmlspecialchars($phone['name']); ?>
                                        <?php if ($phone['id'] === $best_specs_phone['id']): ?>
                                            <div class="mt-1"><span class="badge-best">الأفضل أداءً 🚀</span></div>
                                        <?php endif; ?>
                                        <?php if ($phone['id'] === $best_price_phone['id']): ?>
                                            <div class="mt-1"><span class="badge" style="background: rgba(168,85,247,0.2); color: var(--accent-purple); font-size: 10px;">الأوفر سعراً 💰</span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--accent-purple); font-weight: 700;"><?php echo htmlspecialchars($phone['price']); ?> جنيه</td>
                                    <td><?php echo !empty($phone['ram']) ? htmlspecialchars($phone['ram']) : '-'; ?></td>
                                    <td><?php echo !empty($phone['storage']) ? htmlspecialchars($phone['storage']) : '-'; ?></td>
                                    <td><?php echo !empty($phone['processor']) ? htmlspecialchars($phone['processor']) : '-'; ?></td>
                                    <td>
                                        <a href="compare.php?remove=<?php echo $phone['id']; ?>" class="btn-remove">إزالة ❌</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="bento-card text-center py-5">
                <p class="text-muted mb-3">قائمة المقارنة فارغة حالياً.</p>
                <a href="index.php" class="btn-add-compare mb-2">+ أضف هواتف للمقارنة</a><br>
                <a href="index.php" class="btn-outline-custom">تصفح الهواتف من المتجر 📱</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- شريط التنقل السفلي -->
    <div class="bottom-nav">
        <a href="index.php">🏠 الرئيسية</a>
        <a href="compare.php" class="active">⚖️ المقارنة</a>
        <a href="login.php">🛠️ لوحة التحكم</a>
    </div>

</body>

</html>