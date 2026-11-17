document.addEventListener('DOMContentLoaded', function () {

    // 1. Confirmation avant suppression
    var liensSuppr = document.querySelectorAll('.lien-suppr');
    liensSuppr.forEach(function (lien) {
        lien.addEventListener('click', function (e) {
            if (!confirm('Voulez-vous vraiment supprimer cet étudiant ?')) {
                e.preventDefault();
            }
        });
    });

    // 2. Recherche instantanée (sans recharger la page)
    var champ = document.querySelector('input[name="q"]');
    if (champ) {
        champ.addEventListener('input', function () {
            var texte = champ.value.toLowerCase();
            var lignes = document.querySelectorAll('.tableau tbody tr');

            lignes.forEach(function (ligne) {
                // Ignorer la ligne « Aucun étudiant »
                if (ligne.cells.length < 6) {
                    return;
                }
                var nom = ligne.cells[1].textContent.toLowerCase();
                var prenom = ligne.cells[2].textContent.toLowerCase();
                var visible = nom.includes(texte) || prenom.includes(texte);
                ligne.style.display = visible ? '' : 'none';
            });
        });
    }

        // 3. Afficher / masquer le mot de passe
    var oeil = document.getElementById('voir-mdp');
    var champMdp = document.getElementById('mdp');
    if (oeil && champMdp) {
        oeil.addEventListener('click', function () {
            champMdp.type = champMdp.type === 'password' ? 'text' : 'password';
        });
    }
});