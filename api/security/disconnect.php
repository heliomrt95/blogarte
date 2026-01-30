<?php
// Include the main header file containing database and utility functions
include '../../header.php';

// ========== ÉTAPE 1 : DESTRUCTION DE LA SESSION ==========
// ========================================================
// Supprimer toutes les données stockées dans $_SESSION
// Cela inclut les informations de l'utilisateur, les tokens d'authentification, etc.

session_destroy();
// session_destroy() ferme la session courante et supprime les données associées
// Après l'appel de session_destroy(), on ne peut plus accéder à $_SESSION

// ========== ÉTAPE 2 : SUPPRESSION DES COOKIES D'AUTHENTIFICATION ==========
// ======================================================================
// Si des cookies d'authentification ont été créés (par exemple "remember_token"),
// les supprimer en les expirant immédiatement

// Supprimer le cookie "remember_token" s'il existe
// Pour supprimer un cookie, on le recrée avec une date d'expiration dans le passé
if(isset($_COOKIE['remember_token'])){
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    // time() - 3600 : date d'expiration 1 heure dans le passé
    // Cela indique au navigateur de supprimer le cookie
}

// ========== ÉTAPE 3 : REDIRECTION VERS LA PAGE D'ACCUEIL ==========
// ================================================================
// Après déconnexion, rediriger l'utilisateur vers la page d'accueil
// et afficher un message de confirmation

header('Location: ' . ROOT_URL . '/index.php?success=logout_ok');
exit();
?>
