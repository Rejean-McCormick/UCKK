<?php
// This file is part of Moodle - https://moodle.org/.
// @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

/** Read-only public UCC study guide. No Moodle enrolment, archive import or private files. */
declare(strict_types=1);
require_once(__DIR__ . '/../../config.php');

use local_uckk\local\atlas\ucc_curriculum_registry as curriculum;
use local_uckk\local\atlas\ucc_mediatheque_registry as media;
use local_uckk\local\atlas\ucc_syllabus_registry as syllabi;

if (!empty($CFG->forcelogin)) {
    require_login();
}
$courseid = strtoupper(optional_param('course', '', PARAM_ALPHANUMEXT));
$view = optional_param('view', 'courses', PARAM_ALPHA);
$query = optional_param('q', '', PARAM_TEXT);
$pathway = optional_param('pathway', '', PARAM_RAW_TRIMMED);
if (!in_array($view, ['courses', 'references'], true)) {
    $view = 'courses';
}
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/uckk/ucc_study.php', ['view' => $view, 'course' => $courseid, 'q' => $query, 'pathway' => $pathway]);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('UCC — Plans et lectures');
$PAGE->set_heading('Univers-Cité chrétienne — Plans et lectures');
$plan = null;
if ($courseid !== '') {
    // Validate untrusted IDs before rendering or looking up source records.
    $known = array_column(curriculum::courses(), 'ucc_course_id');
    if (!in_array($courseid, $known, true)) {
        throw new moodle_exception('invalidparameter');
    }
    $plan = syllabi::get_course($courseid);
}
$pathwayids = array_column(curriculum::pathways(), 'pathway_id');
if ($pathway !== '' && !in_array($pathway, $pathwayids, true)) {
    throw new moodle_exception('invalidparameter');
}

echo $OUTPUT->header();
echo html_writer::tag('p', 'Plans éditoriaux à relire : lectures, activités et évaluations proposées. Ces plans sont distincts des espaces Moodle ouverts et ne valent pas validation académique.');
echo html_writer::tag('p', html_writer::link(new moodle_url('/local/uckk/ucc_study.php'), 'Les 110 plans') . ' · ' .
    html_writer::link(new moodle_url('/local/uckk/ucc_study.php', ['view' => 'references']), 'Références et cours associés') . ' · ' .
    html_writer::link(new moodle_url('/local/uckk/courses.php'), 'Espaces Moodle') . ' · ' .
    html_writer::link(new moodle_url('/local/uckk/mediatheque.php'), 'Médiathèque'));

