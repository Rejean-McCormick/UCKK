#!/usr/bin/env python3
"""Rebuild editorial projections from reviewed TSV selections; no Moodle database writes."""
import collections
import html
import json
from pathlib import Path
from urllib.parse import urlparse

ROOT = Path(__file__).resolve().parents[2]
ATLAS = ROOT / 'local/uckk/atlas'
HERE = Path(__file__).resolve().parent
DATE = '2026-09-28'

def read(name):
    return json.loads((ATLAS / name).read_text())
def write(name, obj):
    (ATLAS / name).write_text(json.dumps(obj, ensure_ascii=False, indent=2) + '\n')

curr = read('ucc_curriculum_registry.json')
refs = read('ucc_mediatheque_reference_registry.json')
# Safe repeat builds: existing identifiers are preserved and new sources upserted.
works = {w['work_ref']: w for w in refs['works']}
keys = {k.removeprefix('ucc.work.theophile.'): k for k in works if k.startswith('ucc.work.theophile.')}
keys.update({'cosmic':'ucc.work.le-dieu-cosmique-2008', 'spinoza':'ucc.work.spinoza-ethique-appuhn-1913',
             'pascal-fr':'ucc.work.pascal-pensees-brunschvicg', 'bergson':'ucc.work.bergson-creative-evolution-1907',
             'james':'ucc.work.james-varieties-1902'})
for line in (HERE / 'new_sources.tsv').read_text().splitlines():
    key, title, author, year, lang, url, kind, note = line.split('|')
    ref = 'ucc.work.editorial.' + key
    keys[key] = ref
    works[ref] = dict(work_ref=ref, title=title, creator=author, worktype='text', status='active', visibility='public',
        sourceurl=url, language=lang, rightsstatus='unknown',
        rightsstatement='Référence externe seulement. Aucun droit de copie locale ou de redistribution déduit de la date du texte.',
        citation=f'{author}, {title}. Source : {url}. Notice contrôlée le {DATE}; édition et traduction selon la notice du fournisseur.',
        source_provider=urlparse(url).netloc, teachingnote=note,
        bibliographic=dict(original_date_label=('Édition de traduction : ' if key == 'bible' else 'Date ou borne historique : ') + year,
            historical_cutoff_upper_bound=int(year), date_precision='edition' if key=='bible' else 'year_or_approximation_see_note',
            edition_year={'bible':1923,'darwin':1872,'therese':1912,'sales':1909}.get(key),
            translation_year=None, translator='Augustin Crampon et réviseurs' if key=='bible' else None,
            original_work_date=None if key=='bible' else int(year),
            edition_review='historical_edition_identified' if key in ('bible','darwin','therese','sales') else 'to_verify',
            distinction='Original work, revision, translation and digital host dates are distinct.'),
        reference_review=dict(status='source_identified', checked_on=DATE, evidence_urls=[url],
            scope='Page ou notice identifiée ; ce contrôle ne certifie pas chaque passage, chaque traduction ou les droits territoriaux.'),
        intellectual_status=kind, metadata=dict(provenance='ucc_editorial_revision_2026_09_28',
            collection_eligibility='historical_cutoff', kristal_theme_refs=[], course_refs=[], pathway_refs=[], course_link_roles={}))

# Correct inherited metadata, without silently rewriting the underlying Kristal corpus.
works[keys['aug-doctrine']]['title'] = 'De la doctrine chrétienne, livre II'
works[keys['chesterton']]['metadata']['original_date_label'] = '1908 ; antérieur à sa conversion au catholicisme (1922), et non au christianisme.'
for key in ('aug-city11', 'aug-city14'):
    works[keys[key]]['metadata']['original_date_label'] = '413–427, rédaction de La Cité de Dieu ; datation précise du livre à documenter.'
