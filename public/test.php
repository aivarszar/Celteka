<?php
echo "<h1>Public direktorijas tests</h1>";
echo "<p>Ja redzat šo ziņojumu, public direktorijs darbojas!</p>";
echo "<ul>";
echo "<li>PHP versija: " . PHP_VERSION . "</li>";
echo "<li>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'nav') . "</li>";
echo "<li>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'nav') . "</li>";
echo "<li>Config fails eksistē: " . (file_exists(dirname(__DIR__) . '/app/config/config.php') ? 'JĀ' : 'NĒ') . "</li>";
echo "</ul>";

echo "<h2>Mēģināt instalāciju:</h2>";
echo "<p><a href='../install/'>Doties uz instalāciju</a></p>";
