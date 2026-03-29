<?php



if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
if ($role === 'admin') {
    header('Location: admin\admin_dashboard.php');
} elseif ($role === 'employee') {
    header('Location: employee\employee_dashboard.php');
}
exit();
?>
