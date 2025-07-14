<!DOCTYPE html>
<html>
<head>
    <title>SQL Interface - Crawford Young</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        textarea { width: 100%; height: 100px; }
        .result, .error, .message { margin-top: 20px; padding: 10px; border-radius: 5px; }
        .result { background-color: #f0f8ff; }
        .message { background-color: #e0ffe0; }
        .error { background-color: #ffe0e0; color: red; }
    </style>
</head>
<body>
    <h2>SQL Interface - Crawford Young</h2>
    <form method="post">
        <label for="sql">Enter SQL Statement:</label><br>
        <textarea name="sql" id="sql" required><?php echo isset($_POST['sql']) ? $_POST['sql'] : ''; ?></textarea>
        <input type="submit" value="Execute">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = isset($_POST['sql']) ? stripslashes(trim($_POST['sql'])) : '';

        // Block DROP statements
        if (stripos($sql, 'DROP') === 0) {
            echo "<div class='error'>DROP statements are not allowed.</div>";
            exit;
        }

        // Database connection
        $conn = new mysqli("sysmysql8.auburn.edu", "jcy0016", "rainstorm365", "jcy0016db");

        if ($conn->connect_error) {
            die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
        }

        if ($result = $conn->query($sql)) {
            if (stripos($sql, "SELECT") === 0) {
                // SELECT query - show results
                if ($result->num_rows > 0) {
                    echo "<div class='result'><table border='1' cellpadding='5'><tr>";
                    // Fetch column headers
                    while ($field = $result->fetch_field()) {
                        echo "<th>" . htmlspecialchars($field->name) . "</th>";
                    }
                    echo "</tr>";
                    // Fetch rows
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $data) {
                            echo "<td>" . htmlspecialchars($data) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Rows Retrieved:</strong> " . $result->num_rows . "</p></div>";
                } else {
                    echo "<div class='message'>No results found.</div>";
                }
            } else {
                // Non-SELECT statement
                if (stripos($sql, "INSERT") === 0) {
                    echo "<div class='message'>Row Inserted.</div>";
                } elseif (stripos($sql, "UPDATE") === 0) {
                    echo "<div class='message'>Row(s) Updated.</div>";
                } elseif (stripos($sql, "DELETE") === 0) {
                    echo "<div class='message'>Row(s) Deleted.</div>";
                } elseif (stripos($sql, "CREATE") === 0) {
                    echo "<div class='message'>Table Created.</div>";
                } else {
                    echo "<div class='message'>Statement Executed.</div>";
                }
            }
            $result->free();
        } else {
            echo "<div class='error'>Error: " . htmlspecialchars($conn->error) . "</div>";
        }

        $conn->close();
    }
    ?>
</body>
</html>