if ($plan !== null) {
    echo $OUTPUT->heading(s($plan['course_id'] . ' — ' . $plan['title']), 2);
    echo html_writer::tag('p', s($plan['central_question']));
    echo html_writer::tag('p', s('Charge indicative : ' . $plan['estimated_hours'] . ' h ; sans crédit attribué.'));
    echo html_writer::tag('p', s($plan['language_support']));
    echo $OUTPUT->heading('Prérequis conseillés', 3);
    foreach ($plan['prerequisites'] as $id) {
        echo html_writer::tag('p', html_writer::link(new moodle_url('/local/uckk/ucc_study.php', ['course' => $id]), s($id)));
    }
    if (!$plan['prerequisites']) {
        echo html_writer::tag('p', 'Aucun ; initiation à la lecture critique.');
    }
    echo $OUTPUT->heading('Objectifs', 3);
    echo html_writer::alist(array_map('s', $plan['objectives']));
    echo $OUTPUT->heading('Lectures et passages', 3);
    foreach (media::media_for_course($courseid) as $item) {
        echo html_writer::start_div('card mb-3');
        echo html_writer::start_div('card-body');
        echo $OUTPUT->heading(s($item['title']), 4);
        echo html_writer::tag('p', s($item['creator'] . ' — ' . $item['language']));
        echo html_writer::tag('p', s($item['passage']));
        echo html_writer::tag('p', s($item['rationale']));
        echo html_writer::tag('p', s($item['teachingnote']));
        echo html_writer::tag('p', s($item['reviewnotice']));
        if (preg_match('~^https?://~i', $item['sourceurl'])) {
            echo html_writer::link(new moodle_url($item['sourceurl']), 'Consulter la source externe');
        }
        echo html_writer::end_div() . html_writer::end_div();
    }
    echo $OUTPUT->heading('Séances', 3);
    foreach ($plan['sessions'] as $session) {
        echo $OUTPUT->heading(s($session['sequence'] . '. ' . $session['title']), 4);
        echo html_writer::tag('p', s($session['activity']));
        echo html_writer::tag('p', s('Production : ' . $session['output']));
    }
    echo $OUTPUT->heading('Évaluation', 3);
    echo html_writer::tag('p', s($plan['assessment']['task']));
    echo html_writer::tag('p', s($plan['assessment']['deliverable']));
    echo html_writer::alist(array_map(static function(array $criterion): string {
        return s($criterion['criterion'] . ' : ' . $criterion['weight'] . ' %');
    }, $plan['assessment']['rubric']));
    echo html_writer::tag('p', s($plan['assessment']['integrity']));
    echo $OUTPUT->heading('Contexte et limites', 3);
    echo html_writer::alist(array_map('s', $plan['contextual_cautions']));
} else {
    echo html_writer::start_tag('form', ['method' => 'get', 'action' => new moodle_url('/local/uckk/ucc_study.php')]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => $view]);
    echo html_writer::tag('label', 'Rechercher un titre, un auteur ou un sujet', ['for' => 'ucc-study-q']);
    echo html_writer::empty_tag('input', ['type' => 'search', 'name' => 'q', 'id' => 'ucc-study-q', 'value' => $query, 'class' => 'form-control']);
    $options = ['' => 'Toutes les Voies'];
    foreach (curriculum::pathways() as $p) {
        $options[$p['pathway_id']] = $p['title'];
    }
    echo html_writer::tag('label', 'Voie', ['for' => 'ucc-study-pathway']);
    echo html_writer::select($options, 'pathway', $pathway, false, ['id' => 'ucc-study-pathway', 'class' => 'form-control']);
    echo html_writer::tag('button', 'Rechercher', ['type' => 'submit', 'class' => 'btn btn-primary my-3']);
    echo html_writer::end_tag('form');
    $count = 0;
    if ($view === 'references') {
        echo html_writer::tag('p', 'Fiches d’œuvres, d’éditions ou d’extraits. Les références de réserve et les éditions à vérifier restent identifiées. Aucun fichier privé de la Médiathèque n’est exposé ici.');
        foreach (media::works() as $work) {
            if (($work['visibility'] ?? '') !== 'public' || !in_array($work['status'] ?? '', ['active', 'edition_review'], true)) {
                continue;
            }
            if ($pathway !== '' && !in_array($pathway, $work['metadata']['pathway_refs'] ?? [], true)) {
                continue;
            }
            $text = $work['title'] . ' ' . ($work['creator'] ?? '');
            if ($query !== '' && core_text::strpos(core_text::strtolower($text), core_text::strtolower($query)) === false) {
                continue;
            }
            $count++;
            echo $OUTPUT->heading(s($work['title']), 3);
            echo html_writer::tag('p', s($work['citation']));
            echo html_writer::tag('p', s($work['teachingnote'] ?? 'Édition, traduction et conditions de réutilisation à vérifier.'));
            echo html_writer::tag('p', s('Langue : ' . $work['language'] . '. Droits : ' . $work['rightsstatus'] . '. Référence externe uniquement.'));
            if (preg_match('~^https?://~i', $work['sourceurl'])) {
                echo html_writer::tag('p', html_writer::link(new moodle_url($work['sourceurl']), 'Ouvrir la source'));
            }
            if (empty($work['metadata']['course_refs'])) {
                echo html_writer::tag('p', 'Réserve documentaire : aucune lecture actuellement prescrite.');
            }
            foreach ($work['metadata']['course_refs'] as $id) {
                echo html_writer::link(new moodle_url('/local/uckk/ucc_study.php', ['course' => $id]), s($id)) . ' ';
            }
        }
    } else {
        foreach (syllabi::document()['courses'] as $row) {
            if ($pathway !== '' && $pathway !== $row['pathway_id']) {
                continue;
            }
            $text = $row['course_id'] . ' ' . $row['title'] . ' ' . $row['central_question'];
            if ($query !== '' && core_text::strpos(core_text::strtolower($text), core_text::strtolower($query)) === false) {
                continue;
            }
            $count++;
            echo $OUTPUT->heading(html_writer::link(new moodle_url('/local/uckk/ucc_study.php', ['course' => $row['course_id']]), s($row['course_id'] . ' — ' . $row['title'])), 3);
            echo html_writer::tag('p', s($row['central_question']));
        }
    }
    echo html_writer::tag('p', s($count . ' résultat(s).'));
}
echo $OUTPUT->footer();
