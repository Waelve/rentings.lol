<?php
$pageTitle = 'Editar Propiedad';
require_once __DIR__ . '/../includes/header.php';

if (!isLoggedIn()) {
    redirect('/pages/login.php');
}

$db = getDB();
$user = getCurrentUser();
$uid = $_SESSION['user_id'];

// Verificar si se proporcionó un ID de propiedad
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/pages/dashboard.php');
}

$propiedadId = intval($_GET['id']);

// Obtener detalles de la propiedad
$propiedad = $db->prepare("SELECT * FROM propiedades WHERE id = ? AND usuario_id = ?");
$propiedad->execute([$propiedadId, $uid]);
$propiedad = $propiedad->fetch();

if (!$propiedad) {
    redirect('/pages/dashboard.php');
}

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo']);
    $tipo = sanitize($_POST['tipo']);
    $precio = sanitize($_POST['precio']);
    $moneda = sanitize($_POST['moneda']);
    $descripcion = sanitize($_POST['descripcion']);
    $vistas = $propiedad['vistas'] ?? 0;

    // Actualizar la propiedad
    $stmt = $db->prepare("UPDATE propiedades SET 
        titulo = ?, 
        tipo = ?, 
        precio = ?, 
        moneda = ?, 
        descripcion = ?, 
        vistas = ? 
        WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $tipo, $precio, $moneda, $descripcion, $vistas, $propiedadId, $uid]);

    // Redirigir al dashboard
    redirect('/pages/dashboard.php');
}
?>

<div class="container">
    <div class="page-header">
        <h1>Editar Propiedad</h1>
        <p>Modifica los detalles de tu propiedad en Rentings.lol</p>
    </div>

    <form action="/pages/editar.php?id=<?=$propiedadId?>" method="post">
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" value="<?=$propiedad['titulo']?>" required>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Propiedad</label>
            <select id="tipo" name="tipo" required>
                <option value="casa" <?=($propiedad['tipo'] === 'casa') ? 'selected' : ''?>>Casa</option>
                <option value="departamento" <?=($propiedad['tipo'] === 'departamento') ? 'selected' : ''?>>Departamento</option>
                <option value="terreno" <?=($propiedad['tipo'] === 'terreno') ? 'selected' : ''?>>Terreno</option>
                <option value="local" <?=($propiedad['tipo'] === 'local') ? 'selected' : ''?>>Local</option>
                <option value="bodega" <?=($propiedad['tipo'] === 'bodega') ? 'selected' : ''?>>Bodega</option>
                <option value="quinta" <?=($propiedad['tipo'] === 'quinta') ? 'selected' : ''?>>Quinta</option>
                <option value="rancho" <?=($propiedad['tipo'] === 'rancho') ? 'selected' : ''?>>Rancho</option>
                <option value="oficina" <?=($propiedad['tipo'] === 'oficina') ? 'selected' : ''?>>Oficina</option>
            </select>
        </div>

        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" value="<?=$propiedad['precio']?>" required>
        </div>

        <div class="form-group">
            <label for="moneda">Moneda</label>
            <select id="moneda" name="moneda" required>
                <option value="MXN" <?=($propiedad['moneda'] === 'MXN') ? 'selected' : ''?>>MXN (Pesos Mexicanos)</option>
                <option value="USD" <?=($propiedad['moneda'] === 'USD') ? 'selected' : ''?>>USD (Dólares Americanos)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?=htmlspecialchars($propiedad['descripcion'])?></textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>