# UCKK Ops Console — Runbook

## Objet

UCKK Ops Console pilote les opérations entre le repo source local, le runtime Moodle local, Git, le serveur `uckk.org` et les seeds Moodle.

```text
source locale → runtime local → Git → serveur source → serveur runtime → DB/cache Moodle
```

## Fichiers de l’app

```text
tools/uckk-ops/uckk_ops_gui.ps1
tools/uckk-ops/uckk-ops.config.json
tools/uckk-ops/lib/UckkOps.Common.psm1
tools/uckk-ops/lib/UckkOps.Local.psm1
tools/uckk-ops/lib/UckkOps.Git.psm1
tools/uckk-ops/lib/UckkOps.Server.psm1
tools/uckk-ops/lib/UckkOps.Seed.psm1
tools/uckk-ops/lib/UckkOps.Smoke.psm1
tools/uckk-ops/UCKK_Ops_Console_RUN.bat
docs/UCKK_Ops_Console_Runbook.md
```

## Source de configuration

Toutes les variables viennent de :

```text
tools/uckk-ops/uckk-ops.config.json
```

Les modules lisent la configuration via `UckkOps.Common.psm1`.

## Workflow normal

1. Modifier la source locale dans `C:\mycode\UCKK\uckk-moodle`.
2. Onglet `Local Dev` : sync source → runtime local, purge caches, test local.
3. Onglet `Git` : status, diff, commit + push.
4. Onglet `uckk.org` : test SSH, pull serveur, sync source → runtime, upgrade, purge caches, reload PHP-FPM.
5. Onglet `Seed DB` : dry-run puis apply si un JSON seed doit modifier la DB Moodle.
6. Onglet `Smoke` : tester les URLs locales ou serveur.

## Actions sensibles

Les actions suivantes demandent confirmation dans le GUI :

```text
Commit + push
Pull serveur
Sync serveur source → runtime
Moodle upgrade serveur
Purge caches serveur
Reload PHP-FPM
Apply categories local
Apply categories serveur
```

## Notes

- Le runtime local n’est pas la source.
- Le runtime serveur n’est pas la source.
- La source officielle est le repo `uckk-moodle`.
- Les JSON de seed doivent être appliqués à la DB Moodle avant d’apparaître dans certaines interfaces.


## Rôles consolidés : Source, Ops Console et Publisher

La source de vérité du code UCKK est `C:\mycode\UCKK\uckk-moodle`. Le runtime local `C:\mycode\UCKK\moodle\moodle\public` est une cible d’exécution, pas une source éditoriale.

- **UCKK Ops Console** : développement et opérations locales (diagnostics, démarrage Moodle local, upgrade, purge caches, opérations de maintenance). La synchronisation manuelle source → runtime reste utile pour le développement interactif.
- **UCKK Publisher** : chaîne de publication. À partir de v0.1.8, `Préparer le paquet` synchronise automatiquement `uckk-moodle` vers le webroot runtime, vérifie les empreintes source/runtime, puis construit le paquet. Une divergence résiduelle bloque la publication.
- **VPS** : cible de déploiement seulement. Les corrections normales ne doivent pas être faites directement dans `/opt/uckk/current`.

Docker n’est pas requis pour synchroniser ou préparer un paquet. Il faut uniquement qu’un serveur Moodle local soit démarré (Docker ou autre) lorsqu’on veut tester les URLs `127.0.0.1:8000` dans un navigateur.
