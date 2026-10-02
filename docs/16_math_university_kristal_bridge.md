# 16 — MathKristal → Univers-Cité des mathématiques → SemantiK Architect

## Statut

Contrat spécialisé adopté pour l’Univers-Cité des mathématiques : `uckk.math-university-projection/1.0.0`.

Ce contrat spécialise `uckk.univers-cite-projection/1.0.0` sans en changer la frontière : le Kristal demeure l’autorité épistémique; UCKK construit une projection pédagogique reconstruisible; Moodle conserve l’état transactionnel et humain.

Source actuellement épinglée :

- artefact : `mathkristal`;
- release : `1.6.0`;
- Structured Epistemic State : `sha256:35aef425c4029076e1af3ada6a52413e975ed402ce0c64644943b11a5849ff71`;
- Referent Registry : `sha256:140aad0f12061f1717c0b9f960871b515279f68052f93c54a4d034f06fb37e1a`;
- lock UCKK : `local/uckk/atlas/math_kristal_lock.json`.

## Décision d’architecture

La chaîne canonique est :

```text
MathKristal
  référents • assertions • définitions • propositions • preuves
  dépendances • domaines • chemins pédagogiques • sources/provenance
        |
        v
UCKK Math University Projector
  sélection/topologie pédagogique déterministe
  voies • étapes/cours • page specs • obligations de communication
        |
        +--------------------+
        |                    |
        v                    v
SemantiK Architect        Médiathèque
articulation linguistique corpus + source_refs
        |
        v
artefacts multilingues compilés
        |
        v
Moodle / local_uckk
navigation • usagers • inscriptions • évaluations • notes • remises
```

La formule courte est :

> **Kristal porte la connaissance; UCKK la projette en université; SemantiK Architect l’articule; Moodle l’exécute pédagogiquement.**

## Autorités et responsabilités

### MathKristal

MathKristal possède l’autorité sur :

- l’identité des référents (`urn:mathkristal:*`);
- les assertions mathématiques;
- les définitions, propositions, preuves et contre-exemples;
- les dépendances logiques et pédagogiques;
- les domaines et articulations;
- les sources et la provenance;
- les chemins pédagogiques publiés comme projections du corpus.

Une matérialisation Moodle ne devient jamais un second canon mathématique.

### UCKK Math University Projector

Le projector possède les choix pédagogiques de consommation :

- quelles voies sont exposées;
- comment une voie devient une séquence d’étapes/cours;
- quel référent est le sujet d’une page;
- quelles assertions, preuves, prérequis et sources doivent être communiqués;
- l’ordre des obligations de communication;
- le lien entre projection sémantique et objets Moodle.

Le projector **ne modifie pas** les dépendances logiques du Kristal. Un chemin pédagogique est une vue d’apprentissage, pas une nouvelle preuve ni une nouvelle ontologie.

### SemantiK Architect

SemantiK Architect est un **réalisateur linguistique**, pas un sélecteur de contenu.

UCKK lui transmet des obligations déjà sélectionnées. SA peut réorganiser et réaliser ces obligations selon son contrat, mais ne peut pas :

- inventer une assertion mathématique;
- retirer silencieusement une obligation;
- choisir le curriculum;
- remplacer une dépendance Kristal;
- pivoter implicitement par une langue intermédiaire.

La génération canonique de pages suit la doctrine SA « articulation, pas sélection de contenu ». Une langue n’est utilisable en production que lorsque le couple langue/profil est `RELEASED` pour un RuntimeSet immuable et admissible.

Le chemin canonique n’exige aucun LLM au runtime. L’IA peut assister l’enrichissement, la conception d’exercices, la proposition de parcours ou le développement lexical, mais les pages servies peuvent être compilées à l’avance de façon déterministe.

### Moodle / local_uckk

Moodle possède l’état pédagogique et humain :

- usagers et permissions;
- inscriptions;
- cohortes;
- notes;
- remises;
- annotations;
- progression;
- décisions éditoriales locales;
- activités et évaluations.

Une reconstruction Kristal → projection ne doit jamais écraser cet état.

## Artefacts UCKK

### Lock du Kristal

`local/uckk/atlas/math_kristal_lock.json` épingle la release, le `state_id`, le Referent Registry et les hashes des fichiers structurants consommés.

### Projection universitaire

`local/uckk/atlas/math_university_projection.json` est un artefact dérivé et reconstruisible. La projection actuelle contient :

- 14 voies dérivées de `corpus/pedagogical-paths.json`;
- 72 étapes/cours projetés;
- 72 spécifications de pages;
- un lien stable vers le référent Kristal de chaque cours;
- les assertions pertinentes par rôle épistémique;
- les prérequis pédagogiques;
- les références de définitions et de preuves;
- les `source_refs` de provenance;
- les obligations de communication à réaliser ensuite.

Le fichier est produit par :

```text
tools/uckk-ops/kristal/build_math_university_projection.py
```

Le build est offline, déterministe et échoue si un chemin pédagogique pointe vers un référent absent.

### Snapshot historique

