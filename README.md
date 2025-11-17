# 🧩 CRM Web - Proyecto en Desarrollo

Este repositorio contiene el inicio y la estructura inicial de un sistema CRM (Customer Relationship Management) implementado con PHP y MySQL (XAMPP).

Resumen breve
- Backend: PHP (sin JavaScript en la interfaz por el momento). Las vistas son plantillas PHP sencillas.
- Base de datos: MySQL gestionada localmente con XAMPP (importar el SQL en `src/sql/crm.sql`).

Estado
- En desarrollo — se ha implementado la estructura básica, autenticación simple, modelos y vistas iniciales para login/registro/dashboard.

Tecnologías utilizadas
- PHP 7/8
- MySQL (XAMPP)
- HTML/CSS (sin JavaScript en la UI actual)

Estructura del proyecto (hasta ahora)
```
README.md
photos/
  relaciones.png
src/
  index.php                    # Router / punto de entrada
  config/
    config.php               # Credenciales / configuración DB
  controller/
    usuario_controller.php   # Login / register 
  model/
    usuario.php              # Modelo Usuario
    db.php                   # Wrapper de conexión mysqli
    cliente.php              # Modelo Cliente (esqueleto)
    oportunidad.php          # Modelo Oportunidad (esqueleto)
    tareas.php               # Modelo Tareas (esqueleto)
  view/
    login.php                # Formulario de login
    register.php             # Formulario de registro (auto-login y redirect al dashboard)
    dashboard.php            # Vista protegida de usuario
sql/
  crm.sql                      # Dump / esquema inicial de la BD
```

Notas importantes
- La base de datos está pensada para ejecutarse localmente con XAMPP (MySQL). Actualiza `src/config/config.php` con tus credenciales si es necesario.
- Actualmente la UI no utiliza JavaScript; toda la interacción es por formularios y redirecciones server-side.
- Las contraseñas se manejan con las funciones seguras de PHP (`password_hash` / `password_verify`) a través del modelo `Usuario`.

Cómo probar localmente (rápido)
1. Tener XAMPP con Apache + MySQL activos.
2. Importar `src/sql/crm.sql` en tu servidor MySQL (phpMyAdmin o mysql CLI).
3. Copiar el proyecto a `htdocs` (o ejecutar desde `src/` con `php -S localhost:8000`).
4. Ajustar credenciales en `src/config/config.php` si hace falta.
5. Abrir `http://localhost/.../src/index.php` (o `http://localhost:8000/index.php` si usas el servidor embebido).

Próximos pasos recomendados
- Unificar el acceso a la base de datos vía un repositorio/DAO y añadir pruebas.
- Añadir CSRF y validación más robusta en formularios.
- Considerar introducir JavaScript progresivamente para mejorar UX.

💡 *Proyecto creado como práctica; si quieres que adapte la estructura a inyección de dependencias o añada tests, dímelo y lo implemento.*
