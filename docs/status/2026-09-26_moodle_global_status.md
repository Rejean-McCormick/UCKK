# UCKK Moodle — statut global

Date : 2026-09-26

## 1. Résumé exécutif

Le Moodle UCKK a été reconstruit sur une installation fraîche de Moodle 5.2.3+ et les principaux blocs fonctionnels sont maintenant en place :

- installation Moodle locale opérationnelle côté CLI ;
- structure publique UCKK / UCC / Math installée ;
- trois façades sur une seule installation Moodle et une seule session ;
- switcher de façade fonctionnel ;
- thèmes UCKK / UCC / Math installés comme skins directs de Boost ;
- pages publiques servies par `local_uckk` ;
- catégories et cours injectés nativement via `tool_uckkseed` ;
- Médiathèque importée dans `mod_uckkarchive` via Moodle File API ;
- 64 médias / 64 versions / 64 sources importés sans erreur finale ;
- 381 tags média et 11 advisories / markers importés ;
- nomenclature produit actuelle : **SemantiK Architect** ;
- aucun contenu ne doit réintroduire les anciennes nomenclatures interdites.

Le point ouvert actuel n’est pas un problème de données Moodle : une autre application locale Django répond sur `127.0.0.1:8000` dans certains tests navigateur. Il reste donc à rétablir ou identifier proprement le serveur HTTP Moodle local avant la validation web finale.

---

## 2. Environnement local

### Dépôts et runtime

- Source Moodle UCKK : `C:\mycode\UCKK\uckk-moodle`
- Ops Console : `C:\mycode\UCKK\UCKK_ops_console`
- Moodle installé : `C:\mycode\UCKK\moodle\moodle`
- Webroot/runtime Moodle : `C:\mycode\UCKK\moodle\moodle\public`
- Moodledata : `C:\mycode\UCKK\moodledata`
- Import corpus : `C:\mycode\UCKK\uckk-import`
- PHP : `C:\php\8.4\php.exe`
- Base locale : `uckk_moodle_52`

### Version Moodle

- Moodle : 5.2.3+
- Build : 20260916

---

## 3. Structure Moodle fraîche

L’installation fraîche a été complétée.

Les scripts CLI principaux sont résolus dans la racine Moodle :

- `C:\mycode\UCKK\moodle\moodle\admin\cli\upgrade.php`
- `C:\mycode\UCKK\moodle\moodle\admin\cli\purge_caches.php`

Le diagnostic local confirme aussi la présence de :

- `C:\mycode\UCKK\moodle\moodle\config.php`
- `C:\mycode\UCKK\moodle\moodle\public\config.php`

Le webroot configuré est :

`C:\mycode\UCKK\moodle\moodle\public`

---

## 4. Architecture des façades publiques

Les trois façades sont : UCKK, UCC et Math.

Architecture retenue :

- Moodle reste le shell applicatif ;
- Boost reste la base de thème ;
- `theme_uckk`, `theme_ucc` et `theme_ucmath` sont des skins directs de Boost ;
- `local_uckk` porte la structure publique partagée ;
- la donnée publique est commune ;
- le contexte de façade change la présentation et certains contenus de façade ;
- la session Moodle reste unique pendant le changement de façade.

### Éléments déjà stabilisés

- switcher UCKK / UCC / Math fonctionnel ;
- contraste du switcher corrigé ;
- UCKK conserve son motif visuel héraldique ;
- UCC et Math ne reprennent pas ce motif ;
- images de login UCKK selon la période de la journée fonctionnelles ;
- `local/uckk/classes/output/public_page.php` corrigé en UTF-8 sans BOM.

### Pages publiques présentes dans le code

Le plugin `local_uckk` contient notamment :

- `local/uckk/index.php`
- `local/uckk/about.php`
- `local/uckk/programs.php`
- `local/uckk/courses.php`
- `local/uckk/mediatheque.php`
- `local/uckk/news.php`
- `local/uckk/contact.php`
- `local/uckk/integrity.php`
- `local/uckk/archives.php`
- `local/uckk/assemblies.php`
- `local/uckk/challenges.php`

Les variantes publiques UCC et Math sont présentes sous :

- `local/uckk/classes/local/public_pages/ucc/`
- `local/uckk/classes/local/public_pages/math/`

---

## 5. Switcher multi-façades

Le switcher a été établi sur une seule session Moodle.

Actions exactes présentes dans l’Ops Console :

- `Ouvrir Moodle local`
- `Ouvrir UCKK`
- `Ouvrir UCC`
- `Ouvrir Math`
- `Tester switcher UCKK / UCC / Math`
- `Diagnostiquer Moodle local`
- `Purger les caches locaux`
- `Mettre à jour Moodle local`
- `Synchroniser source vers Moodle local`
- `Vérifier les chemins locaux`

