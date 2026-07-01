<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) redirect('/pages/login.php');

$db   = getDB();
$user = getCurrentUser();
$uid  = $_SESSION['user_id'];

// Mis propiedades
$misProps = $db->prepare("SELECT * FROM propiedades WHERE usuario_id=? ORDER BY created_at DESC");
$misProps->execute([$uid]);
$props = $misProps->fetchAll();

// Mensajes recibidos
$msgs = $db->prepare("
  SELECT c.*, p.titulo as prop_titulo
  FROM contactos c
  JOIN propiedades p ON p.id=c.propiedad_id
  WHERE p.usuario_id=?
  ORDER BY c.created_at DESC
  LIMIT 10
");
$msgs->execute([$uid]);
$mensajes = $msgs->fetchAll();

// Stats
$totalVistas = array_sum(array_column($props,'vistas'));
$totalMsgs   = count($mensajes);
$totalProps  = count($props);

function fmtTipo($t){$m=['casa'=>'Casa','departamento'=>'Depto','terreno'=>'Terreno','local'=>'Local','bodega'=>'Bodega','quinta'=>'Quinta','rancho'=>'Rancho','oficina'=>'Oficina'];return $m[$t]??ucfirst($t);}
function fmtPrecio($n,$m='MXN'){return '$'.number_format($n,0,'.',',').' '.$m;}
?>

<div style="background:var(--gradient-hero);padding:48px 0 80px;position:relative;">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 30% 50%,rgba(123,47,190,.2) 0%,transparent 60%);"></div>
  <div class="container" style="position:relative;z-index:1;">
    <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
      <div style="width:64px;height:64px;border-radius:50%;background:var(--gradient-main);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:white;">
        <?=strtoupper(substr($user['nombre'],0,1))?>
      </div>
      <div>
        <h1 style="color:white;font-size:1.8rem;margin-bottom:4px;">¡Hola, <?=sanitize(explode(' ',$user['nombre'])[0])?>! 👋</h1>
        <p style="color:rgba(255,255,255,.6);margin:0;">Gestiona tus propiedades en Rentings.lol</p>
      </div>
      <a href="/pages/publicar.php" class="btn btn-primary" style="margin-left:auto;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Publicar propiedad
      </a>
    </div>
  </div>
</div>

<section style="background:var(--gray-50);padding:0 0 80px;margin-top:-40px;min-height:60vh;">
  <div class="container">

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:40px;">
      <div class="stat-card">
        <div class="stat-icon purple">🏠</div>
        <div class="stat-num purple"><?=$totalProps?></div>
        <div class="stat-label">Propiedades publicadas</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">👁</div>
        <div class="stat-num orange"><?=$totalVistas?></div>
        <div class="stat-label">Vistas totales</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">💬</div>
        <div class="stat-num blue"><?=$totalMsgs?></div>
        <div class="stat-label">Mensajes recibidos</div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start;" class="dash-grid">

      <!-- Mis propiedades -->
      <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
          <h2 style="font-size:1.3rem;">Mis propiedades</h2>
          <a href="/pages/publicar.php" class="btn btn-purple btn-sm">+ Nueva</a>
        </div>
        <?php if (empty($props)): ?>
        <div class="empty-state" style="background:var(--white);border-radius:var(--radius-lg);padding:48px;">
          <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
          <h3>Aún no tienes propiedades</h3>
          <p>Publica tu primera propiedad gratis.</p>
          <a href="/pages/publicar.php" class="btn btn-primary mt-4">Publicar ahora</a>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:16px;">
          <?php foreach($props as $p): ?>
          <div style="background:var(--white);border-radius:var(--radius-lg);padding:20px;box-shadow:var(--shadow);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="width:72px;height:56px;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--purple-dark),var(--blue-dark));display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.4rem;">
              <?=['casa'=>'🏠','departamento'=>'🏢','terreno'=>'🌿','local'=>'🏪','bodega'=>'🏭','quinta'=>'🌄','rancho'=>'🐄','oficina'=>'💼'][$p['tipo']]?>'🏠'?>
            </div>
            <div style="flex:1;min-width:180px;">
              <div style="font-weight:700;color:var(--gray-900);margin-bottom:2px;"><?=sanitize($p['titulo'])?></div>
              <div style="font-size:.82rem;color:var(--gray-400);"><?=fmtPrecio($p['precio'],$p['moneda'])?> · <?=$p['vistas']?> vistas</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
              <a href="/pages/propiedad.php?id=<?=$p['id']?>" class="btn btn-ghost btn-sm">Ver</a>
              <a href="/pages/editar.php?id=<?=$p['id']?>" class="btn btn-purple btn-sm">Editar</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Mensajes recientes -->
      <div>
        <h2 style="font-size:1.3rem;margin-bottom:20px;">Mensajes recientes</h2>
        <?php if (empty($mensajes)): ?>
        <div style="background:var(--white);border-radius:var(--radius-lg);padding:32px;text-align:center;color:var(--gray-400);">
          <div style="font-size:2rem;margin-bottom:8px;">💬</div>
          <p>Sin mensajes aún</p>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:14px;">
          <?php foreach($mensajes as $msg): ?>
          <div style="background:var(--white);border-radius:var(--radius-lg);padding:18px;box-shadow:var(--shadow);border-left:4px solid var(--purple);">
            <div style="font-weight:700;font-size:.95rem;color:var(--gray-900);margin-bottom:4px;"><?=sanitize($msg['nombre'])?></div>
            <div style="font-size:.8rem;color:var(--gray-400);margin-bottom:8px;"><?=sanitize($msg['prop_titulo'])?> · <?=date('d/m/Y', strtotime($msg['created_at']))?></div>
            <p style="font-size:.88rem;color:var(--gray-600);line-height:1.5;margin:0;"><?=sanitize(substr($msg['mensaje'],0,120)).(strlen($msg['mensaje'])>120?'...':'')?></p>
            <?php if ($msg['telefono']): ?>
            <a href="tel:<?=sanitize($msg['telefono'])?>" style="font-size:.82rem;color:var(--purple);font-weight:600;margin-top:8px;display:inline-block;">📞 <?=sanitize($msg['telefono'])?></a>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<style>
@media(max-width:900px){.dash-grid{grid-template-columns:1fr!important;}}
@media(max-width:480px){.dash-grid>div:first-child .stat-card{padding:20px 14px;}}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
