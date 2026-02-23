<?php
/**
 * File: update_status.php
 * Description: Backend endpoint to process asynchronous status updates via Fetch API.
 * Author: Nicolai Treichel
 * Matriculation Number: 1144582
 * Assignment: Komplexe Übung PR3-SU1
 * Date: 2026-02-23
 */

require 'db.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve data sent from JavaScript
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    // Validate inputs
    if (!empty($id) && $status !== '') {
        
        // Prepare UPDATE statement to prevent SQL injection
        $stmt = $mysqli->prepare("UPDATE Aufgaben SET status = ? WHERE id = ?");
        $stmt->bind_param("is", $status, $id);

        // Execute and return success or error
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'invalid input';
    }
}
?>