for w in works.values():
    meta = w.setdefault('metadata', {})
    meta['course_refs'], meta['pathway_refs'], meta['course_link_roles'] = [], [], {}
    meta.setdefault('kristal_theme_refs', [])
    w.setdefault('source_provider', urlparse(w.get('sourceurl','')).netloc)
    if 'language' not in w:
        host = w['source_provider']
        w['language'] = 'fr' if host in ('www.aelf.org','fr.wikisource.org','classiques.uqam.ca','www.vatican.va') else 'en'
        if 'amis-de-teilhard.org' in host:
            w['language'] = 'mul'
        w['language_review'] = 'inferred_from_source_requires_confirmation'
    else:
        w.setdefault('language_review', 'source_notice')
    w.setdefault('citation', f"{w.get('creator','Auteur à préciser')}, {w['title']}. {meta.get('original_date_label','Datation à préciser')}. {w.get('sourceurl','')}")
    w.setdefault('bibliographic', dict(original_date_label=meta.get('original_date_label','À préciser'),
        original_work_date=w.get('publicationyear'), edition_year=None, translation_year=None, translator=None,
        edition_review='to_verify'))
    w.setdefault('reference_review', dict(status='inherited_not_rechecked', checked_on=None,
        evidence_urls=[w.get('sourceurl','')], scope='Référence héritée ; résolution et édition non revérifiées dans cette passe.'))
    w.setdefault('intellectual_status', 'scripture' if w.get('worktype')=='scripture' else 'historical_author_text')
    w['access_policy'] = dict(mode='external_link_only', mirror_allowed=False,
        note='Accès chez le fournisseur ; vérifier langue, édition et conditions de réutilisation avant toute copie.')
    w.setdefault('formats', [{'format':'source_page', 'url':w.get('sourceurl',''), 'status':'see_provider'}])
    w['metadata'].setdefault('work_family_ref', w['work_ref'])
    if '/summa/' in w.get('sourceurl',''):
        meta['work_family_ref'] = 'ucc.family.summa-theologiae'
        w['record_scope'] = 'question'
    elif 'aelf.org' in w.get('sourceurl',''):
        meta['work_family_ref'] = 'ucc.family.bible'
        w['record_scope'] = 'chapter'
        w['status'] = 'edition_review'
        meta['preferred_historical_reference'] = keys['bible']
        w['teachingnote'] = 'Traduction liturgique moderne ; conservée pour traçabilité, remplacée par Crampon 1923 dans les lectures prescrites.'
    else:
        w.setdefault('record_scope','work_or_selected_part')
    if 'Pensées' in w['title']:
        meta['work_family_ref'] = 'ucc.family.pascal-pensees'
    if 'Cité de Dieu' in w['title'] or 'Cité de Dieu' in w['title'].replace('Cité','Cité'):
        meta['work_family_ref'] = 'ucc.family.augustine-city'
    if w['work_ref'] in (keys.get('gaunilo'),keys.get('anselm-reply')):
        meta['work_family_ref'] = 'ucc.family.gaunilo-anselm-exchange'
    if w['work_ref'] in (keys.get('teilhard-noosphere'),keys.get('teilhard-omega')):
        meta['work_family_ref'] = 'ucc.family.teilhard-phenomene'
works[keys['bible']]['metadata']['work_family_ref'] = 'ucc.family.bible'
for key in ('maritain-art','stein','cusa-text'):
    works[keys[key]]['status'] = 'edition_review'
    works[keys[key]]['bibliographic']['edition_review'] = 'later_revision_or_translation_requires_review'
works[keys['maritain-art']]['teachingnote'] = 'La notice héritée mentionne une révision de 1935. Retirée des lectures prescrites jusqu’à identification d’une édition admissible avant 1927.'
works[keys['th-mary']]['teachingnote'] = 'III, q. 27, a. 2 : distinguer la position médiévale sur la sanctification de la définition de la préservation du péché originel dans Ineffabilis Deus (1854).'
works[keys['cosmic']]['intellectual_status'] = 'comparative_anchor_not_magisterial'
works[keys['cosmic']]['access_policy']['note'] = 'Emprunt ou exemplaire légal requis ; table et bibliographie non consultées. Pas de citations ou de pagination fabriquées.'
works[keys['ft']]['intellectual_status'] = 'magisterial'
works[keys['ft']]['formats'] = [
    {'format':'html','url':works[keys['ft']]['sourceurl'],'status':'inherited_official_url'},
    {'format':'pdf','url':'https://www.vatican.va/content/francesco/fr/encyclicals/documents/papa-francesco_20201003_enciclica-fratelli-tutti.pdf','status':'user_supplied_official_url'}]
refs['policy'].update(editorial_revision='2026-09-28',
    eligibility_basis='Original work before 1927. Later substantive revisions need review; modern translations are separately flagged, not presumed historical or free.',
    exceptions_structured=[{'scope':'author','name':'Pierre Teilhard de Chardin'}, {'scope':'work','work_ref':keys['ft']}, {'scope':'work','work_ref':keys['cosmic']}],
    minimum_links_is_not_quality=True, orphan_policy='Unselected references remain discoverable as reserves; no artificial course association.',
    provenance_rule='Book-derived references require a verified passage in the book. All additional selections are UCC editorial choices.',
    linking='Explicit editorial course selections; semantic intersections are secondary signals, never sufficient evidence of relevance.')

