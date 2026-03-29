<?php



include_once 'includes\config.php';  
include 'includes\user.php';     
include 'includes\employee.php'; 


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
if ($role === 'admin') {
    header('Location: admin_dashboard.php');
} elseif ($role === 'employee') {
    header('Location: employee_dashboard.php');
} else {
    session_destroy();
    header('Location: login.php');
    exit();
}

?>
