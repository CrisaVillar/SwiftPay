<?php

include_once '../includes/config.php';
include_once '../includes/employee.php';
include_once '../includes/payslip.php';

$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);
$payslip = new Payslip($db);

$message = '';
$messageType = '';

/* =========================
   ADD EMPLOYEE
========================= */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']);
    $position = trim($_POST['position']);
    $basic_salary = floatval($_POST['basic_salary']);
    $password = $_POST['password'];

    // Password should be hashed only once.
    // createEmployee() should handle the password_hash().
    if (
        $username &&
        $email &&
        $full_name &&
        $position &&
        $basic_salary > 0 &&
        $password
    ) {

        if (
            $employee->createEmployee(
                $username,
                $email,
                $password,
                $role,
                $full_name,
                $position,
                $basic_salary
            )
        ) {
            $message = "Employee added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding employee. Username or email may already exist.";
            $messageType = "error";
        }

    } else {
        $message = "Please complete all employee fields.";
        $messageType = "error";
    }
}


/* =========================
   GENERATE PAYSLIP
========================= */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_payslip'])) {

    $employee_id = intval($_POST['employee_id']);
    $basic_salary = floatval($_POST['basic_salary']);
    $days_worked = intval($_POST['days_worked']);
    $month = $_POST['month'];

    if ($days_worked < 1 || $days_worked > 31) {

        $message = "Days worked must be between 1 and 31.";
        $messageType = "error";

    } elseif ($basic_salary <= 0) {

        $message = "Basic salary must be greater than zero.";
        $messageType = "error";

    } elseif (empty($month)) {

        $message = "Please select a month.";
        $messageType = "error";

    } else {

        /*
         * Salary calculation:
         * Monthly salary / 30 × days worked
         */
        $total_salary = $basic_salary * ($days_worked / 30);

        /*
         * This requires your Payslip class to accept:
         * employee_id
         * salary
         * days_worked
         * total_salary
         * month
         */
        if (
            $payslip->generatePayslip(
                $employee_id,
                $basic_salary,
                $days_worked,
                $total_salary,
                $month
            )
        ) {

            $message = "Payslip generated successfully!";
            $messageType = "success";

        } else {

            $message = "Error generating payslip.";
            $messageType = "error";
        }
    }
}


/* =========================
   GET EMPLOYEES
========================= */

