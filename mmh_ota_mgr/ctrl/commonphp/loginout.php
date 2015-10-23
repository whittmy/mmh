<?php
@session_start();
unset($_SESSION['user']);
unset($_SESSION['admin']);
echo("<meta http-equiv=refresh content='0; url=../index.php'>");
?>