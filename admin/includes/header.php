<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Admin') ?> – Linardics CMS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', sans-serif; }
    .font-heading { font-family: 'Barlow Condensed', sans-serif; }
    .sidebar-link { display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.875rem; font-size:0.8rem; font-weight:500; letter-spacing:0.05em; text-transform:uppercase; color:rgba(255,255,255,0.45); transition:color 0.15s,background 0.15s; border-radius:2px; }
    .sidebar-link:hover, .sidebar-link.active { color:#fff; background:rgba(255,255,255,0.06); }
    .sidebar-link.active { color:#cc2222; background:rgba(204,34,34,0.08); }
    .toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; padding:0.875rem 1.25rem; font-size:0.85rem; font-weight:500; display:flex; align-items:center; gap:0.5rem; animation:slideIn 0.25s ease; }
    @keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
    .form-input { width:100%; background:#060f1a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0.625rem 0.875rem; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
    .form-input:focus { border-color:#cc2222; }
    .form-input::placeholder { color:rgba(255,255,255,0.2); }
    .form-label { display:block; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.4); margin-bottom:0.375rem; }
    .btn-primary { background:#cc2222; color:#fff; padding:0.625rem 1.25rem; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; cursor:pointer; border:none; transition:background 0.15s; }
    .btn-primary:hover { background:#a01818; }
    .btn-secondary { background:transparent; color:rgba(255,255,255,0.5); border:1px solid rgba(255,255,255,0.12); padding:0.625rem 1.25rem; font-size:0.8rem; font-weight:500; text-transform:uppercase; letter-spacing:0.08em; cursor:pointer; transition:all 0.15s; }
    .btn-secondary:hover { color:#fff; border-color:rgba(255,255,255,0.3); }
    .btn-danger { background:rgba(204,34,34,0.1); color:#cc2222; border:1px solid rgba(204,34,34,0.2); padding:0.375rem 0.75rem; font-size:0.75rem; font-weight:500; cursor:pointer; transition:all 0.15s; }
    .btn-danger:hover { background:#cc2222; color:#fff; }
    select.form-input option { background:#0d1b2a; }
  </style>
</head>
<body class="bg-[#060f1a] text-white min-h-screen flex">

<?php $admin = current_admin(); $current_page = basename($_SERVER['PHP_SELF'], '.php'); ?>

<!-- Sidebar -->
<aside class="w-56 shrink-0 bg-[#060f1a] border-r border-white/8 flex flex-col min-h-screen sticky top-0">
  <div class="px-4 py-5 border-b border-white/8">
    <div class="flex items-center gap-2.5">
      <img src="../assets/images/linardics-logo.png" alt="Linardics" class="h-7 w-auto" style="filter:brightness(0) invert(1);">
      <div>
        <div class="font-heading font-bold text-sm tracking-widest text-white leading-none">LINARDICS</div>
        <div class="text-[10px] text-[#cc2222] tracking-widest uppercase font-semibold">CMS Admin</div>
      </div>
    </div>
  </div>

  <nav class="flex-1 py-4 px-2 space-y-0.5">
    <a href="index.php" class="sidebar-link <?= $current_page === 'index' ? 'active' : '' ?>">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      Dashboard
    </a>
    <a href="machines.php" class="sidebar-link <?= in_array($current_page, ['machines','machine-edit']) ? 'active' : '' ?>">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
      Gépek
    </a>
    <a href="settings.php" class="sidebar-link <?= $current_page === 'settings' ? 'active' : '' ?>">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
      Oldal beállítások
    </a>
    <div class="pt-2 mt-2 border-t border-white/8">
      <a href="../geppark.php" target="_blank" class="sidebar-link">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Előnézet
      </a>
    </div>
  </nav>

  <div class="px-4 py-4 border-t border-white/8">
    <div class="text-xs text-white/30 mb-2 truncate"><?= htmlspecialchars($admin['email']) ?></div>
    <a href="logout.php" class="sidebar-link" style="color:rgba(204,34,34,0.6);">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Kijelentkezés
    </a>
  </div>
</aside>

<!-- Main -->
<main class="flex-1 min-w-0">
  <!-- Top bar -->
  <div class="bg-[#0a1929] border-b border-white/8 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
    <h1 class="font-heading font-bold text-2xl uppercase tracking-widest"><?= htmlspecialchars($page_title ?? '') ?></h1>
    <div class="flex items-center gap-3 text-xs text-white/40">
      <span><?= htmlspecialchars($admin['name']) ?></span>
    </div>
  </div>
  <div class="p-8">
