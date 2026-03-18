<?php
require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Súgó';
include __DIR__ . '/includes/header.php';
?>

<style>
  .help-grid { display: grid; grid-template-columns: 220px 1fr; gap: 28px; align-items: start; }
  .help-toc {
    position: sticky; top: 88px;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 6px;
  }
  .help-toc a {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 500; color: var(--muted);
    text-decoration: none; transition: background 0.15s, color 0.15s;
  }
  .help-toc a:hover { background: rgba(255,255,255,0.05); color: var(--text); }
  .help-toc a.active { background: var(--red-dim); color: #f87171; }
  .help-toc .toc-section {
    font-size: 10px; font-weight: 600; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--subtle);
    padding: 10px 10px 4px;
  }
  .help-toc svg { width: 13px; height: 13px; flex-shrink: 0; opacity: 0.7; }

  .help-content { display: flex; flex-direction: column; gap: 24px; }

  .help-section { scroll-margin-top: 80px; }

  .help-section .card-header { gap: 10px; }
  .help-section .card-header svg { width: 16px; height: 16px; color: #f87171; flex-shrink: 0; }

  .field-list { display: flex; flex-direction: column; }
  .field-row {
    display: grid; grid-template-columns: 180px 1fr;
    gap: 16px; padding: 13px 22px;
    border-bottom: 1px solid var(--border);
    align-items: start;
  }
  .field-row:last-child { border-bottom: none; }
  .field-name {
    font-size: 12.5px; font-weight: 600; color: var(--text);
    display: flex; flex-direction: column; gap: 4px;
  }
  .field-name code {
    font-family: ui-monospace, 'Cascadia Code', Consolas, monospace;
    font-size: 10px; font-weight: 500;
    background: rgba(255,255,255,0.05); border: 1px solid var(--border);
    border-radius: 4px; padding: 1px 5px; color: var(--muted);
    width: fit-content;
  }
  .field-desc { font-size: 13px; color: var(--muted); line-height: 1.6; }
  .field-desc strong { color: var(--text); font-weight: 600; }
  .field-desc .example {
    display: inline-block; margin-top: 5px;
    background: var(--elevated); border: 1px solid var(--border);
    border-radius: 5px; padding: 3px 8px;
    font-size: 11.5px; color: var(--subtle);
    font-family: ui-monospace, Consolas, monospace;
  }

  .tip-box {
    display: flex; gap: 12px;
    background: rgba(147,197,253,0.06); border: 1px solid rgba(147,197,253,0.15);
    border-radius: 8px; padding: 14px 16px;
    font-size: 13px; color: rgba(147,197,253,0.8); line-height: 1.6;
  }
  .tip-box svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; color: #93c5fd; }

  .warn-box {
    display: flex; gap: 12px;
    background: rgba(251,191,36,0.06); border: 1px solid rgba(251,191,36,0.15);
    border-radius: 8px; padding: 14px 16px;
    font-size: 13px; color: rgba(251,191,36,0.75); line-height: 1.6;
  }
  .warn-box svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; color: #fbbf24; }

  .steps { display: flex; flex-direction: column; gap: 0; }
  .step {
    display: flex; gap: 16px; padding: 16px 22px;
    border-bottom: 1px solid var(--border);
  }
  .step:last-child { border-bottom: none; }
  .step-num {
    width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
    background: var(--red-dim); border: 1px solid var(--red-border);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #f87171;
  }
  .step-body { flex: 1; }
  .step-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
  .step-desc  { font-size: 12.5px; color: var(--muted); line-height: 1.55; }

  .pill-demo {
    display: inline-flex; align-items: center; gap: 6px; vertical-align: middle;
  }

  @media (max-width: 860px) {
    .help-grid { grid-template-columns: 1fr; }
    .help-toc { position: static; }
    .field-row { grid-template-columns: 1fr; gap: 4px; }
  }
</style>

<div class="help-grid">

  <!-- Tartalomjegyzék -->
  <nav class="help-toc" id="toc">
    <div class="toc-section">Tartalom</div>
    <a href="#uj-gep"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>Új gép hozzáadása</a>
    <a href="#alapadatok"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Alapadatok mezők</a>
    <a href="#specs"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h8"/></svg>Műszaki adatok</a>
    <a href="#kep"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Képfeltöltés</a>
    <div class="toc-section">Listák kezelése</div>
    <a href="#szekciok"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>Szekciók</a>
    <a href="#kategoriak"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>Kategóriák</a>
    <a href="#gyartok"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Gyártók</a>
    <div class="toc-section">Egyéb</div>
    <a href="#sorrend"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>Sorrend módosítás</a>
    <a href="#aktiv"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Láthatóság</a>
  </nav>

  <!-- Tartalom -->
  <div class="help-content">

    <!-- Új gép -->
    <div class="card help-section" id="uj-gep">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
        <span class="card-title">Új gép hozzáadása – lépések</span>
      </div>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-body">
            <div class="step-title">Nyisd meg az "Új gép" oldalt</div>
            <div class="step-desc">A bal oldali menüből kattints a <strong>Gépek</strong> menüpontra, majd a jobb felső sarokban az <strong>Új gép</strong> gombra. Vagy a Vezérlőpult-ról az "Új gép hozzáadása" gyorsgombra.</div>
          </div>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <div class="step-body">
            <div class="step-title">Töltsd ki az alapadatokat</div>
            <div class="step-desc">A <strong>Gép neve</strong> kötelező. Válassz gyártót, szekciót és kategóriát. A többi mező opcionális, de minél több adatot töltesz ki, annál informatívabb lesz a kártya a weboldalon.</div>
          </div>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <div class="step-body">
            <div class="step-title">Add meg a műszaki adatokat</div>
            <div class="step-desc">A <strong>Műszaki adatok</strong> szekciónál kattints a <strong>+ Sor hozzáadása</strong> gombra. Bal mezőbe a tulajdonság neve (pl. "Munkaterület"), jobb mezőbe az érték (pl. "3000 × 1500 mm"). Annyi sort adhatsz hozzá, amennyit szeretnél.</div>
          </div>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <div class="step-body">
            <div class="step-title">Tölts fel képet (opcionális)</div>
            <div class="step-desc">A <strong>Kép</strong> szekciónál válassz JPG, PNG vagy WebP fájlt, maximum 3 MB méretben. Ha nincs kép, a kártya kép nélkül jelenik meg.</div>
          </div>
        </div>
        <div class="step">
          <div class="step-num">5</div>
          <div class="step-body">
            <div class="step-title">Mentés</div>
            <div class="step-desc">Kattints a <strong>Mentés</strong> gombra. A gép azonnal megjelenik a géplistában. Ha az <strong>Aktív</strong> jelölőnégyzet be van pipálva, a weboldalon is látható lesz.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Alapadatok -->
    <div class="card help-section" id="alapadatok">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span class="card-title">Alapadatok – mezők magyarázata</span>
      </div>
      <div class="field-list">

        <div class="field-row">
          <div class="field-name">
            Gép neve
            <code>name</code>
          </div>
          <div class="field-desc">
            A gép teljes, hivatalos neve. Ez jelenik meg <strong>nagyítva, kiemelten</strong> a weboldalon a kártyán.<br>
            <span class="example">pl. TruLaser 3030 Fiber</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Gyártó
            <code>manufacturer</code>
          </div>
          <div class="field-desc">
            A gép gyártójának neve. A weboldalon <strong>szürke jelölőként</strong> jelenik meg a kártya jobb felső sarkában, a piros jelölő alatt.<br>
            <span class="example">pl. TRUMPF, AMADA, SOCO</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Szekció
            <code>section</code>
          </div>
          <div class="field-desc">
            Meghatározza, hogy <strong>melyik szekció-blokkban</strong> jelenik meg a gép a weboldalon. Minden szekció külön blokk saját fejléccel. A szekciók sorrendje és neve a <strong>Listák</strong> menüpontban szerkeszthető.<br>
            <span class="example">pl. TRUMPF Lézervágók, AMADA Hajlítók</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Kategória
            <code>category</code>
          </div>
          <div class="field-desc">
            Belső besorolás a gép típusáról. Szűrésre és rendszerezésre szolgál — jelenleg az adminban látható, de a weboldalon nem jelenik meg közvetlenül.<br>
            <span class="example">pl. Lézervágó, Hajlító, Csőhajlító</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Kategória felirat
            <code>category_label</code>
          </div>
          <div class="field-desc">
            Szabadon szerkeszthető szöveg, ami a <strong>gép neve felett jelenik meg piros kisbetűkkel</strong> a weboldalon. Ez a látható típusjelölő, pl. a technológia pontos megnevezése.<br>
            <span class="example">pl. Fiber lézer · 5 kW &nbsp;·&nbsp; CO₂ lézer &nbsp;·&nbsp; CNC élhajlító</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Jelölő szöveg
            <code>badge</code>
          </div>
          <div class="field-desc">
            Kis <strong>piros jelölő</strong> a kártya jobb felső sarkában. Rövid, figyelemfelkeltő adat — jellemzően a gép fő teljesítménymutója vagy darabszáma.<br>
            <span class="example">pl. 5 kW &nbsp;·&nbsp; ×2 &nbsp;·&nbsp; 6 kW &nbsp;·&nbsp; 320T</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Rövid leírás
            <code>short_description</code>
          </div>
          <div class="field-desc">
            2–4 mondatos leírás a gépről. A kártyán a gép neve alatt, a műszaki adatok táblázat felett jelenik meg halványabb szöveggel.<br>
            <span class="example">pl. Nagy teljesítményű síklézervágó, 6 kW-os fiber forrással...</span>
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Aktív
            <code>is_active</code>
          </div>
          <div class="field-desc">
            Ha be van pipálva, a gép <strong>megjelenik a weboldalon</strong>. Ha nincs, az adminban látható marad, de a látogatók nem látják. Hasznos, ha egy gépet ideiglenesen el szeretnél rejteni.
          </div>
        </div>

        <div class="field-row">
          <div class="field-name">
            Kiemelt gép
            <code>is_featured</code>
          </div>
          <div class="field-desc">
            Ha be van pipálva, a gép a szekciójában <strong>nagy, kiemelt kártyaként</strong> jelenik meg a többi fölött — széles elrendezéssel és a műszaki adatok táblázatával együtt. Tipikusan a szekció legfontosabb gépe kap kiemelt státuszt.
          </div>
        </div>

      </div>
    </div>

    <!-- Műszaki adatok -->
    <div class="card help-section" id="specs">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h8"/></svg>
        <span class="card-title">Műszaki adatok táblázat</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
        <p class="field-desc">A <strong>Műszaki adatok</strong> szekció egy szabad formátumú sor-páros táblázat. Minden sor egy tulajdonságot és annak értékét tartalmaz. A weboldalon a kártya alján jelenik meg zárt táblázatként.</p>

        <div class="tip-box">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div>
            <strong>Bal mező</strong> = a tulajdonság neve (pl. "Munkaterület", "Max. anyagvastagság", "Lézerteljesítmény")<br>
            <strong>Jobb mező</strong> = az érték (pl. "3000 × 1500 mm", "25 mm acél", "6 kW")<br>
            A <strong>× gombbal</strong> bármelyik sor törölhető, a <strong>+ Sor hozzáadása</strong> gombbal új sor vehető fel.
          </div>
        </div>

        <div class="warn-box">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          A sorok sorrendje a mentéskor rögzítődik. Ha módosítani szeretnéd a sorrendet, töröld ki a sorokat és add vissza a kívánt sorrendben.
        </div>
      </div>
    </div>

    <!-- Kép -->
    <div class="card help-section" id="kep">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="card-title">Képfeltöltés</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
        <div class="field-list" style="border:1px solid var(--border);border-radius:8px;overflow:hidden;">
          <div class="field-row">
            <div class="field-name">Formátum</div>
            <div class="field-desc">JPG, PNG vagy WebP. WebP ajánlott a kisebb fájlméret miatt.</div>
          </div>
          <div class="field-row">
            <div class="field-name">Max. méret</div>
            <div class="field-desc">3 MB. Ennél nagyobb fájl feltöltése hibaüzenetet ad.</div>
          </div>
          <div class="field-row">
            <div class="field-name">Megjelenés</div>
            <div class="field-desc">A kép a kártya tetején jelenik meg, <strong>160px magasan, teljes szélességben vágva</strong>. Legjobb arány: <strong>16:9 vagy 4:3</strong> fekvő formátum.</div>
          </div>
          <div class="field-row">
            <div class="field-name">Csere / Törlés</div>
            <div class="field-desc">Meglévő képnél a "Kép törlése" jelölőnégyzettel törölhető a kép. Új feltöltéssel automatikusan lecserélődik a régi.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Szekciók -->
    <div class="card help-section" id="szekciok">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span class="card-title">Szekciók kezelése</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
        <p class="field-desc">A szekciók a weboldal <strong>fő csoportosítási egységei</strong>. Minden szekció egy önálló blokkként jelenik meg saját fejléccel (pl. "TRUMPF Lézervágók"). A <strong>Listák</strong> menüpontban vehetők fel, módosíthatók vagy törölhetők.</p>
        <div class="tip-box">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div>
            A szekció neve alapján automatikusan generálódik egy <strong>belső azonosító</strong>, pl. "TRUMPF Lézervágók" → <code style="font-family:monospace;font-size:11px;">trumpf-lezervagok</code>. Ha egy szekció nevét módosítod, az összes hozzá tartozó gép automatikusan az új névhez kapcsolódik — nem kell külön frissíteni a gépeket.
          </div>
        </div>
        <div class="warn-box">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          Szekciót <strong>csak akkor lehet törölni</strong>, ha nincs egyetlen gép sem hozzá rendelve. Ha törlés előtt először a gépeket más szekciókba helyezed át, a törlés elvégezhető.
        </div>
      </div>
    </div>

    <!-- Kategóriák -->
    <div class="card help-section" id="kategoriak">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <span class="card-title">Kategóriák kezelése</span>
      </div>
      <div class="card-body">
        <p class="field-desc">A kategóriák <strong>belső típusbesorolást</strong> jelentenek (pl. Lézervágó, Hajlító). Jelenleg az adminban a szűrésre és rendszerezésre szolgálnak. A weboldalon a <strong>Kategória felirat</strong> mező látható, nem a kategória neve — így a belső besorolás és a megjelenített szöveg egymástól függetlenül szerkeszthető.</p>
      </div>
    </div>

    <!-- Gyártók -->
    <div class="card help-section" id="gyartok">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <span class="card-title">Gyártók kezelése</span>
      </div>
      <div class="card-body">
        <p class="field-desc">A gyártók listája határozza meg, hogy a gép szerkesztő felületén milyen gyártók választhatók. Ha egy új gyártó gépet veszel fel, először add hozzá a gyártót a <strong>Listák</strong> menüpontban. A gyártó neve <strong>szürke jelölőként</strong> jelenik meg a weboldalon minden gépen.</p>
      </div>
    </div>

    <!-- Sorrend -->
    <div class="card help-section" id="sorrend">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <span class="card-title">Gépek sorrendjének módosítása</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
        <p class="field-desc">A gépek sorrendje a weboldalon az adminban beállított sorrendet követi. A sorrend módosítása <strong>drag &amp; drop</strong> húzással történik a Gépek listában.</p>
        <div class="tip-box">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div>
            A <strong>hat pontos ikon</strong> (⠿) a sor bal oldalán fogható meg és húzható. Az új sorrend automatikusan mentődik, nem kell külön mentés gombra kattintani.<br><br>
            <strong>Fontos:</strong> a drag &amp; drop csak akkor aktív, ha <strong>nincs aktív szűrő</strong>. Ha szűrsz név, szekció vagy állapot szerint, a húzás le van tiltva, hogy véletlenül ne keveredjen össze a sorrend.
          </div>
        </div>
      </div>
    </div>

    <!-- Láthatóság -->
    <div class="card help-section" id="aktiv">
      <div class="card-header">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <span class="card-title">Láthatóság – aktív / inaktív</span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
        <p class="field-desc">A gépek láthatósága a listában lévő <strong>kapcsolóval (toggle)</strong> gyorsan váltható anélkül, hogy meg kellene nyitni a szerkesztő felületet. Inaktív gép az adminban megmarad, a weboldalon nem jelenik meg.</p>
        <div class="field-list" style="border:1px solid var(--border);border-radius:8px;overflow:hidden;">
          <div class="field-row">
            <div class="field-name">Aktív <span class="pill pill-green" style="font-size:10px;">zöld</span></div>
            <div class="field-desc">A gép látható a weboldalon a megfelelő szekcióban.</div>
          </div>
          <div class="field-row">
            <div class="field-name">Inaktív <span class="pill pill-slate" style="font-size:10px;">szürke</span></div>
            <div class="field-desc">A gép el van rejtve a weboldalon. Adatai megőrződnek, bármikor visszakapcsolható.</div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /.help-content -->
</div><!-- /.help-grid -->

<script>
// Active TOC highlight on scroll
const sections = document.querySelectorAll('.help-section');
const tocLinks  = document.querySelectorAll('#toc a');
const observer  = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      tocLinks.forEach(l => l.classList.remove('active'));
      const active = document.querySelector(`#toc a[href="#${e.target.id}"]`);
      if (active) active.classList.add('active');
    }
  });
}, { rootMargin: '-20% 0px -70% 0px' });
sections.forEach(s => observer.observe(s));
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
