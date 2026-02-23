<?php
/**
 * File: manage_tasks.php
 * Description: Component 2 - Task management, filtering, and dynamic status updates.
 * Author: Nicolai Treichel
 * Matriculation Number: 1144582
 * Assignment: Komplexe Übung PR3-SU1
 * Date: 2026-02-23
 */

require 'db.php';

// Fetch all distinct modules for the filter dropdown
$modulesResult = $mysqli->query("SELECT DISTINCT modul FROM Aufgaben ORDER BY modul ASC");

// Fetch all tasks
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

        <div style="margin-bottom: 20px; padding: 15px; background: #e9e9e9; border-radius: 4px;">
            <strong>Filter:</strong>
            
            <select id="filterModul" style="margin-left: 10px; padding: 5px;">
                <option value="all">Alle Module anzeigen</option>
                <?php while ($modulRow = $modulesResult->fetch_assoc()): ?>
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
                <?php while ($row = $tasksResult->fetch_assoc()): ?>
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

    <script>
        // --- 1. Filter Logic (Frontend only) ---
        const filterModul = document.getElementById('filterModul');
        const hideUsed = document.getElementById('hideUsed');
        const hideReplaced = document.getElementById('hideReplaced');
        const taskRows = document.querySelectorAll('.task-row');

        function applyFilters() {
            const selectedModul = filterModul.value;
            const isHideUsedChecked = hideUsed.checked;
            const isHideReplacedChecked = hideReplaced.checked;

            taskRows.forEach(row => {
                const rowModul = row.getAttribute('data-modul');
                const rowStatus = row.getAttribute('data-status');
                
                let showRow = true;

                // Check module filter
                if (selectedModul !== 'all' && rowModul !== selectedModul) showRow = false;
                
                // Check status filters
                if (isHideUsedChecked && rowStatus === '1') showRow = false;
                if (isHideReplacedChecked && rowStatus === '2') showRow = false;

                // Apply CSS display property
                row.style.display = showRow ? '' : 'none';
            });
        }

        // Attach event listeners to filter inputs
        filterModul.addEventListener('change', applyFilters);
        hideUsed.addEventListener('change', applyFilters);
        hideReplaced.addEventListener('change', applyFilters);


        // --- 2. Status Update Logic (AJAX / Fetch API) ---
        const statusDropdowns = document.querySelectorAll('.status-dropdown');

        statusDropdowns.forEach(dropdown => {
            dropdown.addEventListener('change', function() {
                const taskId = this.getAttribute('data-id');
                const newStatus = this.value;
                const row = this.closest('tr');

                // Send data to backend script
                fetch('update_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${taskId}&status=${newStatus}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        // Update the data-attribute so filters keep working correctly
                        row.setAttribute('data-status', newStatus);
                        applyFilters(); 
                        
                        // Brief green highlight to indicate success
                        this.style.backgroundColor = '#d4edda';
                        setTimeout(() => { this.style.backgroundColor = ''; }, 1000);
                    } else {
                        alert('Fehler beim Aktualisieren: ' + data);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>