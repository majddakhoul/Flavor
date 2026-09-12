#!/usr/bin/env python3
"""Rebuild index.html from every .mmd file in this folder tree.

Usage:  python3 build.py
Add a new diagram: drop a .mmd file into usecase/ activity/ sequence/ state/
(or beside this script) and run the script again.
"""
import json
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent

ROLE_ORDER = ["manager", "chef", "waiter", "delivery", "security", "customer", "guest"]

ROLE_LABEL = {
    "manager": "manager — Manager",
    "chef": "chef — Chef",
    "waiter": "waiter — Waiter",
    "delivery": "delivery — Delivery",
    "security": "security — Security",
    "customer": "customer — Customer",
    "guest": "guest — Guest (unregistered)",
}

ROOT_LABEL = {
    "erd-core": ("Database", "ERD — Core tables"),
    "erd": ("Database", "ERD — Full schema"),
    "classes-core": ("Domain Model", "Classes — Core"),
    "classes-catalog": ("Domain Model", "Classes — Menu catalog"),
    "classes": ("Domain Model", "Classes — Full"),
}

SPECIAL = {
    "usecase/system": ("Use Cases", "System overview — all actors"),
    "activity/full-flow": ("Activity", "End to end flow — all roles"),
    "sequence/e2e-ordering-dinein": ("Sequence — Scenarios", "Dine-in ordering end to end"),
    "sequence/e2e-delivery": ("Sequence — Scenarios", "Delivery order end to end"),
    "sequence/e2e-reservation": ("Sequence — Scenarios", "Reservation and entrance check"),
    "sequence/e2e-offer-redemption": ("Sequence — Scenarios", "Offer browsing and redemption"),
    "sequence/e2e-rating": ("Sequence — Scenarios", "Ratings feeding the menu report"),
    "sequence/e2e-maintenance": ("Sequence — Scenarios", "Facility maintenance"),
    "sequence/e2e-staff-onboarding": ("Sequence — Scenarios", "New employee onboarding"),
}

STATE_LABEL = {
    "order": "Order status",
    "reservation": "Reservation status",
    "table": "Table occupancy",
    "meal": "Meal availability",
    "offer": "Offer lifecycle",
    "customer-account": "Customer account",
}

FOLDER = {"usecase": ("Use Cases", "uc"), "activity": ("Activity", "act"),
          "sequence": ("Sequence by Role", "seq"), "state": ("State Machines", "st")}

GROUP_ORDER = ["Database", "Domain Model", "Use Cases", "Activity",
               "Sequence by Role", "Sequence — Scenarios", "State Machines"]


def collect():
    items = []  # (group, key, title, source)
    for p in sorted(ROOT.glob("*.mmd")):
        group, title = ROOT_LABEL.get(p.stem, ("Database", p.stem))
        items.append((group, p.stem, title, p.read_text()))

    for folder, (group, prefix) in FOLDER.items():
        d = ROOT / folder
        if not d.is_dir():
            continue
        files = sorted(d.glob("*.mmd"), key=lambda f: (
            ROLE_ORDER.index(f.stem) if f.stem in ROLE_ORDER else 99, f.stem))
        for p in files:
            rel = f"{folder}/{p.stem}"
            key = f"{prefix}-{p.stem}"
            if rel in SPECIAL:
                g, title = SPECIAL[rel]
            elif p.stem in ROLE_ORDER:
                g, title = group, ROLE_LABEL[p.stem]
            elif folder == "state":
                g, title = group, STATE_LABEL.get(p.stem, p.stem)
            else:
                g, title = group, p.stem.replace("-", " ")
            items.append((g, key, title, p.read_text()))

    items.sort(key=lambda it: GROUP_ORDER.index(it[0]) if it[0] in GROUP_ORDER else 99)
    return items


def build():
    items = collect()
    sources = {k: s for _, k, _, s in items}
    meta = {k: {"title": f"{g} — {t}", "group": g} for g, k, t, _ in items}

    nav, seen = [], None
    for g, k, t, _ in items:
        if g != seen:
            nav.append(f'<div class="grp">{g}</div>')
            seen = g
        nav.append(f'<button class="tab" data-k="{k}">{t}</button>')

    html = TEMPLATE.replace("__NAV__", "\n  ".join(nav)) \
                   .replace("__SOURCES__", json.dumps(sources, ensure_ascii=False)) \
                   .replace("__META__", json.dumps(meta, ensure_ascii=False)) \
                   .replace("__FIRST__", items[0][1])
    (ROOT / "index.html").write_text(html, encoding="utf-8")
    print(f"index.html rebuilt with {len(items)} diagrams")


