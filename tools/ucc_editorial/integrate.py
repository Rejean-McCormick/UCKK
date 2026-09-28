from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
p=ROOT/'local/uckk/classes/local/atlas/ucc_mediatheque_registry.php'
s=p.read_text()
if "'reviewnotice' =>" in s:
    raise SystemExit('Integration already applied; do not run twice.')
s=s.replace("                'title' => (string)($work['title'] ?? ''),", """                'passage' => (string)($link['passage'] ?? ''),
                'language' => (string)($work['language'] ?? ''),
                'teachingnote' => (string)($work['teachingnote'] ?? ''),
                'reviewnotice' => ($link['role'] ?? '') === 'anchor'
                    ? 'Exemplaire légal requis ; table, bibliographie et passages du livre non vérifiés.'
                    : 'Lecture proposée ; collation du passage et contrôle de l’édition requis. Droits de copie non présumés.',
                'title' => (string)($work['title'] ?? ''),""")
s=s.replace("            foreach ($mediarefs as $link) {", "            $seenmedia = [];\n            foreach ($mediarefs as $link) {")
s=s.replace("                if (!isset($works[$ref])) {", """                if (isset($seenmedia[$ref]) || !in_array($link['role'] ?? '',
                        ['anchor', 'primary', 'supporting', 'editorial_complement'], true)
                        || empty($link['passage']) || empty($link['rationale'])) {
                    throw new \\coding_exception('Invalid UCC editorial reading: ' . $courseid);
                }
                $seenmedia[$ref] = true;
                if (!isset($works[$ref])) {""")
p.write_text(s)
p=ROOT/'local/uckk/classes/external/get_ucc_course.php'
s=p.read_text().replace('use local_uckk\\local\\atlas\\ucc_mediatheque_registry;', 'use local_uckk\\local\\atlas\\ucc_mediatheque_registry;\nuse local_uckk\\local\\atlas\\ucc_syllabus_registry;')
s=s.replace("        return [\n            'ucccourseid'", "        $plan = ucc_syllabus_registry::get_course($course['ucc_course_id']);\n        return [\n            'ucccourseid'",1)
s=s.replace("            'mediarefs' => array_map", """            'syllabus' => [
                'question' => $plan['central_question'],
                'status' => $plan['status'],
                'estimatedhours' => $plan['estimated_hours'],
                'objectives' => $plan['objectives'],
                'prerequisites' => $plan['prerequisites'],
                'languagesupport' => $plan['language_support'],
                'sessions' => $plan['sessions'],
                'assessmenttask' => $plan['assessment']['task'],
                'deliverable' => $plan['assessment']['deliverable'],
                'rubric' => $plan['assessment']['rubric'],
                'cautions' => $plan['contextual_cautions'],
            ],
            'mediarefs' => array_map""")
s=s.replace("                    'rightsstatus' => $item['rightsstatus'],", """                    'rightsstatus' => $item['rightsstatus'],
                    'passage' => $item['passage'],
                    'rationale' => $item['rationale'],
                    'language' => $item['language'],
                    'teachingnote' => $item['teachingnote'],
                    'reviewnotice' => $item['reviewnotice'],""")
s=s.replace("            'mediarefs' => new external_multiple_structure", """            'syllabus' => new external_single_structure([
                'question' => new external_value(PARAM_TEXT, 'Central question'),
                'status' => new external_value(PARAM_ALPHANUMEXT, 'Editorial status, not accreditation'),
                'estimatedhours' => new external_value(PARAM_INT, 'Indicative hours, not credits'),
                'objectives' => new external_multiple_structure(new external_value(PARAM_TEXT, 'Objective')),
                'prerequisites' => new external_multiple_structure(new external_value(PARAM_RAW_TRIMMED, 'Recommended course')),
                'languagesupport' => new external_value(PARAM_TEXT, 'Source language support'),
                'sessions' => new external_multiple_structure(new external_single_structure([
                    'sequence' => new external_value(PARAM_INT, 'Session order'),
                    'title' => new external_value(PARAM_TEXT, 'Session title'),
                    'activity' => new external_value(PARAM_TEXT, 'Activity'),
                    'output' => new external_value(PARAM_TEXT, 'Expected output'),
                ])),
                'assessmenttask' => new external_value(PARAM_TEXT, 'Assessment task'),
                'deliverable' => new external_value(PARAM_TEXT, 'Deliverable'),
                'rubric' => new external_multiple_structure(new external_single_structure([
                    'criterion' => new external_value(PARAM_TEXT, 'Criterion'),
                    'weight' => new external_value(PARAM_INT, 'Percentage weight'),
                ])),
                'cautions' => new external_multiple_structure(new external_value(PARAM_TEXT, 'Contextual caution')),
            ]),
            'mediarefs' => new external_multiple_structure""")
