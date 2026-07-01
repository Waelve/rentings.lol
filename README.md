# 🏠 Rentings.lol — Plataforma Inmobiliaria
### Para NAS

---

## 📁 Estructura del proyecto

```
rentings/
├── index.php               ← Página principal (Hero, búsqueda, propiedades)
├── .htaccess               ← Redirecciones y seguridad Apache
├── config/
│   └── db.php              ← Configuración de base de datos + helpers
├── includes/
│   ├── header.php          ← Navbar responsiva
│   └── footer.php          ← Footer + links
├── pages/
│   ├── login.php           ← Iniciar sesión
│   ├── register.php        ← Registro de usuario
│   ├── logout.php          ← Cerrar sesión
│   ├── dashboard.php       ← Panel del usuario
│   ├── propiedades.php     ← Listado con filtros y paginación
│   ├── propiedad.php       ← Detalle de propiedad + contacto
│   └── publicar.php        ← Formulario publicar propiedad
├── assets/
│   ├── css/style.css       ← Todos los estilos (responsive)
│   ├── js/main.js          ← JavaScript (navbar, counters, validación)
│   └── img/favicon.svg     ← Ícono del sitio
└── database/
    └── rentings.sql        ← Estructura completa + datos de ejemplo
```

---

## 🚀 Instalación en NAS

### 1. Preparar NAS
1. Instala **Web Station** desde el Package Center
2. Instala **PHP 8.x** y **MariaDB 10** desde Package Center
3. En Web Station → crea un Virtual Host con:
   - **Nombre de host**: `rentings.lol`
   - **Raíz del documento**: `/volume1/web/rentings`
   - **Back-end HTTP**: PHP 8.x

### 2. Base de datos (phpMyAdmin)
1. Abre phpMyAdmin (`http://tu-nas:8080/phpMyAdmin`)
2. Crea usuario: `rentings_user` con contraseña de tu elección
3. Importa `database/rentings.sql`
4. Otorga todos los privilegios al usuario en `rentings_db`

### 3. Configurar db.php
Edita `config/db.php` con tus datos:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'rentings_user');   // Tu usuario
define('DB_PASS', 'tu_password');      // Tu contraseña
define('DB_NAME', 'rentings_db');
define('SITE_URL', 'http://rentings.lol');
```

### 4. Subir archivos
Copia toda la carpeta `rentings/` a `/volume1/web/rentings/`

### 5. DNS rentings.lol
Apunta el registro A de `rentings.lol` a la IP de tu NAS o usa DDNS

---

## 🔐 Credenciales de prueba
- **Admin**: admin@rentings.lol / password
- **Anunciante 1**: carlos@ejemplo.com / password
- **Anunciante 2**: maria@ejemplo.com / password

> ⚠️ Cambia las contraseñas antes de poner en producción.

---

## 🎨 Paleta de colores

| Color      | Hex       | Uso |
|------------|-----------|-----|
| Morado     | #7B2FBE   | Principal, gradientes |
| Azul       | #2563EB   | Secundario, botones |
| Naranja    | #F97316   | CTA, acentos, badges |
| Amarillo   | #F59E0B   | Badges destacados |
| Negro      | #0F0F0F   | Fondo hero/footer |
| Blanco     | #FFFFFF   | Fondo principal |
| Gris       | #6B7280   | Textos secundarios |

---

## 📱 Responsive
- ✅ PC (1240px+)
- ✅ Tablet (768px–1024px)
- ✅ Celular (hasta 375px)

---

## 🛠️ Tecnologías
- PHP 8.x (PDO)
- MySQL / MariaDB
- HTML5 + CSS3 puro (sin frameworks)
- JavaScript Vanilla (sin jQuery)
- phpMyAdmin para gestión de BD

---

*Rentings.lol — Plataforma inmobiliaria sin comisiones*
