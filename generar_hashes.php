<?php
/**
 * Script temporal para generar hashes bcrypt
 * Úsalo una sola vez y luego elimina el archivo
 */

$contrasenas = [
    'dev123' => password_hash('dev123', PASSWORD_BCRYPT),
    'cio123' => password_hash('cio123', PASSWORD_BCRYPT),
    'cto123' => password_hash('cto123', PASSWORD_BCRYPT),
    'ciso123' => password_hash('ciso123', PASSWORD_BCRYPT),
];

echo "<div style='padding:20px; background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-family:monospace;'>";
echo "<h3>Hashes bcrypt generados:</h3>";
foreach ($contrasenas as $pass => $hash) {
    echo "<p><strong>$pass:</strong><br/><code>$hash</code></p>";
}
echo "<p style='color:red; margin-top:20px;'><strong>⚠️ Copia los hashes anteriores al SQL de phpMyAdmin y elimina este archivo después.</strong></p>";
echo "</div>";
?>
