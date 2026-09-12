<?php
session_start();
include 'db.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // التحقق الآمن باستخدام password_verify (الأفضل)
    // ولو حابب تخليها مؤقتاً مقارنة عادية لحد ما تحدث الداتا بيس ممكن تسيبها بس يفضل التشفير
    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        
        $_SESSION['user_logged'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin-dashboard.php");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }

    } else {
        $error = "اسم المستخدم أو كلمة المرور غير صحيحة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Spectra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        body { font-family: 'Cairo', sans-serif; background-color: var(--bg-color); color: var(--text-main); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
        .form-control, .form-control:focus { background-color: rgba(255,255,255,0.03); border: 1px solid var(--card-border); color: var(--text-main); border-radius: 12px; padding: 12px; }
        .form-control:focus { border-color: var(--accent-purple); box-shadow: 0 0 10px var(--accent-glow); }
        label { color: var(--text-muted); margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        .btn-custom { background-color: var(--accent-purple); color: #fff; border: none; border-radius: 50px; padding: 12px; font-weight: 700; width: 100%; transition: all 0.2s; }
        .btn-custom:hover { background-color: #9333ea; box-shadow: 0 0 15px var(--accent-glow); }
        .brand-logo { font-size: 26px; font-weight: 700; text-align: center; margin-bottom: 25px; letter-spacing: 0.5px; }
        .alert-custom { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 10px; padding: 10px; font-size: 13px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">Spectra 🚀</div>
        
        <?php if (!empty($error)): ?>
            <div class="alert-custom"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label>اسم المستخدم:</label>
                <input type="text" name="username" class="form-control" required autocomplete="off">
            </div>
            <div class="mb-4">
                <label>كلمة المرور:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn-custom">تسجيل الدخول</button>
        </form>
    </div>

</body>
</html>