<?php
/**
 * Email Utility for Remittance Notifications
 * Sends email notifications to employees when remittances are added
 */

function send_remittance_notification($employee_id, $remittances, $month, $year, $conn) {
    // Get employee details
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $employee = $result->fetch_assoc();
    $stmt->close();
    
    // Check if employee has email
    if (empty($employee['email'])) {
        error_log("No email address for employee ID: $employee_id");
        return false;
    }
    
    $employee_name = $employee['first_name'] . ' ' . $employee['last_name'];
    $employee_email = $employee['email'];
    
    // Build remittance details
    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    $period = "$month_name $year";
    
    $remittance_list = "";
    $total_amount = 0;
    
    foreach ($remittances as $remittance) {
        $remittance_list .= "• " . $remittance['type'] . ": ₱" . number_format($remittance['amount'], 2) . "\n";
        $total_amount += $remittance['amount'];
    }
    
    // Email subject
    $subject = "Government Remittance Notification - $period";
    
    // Email body
    $message = "Dear $employee_name,\n\n";
    $message .= "This is to notify you that the following government remittances have been recorded for the period of $period:\n\n";
    $message .= $remittance_list;
    $message .= "\nTotal Amount: ₱" . number_format($total_amount, 2) . "\n";
    $message .= "Status: Paid\n\n";
    $message .= "If you have any questions regarding these remittances, please contact the HR department.\n\n";
    $message .= "Best regards,\n";
    $message .= "8 RM Utility Projects Construction\n";
    $message .= "HR Department\n\n";
    $message .= "---\n";
    $message .= "This is an automated notification. Please do not reply to this email.";
    
    // Headers
    $headers = "From: HR Department <hr@8rm-construction.com>\r\n";
    $headers .= "Reply-To: hr@8rm-construction.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email
    $email_sent = mail($employee_email, $subject, $message, $headers);
    
    if ($email_sent) {
        error_log("Remittance notification sent to: $employee_email (Employee ID: $employee_id)");
        return true;
    } else {
        error_log("Failed to send remittance notification to: $employee_email (Employee ID: $employee_id)");
        return false;
    }
}

/**
 * Send HTML formatted email (alternative version)
 */
function send_remittance_notification_html($employee_id, $remittances, $month, $year, $conn) {
    // Get employee details
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $employee = $result->fetch_assoc();
    $stmt->close();
    
    if (empty($employee['email'])) {
        return false;
    }
    
    $employee_name = $employee['first_name'] . ' ' . $employee['last_name'];
    $employee_email = $employee['email'];
    
    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    $period = "$month_name $year";
    
    $remittance_rows = "";
    $total_amount = 0;
    
    foreach ($remittances as $remittance) {
        $remittance_rows .= "<tr>
            <td style='padding: 12px; border-bottom: 1px solid #e2e8f0;'>{$remittance['type']}</td>
            <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;'>₱" . number_format($remittance['amount'], 2) . "</td>
        </tr>";
        $total_amount += $remittance['amount'];
    }
    
    $subject = "Government Remittance Notification - $period";
    
    $html_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f5f5f5;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
            <div style='background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Government Remittance Notification</h1>
            </div>
            <div style='padding: 30px;'>
                <p style='margin: 0 0 20px;'>Dear <strong>$employee_name</strong>,</p>
                <p style='margin: 0 0 20px;'>This is to notify you that the following government remittances have been recorded for the period of <strong>$period</strong>:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #e2e8f0;'>
                    <thead>
                        <tr style='background-color: #f8fafc;'>
                            <th style='padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0;'>Remittance Type</th>
                            <th style='padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0;'>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        $remittance_rows
                        <tr style='background-color: #f8fafc; font-weight: bold;'>
                            <td style='padding: 12px;'>Total</td>
                            <td style='padding: 12px; text-align: right;'>₱" . number_format($total_amount, 2) . "</td>
                        </tr>
                    </tbody>
                </table>
                
                <div style='background-color: #dcfce7; border-left: 4px solid #22c55e; padding: 12px; margin: 20px 0;'>
                    <p style='margin: 0; color: #166534;'><strong>Status:</strong> Paid</p>
                </div>
                
                <p style='margin: 20px 0;'>If you have any questions regarding these remittances, please contact the HR department.</p>
                
                <p style='margin: 20px 0 0;'>Best regards,<br>
                <strong>8 RM Utility Projects Construction</strong><br>
                HR Department</p>
            </div>
            <div style='background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b;'>
                <p style='margin: 0;'>This is an automated notification. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "From: HR Department <hr@8rm-construction.com>\r\n";
    $headers .= "Reply-To: hr@8rm-construction.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($employee_email, $subject, $html_message, $headers);
}
?>