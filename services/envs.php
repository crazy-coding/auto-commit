<?php
// Read the .env file
$env_file = __DIR__."/../.env";
$env_data = file_get_contents($env_file);

// Parse the .env data to extract the environment variables
$env_variables = [];
foreach (explode("\n", $env_data) as $line) {
    $line = trim($line);
    if (!empty($line) && strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $env_variables[$key] = $value;
    }
}
?>
