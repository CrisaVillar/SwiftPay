<?php

class Payslip
{
    private $conn;
    private $table_name = "payslips";

    public $id;
    public $employee_id;
    public $month;
    public $days_worked;
    public $salary;
    public $total_salary;
    public $generated_at;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($db)
    {
        $this->conn = $db;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE PAYSLIP
    |--------------------------------------------------------------------------
    */

    public function generatePayslip(
        $employee_id,
        $salary,
        $days_worked,
        $total_salary,
        $month
    ) {

        $query = "
            INSERT INTO payslips
            (
                employee_id,
                month,
                days_worked,
                salary,
                total_salary,
                generated_at
            )
            VALUES (?, ?, ?, ?, ?, NOW())
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "isidd",
            $employee_id,
            $month,
            $days_worked,
            $salary,
            $total_salary
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | GET PAYSLIP BY ID
    |--------------------------------------------------------------------------
    */

    public function getPayslipById($payslip_id)
    {

        $query = "
            SELECT
                id,
                employee_id,
                month,
                days_worked,
                salary,
                total_salary,
                generated_at

            FROM payslips

            WHERE id = ?

            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "i",
            $payslip_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $payslip = $result->fetch_assoc();

        $stmt->close();

        return $payslip;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL PAYSLIPS FOR EMPLOYEE
    |--------------------------------------------------------------------------
    */

    public function getPayslipsByEmployeeId($employee_id)
    {

        $query = "
            SELECT
                id,
                employee_id,
                month,
                days_worked,
                salary,
                total_salary,
                generated_at

            FROM payslips

            WHERE employee_id = ?

            ORDER BY month DESC
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param(
            "i",
            $employee_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $payslips = $result->fetch_all(
            MYSQLI_ASSOC
        );

        $stmt->close();

        return $payslips;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE PAYSLIP
    |--------------------------------------------------------------------------
    */

    public function deletePayslip($payslip_id)
    {

        $query = "
            DELETE FROM payslips
            WHERE id = ?
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "i",
            $payslip_id
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }


    /*
    |--------------------------------------------------------------------------
    | GET LATEST PAYSLIP
    |--------------------------------------------------------------------------
    */

    public function getLatestPayslip($employee_id)
    {

        $query = "
            SELECT
                id,
                employee_id,
                month,
                days_worked,
                salary,
                total_salary,
                generated_at

            FROM payslips

            WHERE employee_id = ?

            ORDER BY month DESC

            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "i",
            $employee_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $payslip = $result->fetch_assoc();

        $stmt->close();

        return $payslip;
    }
}

?>