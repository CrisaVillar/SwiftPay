<?php
session_start(); 

\
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

\
include_once '../includes/config.php'; 
include_once '../includes/employee.php'; 
include_once '../includes/payslip.php'; 

$database = new Database();
$db = $database->getConnection();
$employee = new Employee($db);
$payslip = new Payslip($db);


$employeeId = $_SESSION['employee_id'];


$employeeData = $employee->getEmployeeById($employeeId);


$payslips = $payslip->getPayslipsByEmployeeId($employeeId); 


if (!$employeeData) {
    echo "No employee data found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Employee Dashboard</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5dc; 
            padding: 20px;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: 40px auto;
            transition: transform 0.2s;
            border: 1px solid #d4b483; 
        }
        .container:hover {
            transform: scale(1.02);
        }
        h1 {
            text-align: center;
            color: #b89970; 
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 40px;
            background-color: #fff;
        }
        .greeting {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }
        .table-section {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #d4b483; 
            color: white;
            text-transform: uppercase;
        }
        td {
            background-color: #fcfcfc;
            color: #333;
        }
        a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.2s;
        }
        a:hover {
            color: #2980b9;
        }
        .action-button {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            background-color: #b89970; 
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .action-button:hover {
            background-color: #a38662;
        }
        .message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        
        
        <div class="greeting">
            <p>Welcome, <strong><?php echo htmlspecialchars($employeeData['full_name']); ?></strong>! Here’s your profile and payslip details.</p>
        </div>

       
        <div class="table-section">
            <h2>Employee Details</h2>
            <table>
                <tr>
                    <th>Username</th>
                    <td><?php echo htmlspecialchars($employeeData['username']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($employeeData['email']); ?></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><?php echo htmlspecialchars($employeeData['full_name']); ?></td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td><?php echo htmlspecialchars($employeeData['position']); ?></td>
                </tr>
                <tr>
                    <th>Basic Salary</th>
                    <td><?php echo htmlspecialchars($employeeData['basic_salary']); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo htmlspecialchars($employeeData['role']); ?></td>
                </tr>
            </table>
        </div>

      
        <div class="table-section">
            <h2>Your Payslips</h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Days Worked</th>
                        <th>Total Salary</th>
                        <th>Generated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payslips)): ?>
                        <?php foreach ($payslips as $payslip): ?>
                        <tr>
                            <td><?php echo date("F Y", strtotime($payslip['generated_at']));?></td>
                            <td><?php echo htmlspecialchars($payslip['days_worked']); ?></td>
                            <td><?php echo htmlspecialchars($payslip['total_salary']); ?></td>
                            <td><?php echo htmlspecialchars($payslip['generated_at']); ?></td>
                            <td>
                                <a href="generate_payslip.php?payslip_id=<?php echo htmlspecialchars($payslip['id']); ?>" class="action-button">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No payslips available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
