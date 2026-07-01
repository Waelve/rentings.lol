<?php
$pageTitle = 'Registrarse gratis';
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) redirect('/pages/dashboard.php');

$error = '';
$success = '';
$vals = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $pass     = $_POST['password']      ?? '';
    $pass2    = $_POST['password2']     ?? '';

    $vals = ['nombre'=>$nombre,'email'=>$email,'telefono'=>$telefono];

    if (!$nombre || !$email || !$pass) {
        $error = 'Por favor completa todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un correo electrónico válido.';
    } elseif (strlen($pass) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($pass !== $pass2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $db = getDB();
        $chk = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'Este correo ya está registrado. <a href="/pages/login.php">Inicia sesión</a>.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol, verificado, activo) VALUES (?,?,?,?,'anunciante',1,1)");
            $stmt->execute([$nombre, $email, $telefono, $hash]);
            $uid = $db->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $uid;
            $_SESSION['user_nombre'] = $nombre;
            redirect('/pages/dashboard.php');
        }
    }
}
?>
<div class="page-wrapper">
  <div class="auth-card" style="max-width:520px;">
    <div class="auth-header">
      <div class="auth-logo-wrap">
        <a href="/" class="nav-logo" style="justify-content:center;">
          <span class="logo-icon">
            <svg width="38" height="38" viewBox="0 0 34 34" fill="none"><rect width="34" height="34" rx="8" fill="url(#grad-r)"/><path d="M17 6L28 14V28H22V21H12V28H6V14L17 6Z" fill="white"/><defs><linearGradient id="grad-r" x1="0" y1="0" x2="34" y2="34"><stop stop-color="#7B2FBE"/><stop offset="1" stop-color="#F97316"/></linearGradient></defs></svg>
          </span>
          <span class="logo-text">Rentings<span class="logo-dot">.lol</span></span>
        </a>
      </div>
      <h1 class="auth-title">Crear cuenta gratis</h1>
      <p class="auth-subtitle">Publica tu propiedad en minutos, sin comisiones</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= $error ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" class="form-stack" data-validate>
      <div>
        <label class="form-label" for="nombre">Nombre completo <span style="color:#EF4444">*</span></label>
        <input type="text" id="nombre" name="nombre" class="form-input"
               value="<?= htmlspecialchars($vals['nombre']??'') ?>"
               placeholder="Juan García López" required>
        <span class="form-error"></span>
      </div>
      <div>
        <label class="form-label" for="email">Correo electrónico <span style="color:#EF4444">*</span></label>
        <input type="email" id="email" name="email" class="form-input"
               value="<?= htmlspecialchars($vals['email']??'') ?>"
               placeholder="tu@correo.com" required autocomplete="email">
        <span class="form-error"></span>
      </div>
      <div>
        <label class="form-label" for="telefono">Teléfono (opcional)</label>
        <input type="tel" id="telefono" name="telefono" class="form-input"
               value="<?= htmlspecialchars($vals['telefono']??'') ?>"
               placeholder="+52 825 123 4567">
      </div>
      <div>
        <label class="form-label" for="password">Contraseña <span style="color:#EF4444">*</span></label>
        <div style="position:relative;display:flex;align-items:center;">
          <input type="password" id="password" name="password" class="form-input"
                 placeholder="Mínimo 8 caracteres" required style="padding-right:48px;">
          <button type="button" class="toggle-password"
                  style="position:absolute;right:14px;background:none;border:none;cursor:pointer;font-size:1rem;color:var(--gray-400);">👁</button>
        </div>
        <span class="form-error"></span>
      </div>
      <div>
        <label class="form-label" for="password2">Confirmar contraseña <span style="color:#EF4444">*</span></label>
        <input type="password" id="password2" name="password2" class="form-input"
               placeholder="Repite tu contraseña" required>
        <span class="form-error"></span>
      </div>
      <div style="font-size:.82rem;color:var(--gray-400);line-height:1.5;">
        Al registrarte aceptas nuestros <a href="/pages/terminos.php" style="color:var(--purple);">Términos de uso</a>
        y <a href="/pages/privacidad.php" style="color:var(--purple);">Aviso de privacidad</a>.
      </div>
      <button type="submit" class="btn btn-primary w-full btn-lg" style="justify-content:center;">
        Crear cuenta gratis
      </button>
    </form>

    <div class="form-divider">o</div>
    <p class="auth-link">¿Ya tienes cuenta? <a href="/pages/login.php">Inicia sesión</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