Workflow plus large présent dans l’Accueil :

- `Préparer local et ouvrir Moodle`

---

## 6. Registre académique et cours

Les données académiques sont conservées dans :

`C:\mycode\UCKK\uckk-moodle\academic_registry_json`

Le seeding a été effectué avec l’outil Moodle natif :

`admin/tool/uckkseed`

Résultat actuel :

- catégories : 21
- cours hors site : 114

Le mode de seed a été remis en `dry_run` après application.

---

## 7. Plugins et domaines UCKK

Le dépôt contient les composants principaux de l’architecture actuelle, notamment :

- `local_uckk`
- `mod_uckkarchive`
- `mod_uckkchallenge`
- `admin/tool/uckkseed`
- `report/uckk`
- thèmes `uckk`, `ucc`, `ucmath`
- composants liés aux cours, badges, intégrité, assemblées et rapports.

Principes à conserver :

- données métier dans les plugins Moodle ;
- fichiers gérés par Moodle File API ;
- thèmes réservés à la présentation ;
- aucun contournement des contextes, capacités ou mécanismes backup/restore Moodle.

---

## 8. Médiathèque UCKK

### Corpus source

Inventaire :

`C:\mycode\UCKK\uckk-import\uckkarchive\uckk_inventory.json`

Originaux :

`C:\mycode\UCKK\uckk-import\uckkarchive\originals`

État du corpus :

- 64 entrées inventaire ;
- 64 fichiers présents ;
- 0 fichier manquant ;
- 0 orphelin détecté au prévol ;
- 0 occurrence de nomenclature interdite détectée au prévol final.

### Archive Moodle cible

- titre : `Médiathèque UCKK`
- archive id : 1
- course module id : 3
- context id : 35
- course id : 2

### Import final

- médias : 64
- versions : 64
- sources : 64
- tags média : 381
- advisories / markers : 11
- fichiers manquants : 0
- erreurs : 0

Le résultat final contient `errors: []`.

Les originaux ont été remis à Moodle via la File API et ne sont pas copiés dans `public/`.

### Visibilité

Compteur courant :

`media_public_active_general = 4`

Ce compteur ne remet pas en cause l’import des 64 médias. La visibilité publique réelle doit encore être validée dans l’interface Moodle servie par le bon serveur HTTP.

---

## 9. Correctifs appliqués à l’import Médiathèque

### Code de sortie PowerShell

Le lanceur PowerShell a été corrigé pour :

- envoyer la sortie PHP vers `Out-Host` ;
- capturer explicitement `$LASTEXITCODE` ;
- retourner un code de sortie numérique propre.

### Import des advisories

Le correctif appliqué à :

`tools/uckk-ops/import/import_uckkarchive_media.php`

fait maintenant :

- suppression de l’écriture de l’alias SQL problématique `key` dans le record de tag de contenu ;
- rollback Moodle propre en cas d’exception dans une transaction déléguée ;
- diagnostic DML enrichi avec type d’exception, code et `debuginfo` ;
- conservation UTF-8 sans BOM.

Après correctif, l’import complet a terminé avec succès.

---

## 10. File API, contexte et intégrité Moodle

Le flux d’import respecte le contrat Moodle :

- fichiers stockés via Moodle File API ;
- contexte Moodle utilisé ;
- aucune copie directe des originaux dans `public/` ;
- aucune suppression manuelle de lignes DB requise ;
- import réexécutable et conçu pour réutiliser les éléments existants ;
- rollback transactionnel en cas de nouvel échec.

---

## 11. Nomenclature produit

Le nom produit actuel est :

**SemantiK Architect**

Règle permanente :

- ne jamais réintroduire les anciennes nomenclatures interdites ;
- ne pas les afficher dans l’interface, la documentation ou les scripts ;
- `Abstract Wikipedia` peut seulement être utilisé lorsqu’il désigne explicitement le projet externe correspondant.

---

## 12. Ops Console

Racine :

`C:\mycode\UCKK\UCKK_ops_console`

Modules importants :

- `modules/local/UckkOps.Local.psm1`
- `modules/mediatheque/UckkOps.Mediatheque.psm1`
- `modules/mediatheque/UckkOps.Mediatheque.Moodle.psm1`
- `modules/mediatheque/UckkOps.Mediatheque.Manifest.psm1`
- `modules/moodle-data/UckkOps.MoodleData.psm1`
- `modules/moodle-data/UckkOps.MoodleData.Apply.psm1`
- `modules/moodle-data/UckkOps.MoodleData.Json.psm1`
- `modules/git/UckkOps.Git.psm1`
- `modules/server/UckkOps.Server.psm1`
- `modules/tests/UckkOps.Tests.psm1`

