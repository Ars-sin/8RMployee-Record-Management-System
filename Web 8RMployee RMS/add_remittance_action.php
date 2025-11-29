<?php
require_once 'check_auth_core.php';
require_once 'logger.php';
require_once 'email_utility.php'; // Include email utility

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if we have the required fields
    if (isset($_POST['month_covered'], $_POST['year_covered'], $_POST['employee_id'])) {
        
        $month_covered = $_POST['month_covered'];
        $year_covered = $_POST['year_covered'];
        $employee_ids = $_POST['employee_id']; // This is now an array

        // Validate that employee_ids is an array
        if (!is_array($employee_ids) || empty($employee_ids)) {
            header("Location: government_beneficiary.php?error=InvalidData");
            exit();
        }

        $conn = new mysqli("localhost", "u987478351_ruth", "Qwertyuiop143!", "u987478351_8rm_admin");
        if ($conn->connect_error) {
            die("Connection Failed: " . $conn->connect_error);
        }

        // Start transaction to ensure all inserts succeed or fail together
        $conn->begin_transaction();
        
        try {
            $total_added = 0;
            $total_amount = 0;
            $employees_processed = 0;
            $log_details = [];
            $emails_sent = 0;
            $emails_failed = 0;

            // Prepare the insert statement once
            $insert_sql = "INSERT INTO remittances (employee_id, remittance_type, remittance_month, remittance_year, amount, status) VALUES (?, ?, ?, ?, ?, 'Paid')";
            $stmt_insert = $conn->prepare($insert_sql);

            // Loop through each employee
            foreach ($employee_ids as $emp_index => $employee_id) {
                
                // Skip if no employee ID
                if (empty($employee_id)) {
                    continue;
                }

                // Get remittance types and amounts for this employee
                $remittance_types = $_POST["remittance_type_{$emp_index}"] ?? [];
                $amounts = $_POST["amount_{$emp_index}"] ?? [];

                // Validate arrays exist and have same length
                if (!is_array($remittance_types) || !is_array($amounts) || count($remittance_types) !== count($amounts)) {
                    continue;
                }

                $employee_remittances = 0;
                $employee_total = 0;
                $employee_remittance_details = []; // Store remittances for email

                // Loop through each remittance for this employee
                foreach ($remittance_types as $index => $type) {
                    $amount = $amounts[$index];

                    // Validate data
                    if (!is_numeric($amount) || empty($type) || $amount <= 0) {
                        continue;
                    }

                    // Insert the remittance
                    $stmt_insert->bind_param("isiid", $employee_id, $type, $month_covered, $year_covered, $amount);
                    
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Database insert failed: " . $stmt_insert->error);
                    }
                    
                    $employee_remittances++;
                    $employee_total += $amount;
                    $total_added++;
                    $total_amount += $amount;
                    
                    // Store for email
                    $employee_remittance_details[] = [
                        'type' => $type,
                        'amount' => $amount
                    ];
                }

                // If this employee had remittances added, fetch their name for logging and send email
                if ($employee_remittances > 0) {
                    $employees_processed++;
                    
                    $name_stmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) as name FROM employee WHERE id = ?");
                    $name_stmt->bind_param("i", $employee_id);
                    if ($name_stmt->execute()) {
                        $result = $name_stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            $log_details[] = $row['name'] . " (" . $employee_remittances . " remittance(s), ₱" . number_format($employee_total, 2) . ")";
                        }
                    }
                    $name_stmt->close();
                    
                    // Send email notification to the employee
                    // Using HTML version for better formatting
                    $email_sent = send_remittance_notification_html(
                        $employee_id, 
                        $employee_remittance_details, 
                        $month_covered, 
                        $year_covered, 
                        $conn
                    );
                    
                    if ($email_sent) {
                        $emails_sent++;
                    } else {
                        $emails_failed++;
                    }
                }
            }

            $stmt_insert->close();

            // If we get here, all inserts were successful, so commit them
            $conn->commit();

            // Log the action if something was actually added
            if ($total_added > 0) {
                $month_name = date('F', mktime(0, 0, 0, $month_covered, 1));
                $log_message = "Added {$total_added} remittance(s) for {$employees_processed} employee(s) for the period of {$month_name} {$year_covered}, totaling ₱" . number_format($total_amount, 2);
                
                if (!empty($log_details)) {
                    $log_message .= " - Details: " . implode("; ", $log_details);
                }
                
                // Add email notification info to log
                if ($emails_sent > 0 || $emails_failed > 0) {
                    $log_message .= " | Email notifications: {$emails_sent} sent, {$emails_failed} failed";
                }
                
                log_action('Add Remittance', $log_message);
            }

            $conn->close();

            // Get the first remittance type for redirect (or default to Pag-IBIG)
            $redirect_type = 'Pag-IBIG';
            if (isset($_POST['remittance_type_0']) && is_array($_POST['remittance_type_0']) && !empty($_POST['remittance_type_0'][0])) {
                $redirect_type = $_POST['remittance_type_0'][0];
            }

            // Redirect back to the page with success message
            $redirect_url = "Location: government_beneficiary.php?type=" . urlencode($redirect_type) . 
                          "&month=$month_covered&year=$year_covered&success=true&added=$total_added&employees=$employees_processed";
            
            // Add email notification status to redirect
            if ($emails_sent > 0) {
                $redirect_url .= "&emails_sent=$emails_sent";
            }
            if ($emails_failed > 0) {
                $redirect_url .= "&emails_failed=$emails_failed";
            }
            
            header($redirect_url);
            exit();

        } catch (Exception $e) {
            // If any error occurred, roll everything back
            $conn->rollback();
            $conn->close();
            // Redirect with error
            header("Location: government_beneficiary.php?error=DatabaseError&message=" . urlencode($e->getMessage()));
            exit();
        }

    } else {
        header("Location: government_beneficiary.php?error=MissingData");
        exit();
    }
} else {
    header("Location: government_beneficiary.php");
    exit();
}
?>