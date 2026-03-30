
<?php
echo "<h2>✅ SERVER & PHP WORKING</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Files in current dir:<br><pre>";
print_r(scandir('.'));
echo "</pre>";
?>

