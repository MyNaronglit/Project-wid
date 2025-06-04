<?php
session_start();
session_unset();
session_destroy();
header("Location: /wdi/www.wdi.co.th/th/login.php");
exit;
?>
