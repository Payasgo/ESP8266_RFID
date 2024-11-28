<?php
    // Get the UIDresult from POST and sanitize it
    $UIDresult = isset($_POST["UIDresult"]) ? htmlspecialchars($_POST["UIDresult"], ENT_QUOTES, 'UTF-8') : '';

    // Create the PHP content to be written into the file
    $Write = "<?php\n" . 
             "\$UIDresult = '" . addslashes($UIDresult) . "';\n" . 
             "echo \$UIDresult;\n" . 
             "?>";

    // Write the content to UIDContainer.php
    if (file_put_contents('UIDContainer.php', $Write) !== false) {
        echo "UIDContainer.php updated successfully.";
    } else {
        echo "Failed to update UIDContainer.php.";
    }
?>
