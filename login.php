<?php
session_start(); 

include_once 'includes/config.php';
include_once 'includes/User.php';


$database = new Database();
$db = $database->getConnection();


$user = new User($db);


$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; 


    $logged_in_user = $user->login($username, $password, $role);

    if ($logged_in_user) {
       
        $_SESSION['employee_id'] = $logged_in_user['id']; 
        $_SESSION['username'] = $logged_in_user['username'];
        $_SESSION['role'] = $logged_in_user['role'];
\
        if ($role === 'admin') {
            header("Location: admin/admin_dashboard.php");
            exit();
        } elseif ($role === 'employee') {
            header("Location: employee/employee_dashboard.php");
            exit();
        }
    } else {
 
        $login_error = "Incorrect username, password, or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SwiftPay</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f5dc 30%, #e0d6ba 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        .logo {
            width: 80%;
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto 15px;
        }

        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="password"], select {
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 1rem;
            border: none;
            border-radius: 30px;
            background-color: #f7f3e9;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(245, 245, 220, 0.3);
        }

        input[type="submit"] {
            background: linear-gradient(90deg, #d2b48c, #e3c7a7);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease-in-out;
            font-weight: 600;
        }

        input[type="submit"]:hover {
            background: linear-gradient(90deg, #e3c7a7, #d2b48c);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=nd_wgIYfhv0Q7kNvgFFiwXh&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=AHOPYunucP3v8BeX1k_LZrc&oh=03_Q7cD1QGGGb3m1A94JA--bw9KQjNYDpob3PNH7oPFqz_dLt7nXg&oe=6743EB1F" alt="SwiftPay Logo" class="logo">
        <form method="post" action="login.php">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <select id="role" name="role" required>
                <option value="employee">Employee</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" value="Login">
        </form>

        <?php if (!empty($login_error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

