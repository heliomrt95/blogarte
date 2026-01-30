<?php
// Include the main header file containing database and utility functions
include '../../header.php';

// Vérifier si une demande POST a été envoyée (formulaire de connexion soumis)
if($_POST){
    
    // ========== ÉTAPE 1 : RÉCUPÉRATION DES DONNÉES DU FORMULAIRE ==========
    // =====================================================================
    // Récupérer les données du formulaire de connexion depuis /views/backend/security/login.php
    
    $pseudoMemb = trim($_POST['pseudoMemb']);         // Pseudo de l'utilisateur (6+ caractères)
    $passwordMemb = $_POST['passwordMemb'];           // Mot de passe fourni par l'utilisateur (8-15 caractères)
    
    // ========== ÉTAPE 2 : VALIDATION BASIQUE ==========
    // =================================================
    
    // VALIDATION 1 : Vérifier que le pseudo respecte les critères minimums
    // ==================================================================
    if(strlen($pseudoMemb) < 6){
        // Le pseudo doit avoir au minimum 6 caractères
        header('Location: ' . ROOT_URL . '/views/backend/security/login.php?error=invalid_pseudo');
        exit();
    }
    
    // VALIDATION 2 : Vérifier que le mot de passe respecte les critères minimums
    // =========================================================================
    if(strlen($passwordMemb) < 8 || strlen($passwordMemb) > 15){
        // Le mot de passe doit être entre 8 et 15 caractères
        header('Location: ' . ROOT_URL . '/views/backend/security/login.php?error=invalid_password');
        exit();
    }
    
    // ========== ÉTAPE 3 : RECHERCHE DE L'UTILISATEUR ==========
    // ==========================================================
    // Chercher l'utilisateur dans la table MEMBRE par son pseudo
    // addslashes() pour échapper les caractères spéciaux dans la clause WHERE
    
    $user = sql_select("MEMBRE", "*", "pseudoMemb = '" . addslashes($pseudoMemb) . "'");
    
    // ========== ÉTAPE 4 : VÉRIFICATION DE L'UTILISATEUR ==========
    // ============================================================
    if(empty($user)){
        // Aucun utilisateur trouvé avec ce pseudo
        // Rediriger vers le formulaire de login avec message d'erreur
        header('Location: ' . ROOT_URL . '/views/backend/security/login.php?error=user_not_found');
        exit();
    }
    
    // Récupérer le premier (et seul) résultat
    $user = $user[0];
    
    // ========== ÉTAPE 5 : VÉRIFICATION DU MOT DE PASSE ==========
    // ===========================================================
    // IMPORTANT SÉCURITÉ : Utiliser password_verify() pour comparer le mot de passe saisi
    // avec le hash stocké en base de données
    //
    // password_verify($input, $hash) :
    // - Prend le mot de passe en clair fourni par l'utilisateur ($passwordMemb)
    // - Le compare avec le hash bcrypt stocké en BDD ($user['passwordMemb'])
    // - Retourne TRUE si ils correspondent, FALSE sinon
    // - Même si quelqu'un obtient le hash, il ne peut pas le "inverser" pour obtenir le mot de passe
    
    if(!password_verify($passwordMemb, $user['passwordMemb'])){
        // Le mot de passe ne correspond pas
        // Rediriger vers le formulaire de login avec message d'erreur
        header('Location: ' . ROOT_URL . '/views/backend/security/login.php?error=invalid_credentials');
        exit();
    }
    
    // ========== ÉTAPE 6 : GESTION DE LA SESSION ==========
    // ===================================================
    // Le mot de passe est correct, créer une session pour l'utilisateur
    // Les données de session seront stockées dans $_SESSION et persisteront entre les pages
    
    // Stocker les informations de l'utilisateur dans la session
    $_SESSION['user_id'] = $user['numMemb'];           // ID unique de l'utilisateur
    $_SESSION['pseudoMemb'] = $user['pseudoMemb'];     // Pseudo (pour afficher dans la nav)
    $_SESSION['eMailMemb'] = $user['eMailMemb'];       // Email
    $_SESSION['prenomMemb'] = $user['prenomMemb'];     // Prénom
    $_SESSION['nomMemb'] = $user['nomMemb'];           // Nom
    $_SESSION['numStat'] = $user['numStat'] ?? 2;      // Statut (admin, modo, user, etc.)
    $_SESSION['login_time'] = time();                  // Timestamp de connexion
    $_SESSION['authenticated'] = true;                 // Flag d'authentification
    
    // ========== ÉTAPE 7 : REDIRECTION VERS LE DASHBOARD/ACCUEIL ==========
    // ==================================================================
    // L'authentification a réussi, rediriger vers le dashboard ou la page d'accueil
    // En fonction du statut de l'utilisateur, on pourrait rediriger vers des pages différentes
    
    // Pour simplifier : rediriger toujours vers le dashboard
    header('Location: ' . ROOT_URL . '/views/backend/dashboard.php?success=login_ok');
    exit();
}
?>
