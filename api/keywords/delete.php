<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

$numMotCle = $_POST['numMotCle'];

// Vérification CIR : on vérifie si des articles utilisent ce mot-clé
$relations = sql_select("MOTCLEARTICLE", "COUNT(*) as nb", "numMotCle = $numMotCle");
$nbRelations = $relations[0]['nb'];

if($nbRelations > 0){
    // Il existe des articles avec ce mot-clé, suppression impossible
    echo "<script>alert('Suppression impossible : $nbRelations article(s) utilisent ce mot-clé.'); window.location.href='../../views/backend/keywords/list.php';</script>";
    exit;
} else {
    // Aucun article n'utilise ce mot-clé, on peut supprimer
    sql_delete('MOTCLE', "numMotCle = $numMotCle");
    header('Location: ../../views/backend/keywords/list.php');
}
?>