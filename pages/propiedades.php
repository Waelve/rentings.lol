<?php
$pageTitle = 'Explorar propiedades';
require_once __DIR__ . '/../includes/header.php';
$db = getDB();

// Filtros GET
$tipo      = $_GET['tipo']      ?? '';
$operacion = $_GET['operacion'] ?? '';
$colonia   = trim($_GET['colonia']   ?? '');
$precio_max= (int)($_GET['precio_max'] ?? 0);
$orden     = $_GET['orden']     ?? 'recientes';
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = 12;

// Query base
$where  = ['p.activo=1'];
$params = [];

if ($tipo)       { $where[] = 'p.tipo=?';      $params[] = $tipo; }
if ($operacion)  { $where[] = 'p.operacion=?'; $params[] = $operacion; }
if ($colonia)    { $where[] = 'p.colonia LIKE ?'; $params[] = '%'.$colonia.'%'; }
if ($precio_max) { $where[] = 'p.precio<=?';   $params[] = $precio_max; }

$wSQL = implode(' AND ', $where);

// Count total
$cntStmt = $db->prepare("SELECT COUNT(*) FROM propiedades p WHERE $wSQL");
$cntStmt->execute($params);
$total = $cntStmt->fetchColumn();
$total_pages = max(1, ceil($total / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

// Order
$orderSQL = match($orden) {
    'precio_asc'  => 'p.precio ASC',
    'precio_desc' => 'p.precio DESC',
    'destacadas'  => 'p.destacado DESC, p.created_at DESC',
    default       => 'p.created_at DESC',
};

// Propiedades
$stmt = $db->prepare("
  SELECT p.*, u.nombre as anunciante
  FROM propiedades p
  LEFT JOIN usuarios u ON u.id = p.usuario_id
  WHERE $wSQL
  ORDER BY $orderSQL
  LIMIT $per_page OFFSET $offset
");
$stmt->execute($params);
$props = $stmt->fetchAll();

function fmtPrecio($n, $m='MXN') {
    return '$'.number_format($n,0,'.',',').' '.$m;
}
function fmtTipo($t) {
    $map=['casa'=>'Casa','departamento'=>'Departamento','terreno'=>'Terreno','local'=>'Local',
          'bodega'=>'Bodega','quinta'=>'Quinta','rancho'=>'Rancho','oficina'=>'Oficina'];
    return $map[$t] ?? ucfirst($t);
}
function fmtOp($op) {
    $map=['venta'=>'Venta','renta'=>'Renta','venta_renta'=>'Venta/Renta'];
    return $map[$op] ?? ucfirst($op);
}

// Build query string helper
function buildQ($extra=[]) {
    $p = array_merge($_GET, $extra);
    return '?'.http_build_query(array_filter($p, fn($v) => $v !== '' && $v !== null));
}
?>

<!-- Header de página -->
<div class="props-header">
  <div class="container">
    <h1>Propiedades en Montemorelos</h1>
    <p><?= $total ?> resultado<?= $total!=1?'s':'' ?> encontrado<?= $total!=1?'s':'' ?></p>
  </div>
</div>

<section class="props-page">
  <div class="container">

    <!-- Barra de filtros -->
    <form method="GET" action="" class="props-filter-bar" style="margin-bottom:32px;">
      <div>
        <label class="form-label" for="fTipo">Tipo</label>
        <select id="fTipo" name="tipo" class="form-control" onchange="this.form.submit()">
          <option value="">Todos</option>
          <?php foreach(['casa'=>'🏠 Casa','departamento'=>'🏢 Departamento','terreno'=>'🌿 Terreno',
                         'local'=>'🏪 Local','bodega'=>'🏭 Bodega','quinta'=>'🌄 Quinta',
                         'rancho'=>'🐄 Rancho','oficina'=>'💼 Oficina'] as $k=>$v): ?>
          <option value="<?=$k?>" <?=$tipo===$k?'selected':''?>><?=$v?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label" for="fOp">Operación</label>
        <select id="fOp" name="operacion" class="form-control" onchange="this.form.submit()">
          <option value="">Todas</option>
          <option value="venta"      <?=$operacion==='venta'?'selected':''?>>💰 Venta</option>
          <option value="renta"      <?=$operacion==='renta'?'selected':''?>>🔑 Renta</option>
          <option value="venta_renta"<?=$operacion==='venta_renta'?'selected':''?>>🤝 Venta/Renta</option>
        </select>
      </div>
      <div>
        <label class="form-label" for="fColonia">Colonia/Zona</label>
        <input type="text" id="fColonia" name="colonia" class="form-control" value="<?=htmlspecialchars($colonia)?>" placeholder="Ej: Centro, Sabinos...">
      </div>
      <div>
        <label class="form-label" for="fPrecio">Precio máx.</label>
        <select id="fPrecio" name="precio_max" class="form-control" onchange="this.form.submit()">
          <option value="">Sin límite</option>
          <option value="500000"   <?=$precio_max==500000  ?'selected':''?>>$500,000</option>
          <option value="1000000"  <?=$precio_max==1000000 ?'selected':''?>>$1,000,000</option>
          <option value="2000000"  <?=$precio_max==2000000 ?'selected':''?>>$2,000,000</option>
          <option value="5000000"  <?=$precio_max==5000000 ?'selected':''?>>$5,000,000</option>
        </select>
      </div>
      <div>
        <label class="form-label" for="fOrden">Ordenar</label>
        <select id="fOrden" name="orden" class="form-control" onchange="this.form.submit()">
          <option value="recientes"   <?=$orden==='recientes'  ?'selected':''?>>Más recientes</option>
          <option value="destacadas"  <?=$orden==='destacadas' ?'selected':''?>>Destacadas</option>
          <option value="precio_asc"  <?=$orden==='precio_asc' ?'selected':''?>>Precio: menor a mayor</option>
          <option value="precio_desc" <?=$orden==='precio_desc'?'selected':''?>>Precio: mayor a menor</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-purple">Filtrar</button>
        <a href="/pages/propiedades.php" class="btn btn-ghost btn-sm" style="align-self:flex-end;">Limpiar</a>
      </div>
    </form>

    <!-- Grid de propiedades -->
    <?php if (empty($props)): ?>
    <div class="empty-state">
      <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
      <h3>No encontramos propiedades</h3>
      <p>Prueba con otros filtros o <a href="/pages/propiedades.php" style="color:var(--purple);">ver todas</a></p>
    </div>
    <?php else: ?>
    <div class="props-grid">
      <?php foreach($props as $p): ?>
      <article class="prop-card">
        <a href="/pages/propiedad.php?id=<?=$p['id']?>" style="display:contents;">
          <div class="prop-card-thumb">
            <?php if ($p['imagen_portada']): ?>
              <img src="<?=sanitize($p['imagen_portada'])?>" alt="<?=sanitize($p['titulo'])?>" loading="lazy">
            <?php else: ?>
              <div class="prop-card-thumb-bg">
                <svg width="80" height="80" fill="none" stroke="white" stroke-width="1" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
              </div>
            <?php endif; ?>
            <span class="prop-tipo-badge"><?=fmtTipo($p['tipo'])?></span>
            <span class="prop-op-badge <?=$p['operacion']?>"><?=fmtOp($p['operacion'])?></span>
            <?php if ($p['destacado']): ?><span class="prop-destacado">⭐ Destacada</span><?php endif; ?>
          </div>
        </a>
        <div class="prop-card-content">
          <div class="prop-card-price"><?=fmtPrecio($p['precio'],$p['moneda'])?> <span><?=$p['moneda']?></span></div>
          <div class="prop-card-title"><?=sanitize($p['titulo'])?></div>
          <div class="prop-card-address">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?=sanitize($p['colonia']??'')?><?=$p['colonia']?', ':''?><?=sanitize($p['ciudad'])?>, N.L.
          </div>
          <div class="prop-card-features">
            <?php if ($p['recamaras']): ?><div class="prop-feat-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 9V4a1 1 0 011-1h18a1 1 0 011 1v5"/><path d="M2 9h20v11H2z"/></svg><?=$p['recamaras']?> rec.</div><?php endif; ?>
            <?php if ($p['banos']):    ?><div class="prop-feat-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 12h16M4 6a2 2 0 012-2h1a2 2 0 012 2"/><rect x="4" y="12" width="16" height="8" rx="2"/></svg><?=$p['banos']?> baños</div><?php endif; ?>
            <?php if ($p['metros_c']): ?><div class="prop-feat-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg><?=(int)$p['metros_c']?> m²</div><?php endif; ?>
          </div>
        </div>
        <div class="prop-card-footer">
          <div class="prop-card-anunciante">
            <div class="prop-avatar"><?=strtoupper(substr($p['anunciante'],0,1))?></div>
            <?=sanitize(explode(' ',$p['anunciante'])[0])?>
          </div>
          <a href="/pages/propiedad.php?id=<?=$p['id']?>" class="btn btn-purple btn-sm">Ver detalles</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Paginación -->
    <?php if ($total_pages > 1): ?>
    <nav style="display:flex;justify-content:center;gap:8px;margin-top:40px;flex-wrap:wrap;">
      <?php if ($page > 1): ?>
        <a href="<?=buildQ(['page'=>$page-1])?>" class="btn btn-ghost btn-sm">← Anterior</a>
      <?php endif; ?>
      <?php for ($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++): ?>
        <a href="<?=buildQ(['page'=>$i])?>" class="btn btn-sm <?=$i===$page?'btn-purple':'btn-ghost'?>"><?=$i?></a>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?>
        <a href="<?=buildQ(['page'=>$page+1])?>" class="btn btn-ghost btn-sm">Siguiente →</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>

  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
