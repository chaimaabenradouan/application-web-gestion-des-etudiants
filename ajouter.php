<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// Mode modification si ?id=... existe
$id_edit = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modif   = $id_edit > 0 && isset($etudiants[$id_edit]);

if ($id_edit > 0 && !$modif) {
    $_SESSION['flash'] = 'Étudiant introuvable.';
    header('Location: liste.php');
    exit;
}

// Valeurs du formulaire
$nom = $prenom = $filiere = $note = '';
if ($modif) {
    $nom     = $etudiants[$id_edit]['nom'];
    $prenom  = $etudiants[$id_edit]['prenom'];
    $filiere = $etudiants[$id_edit]['filiere'];
    $note    = (string) $etudiants[$id_edit]['note'];
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = nettoyer($_POST['nom'] ?? '');
    $prenom  = nettoyer($_POST['prenom'] ?? '');
    $filiere = $_POST['filiere'] ?? '';
    $note    = trim($_POST['note'] ?? '');

    // Validation
    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    }
    if ($prenom === '') {
        $erreurs[] = 'Le prénom est obligatoire.';
    }
    if (!in_array($filiere, FILIERES)) {
        $erreurs[] = 'Filière invalide.';
    }
    if (!is_numeric($note) || $note < 0 || $note > 20) {
        $erreurs[] = 'La note doit être un nombre entre 0 et 20.';
    }

    // Photo (facultative)
    $nouvelle_photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Erreur lors de l'envoi de la photo.";
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $erreurs[] = 'Photo : extensions autorisées jpg, jpeg, png.';
            } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $erreurs[] = 'Photo : taille maximale 2 Mo.';
            } else {
                $nouvelle_photo = 'etu_' . uniqid() . '.' . $ext;
            }
        }
    }

    if (count($erreurs) === 0) {
        $photo = $modif ? $etudiants[$id_edit]['photo'] : '';

        if ($nouvelle_photo !== '') {
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $nouvelle_photo);
            // Supprimer l'ancienne photo
            if ($photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
                unlink(__DIR__ . '/uploads/' . $photo);
            }
            $photo = $nouvelle_photo;
        }

        if ($modif) {
            $id = $id_edit;
            $message = 'Étudiant modifié avec succès.';
        } else {
            $id = $etudiants ? max(array_keys($etudiants)) + 1 : 1;
            $message = 'Étudiant ajouté avec succès.';
        }

        $etudiants[$id] = [
            'id'      => $id,
            'nom'     => $nom,
            'prenom'  => $prenom,
            'filiere' => $filiere,
            'note'    => (float) $note,
            'photo'   => $photo,
        ];

        sauver_etudiants($etudiants);
        $_SESSION['flash'] = $message;
        header('Location: liste.php');
        exit;
    }
}

$titre = $modif ? 'Modifier un étudiant' : 'Ajouter un étudiant';
include 'header.php';
?>

<?php if (count($erreurs) > 0): ?>
    <div class="message erreur">
        <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= h($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="carte">
    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" value="<?= h($nom) ?>">

    <label for="prenom">Prénom</label>
    <input type="text" id="prenom" name="prenom" value="<?= h($prenom) ?>">

    <label for="filiere">Filière</label>
    <select id="filiere" name="filiere">
        <option value="">-- Choisir --</option>
        <?php foreach (FILIERES as $f): ?>
            <option value="<?= h($f) ?>" <?= $filiere === $f ? 'selected' : '' ?>><?= h($f) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="note">Note (0 à 20)</label>
    <input type="text" id="note" name="note" value="<?= h($note) ?>">

    <label for="photo">Photo (jpg, jpeg, png, 2 Mo max)</label>
    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">

    <button type="submit"><?= $modif ? 'Enregistrer' : 'Ajouter' ?></button>
</form>

<?php include 'footer.php'; ?>