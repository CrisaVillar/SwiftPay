<?php

include_once '../includes/employee.php'; 
include_once '../includes/config.php'; 

session_start();
$database = new Database();
$db = $database->getConnection();
$employee = new Employee($db);


$message = '';


$id = $_GET['id'] ?? null;
$employeeData = null;
if ($id) {
 
    $employeeData = $employee->getEmployeeById($id); 
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $password = $_POST['password'] ?? ''; 

    if ($id) {
        
        if ($employee->updateEmployee($id, $username, $email, $role, $full_name, $position, $salary, $password)) {
            $message = "Employee updated successfully!";
        } else {
            $message = "Error updating employee!";
        }
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if ($employee->createEmployee($username, $email, $hashed_password, $role, $full_name, $position, $salary)) {
            $message = "Employee added successfully!";
        } else {
            $message = "Error adding employee!";
        }
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if ($employee->deleteEmployee($delete_id)) {
        $message = "Employee deleted successfully!";
    } else {
        $message = "Error deleting employee!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family:  'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc; 
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #4a4a4a; 
            background-color: #d8b895; 
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 1rem;
            margin-bottom: 8px;
            color: #4a4a4a;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #f5f5e9; 
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #d8b895; 
            background-color: #fff;
            box-shadow: 0 0 8px rgba(216, 184, 149, 0.3);
        }
        .form-group input[type="submit"] {
            width: auto;
            background-color: #d8b895;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .form-group input[type="submit"]:hover {
            background-color: #c8a883; 
        }
        .button {
            background-color: #d8b895; 
            color: white;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #c8a883; 
        }

        .nav-bar {
            background-color: #c8a883; 
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 40px;
        }
        .nav-bar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .nav-bar ul li {
            display: inline-block;
            margin: 0 15px;
        }
        .nav-bar ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
        }
        .nav-bar ul li a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            h1 {
                font-size: 2rem;
            }
            .form-group input, .form-group select {
                font-size: 0.9rem;
            }
            .form-group input[type="submit"] {
                font-size: 1rem;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Employee Management</h1>
    
    <div class="nav-bar">
        <ul>
            <li><a href="admin_dashboard.php">Main Dashboard</a></li>
            <li><a href="employee_table.php">Employee List</a></li>
        </ul>
    </div>
  
    <?php if (!empty($message)): ?>
        <p class="message" style="color: #b5651d; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
  
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($employeeData['id'] ?? ''); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($employeeData['username'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employeeData['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="employee" <?php echo (isset($employeeData['role']) && $employeeData['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                <option value="admin" <?php echo (isset($employeeData['role']) && $employeeData['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($employeeData['full_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="position">Position:</label>
            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($employeeData['position'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="basic_salary">Salary:</label>
            <input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($employeeData['salary'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <div class="form-group">
            <input type="submit" value="<?php echo ($id) ? 'UPDATE EMPLOYEE' : 'UPDATE EMPLOYEE'; ?>">
        </div>
    </form>
</div>
</body>
</html>