$employees = $employee->getEmployees();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SwiftPay - Admin Dashboard</title>

    <link rel="icon" href="../images/swiftpay-logo.png" type="image/png">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: linear-gradient(180deg, #0d6efd, #064bb5);
            color: white;
            padding: 25px 18px;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-area img {
            width: 130px;
            max-width: 100%;
            background: white;
            border-radius: 12px;
            padding: 8px;
        }

        .logo-area h2 {
            margin: 12px 0 0;
            font-size: 20px;
        }

        .logo-area p {
            margin: 5px 0;
            font-size: 12px;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.18);
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        .main {
            margin-left: 240px;
            padding: 30px;
        }

        .topbar {
            background: white;
            border-radius: 14px;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 25px;
        }

        .topbar p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .admin-badge {
            background: #e8f1ff;
            color: #0d6efd;
            padding: 9px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           CARDS
        ========================= */

        .card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            margin: 0;
            color: #111827;
            font-size: 20px;
        }

        .card-header p {
            color: #6b7280;
            margin: 5px 0 0;
            font-size: 13px;
        }

        /* =========================
           FORM
        ========================= */

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: span 2;
        }

        label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 7px;
            color: #374151;
        }

        input,
        select {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: #f9fafb;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #0d6efd;
            background: white;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 75px;
        }

        .show-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #0d6efd;
            cursor: pointer;
            font-size: 12px;
            padding: 5px;
        }

        .btn {
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-primary {
            background: #0d6efd;
            color: white;
        }

        .btn-primary:hover {
            background: #0b5ed7;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        /* =========================
           EMPLOYEE TABLE
        ========================= */

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f6fa;
            color: #374151;
            text-align: left;
            padding: 14px;
            font-size: 13px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tr:hover td {
            background: #f9fafb;
        }

        .employee-name {
            font-weight: 600;
        }

        .salary-input {
            min-width: 120px;
        }

        .days-input {
            min-width: 100px;
        }

        .month-input {
            min-width: 150px;
        }

        /* =========================
           EMPTY STATE
        ========================= */

        .empty {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: span 1;
            }

        }

        @media (max-width: 600px) {

            .main {
                padding: 15px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .card {
                padding: 18px;
            }

        }

    </style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="logo-area">

        <img src="../images/swiftpay-logo.png" alt="SwiftPay Logo">

        <h2>SwiftPay</h2>

        <p>Payroll Management System</p>

    </div>


    <ul class="sidebar-menu">

        <li>
            <a href="admin_dashboard.php" class="active">
                Dashboard
            </a>
        </li>

        <li>
            <a href="employee_table.php">
                Employee List
            </a>
        </li>

        <li>
            <a href="employee_table.php">
                Manage Employees
            </a>
        </li>

        <li>
            <a href="../logout.php">
                Logout
            </a>
        </li>

    </ul>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- TOP BAR -->

    <div class="topbar">

        <div>

            <h1>Admin Dashboard</h1>

            <p>Manage employees and generate payroll payslips.</p>

        </div>

        <div class="admin-badge">
            Administrator
        </div>

    </div>


    <!-- MESSAGE -->

    <?php if ($message): ?>

        <div class="message <?php echo $messageType; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         ADD EMPLOYEE
    ========================= -->

    <div class="card">

        <div class="card-header">

            <div>

                <h2>Add Employee</h2>

                <p>Create a new employee account.</p>

            </div>

        </div>


        <form method="POST" action="">

            <div class="form-grid">


                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter username"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="employee@example.com"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter full name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="position">
                        Position
                    </label>

                    <input
                        type="text"
                        id="position"
                        name="position"
                        placeholder="e.g. Staff"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="basic_salary">
                        Basic Salary
                    </label>

                    <input
                        type="number"
                        id="basic_salary"
                        name="basic_salary"
                        placeholder="15000"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="role">
                        Role
                    </label>

                    <select id="role" name="role" required>

                        <option value="employee">
                            Employee
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter password"
                            required
                        >

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword()"
                        >
                            Show
                        </button>

                    </div>

                </div>


                <div class="form-group">

                    <label>&nbsp;</label>

                    <button
                        type="submit"
                        name="add_employee"
                        class="btn btn-primary"
                    >
                        + Add Employee
                    </button>

                </div>


            </div>

        </form>

    </div>


    <!-- =========================
         GENERATE PAYSLIP
    ========================= -->

    <div class="card">

        <div class="card-header">

            <div>

                <h2>Generate Payslip</h2>

                <p>Select an employee and enter their payroll information.</p>

            </div>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Employee Name</th>

                        <th>Basic Salary</th>

                        <th>Days Worked</th>

                        <th>Month</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php if (!empty($employees)): ?>


                    <?php foreach ($employees as $emp): ?>

                        <tr>

                            <form method="POST" action="">

                                <td>

                                    <div class="employee-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $emp['full_name']
                                        );
                                        ?>

                                    </div>

                                </td>


                                <td>

                                    <input
                                        class="salary-input"
                                        type="number"
                                        name="basic_salary"
                                        value="<?php
                                        echo htmlspecialchars(
                                            $emp['basic_salary']
                                        );
                                        ?>"
                                        min="0"
                                        step="0.01"
                                        required
                                    >

                                </td>


                                <td>

                                    <input
                                        class="days-input"
                                        type="number"
                                        name="days_worked"
                                        placeholder="Days"
                                        min="1"
                                        max="31"
                                        required
                                    >

                                </td>


                                <td>

                                    <input
                                        class="month-input"
                                        type="month"
                                        name="month"
                                        required
                                    >

                                </td>


                                <td>

                                    <input
                                        type="hidden"
                                        name="employee_id"
                                        value="<?php
                                        echo htmlspecialchars(
                                            $emp['id']
                                        );
                                        ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="generate_payslip"
                                        class="btn btn-success"
                                    >
                                        Generate
                                    </button>

                                </td>

                            </form>

                        </tr>

                    <?php endforeach; ?>


                <?php else: ?>

                    <tr>

                        <td colspan="5" class="empty">

                            No employees found.

                        </td>

                    </tr>

                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


</main>


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.querySelector(".show-password");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "Hide";

    } else {

        password.type = "password";

        button.textContent = "Show";

    }

}

</script>


</body>

</html>