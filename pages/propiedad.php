<?php
require_once __DIR__ . '/../includes/header.php';
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/propiedades.php');

$stmt = $db->prepare("SELECT p.*, u.nombre as anunciante, u.telefono as tel_anunciante, u.email as email_anunciante FROM propiedades p LEFT JOIN usuarios u ON u.id=p.usuario_id WHERE p.id=? AND p.activo=1");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { header('HTTP/1.0 404 Not Found'); echo '<div style="text-align:center;padding:100px;font-family:sans-serif;"><h2>Propiedad no encontrada</h2><a href="/pages/propiedades.php">← Volver</a></div>'; exit; }

// Incrementar vistas
$db->prepare("UPDATE propiedades SET vistas=vistas+1 WHERE id=?")->execute([$id]);

$pageTitle = $p['titulo'];

$imgs = $db->prepare("SELECT url FROM imagenes WHERE propiedad_id=? ORDER BY orden ASC");
$imgs->execute([$id]);
$imagenes = $imgs->fetchAll();

// Contacto
$msgOk = $msgErr = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['contactar'])) {
    $cnombre  = trim($_POST['c_nombre']   ?? '');
    $cemail   = trim($_POST['c_email']    ?? '');
    $ctel     = trim($_POST['c_telefono'] ?? '');
    $cmensaje = trim($_POST['c_mensaje']  ?? '');
    if (!$cnombre || !$cemail || !$cmensaje) {
        $msgErr = 'Completa todos los campos obligatorios.';
    } else {
        $ins = $db->prepare("INSERT INTO contactos (propiedad_id,nombre,email,telefono,mensaje) VALUES (?,?,?,?,?)");
        $ins->execute([$id,$cnombre,$cemail,$ctel,$cmensaje]);
        $msgOk = '¡Mensaje enviado! El anunciante se pondrá en contacto contigo pronto.';
    }
}

function fmtPrecio($n,$m='MXN'){return '$'.number_format($n,0,'.',',').' '.$m;}
function fmtTipo($t){$m=['casa'=>'Casa','departamento'=>'Departamento','terreno'=>'Terreno','local'=>'Local','bodega'=>'Bodega','quinta'=>'Quinta','rancho'=>'Rancho','oficina'=>'Oficina'];return $m[$t]??ucfirst($t);}
function fmtOp($o){$m=['venta'=>'En Venta','renta'=>'En Renta','venta_renta'=>'Venta/Renta'];return $m[$o]??ucfirst($o);}
?>
<div class="props-header" style="padding:40px 0 60px;">
  <div class="container">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <a href="/pages/propiedades.php" style="color:rgba(255,255,255,.6);font-size:.9rem;">← Propiedades</a>
      <span style="color:rgba(255,255,255,.3)">/</span>
      <span style="color:rgba(255,255,255,.8);font-size:.9rem;"><?=sanitize($p['titulo'])?></span>
    </div>
  </div>
</div>

