<?php
// Include the main header file containing database and utility functions
include '../../header.php';

// Vérifier si une demande POST a été envoyée (formulaire de suppression confirmée)
if($_POST){
    // Récupérer l'ID de l'article depuis le champ caché du formulaire
    $numArt = $_POST['numArt'];
    
    // ÉTAPE 1 : Supprimer d'abord les associations de mots-clés
    // ========================================================
    // IMPORTANT : La table MOTCLEARTICLE a une CONTRAINTE DE CLÉS ÉTRANGÈRES
    // qui référence la table ARTICLE. Il faut donc supprimer les mots-clés associés
    // AVANT de supprimer l'article lui-même, sinon on aurait une erreur de contrainte.
    // Cette approche respecte l'intégrité référentielle de la base de données.
    sql_delete('MOTCLEARTICLE', "numArt = $numArt");
    
    // ÉTAPE 2 : Supprimer ensuite l'article lui-même
    // =================================================
    // Maintenant que toutes les associations sont supprimées,
    // on peut supprimer l'enregistrement de l'article de la table ARTICLE
    sql_delete('ARTICLE', "numArt = $numArt");
    
    // REDIRECTION : Envoyer l'utilisateur vers la liste des articles
    // ==============================================================
    // Après la suppression réussie, on redirige vers la page de liste
    // ROOT_URL est défini dans header.php et contient l'URL de base du site
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    // exit() arrête l'exécution du script après la redirection
    exit();
}
?>