rubric = [
    {'criterion':'Fidélité aux passages et citations localisées','weight':30},
    {'criterion':'Contexte, genre et statut des sources','weight':25},
    {'criterion':'Argumentation et réponse loyale à une objection','weight':25},
    {'criterion':'Clarté, limites et traçabilité','weight':20}]
plans, rows = [], []
index = {c['ucc_course_id']:c for c in curr['courses']}
newtitles = {
    'THE-101':'Lire la Bible : Ancien et Nouveau Testament, canon et interprétation',
    'THE-106':'Sacrements, initiation, Eucharistie et vie liturgique',
    'THE-108':'Église, conciles, ministères et réception historique',
    'THE-109':'Vie spirituelle : prière, monachisme et mystique',
    'EDU-110':'Projet : histoire située d’une œuvre éducative',
    'SAN-109':'Histoire comparée des œuvres et pratiques de soin',
    'SAN-110':'Projet : microhistoire documentée du soin',
    'ART-106':'Théâtre, récit biblique et atelier d’adaptation',
    'LET-106':'Littératures chrétiennes de langue française : œuvres et réceptions',
    'EDU-109':'Méthode critique et atelier de vérification à l’ère de l’IA',
    'ECO-109':'Transformation du travail : principes historiques et études de cas',
}
for line in (HERE/'course_design.tsv').read_text().splitlines():
    code, question, r1, r2, r3, task, caveat = line.split('|')
    cid = 'UCC-'+code
    course = index[cid]
    if code in newtitles:
        course.setdefault('previous_title',course['title'])
        course['title'] = newtitles[code]
    media = []
    for i, spec in enumerate((r1,r2,r3)):
        key, passage, rationale = spec.split('@')
        ref = keys[key]
        w = works[ref]
        isanchor = key=='cosmic'
        media.append(dict(media_ref=ref, role='anchor' if isanchor else ('primary' if i==0 else 'supporting'),
            rationale=rationale, passage=passage, selection_method='explicit_editorial_selection',
            matched_theme_refs=sorted(set(course['kristal_theme_refs']) & set(w['metadata']['kristal_theme_refs'])),
            provenance='ucc_editorial_complement_not_extracted_from_anchor_book' if code.startswith('COS') and not isanchor else 'ucc_editorial_selection',
            reading_status='requires_lawful_copy_and_passage_identification' if isanchor else 'proposed_reading_locator_to_collate',
            edition_status=w['bibliographic']['edition_review']))
    if code.startswith('COS') and all(x['media_ref']!=keys['cosmic'] for x in media):
        media.append(dict(media_ref=keys['cosmic'],role='anchor',rationale='Ouvrage-charnière nommé ; ne présume pas que les compléments figurent dans sa bibliographie.',
            passage='Repérage dans un exemplaire légal ; chapitres et pages non vérifiés.', selection_method='explicit_editorial_selection',
            matched_theme_refs=sorted(set(course['kristal_theme_refs']) & set(works[keys['cosmic']]['metadata']['kristal_theme_refs'])),provenance='anchor_work',reading_status='requires_lawful_copy_and_passage_identification',edition_status='legal_copy_required'))
    number=int(code[-3:])
    prerequisites=[]
    if number>101:
        prerequisites.append('UCC-'+code[:3]+'-101')
    if number==110:
        prerequisites=['UCC-'+code[:3]+'-'+str(n) for n in range(101,110)]
    plan = dict(course_id=cid,pathway_id=course['pathway_id'],title=course['title'],central_question=question,
        status='editorial_draft_for_peer_review', peer_review_status='not_yet_reviewed',
        level='integration' if number==110 else ('introduction' if number<=103 else 'approfondissement'),
        prerequisites=prerequisites, prerequisite_policy='recommended_or_equivalent_experience',
        estimated_hours=18 if number==110 else 12, credits=None,
        language_of_instruction='fr', language_support='Les sources peuvent être en anglais, latin ou italien. Prévoir aide linguistique ; toute traduction pédagogique nouvelle doit être signalée.',
        objectives=[f'Expliquer la question « {question} » en distinguant les termes et les contextes.',
            f"Analyser {media[0]['passage']} dans {works[media[0]['media_ref']]['title']} et justifier une interprétation par un passage localisé.",
            'Comparer les sources en reconstruisant au moins une objection et en distinguant faits, normes et interprétations.'],
        readings=media, sessions=[
            {'sequence':1,'title':'Situer la question et les témoins','activity':'Identifier auteur, genre, date de l’œuvre, édition, langue et destinataires des trois lectures.','output':'Trois notices de source et un lexique de cinq termes.'},
            {'sequence':2,'title':'Lecture principale : '+works[media[0]['media_ref']]['title'],'activity':media[0]['rationale']+' — '+media[0]['passage'],'output':'Un commentaire de 400 à 600 mots avec repères de passage.'},
            {'sequence':3,'title':'Confronter les lectures','activity':media[1]['rationale']+' ; '+media[2]['rationale'],'output':'Tableau comparatif et objection reconstruite loyalement.'},
            {'sequence':4,'title':'Atelier : '+question,'activity':task,'output':'Première version du livrable et liste des informations non établies.'},
            {'sequence':5,'title':'Réviser et défendre','activity':'Soumettre le livrable à une objection documentée, puis corriger sources et conclusions.','output':'Version révisée accompagnée d’un journal de corrections.'}],
        assessment=dict(task=task,deliverable='Dossier de 1 500 à 2 000 mots ou équivalent annoté' if number==110 else 'Production commentée de 800 à 1 200 mots ou équivalent annoté',rubric=rubric,
            integrity='Chaque citation comporte un passage vérifié ; signaler traduction personnelle, aide IA et toute source non consultée. Aucun résultat automatique ne vaut validation académique.'),
        contextual_cautions=[caveat],
        readiness=dict(structure='complete_editorial_plan',source_reading='selected_passages_not_exhaustively_collated',moodle_activity='not_created_by_this_revision',
            note='Plan de séminaire, non cours intégral enregistré, manuel rédigé ou validation académique.'))
    if code.startswith('COS'):
        plan['contextual_cautions'].append('Les compléments ne sont pas attribués à la bibliographie du Dieu cosmique. Les activités comparatives restent possibles ; les activités propres au livre attendent un exemplaire légal.')
    if code.startswith(('SAN','SCI','GOV','ECO','OEU')):
        plan['contextual_cautions'].append('Le fonds historique ne décrit pas automatiquement les pratiques, connaissances ou règles actuelles ; toute application réelle nécessite une documentation contemporaine distincte.')
    plans.append(plan)
    rows.append(dict(course_id=cid,pathway_id=course['pathway_id'],course_title=course['title'],kristal_theme_refs=course['kristal_theme_refs'],media_refs=media))
    course['syllabus_ref'] = cid
    course['editorial_status'] = 'structured_plan_pending_peer_review'
    if 'corpus_readiness' in course:
        course['corpus_readiness']['scope_note'] = 'Mesure héritée du corpus Kristal initial ; non recalculée pour les nouvelles lectures, sans valeur de validation pédagogique.'
    else:
        course['corpus_readiness'] = dict(level='not_assessed',coverage_score=0,method='not_assessed',interpretation='Aucun score de qualité n’est inféré du nombre de liens.')
    for m in media:
        meta=works[m['media_ref']]['metadata']
        meta['course_refs'].append(cid)
        meta['pathway_refs'].append(course['pathway_id'])
        meta['course_link_roles'][m['role']] = meta['course_link_roles'].get(m['role'],0)+1

