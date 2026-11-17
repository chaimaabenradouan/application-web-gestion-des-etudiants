<?php
require_once 'functions.php';

if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
$login = $_COOKIE['dernier_user'] ?? '';
$souvenir = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $souvenir = isset($_POST['souvenir']);

    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        $_SESSION['login'] = $login;
        if ($souvenir) {
            setcookie('dernier_user', $login, time() + 7 * 24 * 3600, '/');
        } else {
            setcookie('dernier_user', '', time() - 3600, '/');
        }
        header('Location: liste.php');
        exit;
    }
    $erreur = 'Login ou mot de passe incorrect.';
}

$titre = 'Connexion';
$page_login = true;
include 'header.php';
?>

<div class="login-scene">
    <div class="login-illus">
        <img src="images/casquette.png" alt="Casquette de diplômé sur des livres">
    </div>

    <form method="post" action="index.php" class="login-card">
        <div class="login-badge">
            <svg viewBox="0 0 24 24" width="46" height="46" fill="#fff">
                <path d="M12 3 1 8.5 12 14l9-4.5V15h2V8.5L12 3z"/>
                <path d="M5 12.8v3.4c0 1.5 3.1 3.3 7 3.3s7-1.8 7-3.3v-3.4L12 16.3 5 12.8z"/>
            </svg>
        </div>

        <h1>Gestion des Étudiants</h1>
        <p class="login-sous">Connectez-vous pour accéder à votre espace</p>

        <?php if ($erreur !== ''): ?>
            <p class="message erreur"><?= h($erreur) ?></p>
        <?php endif; ?>

        <div class="champ">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="#2f5a2c">
                <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/>
            </svg>
            <input type="text" name="login" placeholder="Identifiant" value="<?= h($login) ?>" required>
        </div>

        <div class="champ">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="#2f5a2c">
                <path d="M6 10V8a6 6 0 0 1 12 0v2h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V11a1 1 0 0 1 1-1h1zm2 0h8V8a4 4 0 0 0-8 0v2z"/>
            </svg>
            <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required>
            <button type="button" class="oeil" id="voir-mdp" aria-label="Afficher le mot de passe">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#6b7280" stroke-width="2">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>

        <label class="souvenir">
            <input type="checkbox" name="souvenir" <?= $souvenir ? 'checked' : '' ?>>
            Se souvenir de moi
        </label>

        <button type="submit" class="btn-login">Se connecter</button>

        <div class="login-sep"></div>
        <p class="login-pied">Gestion des étudiants • ENSA Safi</p>
    </form>
</div>

<?php include 'footer.php'; ?>