TEMPLATE = """<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Flavor Restaurant Management System - Diagrams</title>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.1/dist/mermaid.min.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
:root[data-theme="light"]{--primary:#ea580c;--primary-dark:#c2410c;--secondary:#0d9488;
  --bg:#f8fafc;--surface:#fff;--surface2:#f1f5f9;--text:#0f172a;--text2:#64748b;--border:#e2e8f0}
:root[data-theme="dark"]{--primary:#fb923c;--primary-dark:#fdba74;--secondary:#2dd4bf;
  --bg:#0f172a;--surface:#111827;--surface2:#1e293b;--text:#f8fafc;--text2:#94a3b8;--border:#334155}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}
aside{width:270px;background:var(--surface);border-right:1px solid var(--border);
  padding:20px 0;position:fixed;height:100vh;overflow-y:auto}
aside h2{font-size:14px;padding:0 20px 16px;border-bottom:1px solid var(--border)}
.grp{font-size:9.5px;text-transform:uppercase;letter-spacing:.9px;color:var(--text2);
  padding:16px 20px 6px;font-weight:600}
.tab{display:block;width:calc(100% - 20px);margin:2px 10px;padding:9px 14px;text-align:left;
  border:0;background:transparent;color:var(--text2);border-radius:8px;cursor:pointer;
  font-family:inherit;font-size:12.5px}
.tab:hover{background:var(--surface2);color:var(--text)}
.tab.active{background:var(--primary);color:#fff;font-weight:600}
main{margin-left:270px;flex:1;padding:24px 28px}
header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;gap:12px;flex-wrap:wrap}
h1{font-size:20px}
.ctl{display:flex;gap:8px}
button.act{font-family:inherit;cursor:pointer;border-radius:8px;border:1px solid var(--border);
  background:var(--surface);color:var(--text);padding:9px 14px;font-size:13px}
button.act:hover{border-color:var(--primary);color:var(--primary)}
.panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px}
.viewport{overflow:auto;border:1px solid var(--border);border-radius:10px;
  background:var(--surface2);padding:18px;max-height:80vh}
.viewport svg{max-width:none!important;height:auto}
</style>
</head>
<body>
<aside>
  <h2>Flavor Diagrams</h2>
  __NAV__
</aside>
<main>
  <header>
    <h1 id="pTitle"></h1>
    <div class="ctl">
      <button class="act" id="zo">Zoom out</button>
      <button class="act" id="zi">Zoom in</button>
      <button class="act" id="dl">Download SVG</button>
      <button class="act" id="th">Dark mode</button>
    </div>
  </header>
  <div class="panel"><div class="viewport"><div id="canvas"></div></div></div>
</main>
<script>
const SOURCES = __SOURCES__, META = __META__;
let current = '__FIRST__', zoom = 1;
const theme = () => document.documentElement.getAttribute('data-theme');
function initM(){
  mermaid.initialize({startOnLoad:false, theme: theme()==='dark'?'dark':'default',
    themeVariables: theme()==='dark'
      ? {primaryColor:'#1e293b',primaryTextColor:'#f8fafc',primaryBorderColor:'#fb923c',
         lineColor:'#94a3b8',fontFamily:'Poppins, sans-serif'}
      : {primaryColor:'#fff7ed',primaryTextColor:'#0f172a',primaryBorderColor:'#ea580c',
         lineColor:'#64748b',fontFamily:'Poppins, sans-serif'},
    er:{useMaxWidth:false,entityPadding:12,minEntityWidth:130},
    class:{useMaxWidth:false}, sequence:{useMaxWidth:false,wrap:true,width:230},
    state:{useMaxWidth:false},
    flowchart:{useMaxWidth:false,curve:'basis',padding:14}});
}
async function render(k){
  current=k;
  document.querySelectorAll('.tab').forEach(t=>t.classList.toggle('active',t.dataset.k===k));
  document.getElementById('pTitle').textContent=META[k].title;
  const c=document.getElementById('canvas');
  c.innerHTML='<div style="padding:40px;color:#64748b">Rendering...</div>';
  try{ const {svg}=await mermaid.render('g'+Date.now(),SOURCES[k]); c.innerHTML=svg; applyZoom(); }
  catch(e){ c.innerHTML='<pre style="color:#dc2626;padding:20px;white-space:pre-wrap">'+e.message+'</pre>'; }
}
function applyZoom(){const s=document.querySelector('#canvas svg');
  if(s){s.style.transformOrigin='top left';s.style.transform='scale('+zoom+')';}}
document.querySelectorAll('.tab').forEach(t=>t.addEventListener('click',()=>{zoom=1;render(t.dataset.k);}));
document.getElementById('zi').onclick=()=>{zoom=Math.min(zoom+.15,3);applyZoom();};
document.getElementById('zo').onclick=()=>{zoom=Math.max(zoom-.15,.35);applyZoom();};
document.getElementById('dl').onclick=()=>{const s=document.querySelector('#canvas svg');if(!s)return;
  const b=new Blob([s.outerHTML],{type:'image/svg+xml'});const a=document.createElement('a');
  a.href=URL.createObjectURL(b);a.download=current+'.svg';a.click();};
document.getElementById('th').onclick=()=>{const n=theme()==='dark'?'light':'dark';
  document.documentElement.setAttribute('data-theme',n);
  document.getElementById('th').textContent=n==='dark'?'Light mode':'Dark mode';initM();render(current);};
initM();render(current);
</script>
</body>
</html>
"""

if __name__ == "__main__":
    build()
