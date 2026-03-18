<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Admin') ?> – Linardics CMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..700;1,14..32,300..700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:        #080d16;
      --surface:   #0e1623;
      --elevated:  #151f30;
      --border:    rgba(255,255,255,0.07);
      --border-md: rgba(255,255,255,0.12);
      --red:       #dc2626;
      --red-dim:   rgba(220,38,38,0.12);
      --red-border:rgba(220,38,38,0.25);
      --text:      #f1f5f9;
      --muted:     rgba(241,245,249,0.5);
      --subtle:    rgba(241,245,249,0.28);
      --radius:    10px;
      --sidebar-w: 220px;
    }

    html { font-family: 'Inter', system-ui, sans-serif; font-size: 14px; }
    body { background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 99px; }

    /* ── SIDEBAR ── */
    #sidebar {
      width: var(--sidebar-w);
      flex-shrink: 0;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
    }

    .sidebar-logo {
      padding: 20px 16px 18px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar-logo img { height: 28px; width: auto; filter: brightness(0) invert(1); }
    .sidebar-logo-text { line-height: 1; }
    .sidebar-logo-name { font-size: 13px; font-weight: 700; letter-spacing: 0.08em; color: var(--text); }
    .sidebar-logo-sub  { font-size: 10px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--red); margin-top: 2px; }

    .sidebar-nav { flex: 1; padding: 10px 8px; }
    .nav-section-label {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--subtle);
      padding: 12px 8px 6px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 8px 10px;
      border-radius: 7px;
      font-size: 13px;
      font-weight: 500;
      color: var(--muted);
      text-decoration: none;
      transition: background 0.15s, color 0.15s;
      margin-bottom: 2px;
    }
    .nav-item svg { width: 15px; height: 15px; flex-shrink: 0; opacity: 0.7; transition: opacity 0.15s; }
    .nav-item:hover { background: rgba(255,255,255,0.05); color: var(--text); }
    .nav-item:hover svg { opacity: 1; }
    .nav-item.active {
      background: var(--red-dim);
      color: #f87171;
      border: 1px solid var(--red-border);
    }
    .nav-item.active svg { opacity: 1; color: #f87171; }

    .nav-divider { border: none; border-top: 1px solid var(--border); margin: 8px 0; }

    .sidebar-footer {
      padding: 12px 8px;
      border-top: 1px solid var(--border);
    }
    .user-card {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 8px 10px;
      border-radius: 7px;
      margin-bottom: 4px;
    }
    .user-avatar {
      width: 28px; height: 28px;
      border-radius: 50%;
      background: var(--red-dim);
      border: 1px solid var(--red-border);
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #f87171; flex-shrink: 0;
    }
    .user-name  { font-size: 12px; font-weight: 600; color: var(--text); line-height: 1.2; }
    .user-email { font-size: 10px; color: var(--subtle); }
    .logout-btn {
      display: flex; align-items: center; gap: 9px;
      padding: 7px 10px;
      border-radius: 7px;
      font-size: 12px; font-weight: 500;
      color: rgba(248,113,113,0.55);
      text-decoration: none;
      transition: background 0.15s, color 0.15s;
    }
    .logout-btn:hover { background: rgba(220,38,38,0.08); color: #f87171; }
    .logout-btn svg { width: 14px; height: 14px; }

    /* ── MAIN ── */
    #main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    #topbar {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 0 32px;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .topbar-title { font-size: 15px; font-weight: 600; color: var(--text); }
    .topbar-badge {
      font-size: 11px; font-weight: 500;
      background: var(--elevated);
      border: 1px solid var(--border);
      color: var(--muted);
      padding: 3px 10px;
      border-radius: 99px;
    }

    #content { padding: 32px; flex: 1; animation: contentFadeIn 0.22s ease; }
    @keyframes contentFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    /* ── FORM ELEMENTS ── */
    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: var(--muted);
      margin-bottom: 6px;
      letter-spacing: 0.01em;
    }
    .form-input {
      width: 100%;
      background: var(--elevated);
      border: 1px solid var(--border-md);
      border-radius: 7px;
      color: var(--text);
      padding: 9px 12px;
      font-size: 13.5px;
      font-family: inherit;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-input:focus {
      border-color: var(--red);
      box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
    }
    .form-input::placeholder { color: var(--subtle); }
    select.form-input {
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='rgba(241,245,249,0.35)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 34px;
    }
    select.form-input option { background: #0e1623; }
    textarea.form-input { resize: vertical; }

    /* ── BUTTONS ── */
    .btn-primary {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--red);
      color: #fff;
      border: none;
      border-radius: 7px;
      padding: 9px 16px;
      font-size: 13px;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      transition: background 0.15s, box-shadow 0.15s;
      text-decoration: none;
      white-space: nowrap;
    }
    .btn-primary:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,0.3); }

    .btn-secondary {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--elevated);
      color: var(--muted);
      border: 1px solid var(--border-md);
      border-radius: 7px;
      padding: 9px 16px;
      font-size: 13px;
      font-weight: 500;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
      white-space: nowrap;
    }
    .btn-secondary:hover { color: var(--text); border-color: rgba(255,255,255,0.22); background: rgba(255,255,255,0.05); }

    .btn-ghost {
      display: inline-flex; align-items: center; gap: 5px;
      background: transparent;
      color: var(--muted);
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 6px 11px;
      font-size: 12px;
      font-weight: 500;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
    }
    .btn-ghost:hover { color: var(--text); border-color: var(--border-md); }

    .btn-danger-ghost {
      display: inline-flex; align-items: center; gap: 5px;
      background: transparent;
      color: rgba(248,113,113,0.5);
      border: 1px solid rgba(220,38,38,0.15);
      border-radius: 6px;
      padding: 6px 11px;
      font-size: 12px;
      font-weight: 500;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
    }
    .btn-danger-ghost:hover { color: #f87171; border-color: rgba(220,38,38,0.35); background: rgba(220,38,38,0.06); }

    /* ── CARD ── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
    }
    .card-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .card-title { font-size: 14px; font-weight: 600; color: var(--text); }
    .card-body  { padding: 22px; }

    /* ── SECTION LABEL ── */
    .section-label {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--subtle);
      margin-bottom: 12px;
    }

    /* ── TABLE ── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th {
      padding: 11px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--subtle);
      border-bottom: 1px solid var(--border);
    }
    .data-table tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background 0.1s;
    }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: rgba(255,255,255,0.025); }
    .data-table td { padding: 13px 16px; vertical-align: middle; color: var(--muted); }

    /* ── BADGE / PILL ── */
    .pill {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 99px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.03em;
    }
    .pill-red   { background: var(--red-dim); color: #f87171; border: 1px solid var(--red-border); }
    .pill-slate { background: rgba(255,255,255,0.05); color: var(--muted); border: 1px solid var(--border); }
    .pill-green { background: rgba(34,197,94,0.1); color: #86efac; border: 1px solid rgba(34,197,94,0.2); }

    /* ── TOGGLE ── */
    .toggle {
      position: relative;
      display: inline-block;
      width: 36px; height: 20px;
      flex-shrink: 0;
    }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; inset: 0;
      background: rgba(255,255,255,0.1);
      border-radius: 99px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .toggle-slider::before {
      content: '';
      position: absolute;
      left: 3px; top: 3px;
      width: 14px; height: 14px;
      background: rgba(255,255,255,0.5);
      border-radius: 50%;
      transition: transform 0.2s, background 0.2s;
    }
    .toggle input:checked + .toggle-slider { background: var(--red); }
    .toggle input:checked + .toggle-slider::before { transform: translateX(16px); background: #fff; }

    /* ── DRAG HANDLE ── */
    .drag-handle { cursor: grab; color: var(--subtle); transition: color 0.15s; }
    .drag-handle:hover { color: var(--muted); }
    .drag-handle:active { cursor: grabbing; }

    /* ── TOAST ── */
    .toast {
      position: fixed; bottom: 24px; right: 24px; z-index: 9999;
      background: var(--elevated);
      border: 1px solid var(--border-md);
      border-radius: 9px;
      padding: 12px 16px;
      font-size: 13px; font-weight: 500;
      display: flex; align-items: center; gap: 8px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.4);
      animation: toastIn 0.2s ease;
    }
    @keyframes toastIn { from { transform: translateY(8px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* ── STAT CARD ── */
    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px 22px;
    }
    .stat-value { font-size: 28px; font-weight: 700; line-height: 1; margin-bottom: 4px; }
    .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; }

    /* ── QUICK LINK ── */
    .quick-link {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px;
      display: flex; align-items: center; gap: 14px;
      text-decoration: none;
      transition: border-color 0.15s, background 0.15s;
    }
    .quick-link:hover { border-color: var(--red-border); background: rgba(220,38,38,0.03); }
    .quick-link-icon {
      width: 38px; height: 38px;
      background: var(--red-dim);
      border: 1px solid var(--red-border);
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .quick-link-icon svg { width: 17px; height: 17px; color: #f87171; }
    .quick-link-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
    .quick-link-sub   { font-size: 11px; color: var(--muted); }

    /* ── SPEC ROW ── */
    .spec-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
    .spec-row .form-input { flex: 1; }
    .spec-remove {
      background: transparent; border: 1px solid var(--border); border-radius: 6px;
      color: var(--subtle); cursor: pointer; padding: 8px;
      transition: all 0.15s; flex-shrink: 0;
      display: flex; align-items: center;
    }
    .spec-remove:hover { color: #f87171; border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }
    .spec-remove svg { width: 14px; height: 14px; }

    /* ── CHECKBOX ── */
    input[type=checkbox] {
      width: 15px; height: 15px;
      accent-color: var(--red);
      cursor: pointer;
    }

    /* ── EMPTY STATE ── */
    .empty-state { padding: 64px 24px; text-align: center; color: var(--subtle); font-size: 13px; }
  </style>
</head>
<body>

<?php $admin = current_admin(); $current_page = basename($_SERVER['PHP_SELF'], '.php'); ?>

<!-- SIDEBAR -->
<aside id="sidebar">
  <div class="sidebar-logo">
    <img src="../assets/images/linardics-logo.png" alt="Linardics">
    <div class="sidebar-logo-text">
      <div class="sidebar-logo-name">Linardics</div>
      <div class="sidebar-logo-sub">CMS Admin</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Navigáció</div>

    <a href="index.php" class="nav-item <?= $current_page === 'index' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
      Dashboard
    </a>

    <a href="machines.php" class="nav-item <?= in_array($current_page, ['machines','machine-edit']) ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
      Gépek
    </a>

    <a href="lists.php" class="nav-item <?= $current_page === 'lists' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
      Listák
    </a>

    <a href="settings.php" class="nav-item <?= $current_page === 'settings' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
      Beállítások
    </a>

    <a href="help.php" class="nav-item <?= $current_page === 'help' ? 'active' : '' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Súgó
    </a>

    <hr class="nav-divider">

    <a href="../geppark.php" target="_blank" class="nav-item">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      Weboldal előnézet
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar"><?= strtoupper(substr($admin['name'], 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($admin['name']) ?></div>
        <div class="user-email"><?= htmlspecialchars($admin['email']) ?></div>
      </div>
    </div>
    <a href="logout.php" class="logout-btn">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Kijelentkezés
    </a>
  </div>
</aside>

<!-- MAIN -->
<main id="main">
  <div id="topbar">
    <span class="topbar-title"><?= htmlspecialchars($page_title ?? '') ?></span>
    <span class="topbar-badge">Linardics CMS</span>
  </div>
  <div id="content">
