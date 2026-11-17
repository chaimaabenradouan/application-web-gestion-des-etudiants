<?php $page_courante = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titre ?? 'Gestion des étudiants') ?></title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?= !empty($page_login) ? 'page-login' : 'page-app' ?>">
<?php if (!empty($page_login)): ?>
<main class="login-main">
<?php else: ?>
<div class="app">

    <aside class="lateral">
        <div class="lat-haut">
            <svg viewBox="0 0 24 24" width="64" height="64" fill="#fff">
                <path d="M12 2 1 7.5 12 13l9-4.5V14h2V7.5L12 2z"/>
                <path d="M5 11.8v3c0 1.4 3.1 3 7 3s7-1.6 7-3v-3L12 15.3 5 11.8z"/>
                <path d="M2 19.5c3-1.2 6.5-1.2 10 .8 3.5-2 7-2 10-.8V21c-3-1.2-6.5-1.2-10 .8-3.5-2-7-2-10-.8z"/>
            </svg>
            <div class="lat-titre">Gestion<br>des Étudiants</div>
        </div>

        <nav class="lat-menu">
            <a href="liste.php" class="<?= $page_courante === 'liste.php' ? 'actif' : '' ?>">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 3 2 12h3v8h5v-5h4v5h5v-8h3z"/></svg>
                Liste des étudiants
            </a>
            <a href="ajouter.php" class="<?= ($page_courante === 'ajouter.php' && !isset($_GET['id'])) ? 'actif' : '' ?>">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4z"/></svg>
                Ajouter un étudiant
            </a>
            <a href="logout.php">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M10 3H4a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h6v-2H5V5h5zm6 4-1.4 1.4L17.2 11H8v2h9.2l-2.6 2.6L16 17l5-5z"/></svg>
                Déconnexion
            </a>
        </nav>

        <div class="lat-bas">
            <svg viewBox="0 0 60 80" width="60" height="80" fill="#7fa56f">
                <path d="M30 78C28 55 30 30 44 4c8 22 4 44-14 74z"/>
                <path d="M28 60C14 56 6 44 4 30c14 2 24 12 24 30z" opacity=".8"/>
            </svg>
            <p>Apprendre aujourd'hui, réussir demain</p>
        </div>
    </aside>

    <div class="principal">
        <div class="banniere">
            <div class="banniere-texte">
                <h1>Bonjour, <?= h(nom_affiche()) ?> !</h1>
                <p>Bienvenue sur votre espace de gestion</p>
            </div>
            <img class="banniere-img" src="images/casquette_livre.png" alt="">
            <div class="profil" title="<?= h($_SESSION['login'] ?? '') ?>">
                <?= h(mb_strtoupper(mb_substr(nom_affiche(), 0, 1))) ?>
            </div>
        </div>

        <main class="contenu">
            <?php if ($afficher_titre ?? true): ?>
                <h2 class="titre-page"><?= h($titre ?? '') ?></h2>
            <?php endif; ?>
<?php endif; ?>