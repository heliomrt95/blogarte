<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

$numMotCle = $_POST['numMotCle'];
$libMotCle = $_POST['libMotCle'];

sql_update('MOTCLE', "libMotCle = '$libMotCle'", "numMotCle = $numMotCle");

header('Location: ../../views/backend/keywords/list.php');
?>