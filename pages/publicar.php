<?php
$pageTitle = 'Publicar propiedad';
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) redirect('/pages/login.php');

$db = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo']      ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo        = $_POST['tipo']             ?? '';
    $operacion   = $_POST['operacion']        ?? '';
    $precio      = floatval(str_replace(',','',$_POST['precio']??'0'));
    $moneda      = $_POST['moneda']           ?? 'MXN';
    $recamaras   = (int)($_POST['recamaras']  ?? 0);
    $banos       = (int)($_POST['banos']      ?? 0);
    $metros_c    = floatval($_POST['metros_c']?? 0);
    $metros_t    = floatval($_POST['metros_t']?? 0);
    $estac       = (int)($_POST['estacionamiento'] ?? 0);
    $direccion   = trim($_POST['direccion']   ?? '');
    $colonia     = trim($_POST['colonia']     ?? '');
    $ciudad      = trim($_POST['ciudad']      ?? 'Montemorelos');
    $cp          = trim($_POST['cp']          ?? '');

    if (!$titulo || !$descripcion || !$tipo || !$operacion || !$precio || !$direccion) {
        $error = 'Completa todos los campos obligatorios marcados con *.';
    } else {
        $stmt = $db->prepare("INSERT INTO propiedades
          (usuario_id,titulo,descripcion,tipo,operacion,precio,moneda,recamaras,banos,metros_c,metros_t,estacionamiento,direccion,colonia,ciudad,cp,activo)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)");
        $stmt->execute([
            $_SESSION['user_id'],$titulo,$descripcion,$tipo,$operacion,$precio,$moneda,
            $recamaras,$banos,$metros_c,$metros_t,$estac,$direccion,$colonia,$ciudad,$cp
        ]);
        $newId = $db->lastInsertId();
        redirect('/pages/propiedad.php?id='.$newId.'&published=1');
    }
}
?>

<div class="props-header" style="padding:40px 0 60px;">
  <div class="container">
    <h1>Publicar propiedad</h1>
    <p>Completa el formulario para publicar tu propiedad en Rentings.lol</p>
  </div>
</div>

