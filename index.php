<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spectra - Dashboard Bento Grid</title>
    <!-- استدعاء بوتستراب 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- خطوط عصرية -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: #12141c;
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-purple: #a855f7;
            --accent-glow: rgba(168, 85, 247, 0.15);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            padding: 30px 20px 80px 20px;
        }

        /* الهيدر العلوي */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 10px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* تصميم الكروت بنظام Bento Grid متساوي وبوردرات احترافية */
        .bento-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 22px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .bento-card:hover {
            border-color: rgba(168, 85, 247, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 15px 35px var(--accent-glow);
        }

        /* تثبيت أبعاد الكروت لتكون متساوية ومنتظمة */
        .phone-img-container {
            width: 100%;
            height: 160px;
            border-radius: 14px;
            overflow: hidden;
            background: #1a1d26;
            margin-bottom: 15px;
            border: 1px solid var(--card-border);
        }

        .phone-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .bento-card:hover .phone-img {
            transform: scale(1.05);
        }

        .phone-name {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            margin-bottom: 6px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .phone-name:hover {
            color: var(--accent-purple);
        }

        .phone-specs {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .phone-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--accent-purple);
        }

        /* الأزرار الاحترافية */
        .btn-bento {
            background: rgba(168, 85, 247, 0.1);
            color: var(--accent-purple);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 12px;
            padding: 8px 15px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            display: block;
            width: 100%;
            margin-top: 10px;
        }

        .btn-bento:hover {
            background: var(--accent-purple);
            color: #fff;
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--card-border);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-purple);
            color: var(--accent-purple);
        }

        /* شريط التنقل السفلي */
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

        .bottom-nav a.active, .bottom-nav a:hover {
            color: var(--accent-purple);
        }
    </style>
</head>
<body>

    <div class="container" style="max-width: 1200px;">
        
        <!-- هيدر الداشبورد -->
        <div class="dashboard-header">
            <div>
                <div class="brand-title">Spectra Overview 📊</div>
                <p class="text-muted mb-0" style="font-size: 13px;">إدارة وعرض الهواتف بنظام الـ Bento Grid المودرن</p>
            </div>
            <div>
                <a href="compare.php" class="btn-outline-custom">قائمة المقارنة ⚖️</a>
            </div>
        </div>

        <!-- شبكة الكروت المتساوية Bento Grid -->
        <div class="row g-4">
            <?php
            include 'db.php';
            $sql = "SELECT * FROM phones ORDER BY id DESC";
            $stmt = $conn->query($sql);
            $phones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($phones) > 0) {
                foreach ($phones as $phone) {
                    echo '<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">';
                    echo '<div class="bento-card">';
                    
                    // صورة المنتج داخل إطار متساوي (مربوطة بصفحة details.php)
                    if (!empty($phone['image'])) {
                        echo '<div class="phone-img-container">';
                        echo '<a href="details.php?id=' . $phone['id'] . '">';
                        echo '<img src="' . htmlspecialchars($phone['image']) . '" class="phone-img" alt="phone">';
                        echo '</a>';
                        echo '</div>';
                    }

                    // تفاصيل المنتج
                    echo '<div>';
                    echo '<a href="details.php?id=' . $phone['id'] . '" class="phone-name">' . htmlspecialchars($phone['name']) . '</a>';
                    echo '<div class="phone-specs">الرام: ' . (!empty($phone['ram']) ? htmlspecialchars($phone['ram']) : '-') . ' | المساحة: ' . (!empty($phone['storage']) ? htmlspecialchars($phone['storage']) : '-') . '</div>';
                    echo '<div class="phone-price">' . htmlspecialchars($phone['price']) . ' جنيه</div>';
                    echo '</div>';

                    // زر الإضافة للمقارنة
                    echo '<div>';
                    echo '<a href="compare.php?add=' . $phone['id'] . '" class="btn-bento">إضافة للمقارنة ⚖️</a>';
                    echo '</div>';

                    echo '</div>'; // نهاية الـ bento-card
                    echo '</div>'; // نهاية الـ col
                }
            } else {
                echo '<div class="col-12 text-center py-5">';
                echo '<p class="text-muted">لا توجد هواتف مضافة حتى الآن.</p>';
                echo '</div>';
            }
            ?>
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