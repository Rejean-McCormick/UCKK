# Installation et validation

Cette livraison est une modification du snapshot fourni. Aucun déploiement, import de cours, changement de base de données ou inscription n’a été effectué.

## Fichiers de référence

- `local/uckk/atlas/ucc_course_syllabi.json` : 110 plans ; source des objectifs, séances et évaluations.
- `local/uckk/atlas/ucc_mediatheque_course_links.json` : 338 sélections avec passages et raisons.
- `local/uckk/atlas/ucc_mediatheque_reference_registry.json` : 132 notices, dont les réserves et éditions à examiner.
- `tools/ucc_editorial/course_design.tsv` : choix pédagogiques éditables, un cours par ligne.
- `tools/ucc_editorial/new_sources.tsv` : 27 nouvelles sources avec points d’accès.
- `docs/ucc/PLANS_110_COURS.md` et `CATALOGUE_UCC.html` : exports consultables.

## Reproduire les projections

Depuis la racine du projet :

```sh
python3 tools/ucc_editorial/build.py
python3 tools/ucc_editorial/validate.py
python3 tools/ucc_editorial/export_html.py
```

Le script `integrate.py` était un outil ponctuel d’application du patch PHP ; ne pas le relancer sur la livraison intégrée. Les modifications PHP sont déjà présentes.

## Intégration Moodle

Copier les fichiers modifiés et nouveaux dans une installation de test de la même version de Moodle, puis purger les caches selon la procédure habituelle. Aucun changement du schéma de base de données.

La page `/local/uckk/ucc_study.php` expose seulement les plans et notices externes publics. Les liens Accueil/Médiathèque UCC et la navigation UCC la rendent accessible. Recherche textuelle et filtre par Voie ; vue inversée `?view=references` ; détail `?course=UCC-THE-107`. Le réglage Moodle `forcelogin` est respecté. Les médias privés et les contrôles d’accès de `mod_uckkarchive` restent dans leur propre flux.

L’API `get_ucc_course` conserve sa vérification de contexte et de capacité. Elle ajoute `syllabus`, les repères de passages, justifications et notices de contrôle aux références.

Ce catalogue éditorial n’est pas un import de ces notices dans `mod_uckkarchive`. Il donne immédiatement accès aux références externes depuis les plans et la Médiathèque, sans créer de faux enregistrements média ni contourner les permissions des fichiers Moodle.

## Vérifications réalisées et restantes

La validation Python vérifie : identifiants, références, projections inverses, rôles, passages, prérequis acycliques, barèmes, trois exceptions, absence des éditions mises en attente dans les lectures et régressions pédagogiques ciblées.

Le catalogue HTML autonome fait l’objet d’un contrôle structurel et de la logique de filtrage ; son rendu visuel et son comportement dans un navigateur réel restent à confirmer. Les résultats sont consignés dans `VALIDATION.md`.

Le conteneur de préparation ne dispose pas de PHP ni de Moodle exécutable. La syntaxe PHP et les tests PHPUnit doivent donc être exécutés sur l’installation de test :

```sh
php -l local/uckk/ucc_study.php
php -l local/uckk/classes/local/atlas/ucc_syllabus_registry.php
php -l local/uckk/classes/local/atlas/ucc_mediatheque_registry.php
php -l local/uckk/classes/external/get_ucc_course.php
vendor/bin/phpunit local/uckk/tests/ucc_syllabus_registry_test.php
vendor/bin/phpunit local/uckk/tests/ucc_mediatheque_registry_test.php
```

Vérifier aussi une visite anonyme selon `forcelogin`, le thème UCC, un identifiant invalide, une recherche sans résultat, l’API avec et sans capacité, et la non-régression des sites Math/UCKK. Les changements au contrôleur partagé `courses.php` sont conditionnés au site UCC.

## Validation éditoriale

Les repères de passage ne sont pas déclarés exhaustivement collationnés. Avant ouverture d’un séminaire : contrôler l’édition, la traduction et les citations ; adapter la charge aux participants ; faire relire les sujets doctrinaux ; fournir les aides de langue. Aucune attribution au Dieu cosmique n’est autorisée sans vérification dans l’exemplaire.
