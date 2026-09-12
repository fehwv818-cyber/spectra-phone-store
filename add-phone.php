<?php
session_start();
if (!isset($_SESSION['user_logged']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$error_msg = "";

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $ram = trim($_POST['ram']);
    $storage = trim($_POST['storage']);
    $processor = trim($_POST['processor']);

    $imagePath = "";

    // التأكد من رفع صورة وعدم وجود أخطاء
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $uploadDir = "uploads/";

        // التأكد من أن المجلد موجود، وإن لم يكن قم بانشائه
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // استخراج امتداد الملف والتأكد أنه صورة حقيقية
        $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // توليد اسم فريد للصورة لمنع تكرار الأسماء أو استبدالها
            $newImageName = uniqid('phone_', true) . '.' . $fileExtension;
            $imagePath = $uploadDir . $newImageName;

            if (!move_uploaded_file($imageTmp, $imagePath)) {
                $error_msg = "فشل رفع الصورة، يرجى المحاولة مرة أخرى.";
            }
        } else {
            $error_msg = "صيغة الملف غير مسموح بها. يرفع (jpg, jpeg, png, webp, gif) فقط.";
        }
    }

    // إذا لم تحدث أخطاء، قم بالإدخال في القاعدة
    if (empty($error_msg)) {
        $sql = "INSERT INTO phones (name, price, ram, storage, processor, image) VALUES (:name, :price, :ram, :storage, :processor, :image)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':ram' => $ram,
            ':storage' => $storage,
            ':processor' => $processor,
            ':image' => $imagePath
        ]);

        header("Location: admin-dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة هاتف جديد - Spectra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 30px 15px;
        }

        .form-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            max-width: 600px;
            margin: auto;
        }

        .form-control,
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 12px;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 10px rgba(0, 229, 188, 0.2);
        }

        label {
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-custom-accent {
            background-color: var(--accent-color);
            color: #0b0f19;
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-custom-accent:hover {
            background-color: #00c4a1;
            box-shadow: 0 0 15px rgba(0, 229, 188, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 20px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="max-width: 600px; margin: auto;">
            <a href="admin-dashboard.php" class="btn-outline-custom">← العودة للوحة التحكم</a>
        </div>
        <div class="form-card">
            <h2 class="mb-4 fw-bold text-center">إضافة هاتف جديد 📱</h2>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size: 13px; border-radius: 10px;"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="add-phone.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>اسم الهاتف:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>السعر (بالجنيه):</label>
                    <input type="number" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>الرام:</label>
                    <input type="text" name="ram" class="form-control" placeholder="مثال: 8 جيجابايت">
                </div>
                <div class="mb-3">
                    <label>المساحة الداخلية:</label>
                    <input type="text" name="storage" class="form-control" placeholder="مثال: 128 جيجابايت">
                </div>
                <div class="mb-3">
                    <label>المعالج:</label>
                    <input type="text" name="processor" class="form-control" placeholder="مثال: Snapdragon 8 Gen 2">
                </div>
                <div class="mb-4">
                    <label>صورة الهاتف:</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted mt-1 d-block" style="font-size: 11px;">الصور المسموحة: JPG, JPEG, PNG, WEBP</small>
                </div>
                <button type="submit" name="submit" class="btn-custom-accent">حفظ وإضافة الهاتف</button>
            </form>
        </div>
    </div>
</body>

</html>