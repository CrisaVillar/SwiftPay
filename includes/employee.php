<?php

include_once 'user.php';

class Employee extends User
{
    /*
    |--------------------------------------------------------------------------
    | CREATE EMPLOYEE
    |--------------------------------------------------------------------------
    */
    public function createEmployee(
        $username,
        $email,
        $password,
        $role,
        $full_name,
        $position,
        $basic_salary
    ) {
        // Make sure only employee accounts are created here
        $role = 'employee';

        // Check if username already exists
        $check = $this->conn->prepare(
            "SELECT id FROM users WHERE username = ? LIMIT 1"
        );

        $check->bind_param("s", $username);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {
            return false;
        }

        $check->close();


        // Check if email already exists
        $checkEmail = $this->conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();

        $emailResult = $checkEmail->get_result();

        if ($emailResult->num_rows > 0) {
            return false;
        }

        $checkEmail->close();


        // Hash password
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        // Insert user
        $stmt = $this->conn->prepare("
            INSERT INTO users
            (username, email, password, role)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $username,
            $email,
            $hashedPassword,
            $role
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }


        // Get newly created user ID
        $employeeId = $this->conn->insert_id;

        $stmt->close();


        // Insert employee information
        $employeeStmt = $this->conn->prepare("
            INSERT INTO employees
            (user_id, full_name, position, basic_salary)
            VALUES (?, ?, ?, ?)
        ");

        $employeeStmt->bind_param(
            "issd",
            $employeeId,
            $full_name,
            $position,
            $basic_salary
        );

        $success = $employeeStmt->execute();

        $employeeStmt->close();

        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL EMPLOYEES
    |--------------------------------------------------------------------------
    */
    public function getEmployees()
    {
        $stmt = $this->conn->prepare("
            SELECT
                u.id,
                u.username,
                u.email,
                u.role,
                e.employee_id,
                e.full_name,
                e.position,
                e.basic_salary,
                e.status,
                e.date_hired
            FROM users u

            INNER JOIN employees e
                ON u.id = e.user_id

            WHERE u.role = 'employee'

            ORDER BY e.employee_id DESC
        ");

        $stmt->execute();

        $result = $stmt->get_result();

        $employees = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $employees;
    }


    /*
    |--------------------------------------------------------------------------
    | GET EMPLOYEE BY USER ID
    |--------------------------------------------------------------------------
    */
    public function getEmployeeById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT
                u.id,
                u.username,
                u.email,
                u.role,

                e.employee_id,
                e.full_name,
                e.position,
                e.basic_salary,
                e.status,
                e.date_hired

            FROM users u

            INNER JOIN employees e
                ON u.id = e.user_id

            WHERE u.id = ?
            AND u.role = 'employee'

            LIMIT 1
        ");

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $employee = $result->fetch_assoc();

        $stmt->close();

        return $employee;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE EMPLOYEE
    |--------------------------------------------------------------------------
    */
    public function updateEmployee(
        $id,
        $username,
        $email,
        $role,
        $full_name,
        $position,
        $basic_salary
    ) {
        // Only allow employee role from this class
        $role = 'employee';

        $stmt = $this->conn->prepare("
            UPDATE users u

            INNER JOIN employees e
                ON u.id = e.user_id

            SET
                u.username = ?,
                u.email = ?,
                u.role = ?,

                e.full_name = ?,
                e.position = ?,
                e.basic_salary = ?

            WHERE u.id = ?
        ");

        $stmt->bind_param(
            "sssssdi",
            $username,
            $email,
            $role,
            $full_name,
            $position,
            $basic_salary,
            $id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE EMPLOYEE
    |--------------------------------------------------------------------------
    */
    public function deleteEmployee($id)
    {
        /*
         * We don't delete from payslips because our database
         * uses the payroll table instead.
         *
         * The payroll records will be automatically deleted
         * because of the foreign key ON DELETE CASCADE.
         */

        $stmt = $this->conn->prepare("
            DELETE FROM users
            WHERE id = ?
            AND role = 'employee'
        ");

        $stmt->bind_param("i", $id);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
}

?>