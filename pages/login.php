<?php
$pageTitle = 'Iniciar sesión';
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) redirect('/pages/dashboard.php');

$error = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $email_val = htmlspecialchars($email);

    if (!$email || !$pass) {
        $error = 'Por favor completa todos los campos.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            redirect('/pages/dashboard.php');
        } else {
            $error = 'Correo o contraseña incorrectos. Intenta de nuevo.';
        }
    }
}
?>
<div class="page-wrapper">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-logo-wrap">
        <a href="/" class="nav-logo" style="justify-content:center;">
          <span class="logo-icon">
            <svg width="38" height="38" viewBox="0 0 34 34" fill="none"><rect width="34" height="34" rx="8" fill="url(#grad-l)"/><path d="M17 6L28 14V28H22V21H12V28H6V14L17 6Z" fill="white"/><defs><linearGradient id="grad-l" x1="0" y1="0" x2="34" y2="34"><stop stop-color="#7B2FBE"/><stop offset="1" stop-color="#F97316"/></linearGradient></defs></svg>
          </span>
          <span class="logo-text">Rentings<span class="logo-dot">.lol</span></span>
        </a>
      </div>
      <h1 class="auth-title">Bienvenido de vuelta</h1>
      <p class="auth-subtitle">Inicia sesión para gestionar tus propiedades</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" class="form-stack" data-validate>
      <div>
        <label class="form-label" for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" class="form-input"
               value="<?= $email_val ?>" placeholder="tu@correo.com" required autocomplete="email">
        <span class="form-error"></span>
      </div>
      <div>
        <label class="form-label" for="password">Contraseña</label>
        <div style="position:relative;display:flex;align-items:center;">
          <input type="password" id="password" name="password" class="form-input"
                 placeholder="Tu contraseña" required autocomplete="current-password"
                 style="padding-right:48px;">
          <button type="button" class="toggle-password"
                  style="position:absolute;right:14px;background:none;border:none;cursor:pointer;font-size:1rem;color:var(--gray-400);">👁</button>
        </div>
        <span class="form-error"></span>
      </div>
      <div style="text-align:right;">
        <a href="/pages/recuperar.php" style="font-size:.85rem;color:var(--purple);font-weight:500;">¿Olvidaste tu contraseña?</a>
      </div>
      <button type="submit" class="btn btn-primary w-full btn-lg" style="justify-content:center;">
        Iniciar sesión
      </button>
    </form>

    <div class="form-divider">o</div>
    <p class="auth-link">¿No tienes cuenta? <a href="/pages/register.php">Regístrate gratis</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
