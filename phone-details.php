<?php
include 'db.php';

// التأكد من وجود ID في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// جلب بيانات الموبايل المحدد
$stmt = $conn->prepare("SELECT * FROM phones WHERE id = :id");
$stmt->execute([':id' => $id]);
$phone = $stmt->fetch(PDO::FETCH_ASSOC);

// لو الموبايل مش موجود
if (!$phone) {
    echo "<!DOCTYPE html><html lang='ar' dir='rtl'><body style='background:#0b0f19; color:white; font-family:Cairo; text-align:center; padding-top:50px;'>";
    echo "<h2>عذراً، هذا الهاتف غير موجود!</h2>";
    echo "<a href='index.php' style='color:#00e5bc;'>العودة للرئيسية</a>";
    echo "</body></html>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($phone['name']); ?> - Spectra</title>
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

        .details-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .phone-img-large {
            width: 100%;
            max-width: 280px;
            height: 280px;
            object-fit: cover;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .phone-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 20px;
        }

        .phone-price {
            font-size: 22px;
            color: var(--accent-color);
            font-weight: 700;
        }

        .specs-list {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .specs-list li {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .specs-label {
            color: var(--text-muted);
            font-weight: 600;
        }

        .specs-value {
            color: var(--text-main);
            font-weight: 700;
        }

        /* الأزرار */
        .btn-custom-accent {
            background-color: var(--accent-color);
            color: #0b0f19;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-custom-accent:hover {
            background-color: #00c4a1;
            color: #0b0f19;
            box-shadow: 0 0 15px rgba(0, 229, 188, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        /* شريط التنقل السفلي الثابت */
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

    <div class="container" style="max-width: 700px;">
        
        <!-- زر الرجوع -->
        <div class="mb-4">
            <a href="index.php" class="btn-outline-custom">← العودة للمتجر</a>
        </div>

        <div class="details-card text-center">
            
            <?php if (!empty($phone['image'])): ?>
                <img src="<?php echo htmlspecialchars($phone['image']); ?>" class="phone-img-large" alt="Phone Image">
            <?php endif; ?>
            
            <h1 class="phone-title"><?php echo htmlspecialchars($phone['name']); ?></h1>
            <div class="phone-price my-2"><?php echo htmlspecialchars($phone['price']); ?> جنيه</div>

            <hr style="border-color: var(--border-color); margin: 25px 0;">

            <div class="text-start">
                <h5 class="mb-3 text-white fw-bold">المواصفات التقنية:</h5>
                <ul class="specs-list">
                    <li>
                        <span class="specs-label">الرام (RAM)</span>
                        <span class="specs-value"><?php echo !empty($phone['ram']) ? htmlspecialchars($phone['ram']) : 'غير متوفر'; ?></span>
                    </li>
                    <li>
                        <span class="specs-label">المساحة الداخلية</span>
                        <span class="specs-value"><?php echo !empty($phone['storage']) ? htmlspecialchars($phone['storage']) : 'غير متوفر'; ?></span>
                    </li>
                    <li>
                        <span class="specs-label">المعالج (Processor)</span>
                        <!-- تم تصحيح وسم الإغلاق هنا ليكون span صحيحاً -->
                        <span class="specs-value"><?php echo !empty($phone['processor']) ? htmlspecialchars($phone['processor']) : 'غير متوفر'; ?></span>
                    </li>
                </ul>
            </div>

            <div class="mt-4">
                <a href="compare.php?add=<?php echo $phone['id']; ?>" class="btn-custom-accent w-100 py-2">إضافة للمقارنة ⚖️</a>
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