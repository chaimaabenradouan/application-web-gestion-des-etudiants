# Gestion des étudiants (TP PHP)

Application web de gestion d'étudiants développée en **PHP**, **HTML/CSS** et **JavaScript**, sans base de données : les données sont enregistrées dans un **fichier texte**.

Projet réalisé dans le cadre d'un **TP de développement web PHP**, ENSA Safi.

---

## Aperçu

**Page de connexion**

![Connexion](screenshots/connexion.png)

**Liste des étudiants** (recherche, tri, statistiques)

![Liste](screenshots/liste.png)

**Ajout d'un étudiant**

![Ajout](screenshots/ajouter.png)

---

## Fonctionnalités

| Fonctionnalité | Description |
|---|---|
| **Connexion / déconnexion** | Authentification par login et mot de passe (session PHP) |
| **Cookie « dernier login »** | Le dernier identifiant est mémorisé 7 jours si « Se souvenir de moi » est coché |
| **Liste des étudiants** | Tableau avec photo, nom (majuscules), prénom (1re lettre en majuscule), filière, note, actions |
| **Notes colorées** | Vert si la note est ≥ 10, rouge sinon |
| **Recherche** | Par nom ou prénom (formulaire GET) |
| **Recherche instantanée** | Filtrage des lignes en JavaScript, sans recharger la page |
| **Tri** | Par ordre d'ajout, nom (A-Z), filière ou note décroissante |
| **Statistiques** | Nombre d'étudiants affichés, moyenne des notes, meilleure note (liste vide gérée) |
| **Ajout** | Formulaire avec validation côté serveur |
| **Modification** | `ajouter.php?id=...` pré-remplit le formulaire |
| **Suppression** | Avec confirmation JavaScript, suppression de la photo associée |
| **Photo de profil** | Envoi facultatif (jpg, jpeg, png), miniature dans la liste |
| **Messages flash** | Confirmation stockée en session, affichée une seule fois |
| **Interface** | Menu latéral, bannière de bienvenue, cartes, bouton afficher/masquer le mot de passe |

---

## Arborescence

```
gestion_etudiants/
├── data/
│   └── etudiants.txt      # stockage des étudiants (une ligne par étudiant)
├── images/
│   ├── casquette.png      # illustration de la page de connexion
│   ├── casquette_livre.png# illustration de la bannière
│   └── logo.png           # icône de l'onglet
├── screenshots/           # captures d'écran du README
├── uploads/               # photos des étudiants (créées à l'envoi)
├── functions.php          # constantes, session, fonctions communes
├── header.php             # début du HTML, menu latéral, bannière
├── footer.php             # pied de page, fermeture du HTML
├── index.php              # page de connexion
├── liste.php              # liste, recherche, tri, statistiques
├── ajouter.php            # ajout et modification (selon ?id=)
├── supprimer.php          # suppression d'un étudiant
├── logout.php             # déconnexion
├── script.js              # confirmation, recherche instantanée, mot de passe
├── style.css              # mise en forme
└── test.php               # phpinfo() pour vérifier le serveur
```

---

## Installation et lancement (XAMPP)

1. Installer **XAMPP** et démarrer le module **Apache** depuis le panneau de contrôle.
2. Copier le dossier `gestion_etudiants` dans `C:\xampp\htdocs\`.
3. Vérifier que les dossiers `data/` et `uploads/` existent et que `data/etudiants.txt` est présent (fichier vide au départ).
4. Ouvrir le navigateur à l'adresse :

   ```
   http://localhost/gestion_etudiants/
   ```

5. Pour vérifier que PHP fonctionne : `http://localhost/gestion_etudiants/test.php`.

---

## Identifiants de test

| Login | Mot de passe | Nom affiché |
|---|---|---|
| `admin` | `admin123` | Chaimaa |
| `prof` | `prof123` | Professeur |

Ces comptes sont définis dans la constante `UTILISATEURS` de `functions.php`.

---

## Format du fichier `data/etudiants.txt`

Une ligne par étudiant, champs séparés par `;` :

```
id;nom;prenom;filiere;note;photo
1;benali;sara;GI;14.5;etu_65a1.jpg
```

| Champ | Description |
|---|---|
| `id` | Identifiant unique (dernier id + 1 à chaque ajout) |
| `nom`, `prenom` | Texte nettoyé (les `;` et retours à la ligne sont supprimés) |
| `filiere` | L'une de : `GI`, `IDIA`, `GE`, `GC` |
| `note` | Nombre entre 0 et 20 |
| `photo` | Nom du fichier dans `uploads/` (vide si pas de photo) |

---

## Description des fichiers

### `functions.php` (fichier commun)

