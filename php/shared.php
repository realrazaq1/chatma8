<?php
if (!session_id()) {
    session_start();
}

require 'config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
