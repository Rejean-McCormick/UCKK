#!/usr/bin/env python3
"""Content contract and regression checks; no external requests or Moodle state."""
import collections
import json
from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
A=ROOT/'local/uckk/atlas'
def read(n): return json.loads((A/n).read_text())
c=read('ucc_curriculum_registry.json'); r=read('ucc_mediatheque_reference_registry.json'); l=read('ucc_mediatheque_course_links.json'); s=read('ucc_course_syllabi.json')
cs={x['ucc_course_id']:x for x in c['courses']}; ws={x['work_ref']:x for x in r['works']}; plans={x['course_id']:x for x in s['courses']}
assert len(cs)==110 and len(c['pathways'])==11 and len(plans)==110
assert len(ws)==len(r['works'])==132
assert set(plans)==set(cs)=={x['course_id'] for x in l['course_links']}
expected=collections.defaultdict(set); pathways=collections.defaultdict(set); roles=collections.defaultdict(collections.Counter)
for row in l['course_links']:
 cid=row['course_id']; p=plans[cid]
 assert row['pathway_id']==cs[cid]['pathway_id']==p['pathway_id']
 assert row['course_title']==cs[cid]['title']==p['title']
 assert row['media_refs']==p['readings']
 assert 3<=len(row['media_refs'])<=6
 assert len({x['media_ref'] for x in row['media_refs']})==len(row['media_refs'])
 for m in row['media_refs']:
  w=ws[m['media_ref']]
  assert w['status']=='active', (cid,w['work_ref'],w['status'])
  assert m['rationale'] and m['passage'] and m['selection_method']=='explicit_editorial_selection'
  assert set(m['matched_theme_refs'])==set(cs[cid]['kristal_theme_refs']) & set(w['metadata']['kristal_theme_refs'])
  assert 'aelf.org' not in w['sourceurl']
  expected[w['work_ref']].add(cid); pathways[w['work_ref']].add(row['pathway_id']); roles[w['work_ref']][m['role']]+=1
 assert len(p['sessions'])==5 and len(p['objectives'])==3
 assert sum(x['weight'] for x in p['assessment']['rubric'])==100
 assert p['credits'] is None and p['peer_review_status']=='not_yet_reviewed'
 assert p['central_question'] and p['assessment']['task'] and p['contextual_cautions']
 assert set(p['prerequisites'])<=set(cs) and cid not in p['prerequisites']
 for pre in p['prerequisites']:
  assert int(pre[-3:]) < int(cid[-3:]), (cid,pre)
 if '-COS-' in cid:
  assert any(x['media_ref']=='ucc.work.le-dieu-cosmique-2008' and x['role']=='anchor' for x in p['readings'])
for ref,w in ws.items():
 assert set(w['metadata']['course_refs'])==expected[ref]
 assert set(w['metadata']['pathway_refs'])==pathways[ref]
 assert w['metadata']['course_link_roles']==dict(roles[ref])
 assert all(k in w for k in ('language','citation','bibliographic','reference_review','access_policy'))
 assert w['sourceurl'].startswith('https://')
 assert w['access_policy']['mirror_allowed'] is False
 if ref.startswith('ucc.work.editorial.'):
  assert w['bibliographic']['historical_cutoff_upper_bound']<=1926
# Pedagogical regressions: specific, not just counts.
def first(cid): return plans[cid]['readings'][0]['media_ref']
assert first('UCC-OEU-105')=='ucc.work.editorial.history-method'
assert first('UCC-OEU-108')=='ucc.work.editorial.history-method'
assert first('UCC-THE-103')=='ucc.work.editorial.nicaea'
assert first('UCC-THE-104')=='ucc.work.editorial.athanasius'
assert first('UCC-THE-107')=='ucc.work.editorial.ineffabilis'
assert first('UCC-ECO-107')=='ucc.work.theophile.st3078'
assert all('galileo-sentence' not in x['media_ref'] for x in plans['UCC-THE-102']['readings'])
assert any('q. 27' in x['passage'] for x in plans['UCC-THE-107']['readings'])
assert len(r['policy']['exceptions_structured'])==3
assert r['anchor_work_research']['bibliography']['status']=='not_extracted'
assert l['stats']['total_course_media_links']==sum(len(p['readings']) for p in plans.values())==338
assert l['stats']['distinct_linked_media']==sum(bool(x) for x in expected.values())==81
# Protect the semantic corpus from an editorial projection being relabelled as evidence.
assert all(w['metadata']['editorial_theme_provenance'].endswith('not a validated Kristal assertion.') for w in ws.values())
print('PASS: 110 plans, 132 records, 338 selections, inverse projections, prerequisites, policy, theological and pedagogical regressions.')