Inclus dans toutes les pages avec `require_once`. Il démarre la session.

**Constantes**
- `FICHIER` : chemin du fichier texte (avec `__DIR__`)
- `FILIERES` : liste des filières autorisées
- `UTILISATEURS` : tableau login => mot de passe
- `NOMS` : nom affiché pour chaque login

**Fonctions**

| Fonction | Rôle |
|---|---|
| `h(string $s): string` | Protège l'affichage avec `htmlspecialchars` (contre les failles XSS) |
| `connecte(): bool` | Indique si un utilisateur est connecté |
| `exiger_connexion(): void` | Redirige vers `index.php` si non connecté |
| `nettoyer(string $s): string` | Supprime `;`, retours à la ligne et espaces inutiles |
| `lire_etudiants(): array` | Lit le fichier (`file`, `explode`) et retourne un tableau associatif indexé par l'id |
| `sauver_etudiants(array $liste): void` | Reconstruit les lignes (`implode`) et écrit le fichier (`file_put_contents`) |
| `nom_affiche(): string` | Retourne le nom à afficher dans la bannière |

### `index.php`
Formulaire de connexion (POST). Si le login et le mot de passe correspondent à `UTILISATEURS`, le login est enregistré dans `$_SESSION` et l'utilisateur est redirigé vers `liste.php`. Un utilisateur déjà connecté est redirigé directement. Le cookie `dernier_user` (7 jours) pré-remplit le champ login.

### `liste.php`
Page protégée. Lit les étudiants, applique la recherche (`stripos` + `array_filter`) puis le tri (`usort`, `strcasecmp`, `<=>`), calcule les statistiques (`count`, `array_sum`, `array_column`, `max`, `number_format`) et affiche le tableau. Affiche « Aucun étudiant » si la liste est vide, et le message flash puis le supprime avec `unset()`.

### `ajouter.php`
Gère à la fois l'**ajout** et la **modification** :
- sans paramètre : formulaire vide, nouvel id = max + 1 ;
- avec `?id=...` : formulaire pré-rempli, mise à jour de la ligne.

Contrôles serveur : nom et prénom non vides, filière valide (`in_array`), note numérique entre 0 et 20 (`is_numeric`). Toutes les erreurs sont affichées et les valeurs saisies sont conservées. Pour la photo : code d'erreur, extension (jpg, jpeg, png), taille, renommage avec `uniqid()`, déplacement avec `move_uploaded_file()`. Lors d'une modification avec nouvelle photo, l'ancienne est supprimée avec `unlink()`.

### `supprimer.php`
Convertit l'id reçu en entier avec `(int)`, supprime la photo si elle existe, retire l'étudiant avec `unset()`, réécrit le fichier puis redirige vers la liste avec un message flash.

### `logout.php`
Vide `$_SESSION`, détruit la session avec `session_destroy()` et redirige vers `index.php`.

### `header.php` / `footer.php`
Structure HTML commune : menu latéral et bannière affichés seulement pour les pages de l'application (pas pour la page de connexion), titre variable `$titre`, pied de page avec la date (`date('d/m/Y')`).

### `script.js`
1. Confirmation (`confirm`) avant de suivre un lien de suppression.
2. Recherche instantanée : masque les lignes du tableau qui ne correspondent pas au texte saisi.
3. Bouton œil pour afficher ou masquer le mot de passe.

### `style.css`
Mise en forme : menu latéral, bannière, tableau lisible, formulaires en cartes, messages de succès (vert) et d'erreur (rouge), notes colorées, photos rondes.

---

## Sécurité

- **Échappement des sorties** avec `h()` (`htmlspecialchars`) pour éviter l'injection de HTML/JavaScript.
- **Validation côté serveur** de tous les champs (le navigateur n'est jamais cru).
- **Conversion de l'id en entier** avec `(int)` avant toute utilisation.
- **Pages protégées** par `exiger_connexion()`.
- **Upload contrôlé** : extensions limitées à jpg, jpeg, png, taille limitée, fichier renommé avec `uniqid()`.
- **Nettoyage des saisies** pour ne pas casser le format du fichier texte (`;` et retours à la ligne supprimés).

---

## Limites connues

- Les mots de passe sont stockés **en clair** dans `functions.php` : acceptable pour un TP, à remplacer par `password_hash()` et `password_verify()` en production.
- Le stockage dans un fichier texte ne convient pas à un usage multi-utilisateurs intensif (pas de verrouillage des écritures).
- Aucun mécanisme de protection CSRF sur les formulaires et sur le lien de suppression (méthode GET).


---

## Auteur

- **Nom :** Chaimaa BENRADOUAN
- **Établissement :** ENSA Safi
- **Année universitaire :** 2026/2027