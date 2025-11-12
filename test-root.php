<?php
echo "<h1>Root direktorija tests</h1>";
echo "<p>Ja redzat šo ziņojumu, root direktorijs darbojas!</p>";
echo "<ul>";
echo "<li>PHP versija: " . PHP_VERSION . "</li>";
echo "<li>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'nav') . "</li>";
echo "<li>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'nav') . "</li>";
echo "<li>DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'nav') . "</li>";
echo "<li>SCRIPT_FILENAME: " . (__FILE__) . "</li>";
echo "</ul>";

echo "<h2>Direktoriju struktūra:</h2>";
echo "<ul>";
echo "<li>Install direktorijs eksistē: " . (is_dir(__DIR__ . '/install') ? 'JĀ' : 'NĒ') . "</li>";
echo "<li>Public direktorijs eksistē: " . (is_dir(__DIR__ . '/public') ? 'JĀ' : 'NĒ') . "</li>";
echo "<li>App direktorijs eksistē: " . (is_dir(__DIR__ . '/app') ? 'JĀ' : 'NĒ') . "</li>";
echo "<li>Database direktorijs eksistē: " . (is_dir(__DIR__ . '/database') ? 'JĀ' : 'NĒ') . "</li>";
echo "</ul>";

echo "<h2>Linki testēšanai:</h2>";
echo "<ul>";
echo "<li><a href='/install/test.php'>Install test.php</a></li>";
echo "<li><a href='/install/'>Install index.php</a></li>";
echo "<li><a href='/public/'>Public index.php</a></li>";
echo "</ul>";
