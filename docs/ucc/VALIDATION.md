# Validation du 28 septembre 2026

- PASS : contrats JSON et régressions pédagogiques (`tools/ucc_editorial/validate.py`). 110 plans, 132 fiches, 338 sélections, 81 fiches sélectionnées, 51 en réserve.
- PASS : références inverses, titres, passages non vides, barèmes, prérequis acycliques et trois exceptions.
- PASS : régressions ciblées sur archives, audit, Trinité, christologie, mariologie, dette et distinction de la bibliographie du Dieu cosmique.
- PASS : structure HTML (110 plans et identifiants uniques), logique JavaScript de filtrage par Voie, recherche sans accents, résultats vides et ouverture par lien direct. Tests exécutés dans Node avec simulation DOM.
- NON EXÉCUTÉ : rendu visuel et navigation dans Chromium. Playwright est installé, mais aucun exécutable de navigateur n’est disponible ; aucune capture ne vaut validation visuelle.
- NON EXÉCUTÉ : PHP CLI, PHPUnit et parcours Moodle réel, faute de runtime PHP/Moodle dans cet environnement. Les tests PHP sont livrés pour exécution sur l’instance de test.
- NON CERTIFIÉ : collation exhaustive des passages, droits territoriaux, conformité doctrinale et validation académique. Les notices héritées ne sont pas toutes revérifiées en ligne.

Les vérifications réalisées concernent le snapshot, pas une instance déployée.