s=s.replace("                'rightsstatus' => new external_value(PARAM_ALPHANUMEXT, 'Rights status'),", """                'rightsstatus' => new external_value(PARAM_ALPHANUMEXT, 'Rights status'),
                'passage' => new external_value(PARAM_TEXT, 'Proposed reading locator'),
                'rationale' => new external_value(PARAM_TEXT, 'Course-specific selection rationale'),
                'language' => new external_value(PARAM_TEXT, 'Source language'),
                'teachingnote' => new external_value(PARAM_TEXT, 'Source context'),
                'reviewnotice' => new external_value(PARAM_TEXT, 'Remaining verification'),""")
p.write_text(s)
# Add prominent public links to the existing UCC definitions, without changing Math/UCKK routes.
for name in ['home','mediatheque']:
 p=ROOT/f'local/uckk/classes/local/public_pages/ucc/{name}.php'
 s=p.read_text().replace("            'quicklinks' => [", """            'quicklinks' => [
                ['label' => 'Plans et lectures', 'description' => '110 plans de séminaire : passages, objectifs, activités, évaluations et limites documentaires.', 'url' => '/local/uckk/ucc_study.php'],
                ['label' => 'Références par cours', 'description' => 'Consulter les sources externes sélectionnées et retrouver leurs cours.', 'url' => '/local/uckk/ucc_study.php?view=references'],""",1)
 s=s.replace("            'sections' => [", """            'sections' => [
                [
                    'title' => 'Lire les sources, comparer, argumenter',
                    'body' => 'Les 11 Voies disposent de 110 plans de séminaire. Chaque plan propose une question, des passages à lire, cinq séances, une production et un barème. Un socle conseillé relie Écriture, théologie, histoire, vie spirituelle et méthode critique. Les plans restent soumis à relecture ; les espaces Moodle ouverts sont indiqués séparément.',
                    'items' => ['Œuvres jusqu’en 1926 inclusivement ; exceptions : Teilhard de Chardin, Fratelli tutti et Le Dieu cosmique.', 'Distinguer source biblique, définition conciliaire, théologien, témoignage historique et interprétation éditoriale.', 'Les langues, éditions, passages et droits encore à vérifier sont signalés.'],
                ],""",1)
 s=s.replace('Le registre UCC relie maintenant les 110 cours à la Médiathèque. Chaque cours dispose de 3 à 6 références sélectionnées à partir des thèmes Kristal, avec des compléments éditoriaux explicitement signalés lorsque la couverture historique directe est trop faible.', 'Les 110 plans proposent des lectures choisies individuellement, avec passages et raisons pédagogiques. Le catalogue des références externes est accessible depuis Plans et lectures ; l’explorateur ci-dessous conserve les médias effectivement publiés dans Moodle.')
 s=s.replace('Les liens primaires reposent sur les thèmes Kristal communs entre le cours et la ressource.', 'Chaque lecture possède une justification propre au cours ; une proximité de mots-clés ne suffit pas.')
 p.write_text(s)
p=ROOT/'local/uckk/classes/local/public_pages/ucc/site.php'
s=p.read_text().replace("            ['key' => 'home',", "            ['key' => 'uccstudy', 'label' => 'Plans et lectures', 'url' => '/local/uckk/ucc_study.php'],\n            ['key' => 'home',",1)
p.write_text(s)
# The live course controller overwrites the static course intro; link to plans explicitly here.
p=ROOT/'local/uckk/courses.php'
s=p.read_text().replace("    $definition['cards'] = [];", """    if ($isucc) {
        $definition['quicklinks'][] = [
            'label' => 'Plans des 110 cours UCC',
            'description' => 'Lectures, objectifs, séances et évaluations proposées ; distincts des espaces Moodle ouverts.',
            'url' => '/local/uckk/ucc_study.php',
        ];
    }
    $definition['cards'] = [];""",1)
p.write_text(s)