for w in works.values():
    meta=w['metadata']
    meta['course_refs']=sorted(set(meta['course_refs']))
    meta['pathway_refs']=sorted(set(meta['pathway_refs']))
    meta['editorial_theme_refs']=sorted({t for cid in meta['course_refs'] for t in index[cid]['kristal_theme_refs']})
    meta['editorial_theme_provenance']='Projection from selected courses, not a validated Kristal assertion.'
    w['catalogue_role']='selected_reading' if meta['course_refs'] else 'reserve_reference'

stats=dict(courses=len(plans),courses_with_media=len(rows),distinct_linked_media=len({m['media_ref'] for r in rows for m in r['media_refs']}),
    total_course_media_links=sum(len(r['media_refs']) for r in rows),min_media_per_course=min(len(r['media_refs']) for r in rows),
    max_media_per_course=max(len(r['media_refs']) for r in rows),average_media_per_course=round(sum(len(r['media_refs']) for r in rows)/len(rows),2),
    total_reference_records=len(works),reserve_reference_records=sum(not w['metadata']['course_refs'] for w in works.values()),
    count_note='Reference records include excerpts, editions and works. No claim that this count equals distinct works.',pathways=[])
for p in curr['pathways']:
    rs=[r for r in rows if r['pathway_id']==p['pathway_id']]
    stats['pathways'].append(dict(pathway_id=p['pathway_id'],course_count=len(rs),linked_course_count=len(rs),distinct_media_count=len({m['media_ref'] for r in rs for m in r['media_refs']}),min_media_per_course=min(len(r['media_refs']) for r in rs),max_media_per_course=max(len(r['media_refs']) for r in rs)))
    p['editorial_status']='structured_plans_pending_peer_review'

