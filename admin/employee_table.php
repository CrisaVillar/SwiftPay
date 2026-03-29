<?php
include_once "../includes/config.php";
include_once "../includes/employee.php";



$database = new Database();
$conn = $database->getConnection(); 


function searchEmployees($search, $limit, $offset) {
    global $conn;

    
    $sql = "SELECT u.id, u.username, u.email, u.role, e.full_name, e.position, e.basic_salary 
            FROM users u 
            JOIN employees e ON u.id = e.user_id 
            WHERE (e.full_name LIKE ? OR u.username LIKE ?) 
            AND u.role = 'employee' 
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $searchParam = "%" . $search . "%";
    $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset); 
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC); 
}


function countTotalEmployees($search) {
    global $conn;

    
    $sql = "SELECT COUNT(*) AS total 
            FROM users u 
            JOIN employees e ON u.id = e.user_id 
            WHERE (e.full_name LIKE ? OR u.username LIKE ?) 
            AND u.role = 'employee'";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $searchParam = "%" . $search . "%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total']; 
}


$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$search = isset($_GET['search']) ? $_GET['search'] : '';
$employees = searchEmployees($search, $limit, $offset); // Fetch filtered employees


$totalEmployees = countTotalEmployees($search);
$totalPages = ceil($totalEmployees / $limit);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="icon" href="https://scontent.fceb1-2.fna.fbcdn.net/v/t1.15752-9/462545309_1091081045758338_7529969207429204119_n.png?_nc_cat=100&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEAAjtDyMggk85B060KYLmMBe3BO0HALKcF7cE7QcAsp3h-EstzfR3H6vouuyWx0nNyRcW5Z_Bph9UVWZkMkFSm&_nc_ohc=7VA6Rveuy9cQ7kNvgHuLv6P&_nc_zt=23&_nc_ht=scontent.fceb1-2.fna&_nc_gid=ADsN3sxiriDZUvJuzd8kmcK&oh=03_Q7cD1QHIDNRkR7JxgEmoCc4V15oW2q0KGPEB4Of8MiAVKaspsQ&oe=6747A75F" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc; 
        }
        .container {
            width: 100%;
            max-width: 1250px;
            margin: auto;
            padding: 20px;
        }
        .header {
            background-color: #d4b483; 
            color: white;
            text-align: center;
            padding: 20px 0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2rem;
        }
        .nav-bar {
            background-color: #b89970; 
            padding: 10px;
            color: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .nav-bar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }
        .nav-bar ul li {
            display: inline;
            margin-right: 20px;
        }
        .nav-bar ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .nav-bar ul li a:hover {
            color: #ffe4b5; 
        }
        .table-container {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #d4b483; 
            color: white;
            padding: 12px;
        }
        td {
            padding: 12px;
            color: #333;
        }

        
        .search-form { 
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            gap: 5px;
        }
        .search-form input[type="text"] {
            width: 50%;
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }
        .search-form input[type="submit"] {
            padding: 10px 20px;
            background-color: #b89970; 
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .search-form input[type="submit"]:hover {
            background-color: #a38662; 
        }
        .action-buttons a {
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            border-radius: 25px;
            display: inline-block;
        }
        .edit-btn {
            background-color: #d4b483; 
        }
        .delete-btn {
            background-color: #b89970; 
        }
        .edit-btn:hover {
            background-color: #a38662;
        }
        .delete-btn:hover {
            background-color: #8a6f53;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 5px;
            padding: 8px 15px;
            background-color: #ddd;
            color: #333;
            border-radius: 25px;
            text-decoration: none;
            font-size: 1rem;
        }
        .pagination a.active {
            background-color: #b89970;
            color: white;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .table-container {
                width: 100%;
                overflow-x: auto;
            }
            table {
                font-size: 14px;
            }
            .search-form {
                flex-direction: column;
            }
            .search-form input[type="text"], .search-form input[type="submit"] {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee List</h1>
    </div>

    <div class="container">
        <div class="nav-bar">
            <ul>
                <li><a href="admin_dashboard.php">Main Dashboard</a></li>
                <li><a href="employee_table.php">Employee List</a></li>
            </ul>
        </div>

        
        <div class="search-form">
            <form method="GET">
                <input type="text" name="search" placeholder="Search employees..." value="<?php echo htmlspecialchars($search); ?>" required>
                <input type="submit" value="Search">
            </form>
        </div>

      
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($emp['id']); ?></td>
                                <td><?php echo htmlspecialchars($emp['username']); ?></td>
                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td><?php echo htmlspecialchars($emp['role']); ?></td>
                                <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($emp['position']); ?></td>
                                <td><?php echo htmlspecialchars($emp['basic_salary']); ?></td>
                                <td class="action-buttons">
                                    
                                    <a href="employee_management.php?id=<?php echo htmlspecialchars($emp['id']); ?>" class="edit-btn">Edit</a>
                                    
                                    <a href="?delete_id=<?php echo htmlspecialchars($emp['id']); ?>" onclick="return confirm('Are you sure you want to delete this employee?');" class="delete-btn">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No employees found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

      
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
