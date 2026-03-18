<?php
$config = __DIR__ . '/cms/config.php';
if (!file_exists($config)) {
    header('Location: setup.php');
    exit;
}
require_once $config;

$pdo = cms_db();

// Gépek lekérése szekció szerint csoportosítva
$machines_raw = $pdo->query("
    SELECT m.*, GROUP_CONCAT(
        CONCAT(s.spec_key, '||', s.spec_value)
        ORDER BY s.sort_order SEPARATOR ';;'
    ) AS specs_raw
    FROM machines m
    LEFT JOIN machine_specs s ON s.machine_id = m.id
    WHERE m.is_active = 1
    GROUP BY m.id
    ORDER BY m.sort_order
")->fetchAll();

$machines = [];
foreach ($machines_raw as $row) {
    $specs = [];
    if (!empty($row['specs_raw'])) {
        foreach (explode(';;', $row['specs_raw']) as $spec) {
            [$k, $v] = explode('||', $spec, 2);
            $specs[] = ['key' => $k, 'value' => $v];
        }
    }
    $row['specs'] = $specs;
    $machines[$row['section']][] = $row;
}

// Szekciók sorrendben (DB-ből, fallback: $machines kulcsai)
$ordered_sections = [];
try {
    $sl = $pdo->query("SELECT slug, label FROM sections ORDER BY sort_order, id")->fetchAll();
    foreach ($sl as $r) $ordered_sections[$r['slug']] = $r['label'];
} catch (Exception $e) {}
foreach (array_keys($machines) as $slug) {
    if (!isset($ordered_sections[$slug])) $ordered_sections[$slug] = $slug;
}

// Oldal-beállítások
$settings_raw = $pdo->query("SELECT setting_key, setting_value FROM page_settings WHERE setting_group = 'geppark'")->fetchAll();
$s = [];
foreach ($settings_raw as $row) {
    $s[$row['setting_key']] = $row['setting_value'];
}
$get = fn($key, $default = '') => htmlspecialchars($s[$key] ?? $default);

function machine_card(array $m, string $size = 'normal'): void {
    $name  = htmlspecialchars($m['name']);
    $label = htmlspecialchars($m['category_label']);
    $badge = htmlspecialchars($m['badge']);
    $mfr   = htmlspecialchars($m['manufacturer']);
    $desc  = htmlspecialchars($m['short_description']);
    $img   = $m['image'] ? htmlspecialchars($m['image']) : '';
    $pad   = $size === 'small' ? 'p-5' : 'p-6';
    $h     = $size === 'small' ? 'text-xl' : 'text-2xl';
    $td_op = $size === 'small' ? 'text-white/60' : 'text-white/70';
    $td_sz = $size === 'small' ? 'text-xs' : 'text-sm';
    ?>
    <div class="machine-card bg-[#122135] border border-white/8 <?= $pad ?>">
      <?php if ($img): ?>
      <div class="mb-4 overflow-hidden flex items-center justify-center bg-[#0d1a27]" style="height:180px;">
        <img src="<?= $img ?>" alt="<?= $name ?>" class="machine-img" loading="lazy">
      </div>
      <?php endif; ?>
      <div class="flex items-start justify-between mb-4">
        <div>
          <div class="text-[#cc2222] text-xs font-medium tracking-widest uppercase mb-1"><?= $label ?></div>
          <h3 class="font-heading font-semibold <?= $h ?> uppercase tracking-wide text-white"><?= $name ?></h3>
        </div>
        <?php if ($badge || $mfr): ?>
        <div class="flex flex-col items-end gap-1 shrink-0 ml-2">
          <?php if ($badge): ?>
          <div class="bg-[#cc2222]/10 border border-[#cc2222]/30 px-2 py-1">
            <span class="text-[#cc2222] text-xs font-bold tracking-wider"><?= $badge ?></span>
          </div>
          <?php endif; ?>
          <?php if ($mfr): ?>
          <div class="bg-white/5 border border-white/10 px-2 py-1">
            <span class="text-white/40 text-xs font-semibold tracking-wider"><?= $mfr ?></span>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($desc): ?>
      <p class="text-white/45 <?= $td_sz ?> leading-relaxed mb-5"><?= $desc ?></p>
      <?php endif; ?>
      <?php if (!empty($m['specs'])): ?>
      <table class="spec-table w-full <?= $td_sz ?>">
        <tbody>
          <?php foreach ($m['specs'] as $spec): ?>
          <tr>
            <th class="text-left"><?= htmlspecialchars($spec['key']) ?></th>
            <td class="text-right <?= $td_op ?>"><?= htmlspecialchars($spec['value']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Géppark | Linardics Kft.</title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <meta name="description" content="Prémium géppark: TRUMPF TruLaser lézervágók, TruBend hajlítók, AMADA élhajlítók, SOCO CNC csőhajlító, Gema porfestő rendszer. Székesfehérvár.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://linardics.hu/geppark.html">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://linardics.hu/geppark.html">
  <meta property="og:title" content="Géppark – TRUMPF és AMADA lézervágók, hajlítók | Linardics Kft.">
  <meta property="og:description" content="Prémium géppark: TRUMPF TruLaser lézervágók, TruBend hajlítók, AMADA élhajlítók, SOCO CNC csőhajlító, Gema porfestő rendszer. Székesfehérvár.">
  <meta property="og:image" content="https://linardics.hu/assets/images/og-image.jpg">
  <meta property="og:locale" content="hu_HU">
  <meta property="og:site_name" content="Linardics Kft.">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: '#0d1b2a', 'navy-dark': '#060f1a', 'navy-card': '#122135',
            brand: '#cc2222', 'brand-dark': '#a01818',
          }
        }
      }
    }
  </script>
  <style>
    * { font-family: 'Inter', sans-serif; }
    .font-heading, h1, h2, h3 { font-family: 'Barlow Condensed', sans-serif; }
    .navbar-scrolled { background: rgba(6,15,26,0.88)!important; -webkit-backdrop-filter:blur(12px); backdrop-filter:blur(12px); border-bottom:1px solid rgba(255,255,255,0.06); }
    html { scroll-behavior: smooth; }
    #mobile-menu { display: none; }
    #mobile-menu.open { display: flex; }
    .container { width: min(90vw, 96.875rem); margin-left: auto; margin-right: auto; padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); }
    .btn-clipped { clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px)); border-radius: 0!important; position: relative; }
    .btn-label { display: inline-block; transition: transform 0.25s ease; }
    .btn-clipped:hover .btn-label { transform: translateX(-8px); }
    .btn-arrow-icon { position:absolute; right:-20px; top:50%; transform:translateY(-50%); transition:right 0.25s ease,opacity 0.15s ease; opacity:0; pointer-events:none; }
    .btn-clipped:hover .btn-arrow-icon { right:12px; opacity:1; }
    .machine-card { transition: transform 0.2s, border-color 0.2s; }
    .machine-card:hover { transform: translateY(-2px); border-color: rgba(204,34,34,0.4)!important; }
    .spec-table td, .spec-table th { padding: 0.5rem 1rem; }
    .spec-table tr:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.06); }
    .spec-table th { color: rgba(255,255,255,0.4); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 500; }
  </style>
  <script type="application/ld+json">
  {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Főoldal","item":"https://linardics.hu/"},{"@type":"ListItem","position":2,"name":"Géppark","item":"https://linardics.hu/geppark.html"}]}
  </script>
</head>
<body class="bg-[#060f1a] text-white overflow-x-hidden">

  <!-- PRELOADER -->
  <div id="preloader" style="position:fixed;inset:0;z-index:9999;background:#060f1a;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity 0.5s ease,visibility 0.5s ease;">
    <img src="assets/images/linardics-logo.png" alt="Linardics Kft." style="width:72px;height:72px;object-fit:contain;animation:preloader-pulse 1.4s ease-in-out infinite;">
    <div style="margin-top:20px;width:48px;height:2px;background:rgba(255,255,255,0.1);overflow:hidden;position:relative;">
      <div style="position:absolute;left:-100%;top:0;width:100%;height:100%;background:#cc2222;animation:preloader-slide 1.2s ease-in-out infinite;"></div>
    </div>
  </div>
  <style>
    @keyframes preloader-pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.75;transform:scale(0.95)}}
    @keyframes preloader-slide{0%{left:-100%}100%{left:100%}}
    #preloader.hidden{opacity:0;visibility:hidden;}
    body.preloading{overflow:hidden;}
  </style>
  <script>
    document.body.classList.add('preloading');
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        var pl = document.getElementById('preloader');
        pl.classList.add('hidden');
        document.body.classList.remove('preloading');
        setTimeout(function(){ pl.style.display='none'; }, 550);
      }, 350);
    });
  </script>

  <!-- NAVBAR -->
  <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-5">
    <div class="container flex items-center justify-between">
      <a href="index.html" class="flex items-center gap-3 flex-shrink-0">
        <img src="assets/images/linardics-logo.png" alt="Linardics Kft." class="h-9 w-auto" style="filter:brightness(0) invert(1);">
        <span class="font-heading font-bold text-white text-base tracking-widest">LINARDICS KFT.</span>
      </a>
      <div class="hidden md:flex items-center gap-8 text-xs font-medium tracking-widest text-white/85">
        <a href="szolgaltatasok/index.html" class="hover:text-white transition-colors uppercase">Szolgáltatásaink</a>
        <a href="geppark.php" class="text-white uppercase border-b border-[#cc2222] pb-0.5">Géppark</a>
        <a href="referenciak.html" class="hover:text-white transition-colors uppercase">Referenciák</a>
        <a href="rolunk.html" class="hover:text-white transition-colors uppercase">Rólunk</a>
        <a href="kapcsolat.html" class="hover:text-white transition-colors uppercase">Kapcsolat</a>
      </div>
      <div class="flex items-center gap-4 ml-auto md:ml-0">
        <div class="hidden md:flex items-center text-xs font-semibold tracking-widest">
          <span class="text-white cursor-default">HU</span>
          <span class="text-white/20 mx-1.5">|</span>
          <span class="text-white/35 hover:text-white/60 cursor-pointer transition-colors">EN</span>
        </div>
        <a href="kapcsolat.html" class="btn-clipped hidden sm:block bg-[#cc2222] hover:bg-[#a01818] text-white text-xs font-semibold px-5 py-2.5 tracking-wider uppercase transition-colors"><span class="btn-label">Ajánlatkérés</span><svg class="btn-arrow-icon" width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
        <button id="hamburger" class="md:hidden flex flex-col gap-1.5 p-2" onclick="toggleMenu()">
          <span class="w-6 h-0.5 bg-white block"></span>
          <span class="w-6 h-0.5 bg-white block"></span>
          <span class="w-6 h-0.5 bg-white block"></span>
        </button>
      </div>
    </div>
  </nav>

  <!-- Mobile menu -->
  <div id="mobile-menu" class="fixed top-0 left-0 right-0 bottom-0 z-40 bg-[#060f1a] flex-col items-center justify-center gap-8 text-center">
    <a href="szolgaltatasok/index.html" class="font-heading font-bold text-3xl uppercase tracking-widest text-white hover:text-[#cc2222] transition-colors" onclick="toggleMenu()">Szolgáltatásaink</a>
    <a href="geppark.php" class="font-heading font-bold text-3xl uppercase tracking-widest text-[#cc2222]" onclick="toggleMenu()">Géppark</a>
    <a href="referenciak.html" class="font-heading font-bold text-3xl uppercase tracking-widest text-white hover:text-[#cc2222] transition-colors" onclick="toggleMenu()">Referenciák</a>
    <a href="rolunk.html" class="font-heading font-bold text-3xl uppercase tracking-widest text-white hover:text-[#cc2222] transition-colors" onclick="toggleMenu()">Rólunk</a>
    <a href="kapcsolat.html" class="font-heading font-bold text-3xl uppercase tracking-widest text-white hover:text-[#cc2222] transition-colors" onclick="toggleMenu()">Kapcsolat</a>
    <a href="kapcsolat.html" class="mt-4 btn-clipped bg-[#cc2222] text-white font-heading font-bold text-xl uppercase tracking-widest px-10 py-4"><span class="btn-label">Ajánlatkérés</span><svg class="btn-arrow-icon" width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
  </div>

  <!-- PAGE HEADER -->
  <div class="pt-36 pb-16 px-6 md:px-12" style="background:linear-gradient(180deg,#060f1a 0%,#0d1b2a 100%);">
    <div class="container">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-1 h-6 bg-[#cc2222]"></div>
        <span class="font-heading font-semibold text-xl uppercase tracking-[0.15em]" style="-webkit-text-stroke:1.5px rgba(255,255,255,0.45);color:transparent;"><?= $get('geppark_eyebrow', 'Prémium technológia') ?></span>
      </div>
      <h1 class="font-heading font-semibold text-6xl md:text-8xl uppercase tracking-wide text-white leading-none mb-6"><?= $get('geppark_title', 'Géppark') ?></h1>
      <p class="text-white/55 text-base leading-relaxed max-w-2xl mb-8"><?= $get('geppark_intro') ?></p>
      <div class="flex flex-wrap gap-3">
        <?php foreach (['geppark_badge_1','geppark_badge_2','geppark_badge_3','geppark_badge_4'] as $bkey): ?>
          <?php if (!empty($s[$bkey])): ?>
          <div class="flex items-center gap-2 bg-[#122135] border border-white/8 px-4 py-2">
            <div class="w-2 h-2 bg-[#cc2222]"></div>
            <span class="text-xs text-white/70 font-medium tracking-wide uppercase"><?= $get($bkey) ?></span>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- SZEKCIÓK – dinamikusan DB-ből -->
  <?php $bg_cycle = ['#0d1b2a', '#060f1a']; $bg_i = 0; ?>
  <?php foreach ($ordered_sections as $slug => $section_label): ?>
  <?php if (empty($machines[$slug])) { $bg_i++; continue; } ?>
  <?php
    $bg       = $bg_cycle[$bg_i % 2];
    $eyebrow  = $get("{$slug}_eyebrow");
    $title    = !empty($s["{$slug}_title"]) ? htmlspecialchars($s["{$slug}_title"]) : htmlspecialchars($section_label);
    $featured = array_values(array_filter($machines[$slug], fn($m) => $m['is_featured']));
    $regular  = array_values(array_filter($machines[$slug], fn($m) => !$m['is_featured']));
    $bg_i++;
  ?>
  <section style="background:<?= $bg ?>;" class="py-16 px-6 md:px-12" id="<?= htmlspecialchars($slug) ?>">
    <div class="container">
      <?php if ($eyebrow): ?>
      <div class="flex items-center gap-3 mb-2">
        <div class="w-1 h-6 bg-[#cc2222]"></div>
        <span class="font-heading font-semibold text-xl uppercase tracking-[0.15em]" style="-webkit-text-stroke:1.5px rgba(255,255,255,0.45);color:transparent;"><?= $eyebrow ?></span>
      </div>
      <?php endif; ?>
      <h2 class="font-heading font-semibold text-4xl md:text-5xl uppercase tracking-wide text-white mb-10 leading-tight"><?= $title ?></h2>

      <?php foreach ($featured as $m): ?>
      <div class="machine-card bg-[#122135] border border-[#cc2222]/20 p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <div class="text-[#cc2222] text-xs font-medium tracking-widest uppercase"><?= htmlspecialchars($m['category_label']) ?></div>
              <div class="h-px flex-1 bg-[#cc2222]/20"></div>
            </div>
            <h3 class="font-heading font-semibold text-3xl uppercase tracking-wide text-white mb-3"><?= htmlspecialchars($m['name']) ?></h3>
            <p class="text-white/45 text-sm leading-relaxed"><?= htmlspecialchars($m['short_description']) ?></p>
          </div>
          <?php if (!empty($m['specs'])): ?>
          <div class="md:w-64 shrink-0">
            <table class="spec-table w-full text-sm">
              <tbody>
                <?php foreach ($m['specs'] as $spec): ?>
                <tr>
                  <th class="text-left"><?= htmlspecialchars($spec['key']) ?></th>
                  <td class="text-right text-white/70"><?= htmlspecialchars($spec['value']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (!empty($regular)): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 <?= count($regular) > 2 ? 'lg:grid-cols-3' : '' ?> gap-6">
        <?php foreach ($regular as $m): machine_card($m); endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endforeach; ?>

  <!-- WHY SECTION -->
  <section class="bg-[#0d1b2a] py-16 px-6 md:px-12">
    <div class="container">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="flex gap-4">
          <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
          </div>
          <div>
            <h3 class="font-heading font-bold text-lg uppercase tracking-widest text-white mb-2">Folyamatos fejlesztés</h3>
            <p class="text-white/45 text-sm leading-relaxed">Gépparkunk folyamatosan bővül. Az elmúlt 5 évben 3 új TRUMPF gépet helyeztünk üzembe a növekvő kapacitásigény kiszolgálásához.</p>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <div>
            <h3 class="font-heading font-bold text-lg uppercase tracking-widest text-white mb-2">Teljes kapacitás</h3>
            <p class="text-white/45 text-sm leading-relaxed">3 műszakos termelés, 8500 m² gyártóterületen. Prototípustól 100 000+ darabos sorozatig rugalmasan kiszolgálható igények.</p>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="w-10 h-10 bg-[#cc2222]/10 border border-[#cc2222]/20 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-[#cc2222]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
          </div>
          <div>
            <h3 class="font-heading font-bold text-lg uppercase tracking-widest text-white mb-2">Szervizelt gépek</h3>
            <p class="text-white/45 text-sm leading-relaxed">Minden gépünk gyári szervizszerződés alatt van. Az állásidő minimális, a gyártási minőség stabil és ellenőrzött.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section style="background:#cc2222;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 50% 50%,transparent 30%,rgba(0,0,0,0.35) 100%);pointer-events:none;"></div>
    <div style="position:absolute;top:0;left:0;width:120px;height:3px;background:rgba(255,255,255,0.25);"></div>
    <div style="position:absolute;top:0;left:0;width:3px;height:80px;background:rgba(255,255,255,0.25);"></div>
    <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:5rem;">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-10">
        <div>
          <div class="flex items-center gap-3 mb-5">
            <div style="width:28px;height:2px;background:rgba(255,255,255,0.55);"></div>
            <span style="color:rgba(255,255,255,0.65);font-size:0.7rem;letter-spacing:0.22em;font-weight:600;text-transform:uppercase;">Lépjen kapcsolatba velünk</span>
          </div>
          <h2 class="font-heading font-semibold text-4xl md:text-6xl uppercase tracking-wide text-white leading-tight mb-3">Kész az ajánlatra?<br>Írjon nekünk.</h2>
          <p class="text-white/70 text-sm md:text-base leading-relaxed max-w-lg">Töltse ki rövid igényfelmérő űrlapunkat, és 24 órán belül visszajelzünk pontos árajánlattal. Prototípustól nagysorozatig.</p>
        </div>
        <a href="kapcsolat.html" class="btn-clipped flex-shrink-0 bg-white text-[#cc2222] font-heading font-semibold text-lg uppercase tracking-widest px-12 py-4 hover:bg-white/90 transition-colors whitespace-nowrap">
          <span class="btn-label">Ajánlatot kérek</span><svg class="btn-arrow-icon" width="14" height="14" fill="none" stroke="#cc2222" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-[#060f1a] py-10 border-t border-white/8">
    <div class="container">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
        <div>
          <div class="flex items-center gap-3 mb-2">
            <img src="assets/images/linardics-logo.png" alt="Linardics Kft." class="h-8 w-auto">
            <span class="font-heading font-bold text-white text-base tracking-widest">LINARDICS KFT.</span>
          </div>
          <p class="text-white/35 text-xs leading-relaxed">8000 Székesfehérvár, Sóstói Ipari Park<br>+36 22 503 865 · linardicskft@linardics.hu</p>
        </div>
        <nav class="flex flex-wrap gap-x-8 gap-y-2 text-xs text-white/40 tracking-widest uppercase">
          <a href="szolgaltatasok/index.html" class="hover:text-white transition-colors">Szolgáltatásaink</a>
          <a href="geppark.php" class="hover:text-white transition-colors">Géppark</a>
          <a href="referenciak.html" class="hover:text-white transition-colors">Referenciák</a>
          <a href="rolunk.html" class="hover:text-white transition-colors">Rólunk</a>
          <a href="gyik.html" class="hover:text-white transition-colors">GYIK</a>
          <a href="kapcsolat.html" class="hover:text-white transition-colors">Kapcsolat</a>
        </nav>
      </div>
      <div class="flex flex-col md:flex-row items-center justify-between gap-2 pt-6 border-t border-white/8">
        <p class="text-white/25 text-xs">© 2026 Linardics Kft. – Minden jog fenntartva.</p>
        <p class="text-white/25 text-xs">ISO 9001 tanúsított lemezmegmunkálás – Székesfehérvár</p>
      </div>
    </div>
  </footer>

  <script>
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) { navbar.classList.add('navbar-scrolled'); navbar.classList.replace('py-5','py-3'); }
      else { navbar.classList.remove('navbar-scrolled'); navbar.classList.replace('py-3','py-5'); }
    });
    function toggleMenu() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('open');
      document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
    }

    // Auto object-fit: cover for landscape, contain for portrait images
    document.querySelectorAll('.machine-img').forEach(img => {
      const apply = () => {
        const portrait = img.naturalHeight > img.naturalWidth * 0.9;
        if (portrait) {
          img.style.cssText = 'width:auto;height:100%;max-width:100%;object-fit:contain;padding:12px;';
        } else {
          img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        }
      };
      if (img.complete && img.naturalWidth) apply();
      else img.addEventListener('load', apply);
    });
  </script>
</body>
</html>
