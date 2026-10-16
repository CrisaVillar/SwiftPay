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

    <link rel="icon" href="images/swiftpay-logo.png" type="image/png">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background:
                radial-gradient(circle at top left, #dcecff 0%, transparent 35%),
                radial-gradient(circle at bottom right, #dff8ec 0%, transparent 35%),
                #f4f7fb;

            padding: 30px;
        }

        /* MAIN CARD */

        .login-wrapper {

            width: 100%;
            max-width: 950px;
            min-height: 560px;

            display: grid;
            grid-template-columns: 45% 55%;

            background: white;

            border-radius: 28px;

            overflow: hidden;

            box-shadow:
                0 25px 60px rgba(30, 55, 90, 0.15);
        }


        /* LEFT BRANDING */

        .branding {

            position: relative;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            text-align: center;

            padding: 50px;

            color: white;

            background:
                linear-gradient(
                    145deg,
                    #0b5ed7,
                    #147ee8 55%,
                    #13a875
                );

            overflow: hidden;
        }

        .branding::before {

            content: "";

            position: absolute;

            width: 350px;
            height: 350px;

            border-radius: 50%;

            background: rgba(255,255,255,0.08);

            top: -120px;
            left: -120px;
        }

        .branding::after {

            content: "";

            position: absolute;

            width: 300px;
            height: 300px;

            border-radius: 50%;

            background: rgba(255,255,255,0.07);

            bottom: -130px;
            right: -100px;
        }


        .logo {

            position: relative;

            width: 190px;
            max-width: 100%;

            height: auto;

            margin-bottom: 25px;

            z-index: 2;
        }


        .brand-title {

            position: relative;

            font-size: 38px;

            font-weight: 800;

            letter-spacing: -1px;

            margin-bottom: 10px;

            z-index: 2;
        }


        .brand-subtitle {

            position: relative;

            font-size: 15px;

            line-height: 1.7;

            max-width: 300px;

            color: rgba(255,255,255,0.88);

            z-index: 2;
        }


        .brand-line {

            position: relative;

            width: 55px;
            height: 4px;

            border-radius: 10px;

            background: white;

            margin: 20px auto;

            z-index: 2;
        }


        /* RIGHT LOGIN */

        .login-section {

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 55px;
        }


        .login-content {

            width: 100%;

            max-width: 380px;
        }


        .welcome {

            margin-bottom: 35px;
        }


        .welcome h1 {

            color: #172033;

            font-size: 30px;

            font-weight: 800;

            margin-bottom: 8px;
        }


        .welcome p {

            color: #7b8494;

            font-size: 14px;

            line-height: 1.6;
        }


        /* FORM */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 600;

            color: #343d4d;
        }


        .input-wrapper {

            position: relative;
        }


        .input-icon {

            position: absolute;

            left: 17px;
            top: 50%;

            transform: translateY(-50%);

            font-size: 17px;

            color: #9aa4b2;

            pointer-events: none;
        }


        input,
        select {

            width: 100%;

            height: 50px;

            border: 1px solid #e2e7ee;

            border-radius: 12px;

            background: #f8fafc;

            color: #202938;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition: 0.25s;
        }


        input {

            padding: 0 45px;
        }


        select {

            padding: 0 16px;
        }


        input:focus,
        select:focus {

            border-color: #1976e8;

            background: white;

            box-shadow:
                0 0 0 4px rgba(25,118,232,0.08);
        }


        input::placeholder {

            color: #a5adba;
        }


        /* PASSWORD BUTTON */

        .password-toggle {

            position: absolute;

            right: 15px;
            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            cursor: pointer;

            color: #8c96a5;

            font-size: 16px;
        }


        .password-toggle:hover {

            color: #1976e8;
        }


        /* ROLE */

        .role-label {

            display: flex;

            align-items: center;

            gap: 6px;

            margin-bottom: 8px;

            color: #343d4d;

            font-size: 13px;

            font-weight: 600;
        }


        /* LOGIN BUTTON */

        .login-btn {

            width: 100%;

            height: 52px;

            border: none;

            border-radius: 12px;

            margin-top: 8px;

            background:
                linear-gradient(
                    90deg,
                    #1167d8,
                    #1685e8
                );

            color: white;

            font-family: inherit;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 8px 18px rgba(17,103,216,0.22);

            transition: 0.25s;
        }


        .login-btn:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(17,103,216,0.30);
        }


        .login-btn:active {

            transform: translateY(0);
        }


        /* ERROR */

        .error-message {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-top: 18px;

            padding: 12px 14px;

            border-radius: 10px;

            background: #fff1f1;

            border: 1px solid #ffd5d5;

            color: #d93030;

            font-size: 13px;

            line-height: 1.4;
        }


        /* FOOTER */

        .footer {

            text-align: center;

            margin-top: 28px;

            font-size: 12px;

            color: #a0a8b5;
        }


        /* MOBILE */

        @media (max-width: 750px) {

            body {

                padding: 20px;
            }

            .login-wrapper {

                grid-template-columns: 1fr;

                max-width: 500px;
            }

            .branding {

                min-height: 280px;

                padding: 35px;
            }

            .logo {

                width: 130px;

                margin-bottom: 15px;
            }

            .brand-title {

                font-size: 28px;
            }

            .brand-subtitle {

                font-size: 13px;
            }

            .login-section {

                padding: 40px 30px;
            }
        }


        @media (max-width: 450px) {

            body {

                padding: 10px;
            }

            .login-wrapper {

                border-radius: 20px;
            }

            .login-section {

                padding: 35px 22px;
            }

            .welcome h1 {

                font-size: 26px;
            }
        }

    </style>

</head>


<body>


<div class="login-wrapper">


    <!-- LEFT SIDE -->

    <div class="branding">

        <img
            src="images/swiftpay-logo.png"
            alt="SwiftPay Logo"
            class="logo"
        >

        <div class="brand-line"></div>

        <h2 class="brand-title">
            SwiftPay
        </h2>

        <p class="brand-subtitle">
            A simple and efficient payroll management system
            designed to make employee payroll easier.
        </p>

    </div>



    <!-- RIGHT SIDE -->

    <div class="login-section">

        <div class="login-content">


            <div class="welcome">

                <h1>
                    Welcome Back!
                </h1>

                <p>
                    Sign in to access your SwiftPay account.
                </p>

            </div>


            <form method="post" action="login.php">


                <!-- USERNAME -->

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            👤
                        </span>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            required
                        >

                    </div>

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            🔒
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            id="toggleButton"
                        >
                            👁
                        </button>

                    </div>

                </div>



                <!-- ROLE -->

                <div class="form-group">

                    <label for="role" class="role-label">
                        Account Type
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                    >

                        <option value="employee">
                            Employee
                        </option>

                        <option value="admin">
                            Administrator
                        </option>

                    </select>

                </div>



                <!-- LOGIN -->

                <button
                    type="submit"
                    class="login-btn"
                >
                    Sign In
                </button>


            </form>



            <!-- ERROR MESSAGE -->

            <?php if (!empty($login_error)): ?>

                <div class="error-message">

                    ⚠️

                    <span>
                        <?php echo htmlspecialchars($login_error); ?>
                    </span>

                </div>

            <?php endif; ?>



            <div class="footer">

                © 2024 SwiftPay · Payroll Management System

            </div>


        </div>

    </div>


</div>



<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.getElementById("toggleButton");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "🙈";

    } else {

        password.type = "password";

        button.textContent = "👁";

    }

}

</script>


</body>

</html>