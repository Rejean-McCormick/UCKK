#!/usr/bin/env python3
"""Refresh UCKK and Médiathèque bindings from canonical Kristal-Kollection files."""
from __future__ import annotations
import argparse, hashlib, json, os
from pathlib import Path

DEFAULT_KROOT=r'C:\mycode\Kristal\Kristal-Kollection'
DEFAULT_MROOT=r'C:\mycode\kOA_Mediatheque'

def load(p:Path): return json.loads(p.read_text(encoding='utf-8'))
def dump(p:Path,v): p.parent.mkdir(parents=True,exist_ok=True); p.write_text(json.dumps(v,ensure_ascii=False,indent=2)+'\n',encoding='utf-8')
def sha(p:Path): return hashlib.sha256(p.read_bytes()).hexdigest()

def main()->int:
    ap=argparse.ArgumentParser()
    ap.add_argument('--uckk-root',default=None,help='uckk-moodle root; defaults from script location')
    ap.add_argument('--kristal-root',default=None)
    ap.add_argument('--mediatheque-root',default=None)
    a=ap.parse_args()
    script=Path(__file__).resolve(); uroot=Path(a.uckk_root).resolve() if a.uckk_root else script.parents[3]
    kroot=Path(a.kristal_root or os.environ.get('KRISTAL_KOLLECTION_ROOT') or DEFAULT_KROOT)
    mroot=Path(a.mediatheque_root or DEFAULT_MROOT)
    manifest_path=kroot/'universities/uckk/manifest.json'; manifest=load(manifest_path)
    entries=[]
    for e in manifest.get('entries') or []:
        rel=str(e.get('path') or ''); p=kroot/Path(rel)
        if not p.is_file(): raise SystemExit(f'Missing canonical Kristal: {p}')
        obj=load(p)
        row=dict(e); row['repository_relative_path']=rel; row['sha256']=sha(p); row['bytes']=p.stat().st_size; row['status']=obj.get('status','unknown')
        entries.append(row)
    if len(entries)!=111: raise SystemExit(f'Expected 111 Kristals, found {len(entries)}')
    repository={'id':'Kristal-Kollection','root_env':'KRISTAL_KOLLECTION_ROOT','default_windows_root':DEFAULT_KROOT,'github':'https://github.com/Rejean-McCormick/Kristal-Kollection','uckk_subtree':'universities/uckk'}
    binding={'format':'uckk.kristal-bindings/1.0.0','authority':'Bindings only. Canonical Kristal bytes are owned by Kristal-Kollection.','repository':repository,'counts':manifest.get('counts') or {},'root_kristal_ref':manifest.get('root_kristal_ref'),'entries':entries}
    dump(uroot/'local/uckk/atlas/kristal-bindings/manifest.json',binding)
    medbootstrap=mroot/'mediatheque-uckk/bootstrap'
    dump(medbootstrap/'kristal-kollection.binding.json',{'format':'koa.mediatheque.kristal-repository-binding/1.0.0','library':'uckk','repository':repository,'storage_mode':'external_repository_reference','semantic_authority':'Kristal/Kristall','catalog_authority':'Médiathèque UCKK','entries':entries})
    plan_path=medbootstrap/'import-plan.json'; plan=load(plan_path); old={x.get('kristal_ref'):x for x in plan.get('kristals') or []}; ks=[]
    for b in entries:
        ref=b['kristal_ref']; base=dict(old.get(ref) or {})
        base.update({'kristal_ref':ref,'kind':b.get('kind'),'parent_kristal_ref':b.get('parent_kristal_ref'),'title':b.get('title'),'voie_code':b.get('code') or b.get('voie_code'),'course_code':b.get('course_id'),'sortorder':b.get('order',0),'status':b.get('status','unknown'),'portable_contract':'kristal_state/6.0','kristall_baseline':'7.0.0-draft.3.2','source_relative_path':b['repository_relative_path'],'repository_id':'Kristal-Kollection','repository_url':'https://github.com/Rejean-McCormick/Kristal-Kollection','storage_mode':'external_repository_reference','sha256':b['sha256'],'bytes':b['bytes'],'mimetype':'application/json'})
        ks.append(base)
    plan['format']='koa.mediatheque.uckk-bootstrap/1.2.0'; plan['kristals']=ks; plan['kristal_repository']={'binding':'kristal-kollection.binding.json',**repository,'storage_mode':'external_repository_reference'}
    dump(plan_path,plan)
    print(json.dumps({'ok':True,'kristals':len(entries),'uckk_binding':str(uroot/'local/uckk/atlas/kristal-bindings/manifest.json'),'mediatheque_plan':str(plan_path)},ensure_ascii=False,indent=2)); return 0
if __name__=='__main__': raise SystemExit(main())
