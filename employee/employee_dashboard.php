<?php
session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../includes/config.php';
include_once '../includes/employee.php';
include_once '../includes/payslip.php';

$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);
$payslip = new Payslip($db);

$employeeId = $_SESSION['employee_id'];

$employeeData = $employee->getEmployeeById($employeeId);

if (!$employeeData) {
    echo "No employee data found.";
    exit();
}

$payslips = $payslip->getPayslipsByEmployeeId($employeeId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - SwiftPay</title>

    <link rel="icon" href="../images/swiftpay-logo.png" type="image/png">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 6%;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 700;
            color: #1261a0;
        }

        .brand-name span {
            color: #19a974;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .employee-label {
            color: #6b7280;
            font-size: 14px;
        }

        .logout {
            text-decoration: none;
            background: #1261a0;
            color: white;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .logout:hover {
            background: #0d4f83;
        }

        /* =========================
           MAIN CONTAINER
        ========================= */

        .main {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto 60px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 30px;
            color: #111827;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 15px;
        }

        /* =========================
           PROFILE HEADER
        ========================= */

        .profile-card {
            background: linear-gradient(135deg, #1261a0, #168f73);
            color: white;
            border-radius: 18px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(18, 97, 160, 0.15);
        }

        .profile-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
        }

        .profile-info h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .profile-info p {
            opacity: 0.9;
            font-size: 14px;
        }

        .role-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        /* =========================
           INFORMATION CARDS
        ========================= */

        .section-title {
            font-size: 20px;
            margin: 30px 0 15px;
            color: #111827;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .info-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            transition: 0.2s;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        .info-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        .salary {
            color: #168f73;
            font-size: 20px;
        }

        /* =========================
           PAYSLIPS
        ========================= */

        .payslip-section {
            margin-top: 35px;
        }

        .table-container {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.04);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 17px 20px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #eef0f3;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: 0.2s;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        .amount {
            font-weight: 700;
            color: #168f73;
        }

        .date {
            color: #6b7280;
        }

        .view-button {
            display: inline-block;
            text-decoration: none;
            background: #1261a0;
            color: white;
            padding: 8px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .view-button:hover {
            background: #0d4f83;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }

        /* =========================
           FOOTER
        ========================= */

        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            margin-top: 40px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 850px) {

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .profile-card {
                align-items: flex-start;
                gap: 20px;
            }

            .role-badge {
                margin-top: 5px;
            }
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 0 20px;
            }

            .employee-label {
                display: none;
            }

            .main {
                width: 94%;
                margin-top: 25px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .profile-card {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 700px;
            }

            .brand-name {
                font-size: 19px;
            }
        }

    </style>
</head>

<body>

<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">

    <div class="brand">

        <img src="../images/swiftpay-logo.png" alt="SwiftPay Logo">

        <div class="brand-name">
            Swift<span>Pay</span>
        </div>

    </div>

    <div class="nav-right">

        <span class="employee-label">
            Employee Portal
        </span>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<!-- =========================
     MAIN
========================= -->

<main class="main">

    <div class="page-header">

        <h1>My Profile</h1>

        <p>
            View your employee information and payroll records.
        </p>

    </div>


    <!-- PROFILE CARD -->

    <div class="profile-card">

        <div class="profile-left">

            <div class="avatar">
                <?php
                    echo strtoupper(substr($employeeData['full_name'], 0, 1));
                ?>
            </div>

            <div class="profile-info">

                <h2>
                    <?php echo htmlspecialchars($employeeData['full_name']); ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($employeeData['position']); ?>
                </p>

            </div>

        </div>

        <div class="role-badge">

            <?php echo ucfirst(htmlspecialchars($employeeData['role'])); ?>

        </div>

    </div>


    <!-- EMPLOYEE INFORMATION -->

    <h2 class="section-title">
        Employee Information
    </h2>

    <div class="info-grid">

        <div class="info-card">

            <div class="info-label">
                Username
            </div>

            <div class="info-value">
                <?php echo htmlspecialchars($employeeData['username']); ?>
            </div>

        </div>


        <div class="info-card">

            <div class="info-label">
                Email Address
            </div>

            <div class="info-value">
                <?php echo htmlspecialchars($employeeData['email']); ?>
            </div>

        </div>


        <div class="info-card">

            <div class="info-label">
                Full Name
            </div>

            <div class="info-value">
                <?php echo htmlspecialchars($employeeData['full_name']); ?>
            </div>

        </div>


        <div class="info-card">

            <div class="info-label">
                Position
            </div>

            <div class="info-value">
                <?php echo htmlspecialchars($employeeData['position']); ?>
            </div>

        </div>


        <div class="info-card">

            <div class="info-label">
                Basic Salary
            </div>

            <div class="info-value salary">
                ₱<?php echo number_format($employeeData['basic_salary'], 2); ?>
            </div>

        </div>


        <div class="info-card">

            <div class="info-label">
                Account Role
            </div>

            <div class="info-value">
                <?php echo ucfirst(htmlspecialchars($employeeData['role'])); ?>
            </div>

        </div>

    </div>


    <!-- PAYSLIPS -->

    <div class="payslip-section">

        <h2 class="section-title">
            My Payslips
        </h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Month</th>

                        <th>Days Worked</th>

                        <th>Total Salary</th>

                        <th>Generated</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (!empty($payslips)): ?>

                    <?php foreach ($payslips as $payslip): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php
                                    echo date(
                                        "F Y",
                                        strtotime($payslip['generated_at'])
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $payslip['days_worked']
                                );
                                ?> days
                            </td>

                            <td class="amount">

                                ₱<?php
                                echo number_format(
                                    $payslip['total_salary'],
                                    2
                                );
                                ?>

                            </td>

                            <td class="date">

                                <?php
                                echo date(
                                    "M d, Y h:i A",
                                    strtotime($payslip['generated_at'])
                                );
                                ?>

                            </td>

                            <td>

                                <a
                                    href="generate_payslip.php?payslip_id=<?php echo urlencode($payslip['id']); ?>"
                                    class="view-button"
                                >
                                    View Payslip
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5" class="empty">

                            No payslips available yet.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <div class="footer">

        © <?php echo date("Y"); ?> SwiftPay · Payroll Made Simple

    </div>

</main>

</body>
</html>