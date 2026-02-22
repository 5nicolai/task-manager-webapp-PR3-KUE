<?php
/**
 * File: index.php
 * Description: Main script for the task management tool.
 * * Author: Nicolai Treichel
 * Matriculation Number: 1144582
 * Assignment: Komplexe Übung PR3-SU1
 * Date: 2026-02-22
 */

require 'db.php'; // Include database connection

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modul = $_POST['modul'];
    $referenz = $_POST['referenz'];
    $aufgabentext = $_POST['aufgabentext'];
    $verwendung = $_POST['verwendung'];
    
    // Generate a unique 8-character ID
    $id = substr(bin2hex(random_bytes(4)), 0, 8); 

    // Prepare and bind the INSERT statement (Status is automatically 0 due to DB default)
    $stmt = $mysqli->prepare("INSERT INTO Aufgaben (id, modul, referenz, aufgabentext, verwendung) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $id, $modul, $referenz, $aufgabentext, $verwendung);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Aufgabe erfolgreich gespeichert! ID: $id</p>";
    } else {
        $message = "<p style='color: red;'>Fehler beim Speichern: " . $mysqli->error . "</p>";
    }
    $stmt->close();
}

// Get total count of tasks
$countResult = $mysqli->query("SELECT COUNT(*) AS total FROM Aufgaben");
$countRow = $countResult->fetch_assoc();
$totalTasks = $countRow['total'];

// Fetch all tasks for the list
$tasksResult = $mysqli->query("SELECT * FROM Aufgaben ORDER BY verwendung DESC");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Aufgabe erfassen - Komponente 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Neue Aufgabe erfassen</h1>
        <?= $message ?>
        
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="modul">Modul (3 Zeichen):</label>
                <input type="text" id="modul" name="modul" maxlength="3" required>
            </div>
            
            <div class="form-group">
                <label for="referenz">Referenznummer (6 Zeichen):</label>
                <input type="text" id="referenz" name="referenz" maxlength="6" required>
            </div>
            
            <div class="form-group">
                <label for="aufgabentext">Aufgabenstellung:</label>
                <textarea id="aufgabentext" name="aufgabentext" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="verwendung">Verwendungsdatum:</label>
                <input type="date" id="verwendung" name="verwendung" required>
            </div>
            
            <button type="submit" style="padding: 10px 20px; cursor: pointer;">Speichern</button>
        </form>

        <hr style="margin: 40px 0;">

        <h2>Bisherige Aufgaben (Anzahl: <?= $totalTasks ?>)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Modul</th>
                    <th>Referenz</th>
                    <th>Aufgabentext</th>
                    <th>Datum</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tasksResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['modul']) ?></td>
                        <td><?= htmlspecialchars($row['referenz']) ?></td>
                        <td><?= htmlspecialchars($row['aufgabentext']) ?></td>
                        <td><?= htmlspecialchars($row['verwendung']) ?></td>
                        <td>
                            <?php 
                                // Map the integer status to text
                                if ($row['status'] == 0) echo "0 (offen/neu)";
                                elseif ($row['status'] == 1) echo "1 (verwendet)";
                                elseif ($row['status'] == 2) echo "2 (ersetzt)";
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>