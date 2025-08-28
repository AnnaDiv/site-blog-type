<?php
    $target_dir = __DIR__ . '/../post/random';

    if (is_dir($target_dir)) {
        $files = glob($target_dir . '/*');
        foreach ($files as $file) {
            is_dir($file) ? rmdir($file) : unlink($file);
        }
        echo "Folder contents deleted successfully.\n";
    } 
    else {
        echo "Error: Target directory not found.\n";
    }
?>