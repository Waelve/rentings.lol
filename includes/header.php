<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
$user = getCurrentUser();
$page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Rentings.lol — Plataforma inmobiliaria. Encuentra casas, departamentos, terrenos y locales sin intermediarios."/>
  <title><?= isset($pageTitle) ? sanitize($pageTitle).' | ' : '' ?>Rentings.lol</title>
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml"/>
  <link rel="stylesheet" href="/assets/css/style.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<header class="navbar" id="navbar">
  <div class="container nav-inner">

    <!-- Logo -->
    <a href="/" class="nav-logo" aria-label="Rentings.lol inicio">
      <span class="logo-icon">
        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="34" height="34" rx="8" fill="url(#grad-logo)"/>
          <path d="M17 6L28 14V28H22V21H12V28H6V14L17 6Z" fill="white"/>
          <defs>
            <linearGradient id="grad-logo" x1="0" y1="0" x2="34" y2="34" gradientUnits="userSpaceOnUse">
              <stop stop-color="#7B2FBE"/>
              <stop offset="1" stop-color="#F97316"/>
            </linearGradient>
          </defs>
        </svg>
      </span>
      <span class="logo-text">Rentings<span class="logo-dot">.lol</span></span>
    </a>

    <!-- Nav Links Desktop -->
    <nav class="nav-links" id="navLinks" aria-label="Menú principal">
      <a href="/pages/propiedades.php" class="nav-link <?= $page==='propiedades' ? 'active' : '' ?>">Explorar</a>
      <a href="/pages/propiedades.php?operacion=renta" class="nav-link">Rentas</a>
      <a href="/pages/propiedades.php?operacion=venta" class="nav-link">Ventas</a>
      <a href="/pages/como-funciona.php" class="nav-link">¿Cómo funciona?</a>
      <a href="/pages/precios.php" class="nav-link">Planes</a>
    </nav>

    <!-- Auth Buttons -->
    <div class="nav-auth">
      <?php if (isLoggedIn()): ?>
        <div class="nav-user-menu">
          <button class="btn-avatar" id="btnUserMenu" aria-label="Menú usuario">
            <span class="avatar-circle"><?= strtoupper(substr($user['nombre'],0,1)) ?></span>
            <span class="nav-username"><?= sanitize(explode(' ',$user['nombre'])[0]) ?></span>
            <svg class="chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <a href="/pages/dashboard.php"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg> Dashboard</a>
            <a href="/pages/mis-propiedades.php"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg> Mis propiedades</a>
            <a href="/pages/publicar.php"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg> Publicar</a>
            <hr/>
            <a href="/pages/logout.php" class="logout-link"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg> Cerrar sesión</a>
          </div>
        </div>
      <?php else: ?>
        <a href="/pages/login.php" class="btn btn-outline">Iniciar sesión</a>
        <a href="/pages/register.php" class="btn btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
          Registrarse
        </a>
      <?php endif; ?>
    </div>

    <!-- Hamburger Mobile -->
    <button class="nav-hamburger" id="btnHamburger" aria-label="Abrir menú" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <nav>
      <a href="/pages/propiedades.php">🏠 Explorar propiedades</a>
      <a href="/pages/propiedades.php?operacion=renta">🔑 Rentas</a>
      <a href="/pages/propiedades.php?operacion=venta">💰 Ventas</a>
      <a href="/pages/como-funciona.php">❓ ¿Cómo funciona?</a>
      <a href="/pages/precios.php">💎 Planes</a>
      <?php if (isLoggedIn()): ?>
        <hr/>
        <a href="/pages/dashboard.php">📊 Dashboard</a>
        <a href="/pages/publicar.php">➕ Publicar propiedad</a>
        <a href="/pages/logout.php">🚪 Cerrar sesión</a>
      <?php else: ?>
        <hr/>
        <a href="/pages/login.php" class="mobile-btn-outline">Iniciar sesión</a>
        <a href="/pages/register.php" class="mobile-btn-primary">Registrarse gratis</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<!-- Spacer para fixed header -->
<div class="navbar-spacer"></div>
