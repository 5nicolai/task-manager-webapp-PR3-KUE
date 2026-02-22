<?php
/**
 * File: db.php
 * Description: Establishes the database connection using mysqli.
 * * Author: Nicolai Treichel
 * Matriculation Number: 1144582
 * Assignment: Komplexe Übung PR3-SU1
 * Date: 2026-02-22
 */

$host = "localhost";
$user = "root";     // Default XAMPP username
$password = "";     // Default XAMPP password is empty
$db = "task_management";

// Create connection
$mysqli = new mysqli($host, $user, $password, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set charset to ensure special characters (like German Umlaute) display correctly
$mysqli->set_charset("utf8mb4");
?>