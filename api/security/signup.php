<?php
// Include the main header file containing database and utility functions
include '../../header.php';

// Vérifier si une demande POST a été envoyée (formulaire de connexion soumis)
if($_POST){

    // Récupérer les données du formulaire de connexion depuis /views/backend/security/login.php
    
    $identifiant = trim($_POST['identifiant']);         // Pseudo ou email de l'utilisateur
    $passwordMemb = $_POST['passwordMemb'];             // Mot de passe fourni par l'utilisateur
    $rememberMe = isset($_POST['rememberMe']) ? true : false; // Case "Se souvenir de moi"

    // Chercher l'utilisateur dans la table MEMBRE en utilisant soit le pseudo soit l'email
    // L'utilisateur peut se connecter avec l'une ou l'autre de ces informations
    
    // D'abord, essayer de trouver par pseudo
    // addslashes() pour échapper les caractères spéciaux dans la clause WHERE
    $user = sql_select("MEMBRE", "*", "pseudoMemb = '" . addslashes($identifiant) . "'");
    
    // Si pas trouvé par pseudo, essayer par email
    if(empty($user)){
        $user = sql_select("MEMBRE", "*", "eMailMemb = '" . addslashes($identifiant) . "'");
    }

    if(empty($user)){
        // Aucun utilisateur trouvé avec ce pseudo ou cet email
        // Rediriger vers le formulaire de login avec message d'erreur
        header('Location: ' . ROOT_URL . '/views/backend/security/login.php?error=user_not_found');
        exit();
    }
    
    // Récupérer le premier (et seul) résultat
    $user = $user[0];
    
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
    
    // Le mot de passe est correct, créer une session pour l'utilisateur
    // Les données de session seront stockées dans $_SESSION et persisteront entre les pages
    
    // Stocker les informations de l'utilisateur dans la session
    $_SESSION['user_id'] = $user['numMemb'];           // ID unique de l'utilisateur
    $_SESSION['pseudoMemb'] = $user['pseudoMemb'];     // Pseudo (pour afficher dans la nav)
    $_SESSION['eMailMemb'] = $user['eMailMemb'];       // Email
    $_SESSION['prenomMemb'] = $user['prenomMemb'];     // Prénom
    $_SESSION['nomMemb'] = $user['nomMemb'];           // Nom
    $_SESSION['numStat'] = $user['numStat'];           // Statut (admin, modo, user, etc.)
    $_SESSION['login_time'] = time();                  // Timestamp de connexion
    $_SESSION['authenticated'] = true;                 // Flag d'authentification

    // Si l'utilisateur a coché "Se souvenir de moi", créer un token persistant
    // Ce token sera stocké en cookie pour permettre une reconnexion automatique
    
    if($rememberMe){
        // Générer un token aléatoire sécurisé
        $rememberToken = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux aléatoires
        
        // Créer un cookie qui expire dans 30 jours
        $cookie_expiry = time() + (30 * 24 * 60 * 60); // 30 jours en secondes
        setcookie(
            'remember_token',           // Nom du cookie
            $rememberToken,             // Valeur du token
            $cookie_expiry,             // Date d'expiration
            '/',                        // Chemin (accessible partout)
            '',                         // Domaine (vide = domaine courant)
            false,                      // Secure (false pour HTTP, true pour HTTPS)
            true                        // HttpOnly (true = non accessible en JavaScript)
        );
        
        // Optionnel : sauvegarder le token en BDD pour validation ultérieure
        // Cela permettrait d'invalider les tokens en cas de changement de mot de passe
        // sql_update("MEMBRE", ["rememberToken" => $rememberToken], "numMemb = " . $user['numMemb']);
    }
    
    // L'authentification a réussi, rediriger vers le dashboard ou la page d'accueil
    // En fonction du statut de l'utilisateur, on pourrait rediriger vers des pages différentes
    
    if($user['numStat'] == 1){  // Supposons que numStat=1 est admin
        // Admin : rediriger vers le dashboard admin
        header('Location: ' . ROOT_URL . '/views/backend/dashboard.php');
    } else {
        // Utilisateur normal : rediriger vers la page d'accueil
        header('Location: ' . ROOT_URL . '/index.php');
    }
    exit();
}
?>