<section style="background:var(--gray-50);padding:0 0 80px;margin-top:-40px;min-height:60vh;">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 380px;gap:40px;align-items:start;" class="prop-detail-grid">

      <!-- Columna izquierda -->
      <div>
        <!-- Galería -->
        <div style="background:linear-gradient(135deg,var(--purple-dark),var(--blue-dark));border-radius:var(--radius-xl);overflow:hidden;height:380px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;position:relative;">
          <?php if (!empty($imagenes)): ?>
            <img id="mainImage" src="<?=sanitize($imagenes[0]['url'])?>" alt="<?=sanitize($p['titulo'])?>" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <svg width="100" height="100" fill="none" stroke="white" stroke-width=".8" viewBox="0 0 24 24" style="opacity:.3;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
          <?php endif; ?>
          <span class="prop-op-badge <?=$p['operacion']?>" style="position:absolute;top:16px;left:16px;"><?=fmtOp($p['operacion'])?></span>
          <?php if ($p['destacado']): ?><span class="prop-destacado" style="position:absolute;bottom:16px;left:16px;">⭐ Destacada</span><?php endif; ?>
        </div>
        <?php if (count($imagenes)>1): ?>
        <div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;">
          <?php foreach($imagenes as $img): ?>
          <img class="gallery-thumb" src="<?=sanitize($img['url'])?>" style="width:80px;height:60px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid transparent;flex-shrink:0;" alt="">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Detalles -->
        <div style="background:var(--white);border-radius:var(--radius-lg);padding:32px;margin-top:24px;box-shadow:var(--shadow);">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <div>
              <span class="prop-tipo-badge" style="position:static;display:inline-block;margin-bottom:8px;"><?=fmtTipo($p['tipo'])?></span>
              <h1 style="font-size:1.7rem;margin-bottom:6px;"><?=sanitize($p['titulo'])?></h1>
              <div style="display:flex;align-items:center;gap:6px;color:var(--gray-400);font-size:.9rem;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?=sanitize($p['direccion'])?>, <?=sanitize($p['colonia']??'')?> <?=sanitize($p['ciudad'])?>, N.L.
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:2rem;font-weight:900;color:var(--gray-900);"><?=fmtPrecio($p['precio'],$p['moneda'])?></div>
              <?php if ($p['operacion']==='renta'): ?><div style="font-size:.85rem;color:var(--gray-400);">por mes</div><?php endif; ?>
            </div>
          </div>

          <!-- Características -->
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:16px;padding:24px 0;border-top:1px solid var(--gray-100);border-bottom:1px solid var(--gray-100);margin-bottom:24px;">
            <?php if($p['recamaras']):?><div style="text-align:center;"><div style="font-size:1.6rem;">🛏</div><div style="font-weight:700;font-size:1.1rem;"><?=$p['recamaras']?></div><div style="font-size:.8rem;color:var(--gray-400);">Recámaras</div></div><?php endif;?>
            <?php if($p['banos']):    ?><div style="text-align:center;"><div style="font-size:1.6rem;">🚿</div><div style="font-weight:700;font-size:1.1rem;"><?=$p['banos']?></div><div style="font-size:.8rem;color:var(--gray-400);">Baños</div></div><?php endif;?>
            <?php if($p['metros_c']): ?><div style="text-align:center;"><div style="font-size:1.6rem;">📐</div><div style="font-weight:700;font-size:1.1rem;"><?=(int)$p['metros_c']?></div><div style="font-size:.8rem;color:var(--gray-400);">m² Construc.</div></div><?php endif;?>
            <?php if($p['metros_t']): ?><div style="text-align:center;"><div style="font-size:1.6rem;">🌿</div><div style="font-weight:700;font-size:1.1rem;"><?=(int)$p['metros_t']?></div><div style="font-size:.8rem;color:var(--gray-400);">m² Terreno</div></div><?php endif;?>
            <?php if($p['estacionamiento']): ?><div style="text-align:center;"><div style="font-size:1.6rem;">🚗</div><div style="font-weight:700;font-size:1.1rem;"><?=$p['estacionamiento']?></div><div style="font-size:.8rem;color:var(--gray-400);">Estaciona.</div></div><?php endif;?>
            <div style="text-align:center;"><div style="font-size:1.6rem;">👁</div><div style="font-weight:700;font-size:1.1rem;"><?=$p['vistas']?></div><div style="font-size:.8rem;color:var(--gray-400);">Vistas</div></div>
          </div>

          <h3 style="margin-bottom:12px;">Descripción</h3>
          <p style="line-height:1.8;"><?=nl2br(sanitize($p['descripcion']))?></p>
        </div>
      </div>

      <!-- Columna derecha: Contacto -->
      <div style="position:sticky;top:88px;">
        <div style="background:var(--white);border-radius:var(--radius-xl);padding:28px;box-shadow:var(--shadow-lg);border:2px solid var(--gray-200);">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--gray-100);">
            <div class="prop-avatar" style="width:46px;height:46px;font-size:1rem;"><?=strtoupper(substr($p['anunciante'],0,1))?></div>
            <div>
              <div style="font-weight:700;color:var(--gray-900);"><?=sanitize($p['anunciante'])?></div>
              <div style="font-size:.8rem;color:var(--gray-400);">Anunciante verificado ✓</div>
            </div>
          </div>

          <?php if ($msgOk): ?><div class="alert alert-success"><?=$msgOk?></div><?php endif; ?>
          <?php if ($msgErr): ?><div class="alert alert-error"><?=$msgErr?></div><?php endif; ?>

          <?php if (!$msgOk): ?>
          <form method="POST" action="" class="form-stack" style="gap:14px;" data-validate>
            <h3 style="margin-bottom:0;font-size:1.1rem;">Contactar anunciante</h3>
            <div>
              <label class="form-label" for="c_nombre">Nombre <span style="color:#EF4444">*</span></label>
              <input type="text" id="c_nombre" name="c_nombre" class="form-input" placeholder="Tu nombre" required>
            </div>
            <div>
              <label class="form-label" for="c_email">Correo <span style="color:#EF4444">*</span></label>
              <input type="email" id="c_email" name="c_email" class="form-input" placeholder="tu@correo.com" required>
            </div>
            <div>
              <label class="form-label" for="c_telefono">Teléfono</label>
              <input type="tel" id="c_telefono" name="c_telefono" class="form-input" placeholder="+52 825 000 0000">
            </div>
            <div>
              <label class="form-label" for="c_mensaje">Mensaje <span style="color:#EF4444">*</span></label>
              <textarea id="c_mensaje" name="c_mensaje" class="form-input" rows="4" placeholder="Hola, me interesa esta propiedad..." required style="resize:vertical;"></textarea>
            </div>
            <button type="submit" name="contactar" class="btn btn-primary w-full" style="justify-content:center;">
              Enviar mensaje
            </button>
          </form>
          <?php endif; ?>

          <?php if ($p['tel_anunciante']): ?>
          <a href="tel:<?=sanitize($p['tel_anunciante'])?>" class="btn btn-ghost w-full mt-4" style="justify-content:center;">
            📞 <?=sanitize($p['tel_anunciante'])?>
          </a>
          <?php endif; ?>

          <div style="margin-top:16px;display:flex;align-items:center;gap:6px;justify-content:center;color:var(--gray-400);font-size:.8rem;">
            🚫 Sin intermediarios · Sin comisiones
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<style>
@media(max-width:900px){
  .prop-detail-grid { grid-template-columns:1fr !important; }
  .prop-detail-grid > div:last-child { position:static !important; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>