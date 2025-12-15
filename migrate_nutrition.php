<?php
require_once 'auth/config.php';

try {
    echo "Starting migration...<br>";

    // Add columns to gizi_items if they don't exist
    $columns = [
        "description TEXT NULL",
        "portion_size VARCHAR(50) NULL DEFAULT '100 gram'",
        "energy VARCHAR(50) NULL",
        "fat VARCHAR(50) NULL",
        "protein VARCHAR(50) NULL",
        "carbs VARCHAR(50) NULL",
        "sodium VARCHAR(50) NULL",
        "calcium VARCHAR(50) NULL"
    ];

    foreach ($columns as $col) {
        try {
            // Attempt to add column. ignore error if exists (simple approach)
            // Or cleaner: check information_schema, but direct ALTER IGNORE isn't standard in PDO.
            // We'll wrap each in try-catch.
            $colName = explode(' ', $col)[0];
            $pdo->exec("ALTER TABLE gizi_items ADD COLUMN $col");
            echo "Added column: $colName <br>";
        } catch (PDOException $e) {
            // Column likely exists
            echo "Skipped (likely exists): " . explode(' ', $col)[0] . "<br>";
        }
    }

    echo "Migration completed successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>