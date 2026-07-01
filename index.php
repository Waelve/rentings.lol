<?php
$pageTitle = 'Plataforma inmobiliaria sin comisiones';
require_once __DIR__ . '/includes/header.php';
$db = getDB();

// Estadísticas
$stats = $db->query("SELECT
  (SELECT COUNT(*) FROM propiedades WHERE activo=1) as total_props,
  (SELECT COUNT(*) FROM usuarios WHERE activo=1) as total_users,
  (SELECT COUNT(*) FROM propiedades WHERE destacado=1 AND activo=1) as destacadas
")->fetch();

// Propiedades destacadas (últimas 6)
$propsDest = $db->query("
  SELECT p.*, u.nombre as anunciante
  FROM propiedades p
  LEFT JOIN usuarios u ON u.id = p.usuario_id
  WHERE p.activo=1
  ORDER BY p.destacado DESC, p.created_at DESC
  LIMIT 6
")->fetchAll();

// Conteo por tipo
$tiposCounts = $db->query("
  SELECT tipo, COUNT(*) as cnt
  FROM propiedades WHERE activo=1
  GROUP BY tipo
")->fetchAll(PDO::FETCH_KEY_PAIR);

$tipos = [
  'casa'         => ['icon'=>'🏠','label'=>'Casas'],
  'departamento' => ['icon'=>'🏢','label'=>'Deptos'],
  'terreno'      => ['icon'=>'🌿','label'=>'Terrenos'],
  'local'        => ['icon'=>'🏪','label'=>'Locales'],
  'bodega'       => ['icon'=>'🏭','label'=>'Bodegas'],
  'quinta'       => ['icon'=>'🌄','label'=>'Quintas'],
  'rancho'       => ['icon'=>'🐄','label'=>'Ranchos'],
  'oficina'      => ['icon'=>'💼','label'=>'Oficinas'],
];

function fmtPrecio($n, $m='MXN') {
  return '$'.number_format($n,0,'.',',').' '.$m;
}
function fmtTipo($t) {
  $map=['casa'=>'Casa','departamento'=>'Depto','terreno'=>'Terreno','local'=>'Local',
        'bodega'=>'Bodega','quinta'=>'Quinta','rancho'=>'Rancho','oficina'=>'Oficina'];
  return $map[$t] ?? ucfirst($t);
}
function fmtOp($op) {
  $map=['venta'=>'Venta','renta'=>'Renta','venta_renta'=>'Venta/Renta'];
  return $map[$op] ?? ucfirst($op);
}
?>

<!-- ========== HERO ========== -->
<section class="hero">
  <div class="container">
    <div class="hero-inner">
      <div class="hero-content">
        <div class="hero-badge">
          <span class="hero-badge-dot"></span>
          Plataforma inmobiliaria — Montemorelos, N.L.
        </div>
        <h1 class="hero-title">
          Propiedades en<br>
          <span class="accent">venta y renta</span><br>
          sin comisiones
        </h1>
        <p class="hero-subtitle">
          Encuentra casas, departamentos, terrenos y locales en Montemorelos, Nuevo León.
          Conecta directamente con propietarios, sin intermediarios y sin costos ocultos.
        </p>
        <div class="hero-actions">
          <a href="/pages/propiedades.php" class="btn btn-primary btn-lg">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Buscar propiedades
          </a>
          <a href="/pages/register.php" class="btn btn-outline btn-lg">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Publicar gratis
          </a>
        </div>
        <div class="hero-trust">
          <div class="trust-item">
            <span class="trust-icon">✓</span>
            Anunciantes verificados
          </div>
          <div class="trust-item">
            <span class="trust-icon">🤝</span>
            Sin intermediarios
          </div>
          <div class="trust-item">
            <span class="trust-icon">📍</span>
            100% Montemorelos
          </div>
        </div>
      </div>

      <!-- Visual flotante (desktop) -->
      <div class="hero-visual">
        <?php if (!empty($propsDest)): $p = $propsDest[0]; ?>
        <div class="prop-card-float">
          <div class="prop-card-img">
            <div class="prop-card-img-bg">
              <svg width="100" height="100" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
            </div>
            <span class="prop-type-badge"><?= fmtTipo($p['tipo']) ?></span>
            <span class="prop-verified-badge">✓ Verificado</span>
          </div>
          <div class="prop-card-body">
            <div class="prop-price"><?= fmtPrecio($p['precio'],$p['moneda']) ?> <span><?= strtoupper($p['moneda']) ?></span></div>
            <div class="prop-address">
              <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <?= sanitize($p['colonia'] ?? $p['ciudad']) ?>, <?= sanitize($p['ciudad']) ?>
            </div>
            <div class="prop-features">
              <?php if ($p['recamaras']): ?><span class="prop-feat">🛏 <?= $p['recamaras'] ?> rec.</span><?php endif; ?>
              <?php if ($p['banos']):     ?><span class="prop-feat">🚿 <?= $p['banos'] ?> baños</span><?php endif; ?>
              <?php if ($p['metros_c']):  ?><span class="prop-feat">📐 <?= (int)$p['metros_c'] ?> m²</span><?php endif; ?>
            </div>
            <div class="no-commission-badge">🚫 Sin comisiones</div>
          </div>
        </div>
        <?php endif; ?>
        <div class="stats-mini">
          <div class="stat-mini-card">
            <div class="stat-mini-num" data-counter data-target="<?= $stats['total_props'] ?>"><?= $stats['total_props'] ?></div>
            <div class="stat-mini-label">Propiedades</div>
          </div>
          <div class="stat-mini-card">
            <div class="stat-mini-num" data-counter data-target="<?= $stats['total_users'] ?>"><?= $stats['total_users'] ?></div>
            <div class="stat-mini-label">Usuarios</div>
          </div>
          <div class="stat-mini-card">
            <div class="stat-mini-num" data-counter data-target="100" data-suffix="%">100%</div>
            <div class="stat-mini-label">Sin comisión</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========== BUSCADOR ========== -->
<section class="search-section">
  <div class="container">
    <div class="search-card">
      <div class="search-tabs">
        <div class="search-tab active" data-op="">🏠 Inicio</div>
        <div class="search-tab" data-op="venta">💰 Comprar</div>
        <div class="search-tab" data-op="renta">🔑 Rentar</div>
        <div class="search-tab" data-op="venta_renta">🤝 Compra/Renta</div>
      </div>
      <form action="/pages/propiedades.php" method="GET" class="search-form">
        <input type="hidden" name="operacion" id="searchOperacion" value="">
        <div class="form-group">
          <label for="srchColonia">Colonia o zona</label>
          <input type="text" id="srchColonia" name="colonia" class="form-control" placeholder="Ej: Centro, Los Sabinos, Zona UM...">
        </div>
        <div class="form-group">
          <label for="srchTipo">Tipo de propiedad</label>
          <select id="srchTipo" name="tipo" class="form-control">
            <option value="">Todos los tipos</option>
            <?php foreach($tipos as $k=>$t): ?>
            <option value="<?= $k ?>"><?= $t['icon'].' '.$t['label'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="srchPrecio">Precio máximo</label>
          <select id="srchPrecio" name="precio_max" class="form-control">
            <option value="">Sin límite</option>
            <option value="500000">Hasta $500,000</option>
            <option value="1000000">Hasta $1,000,000</option>
            <option value="2000000">Hasta $2,000,000</option>
            <option value="5000000">Hasta $5,000,000</option>
          </select>
        </div>
        <div class="form-group">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-primary w-full">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Buscar
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- ========== ESTADÍSTICAS ========== -->
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon purple">🏠</div>
        <div class="stat-num purple" data-counter data-target="<?= $stats['total_props'] ?>"><?= $stats['total_props'] ?></div>
        <div class="stat-label">Propiedades publicadas</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">👥</div>
        <div class="stat-num orange" data-counter data-target="<?= $stats['total_users'] ?>"><?= $stats['total_users'] ?></div>
        <div class="stat-label">Usuarios registrados</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">🤝</div>
        <div class="stat-num blue" data-counter data-target="100" data-suffix="%">100%</div>
        <div class="stat-label">Tratos sin intermediarios</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon yellow">📍</div>
        <div class="stat-num yellow">Local</div>
        <div class="stat-label">Enfoque en Montemorelos, N.L.</div>
      </div>
    </div>
  </div>
</section>

<!-- ========== PROPIEDADES DESTACADAS ========== -->
<?php if (!empty($propsDest)): ?>
<section class="properties-section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">🔥 Propiedades destacadas</div>
      <h2 class="section-title">Las mejores opciones<br>en <span class="text-gradient">Montemorelos</span></h2>
      <p class="section-sub">Propiedades verificadas, sin intermediarios, directo con el propietario.</p>
    </div>
    <div class="props-grid">
      <?php foreach($propsDest as $p): ?>
      <article class="prop-card">
        <div class="prop-card-thumb">
          <?php if ($p['imagen_portada']): ?>
            <img src="<?= sanitize($p['imagen_portada']) ?>" alt="<?= sanitize($p['titulo']) ?>" loading="lazy">
          <?php else: ?>
            <div class="prop-card-thumb-bg">
              <svg width="80" height="80" fill="none" stroke="white" stroke-width="1" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
            </div>
          <?php endif; ?>
          <span class="prop-tipo-badge"><?= fmtTipo($p['tipo']) ?></span>
          <span class="prop-op-badge <?= $p['operacion'] ?>"><?= fmtOp($p['operacion']) ?></span>
          <?php if ($p['destacado']): ?>
            <span class="prop-destacado">⭐ Destacada</span>
          <?php endif; ?>
        </div>
        <div class="prop-card-content">
          <div class="prop-card-price"><?= fmtPrecio($p['precio'],$p['moneda']) ?> <span><?= $p['moneda'] ?></span></div>
          <div class="prop-card-title"><?= sanitize($p['titulo']) ?></div>
          <div class="prop-card-address">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= sanitize($p['colonia'] ?? '') ?><?= $p['colonia'] ? ', ' : '' ?><?= sanitize($p['ciudad']) ?>, N.L.
          </div>
          <div class="prop-card-features">
            <?php if ($p['recamaras']): ?>
            <div class="prop-feat-item">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 9V4a1 1 0 011-1h18a1 1 0 011 1v5"/><path d="M2 9h20v11H2z"/></svg>
              <?= $p['recamaras'] ?> rec.
            </div>
            <?php endif; ?>
            <?php if ($p['banos']): ?>
            <div class="prop-feat-item">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 12h16M4 6a2 2 0 012-2h1a2 2 0 012 2"/><rect x="4" y="12" width="16" height="8" rx="2"/></svg>
              <?= $p['banos'] ?> baños
            </div>
            <?php endif; ?>
            <?php if ($p['metros_c']): ?>
            <div class="prop-feat-item">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
              <?= (int)$p['metros_c'] ?> m²
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="prop-card-footer">
          <div class="prop-card-anunciante">
            <div class="prop-avatar"><?= strtoupper(substr($p['anunciante'],0,1)) ?></div>
            <?= sanitize(explode(' ',$p['anunciante'])[0]) ?>
          </div>
          <a href="/pages/propiedad.php?id=<?= $p['id'] ?>" class="btn btn-purple btn-sm">Ver más</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center">
      <a href="/pages/propiedades.php" class="btn btn-ghost btn-lg">
        Ver todas las propiedades
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========== TIPOS DE PROPIEDAD ========== -->
<section class="tipos-section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge orange">¿Qué estás buscando?</div>
      <h2 class="section-title">Propiedades por <span class="text-gradient">tipo</span></h2>
      <p class="section-sub">Casas, departamentos, terrenos, locales y más en Montemorelos, N.L.</p>
    </div>
    <div class="tipos-grid">
      <?php foreach($tipos as $k=>$t): ?>
      <a href="/pages/propiedades.php?tipo=<?= $k ?>" class="tipo-card">
        <span class="tipo-icon"><?= $t['icon'] ?></span>
        <div class="tipo-name"><?= $t['label'] ?></div>
        <div class="tipo-count"><?= ($tiposCounts[$k] ?? 0) ?> disponibles</div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========== CÓMO FUNCIONA ========== -->
<section class="como-section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge blue">Simple y rápido</div>
      <h2 class="section-title">¿Cómo funciona <span class="text-gradient">Rentings.lol</span>?</h2>
      <p class="section-sub">Publica o encuentra tu propiedad en Montemorelos en minutos.</p>
    </div>
    <div class="como-grid">
      <div class="como-step">
        <div class="como-num">1</div>
        <div class="como-title">Regístrate gratis</div>
        <p class="como-desc">Crea tu cuenta en menos de 2 minutos. Solo necesitas tu correo y nombre. Sin tarjeta de crédito.</p>
      </div>
      <div class="como-step">
        <div class="como-num">2</div>
        <div class="como-title">Publica tu propiedad</div>
        <p class="como-desc">Sube fotos, escribe la descripción y elige tu plan. Tu anuncio estará visible en minutos.</p>
      </div>
      <div class="como-step">
        <div class="como-num">3</div>
        <div class="como-title">Conecta directamente</div>
        <p class="como-desc">Recibe mensajes y llamadas directamente. Sin intermediarios, sin comisiones. Tú decides.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========== BENEFICIOS ========== -->
<section class="beneficios-section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);">¿Por qué Rentings.lol?</div>
      <h2 class="section-title" style="color:var(--white);">La plataforma <span class="text-gradient">local</span> de bienes raíces</h2>
      <p class="section-sub" style="color:rgba(255,255,255,.55);">Construida para el mercado inmobiliario de Montemorelos, Nuevo León.</p>
    </div>
    <div class="beneficios-grid">
      <div class="beneficio-card">
        <div class="beneficio-icon">🚫</div>
        <div class="beneficio-title">Sin comisiones</div>
        <p class="beneficio-desc">Pagas solo el anuncio. No hay comisión por venta o renta. Todo el dinero entre propietario e interesado.</p>
      </div>
      <div class="beneficio-card">
        <div class="beneficio-icon">⚡</div>
        <div class="beneficio-title">Publicación instantánea</div>
        <p class="beneficio-desc">Tu propiedad está visible en menos de 5 minutos. Proceso simple y sin burocracia.</p>
      </div>
      <div class="beneficio-card">
        <div class="beneficio-icon">📍</div>
        <div class="beneficio-title">Enfoque local</div>
        <p class="beneficio-desc">100% dedicado a Montemorelos y alrededores. No te perdemos entre miles de ciudades.</p>
      </div>
      <div class="beneficio-card">
        <div class="beneficio-icon">🔒</div>
        <div class="beneficio-title">Anunciantes verificados</div>
        <p class="beneficio-desc">Revisamos a los anunciantes para garantizar que son propietarios o autorizados reales.</p>
      </div>
      <div class="beneficio-card">
        <div class="beneficio-icon">💬</div>
        <div class="beneficio-title">Contacto directo</div>
        <p class="beneficio-desc">Envía mensajes directo al propietario desde la plataforma. Sin intermediarios de ningún tipo.</p>
      </div>
      <div class="beneficio-card">
        <div class="beneficio-icon">📱</div>
        <div class="beneficio-title">Funciona en todo</div>
        <p class="beneficio-desc">Diseñada para PC, tablet y celular. Accede desde cualquier dispositivo sin instalar nada.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========== PLANES ========== -->
<section class="planes-section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">🔥 Precios de lanzamiento</div>
      <h2 class="section-title">Planes <span class="text-gradient">transparentes</span></h2>
      <p class="section-sub">Sin comisiones. Paga solo tu anuncio. Cancela cuando quieras.</p>
    </div>
    <div class="planes-grid">
      <!-- Plan Básico -->
      <div class="plan-card">
        <div class="plan-name">Básico</div>
        <div class="plan-price"><sup>$</sup>199 <span>/ mes</span></div>
        <p class="plan-desc">Perfecto para comenzar a publicar tu propiedad.</p>
        <ul class="plan-features">
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>1 propiedad publicada</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Hasta 5 fotos</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Formulario de contacto</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>30 días de vigencia</li>
          <li class="disabled"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>Sin posición destacada</li>
          <li class="disabled"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>Sin estadísticas</li>
        </ul>
        <a href="/pages/register.php" class="btn btn-ghost w-full">Comenzar</a>
      </div>
      <!-- Plan Destacado -->
      <div class="plan-card destacado">
        <div class="plan-popular-badge">⭐ Más popular</div>
        <div class="plan-name">Destacado</div>
        <div class="plan-price"><sup>$</sup>399 <span>/ mes</span></div>
        <p class="plan-desc">Destaca tu propiedad y recibe más contactos.</p>
        <ul class="plan-features">
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>3 propiedades publicadas</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Hasta 15 fotos por propiedad</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Posición destacada</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>60 días de vigencia</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Estadísticas básicas</li>
          <li class="disabled"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>Sin soporte prioritario</li>
        </ul>
        <a href="/pages/register.php" class="btn btn-primary w-full">Elegir Destacado</a>
      </div>
      <!-- Plan Premium -->
      <div class="plan-card">
        <div class="plan-name">Premium</div>
        <div class="plan-price"><sup>$</sup>699 <span>/ mes</span></div>
        <p class="plan-desc">La experiencia completa para inmobiliarias y agentes.</p>
        <ul class="plan-features">
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Propiedades ilimitadas</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Fotos ilimitadas</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Top de resultados siempre</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Vigencia permanente</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Estadísticas avanzadas</li>
          <li><svg class="check-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Soporte prioritario</li>
        </ul>
        <a href="/pages/register.php" class="btn btn-purple w-full">Elegir Premium</a>
      </div>
    </div>
  </div>
</section>

<!-- ========== CTA FINAL ========== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-inner">
      <h2 class="cta-title">¿Buscas propiedad en Montemorelos?</h2>
      <p class="cta-sub">Explora casas en renta y venta, departamentos, terrenos y locales. Todo directo con propietarios, sin intermediarios.</p>
      <div class="cta-actions">
        <a href="/pages/propiedades.php" class="btn btn-white btn-lg">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          Ver propiedades
        </a>
        <a href="/pages/register.php" class="btn btn-white-outline btn-lg">
          Registro gratuito →
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
