<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

$numThem = $_POST['numThem'];

// Vérification CIR : on vérifie si des articles utilisent cette thématique
$articles = sql_select("ARTICLE", "COUNT(*) as nb", "numThem = $numThem");
$nbArticles = $articles[0]['nb'];

if($nbArticles > 0){
    // Il existe des articles avec cette thématique, suppression impossible
    echo "<script>alert('Suppression impossible : $nbArticles article(s) utilisent cette thématique.'); window.location.href='../../views/backend/thematiques/list.php';</script>";
    exit;
} else {
    // Aucun article n'utilise cette thématique, on peut supprimer
    sql_delete('THEMATIQUE', "numThem = $numThem");
    header('Location: ../../views/backend/thematiques/list.php');
}
?>