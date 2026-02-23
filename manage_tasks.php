<?php
/**
 * File: manage_tasks.php
 * Description: Component 2 - Task management, filtering, and dynamic status updates.
 * Author: Nicolai Treichel
 * Matriculation Number: 1144582
 * Assignment: Komplexe Übung PR3-SU1
 * Submission Date: 28.02.2026
 */

require 'db.php';

// Fetch all distinct modules from the database to populate the filter dropdown dynamically
$modulesResult = $mysqli->query("SELECT DISTINCT modul FROM Aufgaben ORDER BY modul ASC");

// Fetch all tasks from the database, ordered by their usage date (newest first)
$tasksResult = $mysqli->query("SELECT * FROM Aufgaben ORDER BY verwendung DESC");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Aufgaben verwalten - Komponente 2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Aufgaben verwalten</h1>

        <div id="loginSection" style="margin-bottom: 20px;">
            <label for="passwordInput">Bitte Passwort eingeben:</label>
            <input type="password" id="passwordInput" placeholder="Passwort">
            <button id="unlockButton" style="padding: 5px 10px;">Entsperren</button>
            <p id="errorMsg" style="color: red; display: none; margin-top: 10px;">Falsches Passwort!</p>
        </div>

        <div id="mainContent" style="display: none;">
            
            <div style="margin-bottom: 20px; padding: 15px; background: #e9e9e9; border-radius: 4px;">
                <strong>Filter:</strong>
                
                <select id="filterModul" style="margin-left: 10px; padding: 5px;">
                    <option value="all">Alle Module anzeigen</option>
                    <?php 
                    // Reset the result pointer in case the query was already used
                    $modulesResult->data_seek(0);
                    // Loop through each distinct module and create an option tag
                    while ($modulRow = $modulesResult->fetch_assoc()): 
                    ?>
                        <option value="<?= htmlspecialchars($modulRow['modul']) ?>">
                            <?= htmlspecialchars($modulRow['modul']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label style="margin-left: 20px;">
                    <input type="checkbox" id="hideUsed"> Verwendete ausblenden (Status 1)
                </label>
                
                <label style="margin-left: 10px;">
                    <input type="checkbox" id="hideReplaced"> Ersetzte ausblenden (Status 2)
                </label>
            </div>

            <table id="taskTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modul</th>
                        <th>Referenz</th>
                        <th>Aufgabentext</th>
                        <th>Datum</th>
                        <th>Status ändern</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset the result pointer for the tasks
                    $tasksResult->data_seek(0);
                    // Loop through all tasks and generate a table row for each
                    while ($row = $tasksResult->fetch_assoc()): 
                    ?>
                        <tr class="task-row" data-modul="<?= htmlspecialchars($row['modul']) ?>" data-status="<?= $row['status'] ?>">
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['modul']) ?></td>
                            <td><?= htmlspecialchars($row['referenz']) ?></td>
                            <td><?= htmlspecialchars($row['aufgabentext']) ?></td>
                            <td><?= htmlspecialchars($row['verwendung']) ?></td>
                            <td>
                                <select class="status-dropdown" data-id="<?= htmlspecialchars($row['id']) ?>">
                                    <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>0 (neu/offen)</option>
                                    <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>1 (verwendet)</option>
                                    <option value="2" <?= $row['status'] == 2 ? 'selected' : '' ?>>2 (ersetzt)</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // --- 1. Password Verification Logic ---
        const loginSection = document.getElementById('loginSection');
        const mainContent = document.getElementById('mainContent');
        const passwordInput = document.getElementById('passwordInput');
        const unlockButton = document.getElementById('unlockButton');
        const errorMsg = document.getElementById('errorMsg');

        // Function to check if the entered password is correct
        function checkPassword() {
            if (passwordInput.value === '123') {
                // Hide login section and show the main content table
                loginSection.style.display = 'none';
                mainContent.style.display = 'block';
            } else {
                // Show error message if password is wrong
                errorMsg.style.display = 'block';
            }
        }

        // Trigger password check when clicking the button or pressing "Enter"
        unlockButton.addEventListener('click', checkPassword);
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') checkPassword();
        });

        // --- 2. Frontend Filtering Logic ---
        const filterModul = document.getElementById('filterModul');
        const hideUsed = document.getElementById('hideUsed');
        const hideReplaced = document.getElementById('hideReplaced');
        const taskRows = document.querySelectorAll('.task-row');

        // Function to evaluate which rows should be visible based on filter settings
        function applyFilters() {
            const selectedModul = filterModul.value;
            const isHideUsedChecked = hideUsed.checked;
            const isHideReplacedChecked = hideReplaced.checked;

            // Iterate through every single row in the table
            taskRows.forEach(row => {
                // Get the module and status of the current row from its data-attributes
                const rowModul = row.getAttribute('data-modul');
                const rowStatus = row.getAttribute('data-status');
                
                let showRow = true; // Assume row is visible by default

                // Condition 1: If a specific module is selected and doesn't match the row's module
                if (selectedModul !== 'all' && rowModul !== selectedModul) showRow = false;
                
                // Condition 2: If "hide used" is checked and the row status is '1'
                if (isHideUsedChecked && rowStatus === '1') showRow = false;
                
                // Condition 3: If "hide replaced" is checked and the row status is '2'
                if (isHideReplacedChecked && rowStatus === '2') showRow = false;

                // Apply the result by changing the CSS display property
                row.style.display = showRow ? '' : 'none';
            });
        }

        // Attach the applyFilters function to the change events of the inputs
        filterModul.addEventListener('change', applyFilters);
        hideUsed.addEventListener('change', applyFilters);
        hideReplaced.addEventListener('change', applyFilters);

        // --- 3. AJAX Status Update Logic (Fetch API) ---
        const statusDropdowns = document.querySelectorAll('.status-dropdown');

        // Add an event listener to every status dropdown in the table
        statusDropdowns.forEach(dropdown => {
            dropdown.addEventListener('change', function() {
                // Get the task ID and the newly selected status value
                const taskId = this.getAttribute('data-id');
                const newStatus = this.value;
                const row = this.closest('tr'); // Find the parent row <tr> of the dropdown

                // Send a POST request to the backend script in the background
                fetch('update_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${taskId}&status=${newStatus}` // Data payload sent to PHP
                })
                .then(response => response.text()) // Convert response to plain text
                .then(data => {
                    // If the PHP script successfully updated the database
                    if (data === 'success') {
                        // Update the data-attribute of the HTML row to match the new database state
                        row.setAttribute('data-status', newStatus);
                        
                        // Re-apply filters in case the new status means the row should now be hidden
                        applyFilters(); 
                        
                        // Briefly flash the dropdown green to give the user visual feedback
                        this.style.backgroundColor = '#d4edda';
                        setTimeout(() => { this.style.backgroundColor = ''; }, 1000);
                    } else {
                        // Display error if backend failed
                        alert('Fehler beim Aktualisieren: ' + data);
                    }
                })
                .catch(error => console.error('Error:', error)); // Log network errors to the browser console
            });
        });
    </script>
</body>
</html>