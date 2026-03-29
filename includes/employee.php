<?php
include_once 'user.php';

class Employee extends User {
    // Inheritance: Employee-specific management methods

    // ADD/CREATE
    public function createEmployee($username, $email, $password, $role, $full_name, $position, $basic_salary) {
        // First, register the user in the users table
        $this->register($username, $email, $password, $role);

        // Get the last inserted user ID (which will be the employee's ID)
        $employeeId = $this->conn->insert_id; 

        // Insert into the employees table
        $stmt = $this->conn->prepare("INSERT INTO employees (user_id, full_name, position, basic_salary) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $employeeId, $full_name, $position, $basic_salary); // Adjust type to match the database
        return $stmt->execute(); // Execute the statement
    }

    // RETRIEVE
    public function getEmployees() {
        // Join users and employees tables to fetch employee details
        $stmt = $this->conn->prepare("
            SELECT u.id, u.username, u.email, u.role, e.full_name, e.position, e.basic_salary
            FROM users u
            JOIN employees e ON u.id = e.user_id
            WHERE u.role = 'employee'
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array
    }

    public function getEmployeeById($id) {
        // Join users and employees tables to fetch employee details by id
        $stmt = $this->conn->prepare("
            SELECT u.id, u.username, u.email, u.role, e.full_name, e.position, e.basic_salary
            FROM users u
            JOIN employees e ON u.id = e.user_id
            WHERE u.id = ? AND u.role = 'employee'
        ");
        $stmt->bind_param("i", $id); // Bind the employee id
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc(); // Fetch a single employee's data
    }

    // UPDATE
    public function updateEmployee($id, $username, $email, $role, $full_name, $position, $basic_salary) {
        $stmt = $this->conn->prepare("UPDATE users u JOIN employees e ON u.id = e.user_id SET u.username = ?, u.email = ?, u.role = ?, e.full_name = ?, e.position = ?, e.basic_salary = ? WHERE u.id = ?");
        $stmt->bind_param("sssssdi", $username, $email, $role, $full_name, $position, $basic_salary, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deleteEmployee($id) {
        // First, delete related records in the `payslips` table
        $stmt1 = $this->conn->prepare("DELETE FROM payslips WHERE employee_id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();

        // Next, delete related records in the `employees` table
        $stmt2 = $this->conn->prepare("DELETE FROM employees WHERE user_id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        // Finally, delete the user from the `users` table
        $stmt3 = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt3->bind_param("i", $id);
        return $stmt3->execute();
    }
}
?>
