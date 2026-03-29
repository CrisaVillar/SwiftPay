<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/employee.php';
include_once '../includes/payslip.php';


if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$employee = new Employee($db);
$payslip = new Payslip($db);

$employeeId = $_SESSION['employee_id'];
$employeeData = $employee->getEmployeeById($employeeId);

if (!$employeeData) {
    echo "<p>No employee data found.</p>";
    exit();
}


$payslipId = isset($_GET['payslip_id']) ? intval($_GET['payslip_id']) : null;
$selectedPayslip = null;

if ($payslipId) {
   
    $selectedPayslip = $payslip->getPayslipById($employeeId, $payslipId);
}


$payslips = $payslip->getPayslipsByEmployeeId($employeeId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Details - SwiftPay</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f4e3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #f0e6d2;
            padding: 20px;
            text-align: center;
        }
        h1, h2 {
            margin: 0;
            color: #6a4c93;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .employee-info, .payslip-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0e6d2;
            font-weight: bold;
        }
        .payslip-list {
            margin-top: 20px;
        }
        .payslip-list h3 {
            margin-bottom: 10px;
        }
        .payslip-list ul {
            list-style-type: none;
            padding: 0;
        }
        .payslip-list li {
            margin: 5px 0;
        }
        .payslip-list a {
            text-decoration: none;
            color: #6a4c93;
            transition: color 0.3s;
        }
        .payslip-list a:hover {
            color: #d95d0f;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #666;
        }
        .employee-header {
            background-color: #6a4c93;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .employee-header h2 {
            color: white;
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>SwiftPay Payroll System</h1>
    </header>

    <div class="container">
        <?php if ($selectedPayslip): ?>
            <div class="employee-header">
                <h2><?php echo htmlspecialchars($employeeData['full_name']); ?>'s Payslip</h2>
            </div>
            <div class="employee-info">
                <table>
                    <tr>
                        <th>Employee ID:</th>
                        <td><?php echo htmlspecialchars($employeeData['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Position:</th>
                        <td><?php echo htmlspecialchars($employeeData['position']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($employeeData['email']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="payslip-details">
                <h2>Payslip Details</h2>
                <table>
                    <tr>
                        <th>Pay Period:</th>
                        <td><?php echo date("F Y", strtotime($selectedPayslip['generated_at'])); ?></td>
                    </tr>
                    <tr>
                        <th>Days Worked:</th>
                        <td><?php echo htmlspecialchars($selectedPayslip['days_worked']); ?></td>
                    </tr>
                    <tr>
                        <th>Basic Salary:</th>
                        <td><?php echo number_format($selectedPayslip['salary'], 2); ?> PHP</td>
                    </tr>
                    <tr>
                        <th>Total Salary:</th>
                        <td><?php echo number_format($selectedPayslip['total_salary'], 2); ?> PHP</td>
                    </tr>
                    <tr>
                        <th>Generated At:</th>
                        <td><?php echo htmlspecialchars($selectedPayslip['generated_at']); ?></td>
                    </tr>
                </table>
            </div>
        <?php elseif ($payslipId): ?>
            <p>No payslip found for the selected ID.</p>
        <?php else: ?>
            <div class="employee-header">
                <h2><?php echo htmlspecialchars($employeeData['full_name']); ?>'s Payslips</h2>
            </div>
            <p>Please select a payslip to view details.</p>
        <?php endif; ?>

        <div class="payslip-list">
            <h3>Available Payslips</h3>
            <?php if (count($payslips) > 0): ?>
                <ul>
                    <?php foreach ($payslips as $entry): ?>
                        <li>
                            <a href='?payslip_id=<?php echo htmlspecialchars($entry['id']); ?>'>
                                <?php echo date("F Y", strtotime($entry['generated_at'])); ?> - 
                                <?php echo number_format($entry['total_salary'], 2); ?> PHP
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No payslips available.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> SwiftPay Payroll System</p>
    </footer>
</body>
</html>