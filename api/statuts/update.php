<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

$numStat = $_POST['numStat'];
$libStat = $_POST['libStat'];

// Update avec la syntaxe : sql_update(table, "colonne1='valeur1', colonne2='valeur2'", "condition")
sql_update('STATUT', "libStat = '$libStat'", "numStat = $numStat");

header('Location: ../../views/backend/statuts/list.php');
?>