<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// Paramètres GET
$q   = trim($_GET['q'] ?? '');
$tri = $_GET['tri'] ?? 'ajout';

$tris_valides = ['ajout', 'nom', 'filiere', 'note'];
if (!in_array($tri, $tris_valides)) {
    $tri = 'ajout';
}

// Recherche : nom ou prénom contient le texte
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($e) use ($q) {
        return stripos($e['nom'], $q) !== false
            || stripos($e['prenom'], $q) !== false;
    });
}

// Tri
if ($tri === 'nom') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
} elseif ($tri === 'filiere') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
} elseif ($tri === 'note') {
    usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']);
} else {
    $etudiants = array_values($etudiants); // ordre d'ajout
}

// Statistiques (attention : liste vide)
$nb = count($etudiants);
if ($nb > 0) {
    $notes   = array_column($etudiants, 'note');
    $moyenne = number_format(array_sum($notes) / $nb, 2);
    $meilleure = max($notes);
} else {
    $moyenne = '-';
    $meilleure = '-';
}

// Message flash : lu une fois, puis supprimé
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$titre = 'Liste des étudiants';
$afficher_titre = false;
include 'header.php';
?>

<?php if ($flash !== ''): ?>
    <p class="message succes"><?= h($flash) ?></p>
<?php endif; ?>

<form method="get" action="liste.php" class="barre-recherche">
    <div class="champ-recherche">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#2f5a2c" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
        <input type="text" name="q" placeholder="Rechercher un étudiant..." value="<?= h($q) ?>">
    </div>

    <label class="tri-label" for="tri">Trier par :</label>
    <select id="tri" name="tri">
        <option value="ajout"   <?= $tri === 'ajout'   ? 'selected' : '' ?>>Ordre d'ajout</option>
        <option value="nom"     <?= $tri === 'nom'     ? 'selected' : '' ?>>Nom (A-Z)</option>
        <option value="filiere" <?= $tri === 'filiere' ? 'selected' : '' ?>>Filière</option>
        <option value="note"    <?= $tri === 'note'    ? 'selected' : '' ?>>Note décroissante</option>
    </select>

    <button type="submit" class="btn-rech">Rechercher</button>
</form>

<div class="stats-grille">
    <div class="stat">
        <div class="stat-icone">
            <svg viewBox="0 0 24 24" width="30" height="30" fill="#2f5a2c"><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 19c0-3 2.7-5 6-5s6 2 6 5zm10 0c0-1.6-.5-2.9-1.4-3.9.5-.1 1-.1 1.4-.1 3.3 0 6 2 6 5z"/></svg>
        </div>
        <div><div class="stat-val"><?= $nb ?></div><div class="stat-lib">Étudiants affichés</div></div>
    </div>
    <div class="stat">
        <div class="stat-icone">
            <svg viewBox="0 0 24 24" width="30" height="30" fill="#2f5a2c"><rect x="3" y="12" width="4" height="9"/><rect x="10" y="4" width="4" height="17"/><rect x="17" y="9" width="4" height="12"/></svg>
        </div>
        <div><div class="stat-val"><?= h((string) $moyenne) ?></div><div class="stat-lib">Moyenne des notes</div></div>
    </div>
    <div class="stat">
        <div class="stat-icone">
            <svg viewBox="0 0 24 24" width="30" height="30" fill="#2f5a2c"><path d="M7 3h10v4a5 5 0 0 1-4 4.9V15h3v2h1v3H7v-3h1v-2h3v-3.1A5 5 0 0 1 7 7zM5 4h2v3a3 3 0 0 1-2-2zm14 0h-2v3a3 3 0 0 0 2-2z"/></svg>
        </div>
        <div><div class="stat-val"><?= is_numeric($meilleure) ? number_format((float) $meilleure, 2) : '-' ?></div><div class="stat-lib">Meilleure note</div></div>
    </div>
</div>

<div class="carte-tableau">
    <table class="tableau">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Filière</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($etudiants) === 0): ?>
                <tr><td colspan="6">Aucun étudiant</td></tr>
            <?php else: ?>
                <?php foreach ($etudiants as $e): ?>
                    <tr>
                        <td>
                            <?php if ($e['photo'] !== '' && file_exists(__DIR__ . '/uploads/' . $e['photo'])): ?>
                                <img class="photo" src="uploads/<?= h($e['photo']) ?>" alt="Photo">
                            <?php else: ?>
                                <span class="photo photo-vide"><?= h(mb_strtoupper(mb_substr($e['prenom'], 0, 1) . mb_substr($e['nom'], 0, 1))) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="col-nom"><?= h(strtoupper($e['nom'])) ?></td>
                        <td><?= h(ucfirst($e['prenom'])) ?></td>
                        <td><span class="pastille"><?= h($e['filiere']) ?></span></td>
                        <td class="<?= $e['note'] >= 10 ? 'note-ok' : 'note-ko' ?>">
                            <?= number_format((float) $e['note'], 2) ?>
                        </td>
                        <td class="actions">
                            <a class="btn btn-modif" href="ajouter.php?id=<?= $e['id'] ?>">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 17.3V21h3.7L17.8 9.9l-3.7-3.7zM20.7 7a1 1 0 0 0 0-1.4l-2.3-2.3a1 1 0 0 0-1.4 0l-1.8 1.8 3.7 3.7z"/></svg>
                                Modifier
                            </a>
                            <a class="btn btn-suppr lien-suppr" href="supprimer.php?id=<?= $e['id'] ?>">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 7h12l-1 14H7zM9 3h6l1 2h4v2H4V5h4z"/></svg>
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <p class="affichage">Affichage de <?= $nb ?> étudiant(s)</p>
</div>

<?php include 'footer.php'; ?>