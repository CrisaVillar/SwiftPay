<?php

include_once '../includes/config.php'; 
include_once '../includes/employee.php'; 
include_once '../includes/payslip.php';

$database = new Database();
$db = $database->getConnection();
$employee = new Employee($db);
$payslip = new Payslip($db);


$message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $position = $_POST['position'];
    $basic_salary = $_POST['basic_salary'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($employee->createEmployee($username, $email, $password, $role, $full_name, $position, $basic_salary)) {
        $message = "Employee added successfully!";
    } else {
        $message = "Error adding employee!";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_payslip'])) {
    $employee_id = $_POST['employee_id'];
    $basic_salary = $_POST['basic_salary'];
    $days_worked = $_POST['days_worked'];
    $month = $_POST['month'];
    
    $total_salary = $basic_salary * ($days_worked / 30); 

    if ($payslip->generatePayslip($employee_id, $basic_salary, $days_worked, $total_salary, $month)) {
        $message = "Payslip generated successfully for employee ID: {$employee_id}!";
    } else {
        $message = "Error generating payslip for employee ID: {$employee_id}!";
    }
}


$employees = $employee->getEmployees();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fafafa; 
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #f5e6cc, #f0d9b5); 
            color: #333;
            text-align: center;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
        }
        .nav-bar {
            background-color: #f0d9b5;
            margin-top: 20px;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .nav-bar ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-bar ul li {
            display: inline-block;
            margin-right: 20px;
        }
        .nav-bar ul li a {
            color: #333;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .add-employee-area, .generate-payslip-area {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }
        .add-employee-area h2, .generate-payslip-area h2 {
            color: #d68c45;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.8rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
            color: #555;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            box-sizing: border-box;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #d68c45;
            box-shadow: 0 0 8px rgba(214, 140, 69, 0.2);
            background-color: white;
        }
        button {
            background-color: #d68c45;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease-in-out;
            font-weight: 600;
        }
        button:hover {
            background-color: #f0d9b5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
        }
        table th {
            background-color: #d68c45;
            color: white;
            font-weight: 600;
        }
        table td {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover td {
            background-color: #f9f9f9;
        }
       
        @media (max-width: 768px) {
            .nav-bar ul li {
                display: block;
                margin-bottom: 10px;
            }
            .nav-bar ul li a {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>
    <div class="container">
        <div class="nav-bar">
            <ul>
                <li><a href="#">Main Dashboard</a></li>
                <li><a href="employee_table.php">Employee List</a></li>
            </ul>
        </div>
        <div class="add-employee-area">
            <h2>Add Employee</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" required>
                </div>
                <div class="form-group">
                    <label for="basic_salary">Basic Salary:</label>
                    <input type="number" id="basic_salary" name="basic_salary" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <input type="text" id="role" name="role" required>
                </div>
                <button type="submit" name="add_employee">Add Employee</button>
            </form>
        </div>
        <div class="generate-payslip-area">
            <h2>Generate Payslip</h2>
            <?php if ($message): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
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
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <form method="POST" action="">
                            <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                            <td>
                                <input type="number" name="basic_salary" value="<?php echo htmlspecialchars($emp['basic_salary']); ?>" required>
                            </td>
                            <td>
                                <input type="number" name="days_worked" placeholder="Days Worked" required>
                            </td>
                            <td>
                                <input type="month" name="month" required>
                            </td>
                            <td>
                                <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($emp['id']); ?>">
                                <button type="submit" name="generate_payslip">Generate Payslip</button>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
