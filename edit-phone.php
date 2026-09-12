<?php
session_start();
if (!isset($_SESSION['user_logged']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin-dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM phones WHERE id = :id");
$stmt->execute([':id' => $id]);
$phone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$phone) {
    header("Location: admin-dashboard.php");
    exit();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $processor = $_POST['processor'];
    $imagePath = $phone['image'];

    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newImagePath = $uploadDir . basename($imageName);
        if (move_uploaded_file($imageTmp, $newImagePath)) {
            if (!empty($phone['image']) && file_exists($phone['image'])) {
                unlink($phone['image']);
            }
            $imagePath = $newImagePath;
        }
    }

    $updateSql = "UPDATE phones SET name = :name, price = :price, ram = :ram, storage = :storage, processor = :processor, image = :image WHERE id = :id";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute([
        ':name' => $name,
        ':price' => $price,
        ':ram' => $ram,
        ':storage' => $storage,
        ':processor' => $processor,
        ':image' => $imagePath,
        ':id' => $id
    ]);

    header("Location: admin-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الهاتف - Spectra</title>
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
            padding: 40px 0;
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
        }

        .btn-outline-custom:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .current-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="max-width: 600px; margin: auto;">
            <a href="admin-dashboard.php" class="btn-outline-custom">← العودة للوحة التحكم</a>
        </div>
        <div class="form-card">
            <!-- مكان التعديل الأول في العنوان -->
            <h2 class="mb-4 fw-bold text-center">تعديل بيانات: <?php echo htmlspecialchars($phone['name']); ?></h2>
            
            <form action="edit-phone.php?id=<?php echo $phone['id']; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label>اسم الهاتف:</label>
                    <!-- مكان التعديل الثاني في حقل الاسم -->
                    <input type="text" name="name" value="<?php echo htmlspecialchars($phone['name']); ?>" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label>السعر:</label>
                    <!-- مكان التعديل الثالث في حقل السعر -->
                    <input type="number" name="price" value="<?php echo htmlspecialchars($phone['price']); ?>" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label>الرام:</label>
                    <input type="text" name="ram" value="<?php echo htmlspecialchars($phone['ram']); ?>" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label>المساحة الداخلية:</label>
                    <input type="text" name="storage" value="<?php echo htmlspecialchars($phone['storage']); ?>" class="form-control">
                </div>
                
                <div class="mb-3">
                    <label>المعالج:</label>
                    <input type="text" name="processor" value="<?php echo htmlspecialchars($phone['processor']); ?>" class="form-control">
                </div>
                
                <div class="mb-4">
                    <label>صورة الهاتف الحالية:</label><br>
                    <?php if (!empty($phone['image'])): ?>
                        <img src="<?php echo htmlspecialchars($phone['image']); ?>" class="current-img" alt="phone">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                    <small style="color: var(--text-muted);">اتركها فارغة إذا لم ترد تغيير الصورة</small>
                </div>
                
                <button type="submit" name="update" class="btn-custom-accent">تحديث البيانات</button>
            </form>
        </div>
    </div>
</body>

</html>