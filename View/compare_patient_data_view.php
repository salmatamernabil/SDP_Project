
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Import</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .navbar .logo {
            color: #3366ff;
            font-size: 2em;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.1);
        }

        /* Content */
        .content {
            padding-top: 100px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Container Styling */
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px 0; /* Adds spacing between the containers */
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .container h1 {
            color: #3366ff;
            margin-bottom: 20px;
        }

        .file-upload {
            margin: 20px 0;
        }

        .file-upload input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #3366ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #254eda;
            transform: scale(1.02);
        }

        .success {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <div class="content">
        <div class="container">
            <h1>Import File</h1>
            <form action="../Controller/compare_patient_data_controller.php" method="POST" enctype="multipart/form-data">
                <div class="file-upload">
                    <input type="file" name="importFile" required>
                </div>
                <button type="submit" name="action" value="importFile">Upload File</button>
            </form>
        </div>

    <div class="container">
    <h1>Parsed File Content</h1>

    <!-- Display Success or Error Messages -->
    <?php if (isset($_SESSION['uploadSuccess'])): ?>
        <p class="success"><?php echo htmlspecialchars($_SESSION['uploadSuccess']); ?></p>
    <?php elseif (isset($_SESSION['uploadError'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['uploadError']); ?></p>
    <?php endif; ?>

    <!-- Display Parsed Content -->
    <?php
$parsedContent = $_SESSION['parsedContent'] ?? '';
if (!empty($parsedContent)):
?>
    <table id="parsedTable" style="border: 1px solid #ddd; margin: 20px auto; width: 50%; border-collapse: collapse;">
        <thead>
        
        </thead>
        <tbody>
            <?php
            $lines = explode("\n", $parsedContent);
            foreach ($lines as $line):
                if (strpos($line, ':') !== false):
                    list($key, $value) = explode(':', $line, 2);
            ?>
                    <tr>
                        <th style="text-align: left; padding: 8px; background-color: #f9f9f9;">
                            <?php echo htmlspecialchars(trim($key)); ?>
                        </th>
                        <td style="padding: 8px;"><?php echo htmlspecialchars(trim($value)); ?></td>
                    </tr>
            <?php
                endif;
            endforeach;
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align: center;">No parsed content available.</p>
<?php endif; ?>
    <!-- Clear Data Button -->
    <form action="../Controller/compare_patient_data_controller.php?action=clear" method="GET">
        <button type="submit" name="action" value="clear">Clear Data</button>
    </form>

</div>

<!-- Display Follow-Up Data -->
<div class="container">
    <h1>Patient Follow-Up Data</h1>
    <?php
    $followUpData = $_SESSION['followUpData'] ?? []; // Fetch data from session
    if (!empty($followUpData) && is_array($followUpData)): // Ensure it's an array
    ?>
        <table id="followUpTable" style="border: 1px solid #ddd; margin: 20px auto; width: 50%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="background-color: #f4f4f4; padding: 10px;">Field</th>
                    <th style="background-color: #f4f4f4; padding: 10px;">Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($followUpData as $key => $value): ?>
                    <tr>
                        <th style="text-align: left; padding: 8px; background-color: #f9f9f9;">
                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?>
                        </th>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">No follow-up data available.</p>
    <?php endif; ?>
</div>
<div>
   
<!-- Compare Button -->
<div style="text-align: center;">
    <button id="compareButton" style="margin: 20px; padding: 10px 20px; background-color: #3366ff; color: white; border: none; border-radius: 5px; cursor: pointer;">Compare</button>
</div>

<script>
document.getElementById('compareButton').addEventListener('click', function () {
    const normalizeField = (field) =>
        field.toLowerCase().replace(/\s+/g, '_').trim();

    // Debugging: Check if parsed table exists
    const parsedTable = document.getElementById('parsedTable');
    if (!parsedTable) {
        console.error('Parsed table not found!');
        return;
    }

    const parsedRows = parsedTable.querySelectorAll('tbody tr');
    const followUpRows = document.querySelectorAll('#followUpTable tbody tr');

    console.log("Parsed Rows:", parsedRows.length);
    console.log("Follow-Up Rows:", followUpRows.length);

    // Build a map of Field -> Value for parsedTable
    const parsedMap = {};
    parsedRows.forEach(row => {
        const field = normalizeField(row.cells[0]?.innerText);
        const value = row.cells[1]?.innerText.trim();
        console.log('Parsed Row:', field, value); // Debugging log
        if (field) {
            parsedMap[field] = value;
        }
    });

    // Compare followUpTable rows against parsedTable map
    followUpRows.forEach(row => {
        const field = normalizeField(row.cells[0]?.innerText);
        const value = row.cells[1]?.innerText.trim();
        console.log('Follow-Up Row:', field, value); // Debugging log

        if (parsedMap.hasOwnProperty(field)) {
            if (parsedMap[field] === value) {
                // Highlight matching rows in green
                row.style.backgroundColor = '#CCFFCC';
                const matchingParsedRow = Array.from(parsedRows).find(r =>
                    normalizeField(r.cells[0]?.innerText) === field
                );
                if (matchingParsedRow) {
                    matchingParsedRow.style.backgroundColor = '#CCFFCC';
                }
            } else {
                // Highlight mismatched rows in red
                row.style.backgroundColor = '#FFCCCC';
                const mismatchedParsedRow = Array.from(parsedRows).find(r =>
                    normalizeField(r.cells[0]?.innerText) === field
                );
                if (mismatchedParsedRow) {
                    mismatchedParsedRow.style.backgroundColor = '#FFCCCC';
                }
            }
        } else {
            // Highlight missing fields in yellow
            console.log('Field not found in parsed table:', field);
            row.style.backgroundColor = '#FFD700';
        }
    });

    // Highlight rows in parsedTable that are not in followUpTable
    parsedRows.forEach(row => {
        const field = normalizeField(row.cells[0]?.innerText);
        if (!Array.from(followUpRows).some(r =>
            normalizeField(r.cells[0]?.innerText) === field
        )) {
            console.log('Field not found in follow-up table:', field);
            row.style.backgroundColor = '#FFD700';
        }
    });
});
    </script>
</body>
</html>
