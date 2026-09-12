<?php
session_start();

if (!isset($_SESSION['user_logged']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// التأكد من وجود ID وأنّه رقم صحيح
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = (int)$_GET['id'];

    // 1. نجيب مسار الصورة الخاصة بالموبايل الأول عشان نحذفها من الفولدر
    $stmt = $conn->prepare("SELECT image FROM phones WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $phone = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($phone) {
        $imagePath = $phone['image'];

        // لو الملف موجود فعلاً في السيرفر، نحذفه
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        // 2. نحذف سجل الموبايل من قاعدة البيانات
        $deleteStmt = $conn->prepare("DELETE FROM phones WHERE id = :id");
        $deleteStmt->execute([':id' => $id]);
    }
}

// نرجع الأدمن لوحة التحكم تاني بعد الحذف
header("Location: admin-dashboard.php");
exit();