foundation = [
 ('Écriture et genres','UCC-THE-101'),('Trinité et christologie','UCC-THE-103'),('Incarnation et conciles','UCC-THE-104'),
 ('Sacrements et vie liturgique','UCC-THE-106'),('Histoire et institutions ecclésiales','UCC-THE-108'),
 ('Prière et vie spirituelle','UCC-THE-109'),('Méthode des sources','UCC-OEU-105'),('Argument et objection','UCC-PHI-101')]
syllabi=dict(schema_version='UCC-SYLLABI-1.0',universe_id='ucc',revision_date=DATE,
    editorial_policy='Read primary passages in context; identify genre and authority; compare and argue. No automatic equivalence by shared keyword.',
    recommended_foundation=[dict(label=a,course_ref=b) for a,b in foundation],
    publication_status='public_editorial_plans_not_moodle_course_instances',
    open_gaps=[
        'Table des matières et bibliographie du Dieu cosmique non consultées : aucune référence prétendument extraite.',
        'Collation exhaustive des passages et contrôle des éditions/traductions encore requis avant validation académique.',
        'Ordre, pénitence et histoire complète des sacrements demandent un corpus plus étendu.',
        'Partitions, enregistrements et histoire du cinéma ne forment pas encore un fonds constitué.',
        'Femmes auteurs, christianismes orientaux, Afrique, Asie, Amériques et voix autochtones restent insuffisamment représentés.',
        'Le fonds historique ne prétend pas couvrir la science, la médecine, le droit ou le magistère contemporains.'], courses=plans)
refs['works']=list(works.values())
refs['source_corpus']['note']='Inherited semantic corpus; 27 additional editorial source records are outside its validated assertions.'
refs['editorial_stats']=stats
write('ucc_curriculum_registry.json',curr)
write('ucc_mediatheque_reference_registry.json',refs)
write('ucc_course_syllabi.json',syllabi)
write('ucc_mediatheque_course_links.json',dict(schema_version='UCC-MEDIATHEQUE-LINKS-1.0',universe_id='ucc',authority='ucc_editorial_course_selection',
    generated_from=dict(curriculum='atlas/ucc_curriculum_registry.json',mediatheque='atlas/ucc_mediatheque_reference_registry.json',selection='tools/ucc_editorial/course_design.tsv'),
    linking_policy=dict(primary_method='Explicit passage selection and course-specific rationale; no keyword-only selection.',minimum_media_per_course=3,maximum_media_per_course=6,
        roles=['anchor','primary','supporting','editorial_complement'],collection_cutoff='1926 inclusive, with three explicit exceptions.',semantic_note='New editorial tags are separate from immutable Kristal theme evidence.'),stats=stats,course_links=rows))

# Human-readable handbook: exact projections from the same canonical plans.
lines=['# UCC — Plans de séminaire et lectures commentées', '', 'Révision du 28 septembre 2026. 11 Voies, 110 plans. Statut : propositions éditoriales à relire ; aucune reconnaissance académique ou ecclésiale revendiquée.', '',
       'Les séances, activités et barèmes sont rédigés pour ce parcours. Les textes restent chez leurs fournisseurs. Les repères de lecture sont proposés et doivent être collationnés dans l’édition utilisée.', '', '## Socle conseillé', '']