`local/uckk/atlas/math_curriculum_registry.json` reste lisible comme snapshot de compatibilité `MATH-CURRICULUM-2.0`. Il n’est plus l’autorité épistémique ni la source de la navigation publique Math.

Les contraintes codées « exactement 8 voies / exactement 64 cours » sont retirées. Une migration des espaces Moodle historiques est une opération séparée et doit préserver tout état humain.

## Page specification

Une page projetée garde au minimum :

```json
{
  "page_spec_id": "math.page.eul.105",
  "math_course_id": "MATH-EUL-105",
  "subject": {
    "ref": "urn:mathkristal:proposition:euler-formula",
    "label": "Formule d’Euler"
  },
  "selection": {
    "assertion_refs": ["sha256:..."],
    "definition_refs": [],
    "proof_refs": ["urn:mathkristal:proof:euler-series"],
    "pedagogical_prerequisite_refs": [],
    "source_refs": ["urn:mathkristal:source:euler-analysis-v0.2.0"]
  },
  "communication_obligations": [
    {"kind": "heading", "required": true},
    {"kind": "referent_identity", "required": true},
    {"kind": "assertion_projection", "required": true},
    {"kind": "proof_links", "required": true},
    {"kind": "source_provenance", "required": true}
  ]
}
```

La page spec ne contient pas nécessairement du texte final. Elle contient la **matière sémantique obligatoire** à articuler.

## Multilingue

Le modèle multilingue n’est pas :

```text
Kristal -> page française -> traduction anglaise -> traduction espagnole
```

Il est :

```text
                    même page spec
                         |
          +--------------+--------------+
          |              |              |
          v              v              v
       plan FR         plan EN         plan ES
          |              |              |
      RuntimeSet FR   RuntimeSet EN   RuntimeSet ES
          |              |              |
          +--------------+--------------+
                         |
                 artefacts de page
```

Chaque langue réalise le même sens sélectionné. L’ajout d’une langue ne requiert pas de refaire la recherche mathématique; il requiert un profil SA/GF qualifié pour cette langue.

## Médiathèque

La médiathèque Math référence désormais MathKristal comme **corpus de connaissance épinglé**. Les documents d’ancrage historiques restent disponibles, mais ne sont plus l’unique source structurelle du curriculum.

Le lien page/cours → médiathèque doit être construit en priorité à partir des `source_refs` du Kristal et de son Source Store. Les vieux `concept_refs` restent un mécanisme de compatibilité pour le snapshot `MATH-CURRICULUM-2.0`.

## Matérialisation Moodle

La matérialisation future doit être idempotente et utiliser des identités stables :

```text
kristal_state_id
projection_schema_version
pathway_id
math_course_id
page_spec_id
kristal_ref
projection_hash
render_runtime_set_id
render_result_hash
```

Une synchronisation peut créer ou mettre à jour les surfaces **dérivées**. Elle ne peut pas supprimer ou écraser automatiquement l’état humain.

### Binding du catalogue public Moodle

Le catalogue public Math reconnaît une matérialisation projetée par son identifiant stable `MATH-<CODE>-1xx` présent dans le `shortname` ou le `idnumber` du cours. Ce binding est **en lecture seule** : il enrichit la carte publique avec la voie projetée, le référent Kristal et la `page_spec_id`, sans modifier l’objet Moodle. Un cours historique sans identifiant projeté peut rester visible pendant la migration, mais il n'est pas présenté comme lié au Kristal courant.

L'endpoint AJAX de recherche applique la même règle en contexte Math : il recherche les cours `MATH-*` et utilise les voies de `math_university_projection.json` pour les filtres publics. Les autres Univers-Cités conservent leur comportement `UCKK-*`.

## Événement Kristal

L’écosystème peut recevoir `kristal.artifact.ready/2.0.0` via l’Interaction Kernel. Cet événement est un déclencheur de vérification/build, pas une permission de modifier Moodle sans validation de lock, de contrat et de politique de matérialisation.

Pipeline recommandé :

```text
kristal.artifact.ready
  -> vérifier state_id / hashes
  -> reconstruire math_university_projection.json
  -> calculer le diff
  -> compiler les langues SA admises
  -> valider
  -> matérialiser les changements dérivés dans Moodle
```

## Invariants

1. MathKristal demeure l’autorité épistémique.
2. La projection UCKK est reconstruisible depuis un Kristal épinglé.
3. Une voie pédagogique n’altère jamais la dépendance logique du corpus.
4. SemantiK Architect articule; il ne sélectionne pas les faits.
5. Aucune langue non `RELEASED` n’est servie comme succès silencieux.
6. Aucun LLM n’est requis pour servir les pages canoniques compilées.
7. Moodle demeure propriétaire de l’état utilisateur et pédagogique transactionnel.
8. Une reconstruction ne détruit jamais l’état humain.
9. Les sources sont conservées par `source_refs` et provenance, pas recopiées comme autorité locale.
10. Le snapshot 8 voies / 64 cours est une compatibilité historique, pas une contrainte architecturale.