L’Ops Console reste l’outil principal pour les opérations locales et serveur.

---

## 13. État HTTP local actuel

### Symptôme

La requête :

`http://127.0.0.1:8000/local/uckk/mediatheque.php`

a retourné une page 404 Django 5.1.9.

La requête n’a donc pas atteint Moodle.

### Interprétation

Le problème actuel est au niveau du serveur local / port HTTP, pas au niveau de la Médiathèque Moodle.

La page Moodle existe bien dans le code :

`local/uckk/mediatheque.php`

Le port `127.0.0.1:8000` est ou a été occupé par une application Django.

### Risque identifié dans l’outil local

L’action locale qui ouvre Moodle vérifie qu’un serveur HTTP répond sur l’URL configurée. Une réponse HTTP quelconque sur ce port peut donc être interprétée comme un serveur déjà disponible même si elle vient d’une autre application.

---

## 14. Diagnostic Moodle local le plus récent

Rapport :

`C:\mycode\UCKK\UCKK_ops_console\reports\20260925_174239_local_diagnostiquer_moodle_local_moodle_local.md`

Log :

`C:\mycode\UCKK\UCKK_ops_console\logs\20260925_174239_local_diagnostiquer_moodle_local_moodle_local.log`

Le diagnostic confirme au minimum la résolution correcte des scripts CLI Moodle et des fichiers `config.php`.

Il ne suffit pas à confirmer que le serveur HTTP répond actuellement avec Moodle.

---

## 15. Éléments considérés stabilisés

- installation fraîche Moodle ;
- structure source/runtime ;
- plugins UCKK principaux présents ;
- façades UCKK / UCC / Math ;
- switcher ;
- skins Boost ;
- structure publique `local_uckk` ;
- correctif BOM de `public_page.php` ;
- images de login UCKK ;
- registre académique injecté ;
- 21 catégories ;
- 114 cours ;
- archive Moodle de Médiathèque ;
- import des 64 médias ;
- File API ;
- 64 versions ;
- 64 sources ;
- 381 tags ;
- 11 advisories / markers ;
- correctifs transactionnels de l’importeur ;
- nomenclature SemantiK Architect.

---

## 16. Ce qui reste à valider localement

Avant mise en ligne :

1. identifier ou rétablir le serveur HTTP Moodle local ;
2. confirmer que la racine Moodle répond et non Django ;
3. exécuter `Tester switcher UCKK / UCC / Math` ;
4. confirmer HTTP 200 pour les pages publiques principales ;
5. ouvrir `local/uckk/mediatheque.php` ;
6. confirmer les médias publics attendus ;
7. ouvrir au moins un fichier via Moodle File API ;
8. vérifier au moins un contenu avec advisory ;
9. confirmer le même corpus sous UCKK / UCC / Math ;
10. confirmer l’absence de nomenclature interdite dans le rendu ;
11. vérifier les logs PHP/Moodle après navigation ;
12. seulement ensuite passer à la validation serveur / mise en ligne.

---

## 17. Règles de correction à conserver

1. prendre le log exact ;
2. inspecter le module ou fichier réellement concerné ;
3. appliquer le plus petit correctif possible ;
4. utiliser les mécanismes Moodle natifs ;
5. ne pas contourner File API, contextes, capacités, backup/restore ;
6. ne pas modifier les thèmes pour corriger un problème de données ou d’import ;
7. conserver PHP namespacé et UTF-8 sans BOM ;
8. ne pas recréer une logique déjà fournie par Moodle ou les plugins UCKK ;
9. ne pas réinitialiser la base ou supprimer manuellement des données sans preuve qu’un rollback/import idempotent ne suffit pas.

---

## 18. Préparation à la mise en ligne

### Prêt côté code/données

- code Moodle principal ;
- données académiques ;
- corpus Médiathèque ;
- structure des trois façades ;
- données File API ;
- import final sans erreur.

### Restant avant déploiement

- validation HTTP locale réelle de Moodle ;
- validation fonctionnelle de la Médiathèque publique ;
- validation finale du switcher sur les pages publiques ;
- contrôle final des logs après navigation.

Aucun reset de base et aucun nouvel import complet du corpus ne sont requis à ce stade.

---

## 19. Prochaine action immédiate

La prochaine action doit porter uniquement sur le serveur HTTP Moodle local.

Une fois Moodle réellement servi, utiliser les actions exactes de l’Ops Console :

- `Ouvrir Moodle local`
- `Tester switcher UCKK / UCC / Math`

Puis valider la Médiathèque depuis la page Moodle réelle.

Ne pas relancer `IMPORT_LOCAL_MEDIA.cmd` sauf si un nouveau défaut d’intégrité des données est démontré.
