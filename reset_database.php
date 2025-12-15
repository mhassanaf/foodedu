<?php
require_once 'auth/config.php';

echo "<h1>Resetting Database...</h1>";

$sqlFile = 'database.sql';

if (!file_exists($sqlFile)) {
    die("Error: File $sqlFile not found.");
}

$sql = file_get_contents($sqlFile);

try {
    // Enable exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Split SQL into individual queries to handle them better
    // This is a basic split, assumes ; is at the end of the line or query
    // For more complex SQL dumps, a better parser is needed, but this works for simple dumps
    $queries = explode(';', $sql);

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                // Summarize the action
                if (preg_match('/^CREATE TABLE IF NOT EXISTS "?(\w+)"?/i', $query, $matches)) {
                    echo "Processed table: <strong>" . htmlspecialchars($matches[1]) . "</strong><br>";
                } else if (preg_match('/^INSERT INTO "?(\w+)"?/i', $query, $matches)) {
                    echo "Inserted data into: <strong>" . htmlspecialchars($matches[1]) . "</strong><br>";
                }
            } catch (PDOException $e) {
                // Ignore "Table already exists" or similar non-critical errors if intended
                echo "<div style='color:orange'>Notice: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    echo "<h2 style='color:green'>Database Reset Complete!</h2>";
    echo "<p>The database has been restored from database.sql</p>";
    echo "<p><a href='index.html'>Go to Home</a></p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Critical Error</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>