<?php
require_once 'functions.php';

$liste = [
    1 => ['id' => 1, 'nom' => 'benali',  'prenom' => 'sara',  'filiere' => 'GI',   'note' => 14.5, 'photo' => ''],
    2 => ['id' => 2, 'nom' => 'alaoui',  'prenom' => 'omar',  'filiere' => 'IDIA', 'note' => 8,    'photo' => ''],
    3 => ['id' => 3, 'nom' => 'tazi',    'prenom' => 'lina',  'filiere' => 'GE',   'note' => 12.25,'photo' => ''],
];

sauver_etudiants($liste);

echo '<h3>Contenu brut du fichier</h3>';
echo '<pre>' . h(file_get_contents(FICHIER)) . '</pre>';

echo '<h3>Tableau relu avec lire_etudiants()</h3>';
echo '<pre>';
print_r(lire_etudiants());
echo '</pre>';