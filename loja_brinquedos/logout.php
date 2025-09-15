<?php
session_start();
session_unset();
session_destroy();
header("Location: loja.php");
exit();
?>