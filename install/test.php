<?php
echo "Install direktorijs darbojas!<br>";
echo "PHP versija: " . PHP_VERSION . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'nav') . "<br>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'nav') . "<br>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'nav') . "<br>";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'nav') . "<br>";
phpinfo();
