<?php
session_start();

include_once '../includes/config.php';
include_once '../includes/employee.php';
include_once '../includes/payslip.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
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

$payslipId = isset($_GET['payslip_id']) ? intval($_GET['payslip_id']) : 0;

$selectedPayslip = null;

if ($payslipId > 0) {
    $selectedPayslip = $payslip->getPayslipById($payslipId);
}

$payslips = $payslip->getPayslipsByEmployeeId($employeeId);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payslip Details - SwiftPay</title>

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
            background: #ffffff;
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

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-button {
            text-decoration: none;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .back-button {
            color: #1261a0;
            background: #edf5fb;
        }

        .back-button:hover {
            background: #dcecf8;
        }

        .logout-button {
            color: white;
            background: #1261a0;
        }

        .logout-button:hover {
            background: #0d4f83;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            width: 90%;
            max-width: 1000px;
            margin: 35px auto 60px;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 30px;
            color: #111827;
            margin-bottom: 5px;
        }

        .page-title p {
            color: #6b7280;
            font-size: 14px;
        }

        /* =========================
           PAYSLIP CARD
        ========================= */

        .payslip-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .payslip-header {
            background: linear-gradient(
                135deg,
                #1261a0,
                #168f73
            );

            color: white;
            padding: 30px;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .employee-header {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .avatar {
            width: 65px;
            height: 65px;

            border-radius: 50%;

            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.5);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;
            font-weight: 700;
        }

        .employee-header h2 {
            font-size: 23px;
            margin-bottom: 4px;
        }

        .employee-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .pay-period {
            text-align: right;
        }

        .pay-period-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .pay-period-value {
            font-size: 20px;
            font-weight: 700;
        }

        /* =========================
           EMPLOYEE INFORMATION
        ========================= */

        .section {
            padding: 28px 30px;
        }

        .section-title {
            font-size: 18px;
            color: #111827;
            margin-bottom: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        /* =========================
           SALARY SECTION
        ========================= */

        .salary-box {
            border-top: 1px solid #e5e7eb;
            padding: 28px 30px;
        }

        .salary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 15px 0;

            border-bottom: 1px solid #eef0f3;
        }

        .salary-row:last-child {
            border-bottom: none;
        }

        .salary-label {
            color: #64748b;
            font-size: 14px;
        }

        .salary-value {
            font-weight: 600;
            color: #111827;
        }

        .total-row {
            margin-top: 10px;
            padding: 20px;

            border-radius: 12px;

            background: #ecfdf5;

            border: 1px solid #bbf7d0;
        }

        .total-row .salary-label {
            color: #166534;
            font-weight: 600;
        }

        .total-row .salary-value {
            color: #168f73;
            font-size: 25px;
            font-weight: 800;
        }

        /* =========================
           GENERATED INFORMATION
        ========================= */

        .generated {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;

            padding: 18px 30px;

            color: #64748b;
            font-size: 13px;

            display: flex;
            justify-content: space-between;
        }

        /* =========================
           AVAILABLE PAYSLIPS
        ========================= */

        .available {
            margin-top: 25px;
        }

        .available-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .available-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .available-header h2 {
            font-size: 18px;
            color: #111827;
        }

        .payslip-item {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 17px 25px;

            border-bottom: 1px solid #eef0f3;

            transition: 0.2s;
        }

        .payslip-item:last-child {
            border-bottom: none;
        }

        .payslip-item:hover {
            background: #f8fbff;
        }

        .payslip-month {
            font-weight: 600;
            color: #111827;
        }

        .payslip-amount {
            color: #168f73;
            font-weight: 700;
            margin-left: 20px;
        }

        .view-button {
            text-decoration: none;

            background: #1261a0;
            color: white;

            padding: 8px 15px;

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
            padding: 35px;
            color: #9ca3af;
        }

        /* =========================
           PRINT
        ========================= */

        .print-button {
            margin-top: 20px;

            display: inline-block;

            border: none;
            cursor: pointer;

            background: #168f73;
            color: white;

            padding: 11px 20px;

            border-radius: 8px;

            font-size: 14px;
            font-weight: 600;
        }

        .print-button:hover {
            background: #11755d;
        }

        /* =========================
           FOOTER
        ========================= */

        footer {
            text-align: center;
            margin-top: 40px;
            color: #9ca3af;
            font-size: 13px;
        }

        /* =========================
           PRINT STYLING
        ========================= */

        @media print {

            body {
                background: white;
            }

            .navbar,
            .available,
            .page-title,
            .print-button,
            footer {
                display: none;
            }

            .main {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .payslip-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .navbar {
                padding: 0 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .payslip-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .pay-period {
                text-align: left;
            }

            .generated {
                flex-direction: column;
                gap: 5px;
            }

            .payslip-item {
                gap: 15px;
                flex-wrap: wrap;
            }

            .payslip-amount {
                margin-left: 0;
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

        <img
            src="../images/swiftpay-logo.png"
            alt="SwiftPay Logo"
        >

        <div class="brand-name">
            Swift<span>Pay</span>
        </div>

    </div>


    <div class="nav-links">

        <a
            href="employee_dashboard.php"
            class="nav-button back-button"
        >
            ← My Profile
        </a>

        <a
            href="../logout.php"
            class="nav-button logout-button"
        >
            Logout
        </a>

    </div>

</nav>


<!-- =========================
     MAIN
========================= -->

<main class="main">


    <div class="page-title">

        <h1>Payslip Details</h1>

        <p>
            Review your payroll information and salary details.
        </p>

    </div>


<?php if ($selectedPayslip): ?>


    <!-- PAYSLIP -->

    <div class="payslip-card">


        <!-- HEADER -->

        <div class="payslip-header">

            <div class="employee-header">

                <div class="avatar">

                    <?php
                    echo strtoupper(
                        substr(
                            $employeeData['full_name'],
                            0,
                            1
                        )
                    );
                    ?>

                </div>


                <div>

                    <h2>
                        <?php
                        echo htmlspecialchars(
                            $employeeData['full_name']
                        );
                        ?>
                    </h2>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $employeeData['position']
                        );
                        ?>
                    </p>

                </div>

            </div>


            <div class="pay-period">

                <div class="pay-period-label">
                    PAY PERIOD
                </div>

                <div class="pay-period-value">

                    <?php
                    echo date(
                        "F Y",
                        strtotime(
                            $selectedPayslip['generated_at']
                        )
                    );
                    ?>

                </div>

            </div>

        </div>


        <!-- EMPLOYEE INFORMATION -->

        <div class="section">

            <h2 class="section-title">
                Employee Information
            </h2>


            <div class="info-grid">


                <div class="info-box">

                    <div class="info-label">
                        Employee ID
                    </div>

                    <div class="info-value">
                        <?php
                        echo htmlspecialchars(
                            $employeeData['id']
                        );
                        ?>
                    </div>

                </div>


                <div class="info-box">

                    <div class="info-label">
                        Email
                    </div>

                    <div class="info-value">
                        <?php
                        echo htmlspecialchars(
                            $employeeData['email']
                        );
                        ?>
                    </div>

                </div>


                <div class="info-box">

                    <div class="info-label">
                        Position
                    </div>

                    <div class="info-value">
                        <?php
                        echo htmlspecialchars(
                            $employeeData['position']
                        );
                        ?>
                    </div>

                </div>


            </div>

        </div>


        <!-- SALARY DETAILS -->

        <div class="salary-box">

            <h2 class="section-title">
                Salary Details
            </h2>


            <div class="salary-row">

                <span class="salary-label">
                    Days Worked
                </span>

                <span class="salary-value">

                    <?php
                    echo htmlspecialchars(
                        $selectedPayslip['days_worked']
                    );
                    ?>

                    days

                </span>

            </div>


            <div class="salary-row">

                <span class="salary-label">
                    Basic Salary
                </span>

                <span class="salary-value">

                    ₱<?php
                    echo number_format(
                        $selectedPayslip['salary'],
                        2
                    );
                    ?>

                </span>

            </div>


            <div class="salary-row total-row">

                <span class="salary-label">
                    Total Salary
                </span>

                <span class="salary-value">

                    ₱<?php
                    echo number_format(
                        $selectedPayslip['total_salary'],
                        2
                    );
                    ?>

                </span>

            </div>


        </div>


        <!-- GENERATED -->

        <div class="generated">

            <span>
                Payslip generated
            </span>

            <strong>

                <?php
                echo date(
                    "M d, Y h:i A",
                    strtotime(
                        $selectedPayslip['generated_at']
                    )
                );
                ?>

            </strong>

        </div>


    </div>


    <!-- PRINT BUTTON -->

    <button
        onclick="window.print()"
        class="print-button"
    >
        Print Payslip
    </button>


<?php elseif ($payslipId): ?>


    <div class="payslip-card">

        <div class="empty">

            <h2>
                Payslip Not Found
            </h2>

            <p style="margin-top: 8px;">
                The selected payslip could not be found.
            </p>

        </div>

    </div>


<?php else: ?>


    <div class="payslip-card">

        <div class="empty">

            <h2>
                No Payslip Selected
            </h2>

            <p style="margin-top: 8px;">
                Please select a payslip from the list below.
            </p>

        </div>

    </div>


<?php endif; ?>


    <!-- AVAILABLE PAYSLIPS -->

    <div class="available">

        <div class="available-card">

            <div class="available-header">

                <h2>
                    Available Payslips
                </h2>

            </div>


            <?php if (!empty($payslips)): ?>


                <?php foreach ($payslips as $entry): ?>

                    <div class="payslip-item">


                        <div>

                            <span class="payslip-month">

                                <?php
                                echo date(
                                    "F Y",
                                    strtotime(
                                        $entry['generated_at']
                                    )
                                );
                                ?>

                            </span>

                            <span class="payslip-amount">

                                ₱<?php
                                echo number_format(
                                    $entry['total_salary'],
                                    2
                                );
                                ?>

                            </span>

                        </div>


                        <a
                            href="?payslip_id=<?php echo urlencode($entry['id']); ?>"
                            class="view-button"
                        >
                            View
                        </a>


                    </div>

                <?php endforeach; ?>


            <?php else: ?>

                <div class="empty">

                    No payslips available yet.

                </div>

            <?php endif; ?>


        </div>

    </div>


    <footer>

        © <?php echo date("Y"); ?> SwiftPay · Payroll Made Simple

    </footer>


</main>

</body>

</html>