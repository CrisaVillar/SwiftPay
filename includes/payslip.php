<?php
class Payslip {
    // Database connection and table name
    private $conn;
    private $table_name = "payslips"; // Ensure this is the correct table name

    
    public $id;
    public $employee_id;
    public $month;
    public $days_worked;
    public $salary;
    public $total_salary;
    public $generated_at;
    public $daily_rate;

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to generate (insert) a new payslip
    public function generatePayslip($employee_id, $salary, $days_worked, $total_salary) {

        $total_salary = $salary * $days_worked;
        

        $query = "INSERT INTO " . $this->table_name . " (employee_id, days_worked, salary, total_salary, generated_at) 
                  VALUES (?, ?, ?, ?, NOW())";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidd", $employee_id, $days_worked, $salary, $total_salary);
    
        return $stmt->execute();
    }
    

    // Method to get a single payslip by payslip ID
    public function getPayslipById($payslip_id) {
        // Query to get a specific payslip
        $query = "SELECT id, employee_id, days_worked, salary, total_salary, generated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind the payslip ID
        $stmt->bind_param("i", $payslip_id);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch and return the data as an associative array
        return $result->fetch_assoc();
    }

    // Method to get all payslips for a specific employee
    public function getPayslipsByEmployeeId($employee_id) {
        // Query to get all payslips for a specific employee
        $query = "SELECT id, days_worked, salary, total_salary, generated_at 
                  FROM " . $this->table_name . " 
                  WHERE employee_id = ? ORDER BY generated_at ASC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind the employee ID
        $stmt->bind_param("i", $employee_id);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}
?>