lines += [f'- {label} : {ref}.' for label,ref in foundation]
for p in curr['pathways']:
    lines+=['', '## '+p['title'], '']
    for plan in [x for x in plans if x['pathway_id']==p['pathway_id']]:
        lines+=['### '+plan['course_id']+' — '+plan['title'],'', '**Question :** '+plan['central_question'],'',
            f"Niveau : {plan['level']}. Charge indicative : {plan['estimated_hours']} h, sans crédit attribué. Prérequis conseillés : {', '.join(plan['prerequisites']) or 'aucun ; initiation à la lecture critique' }.",'','**Objectifs**','']
        lines += ['- '+x for x in plan['objectives']]
        lines+=['','**Lectures et raisons du choix**','']
        for m in plan['readings']:
            w=works[m['media_ref']]
            lines += [f"- **{m['role']} — {w['title']}**, {w.get('creator','Auteur à préciser')} ({w['language']}). [Source]({w['sourceurl']}). Passage : {m['passage']}. {m['rationale']}. Statut : {m['reading_status']}; édition : {m['edition_status']}."]
        lines += ['', '**Séances**','']
        lines += [f"{s['sequence']}. **{s['title']}** — {s['activity']} Livrable : {s['output']}" for s in plan['sessions']]
        lines += ['', '**Évaluation :** '+plan['assessment']['task']+' '+plan['assessment']['deliverable']+'.', '',
            'Barème : fidélité aux sources 30 %, contexte et statut 25 %, argumentation contradictoire 25 %, clarté et traçabilité 20 %.','','**Précautions de lecture**','']
        lines += ['- '+x for x in plan['contextual_cautions']]
lines += ['', '## Lacunes explicites', '']+['- '+x for x in syllabi['open_gaps']]
(ROOT/'docs/ucc/PLANS_110_COURS.md').write_text('\n'.join(lines)+'\n')
report=f'''# Révision UCC — 28 septembre 2026

## Résultat mesurable

- 11 Voies et 110 identifiants conservés.
- 110 plans de séminaire, 550 séances proposées, objectifs, activités, évaluations et barème commun.
- {stats['total_reference_records']} fiches documentaires, dont 27 nouvelles sources repérées.
- {stats['total_course_media_links']} liens éditoriaux ; {stats['distinct_linked_media']} fiches effectivement sélectionnées ; {stats['reserve_reference_records']} références conservées en réserve.
- Chaque lecture précise un passage et une raison propre au cours. Les références non pertinentes ne sont plus rattachées pour atteindre un quota.
- Les œuvres, extraits et éditions ne sont pas comptés comme autant d’œuvres indépendantes.

## Corrections structurantes

Bible Crampon 1923 et lectures de l’Ancien Testament ; Trinité, Incarnation, conciles, sacrements et vie monastique renforcés. Les Arts font étudier Dante, Racine, des planches de Doré et Ruskin. La méthode historique remplace les rapprochements abstraits dans les cours d’archives et d’audit. Le soin s’appuie sur un corpus historique clairement distinct d’un protocole clinique actuel.

Thomas III, q. 27 est confronté à Ineffabilis Deus (1854). La sentence de Galilée conserve sa place dans l’histoire des controverses. L’édition révisée de Maritain de 1935 et certaines traductions modernes sont signalées pour examen. Aucune nouvelle exception à la politique de 1926 n’est introduite.

La Voie cosmique conserve l’ouvrage de Languirand et Proulx comme ancre. Ses compléments ne sont pas faussement présentés comme extraits de sa bibliographie. Le plan de livre et les pages non consultées ne sont pas inventés.

## Nature de la livraison

Il s’agit de 110 plans éditoriaux détaillés, pas de 110 manuels rédigés ou activités Moodle déjà déployées. Les estimations horaires ne sont pas des crédits. Une relecture savante, une collation des passages et une validation d’enseignement restent nécessaires. Les indicateurs Kristal antérieurs sont conservés comme mesures héritées, sans leur attribuer une nouvelle validation.

## Sources et accès

Les 27 nouveaux points d’accès sont consignés dans tools/ucc_editorial/new_sources.tsv et les notices JSON. Les sources historiques héritées ne sont pas déclarées toutes revérifiées. Une page accessible ne prouve pas un droit de redistribution. Aucune copie des livres n’est ajoutée.

## Limites prioritaires

'''+ '\n'.join('- '+x for x in syllabi['open_gaps'])+'\n'
(ROOT/'docs/ucc/RAPPORT_REFONTE.md').write_text(report)
(ROOT/'UCC_MEDIATHEQUE_COURSE_LINK_AUDIT.md').write_text(report+'\nDétail des 110 cours : docs/ucc/PLANS_110_COURS.md.\n')
print(json.dumps(stats,ensure_ascii=False,indent=2))