<section style="background:var(--gray-50);padding:40px 0 80px;margin-top:-40px;">
  <div class="container" style="max-width:760px;">

    <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:24px;"><?=$error?></div><?php endif; ?>

    <form method="POST" action="" class="form-stack" style="gap:28px;" data-validate>
      <!-- Sección 1: Información básica -->
      <div style="background:var(--white);border-radius:var(--radius-xl);padding:32px;box-shadow:var(--shadow);">
        <h3 style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
          <span style="width:32px;height:32px;background:var(--gradient-main);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:.85rem;font-weight:700;">1</span>
          Información básica
        </h3>
        <div class="form-stack" style="gap:18px;">
          <div>
            <label class="form-label" for="titulo">Título del anuncio <span style="color:#EF4444">*</span></label>
            <input type="text" id="titulo" name="titulo" class="form-input" placeholder="Ej: Casa en Col. Los Sabinos con alberca" required maxlength="200">
            <span class="form-error"></span>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
              <label class="form-label" for="tipo">Tipo de propiedad <span style="color:#EF4444">*</span></label>
              <select id="tipo" name="tipo" class="form-input" required>
                <option value="">Seleccionar...</option>
                <option value="casa">🏠 Casa</option>
                <option value="departamento">🏢 Departamento</option>
                <option value="terreno">🌿 Terreno</option>
                <option value="local">🏪 Local comercial</option>
                <option value="bodega">🏭 Bodega</option>
                <option value="quinta">🌄 Quinta</option>
                <option value="rancho">🐄 Rancho</option>
                <option value="oficina">💼 Oficina</option>
              </select>
            </div>
            <div>
              <label class="form-label" for="operacion">Operación <span style="color:#EF4444">*</span></label>
              <select id="operacion" name="operacion" class="form-input" required>
                <option value="">Seleccionar...</option>
                <option value="venta">💰 Venta</option>
                <option value="renta">🔑 Renta</option>
                <option value="venta_renta">🤝 Venta o Renta</option>
              </select>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
            <div>
              <label class="form-label" for="precio">Precio <span style="color:#EF4444">*</span></label>
              <input type="number" id="precio" name="precio" class="form-input" placeholder="2350000" min="0" step="1000" required>
            </div>
            <div>
              <label class="form-label" for="moneda">Moneda</label>
              <select id="moneda" name="moneda" class="form-input">
                <option value="MXN">🇲🇽 MXN</option>
                <option value="USD">🇺🇸 USD</option>
              </select>
            </div>
          </div>
          <div>
            <label class="form-label" for="descripcion">Descripción <span style="color:#EF4444">*</span></label>
            <textarea id="descripcion" name="descripcion" class="form-input" rows="5" required
              placeholder="Describe las características de tu propiedad, acabados, ubicación, amenidades..." style="resize:vertical;"></textarea>
          </div>
        </div>
      </div>

      <!-- Sección 2: Características -->
      <div style="background:var(--white);border-radius:var(--radius-xl);padding:32px;box-shadow:var(--shadow);">
        <h3 style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
          <span style="width:32px;height:32px;background:var(--gradient-main);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:.85rem;font-weight:700;">2</span>
          Características
        </h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
          <div>
            <label class="form-label" for="recamaras">🛏 Recámaras</label>
            <input type="number" id="recamaras" name="recamaras" class="form-input" min="0" max="20" value="0">
          </div>
          <div>
            <label class="form-label" for="banos">🚿 Baños</label>
            <input type="number" id="banos" name="banos" class="form-input" min="0" max="20" value="0">
          </div>
          <div>
            <label class="form-label" for="estacionamiento">🚗 Estacionamientos</label>
            <input type="number" id="estacionamiento" name="estacionamiento" class="form-input" min="0" max="20" value="0">
          </div>
          <div>
            <label class="form-label" for="metros_c">📐 m² Construcción</label>
            <input type="number" id="metros_c" name="metros_c" class="form-input" min="0" step="0.5" value="0">
          </div>
          <div>
            <label class="form-label" for="metros_t">🌿 m² Terreno</label>
            <input type="number" id="metros_t" name="metros_t" class="form-input" min="0" step="0.5" value="0">
          </div>
        </div>
      </div>

      <!-- Sección 3: Ubicación -->
      <div style="background:var(--white);border-radius:var(--radius-xl);padding:32px;box-shadow:var(--shadow);">
        <h3 style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
          <span style="width:32px;height:32px;background:var(--gradient-main);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:.85rem;font-weight:700;">3</span>
          Ubicación
        </h3>
        <div class="form-stack" style="gap:18px;">
          <div>
            <label class="form-label" for="direccion">Dirección <span style="color:#EF4444">*</span></label>
            <input type="text" id="direccion" name="direccion" class="form-input" placeholder="Calle, número, entre calles..." required>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
            <div>
              <label class="form-label" for="colonia">Colonia/Zona</label>
              <input type="text" id="colonia" name="colonia" class="form-input" placeholder="Los Sabinos">
            </div>
            <div>
              <label class="form-label" for="ciudad">Ciudad</label>
              <input type="text" id="ciudad" name="ciudad" class="form-input" value="Montemorelos">
            </div>
            <div>
              <label class="form-label" for="cp">Código Postal</label>
              <input type="text" id="cp" name="cp" class="form-input" placeholder="67500" maxlength="5">
            </div>
          </div>
        </div>
      </div>

      <!-- Botón submit -->
      <div style="display:flex;gap:14px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary btn-lg">
          ✅ Publicar propiedad ahora
        </button>
        <a href="/pages/dashboard.php" class="btn btn-ghost btn-lg">Cancelar</a>
      </div>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
