# 🧩 CRM Web - Proyecto en Desarrollo

Este repositorio contiene el inicio y la estructura inicial de un sistema CRM (Customer Relationship Management) implementado con PHP y MySQL (XAMPP).

Resumen breve
- Backend: PHP (sin JavaScript en la interfaz por el momento). Las vistas son plantillas PHP sencillas.
- Base de datos: MySQL gestionada localmente con XAMPP (importar el SQL en `src/sql/crm.sql`).

Estado
- En desarrollo — se ha implementado la estructura básica, autenticación simple, modelos y vistas iniciales para login/registro/dashboard.

# CRM PHP — resumen del proyecto

Mini-CRM implementado con PHP y MySQL pensado para ejecutarse localmente (XAMPP / Apache + MySQL). El proyecto contiene modelos, controladores y vistas PHP sencillas que cubren: autenticación, gestión de usuarios, clientes, oportunidades y tareas.

Contenido y estado
- Lenguaje: PHP 7/8
- Base de datos: MySQL (dump en `src/sql/crm.sql`)
- Plantillas: PHP (server-side rendering), HTML/CSS. Interacciones por formularios y redirecciones.
- Estado: funcional y en evolución — incluye registro/login (con password_hash), panel administrador, CRUD de usuarios/clientes/oportunidades y gestión básica de tareas.

Estructura principal

```
README.md
photos/
src/
  index.php                     # Router / punto de entrada
  config/
    config.php                  # Configuración / credenciales DB
  controller/
    usuario_controller.php      # Registro, login, usuarios CRUD, contadores para dashboard
    client_controller.php       # CRUD y helpers para clientes
    oportunity_controller.php   # Oportunidades: listar, crear, editar, eliminar, filtros
    tareas_controller.php       # Tareas: listar por oportunidad, crear, completar
  model/
    db.php                      # Wrapper de conexión mysqli
    usuario.php                 # Modelo Usuario
    cliente.php                 # Modelo Cliente
    oportunidad.php             # Modelo Oportunidad
    tareas.php                  # Modelo Tareas
  view/
    login.php
    register.php
    admindashboard.php
    listadoclientes.php
    listadooportunidades.php
    crearoportunidad.php
    editaroportunidad.php
    listatareas.php
    creartareas.php
    crearusuario.php
    editarusuario.php
    listausuarios.php
sql/
  crm.sql                       # Esquema / datos iniciales
```

Principales características implementadas
- Autenticación: registro y login con contraseñas seguras (password_hash / password_verify). Auto-login después del registro.
- Roles: `admin` y `vendedor` (control de permisos en controladores).
- Usuarios: listado, creación, edición y eliminación (admin).
- Clientes: CRUD básico; los clientes están asignados a un `usuario_responsable`.
- Oportunidades: crear (usa `usuario_responsable` del cliente por defecto), listar, filtrar por estado, editar (solo admin puede cambiar estado), eliminar.
- Tareas: listar por oportunidad, crear tareas vinculadas a oportunidades, marcar tareas como completadas.
- Dashboard admin: resumen con contadores (clientes, oportunidades por estado, tareas pendientes).

Notas técnicas importantes
- Sesión: la aplicación usa `$_SESSION` para mantener `id_usuario`, `nombre_completo`, `email` y `role`.
- Seguridad: contraseñas almacenadas con `password_hash`. Validaciones y checks de autorización en controladores.
- Prepared statements: se usan consultas preparadas (`$conexion->prepare`) para evitar inyección SQL.

Cómo ejecutar localmente (Windows + XAMPP)
1. Instala XAMPP y arranca Apache + MySQL.
2. Importa `src/sql/crm.sql` con phpMyAdmin o desde la CLI: `mysql -u root -p < src/sql/crm.sql`.
3. Copia el repositorio dentro de `htdocs` (ej. `C:\xampp\htdocs\crm-php`) o apunta Apache al directorio `src/`.
4. Ajusta `src/config/config.php` con las credenciales de tu MySQL si es necesario.
5. Abre en el navegador: `http://localhost/crm-php/src/index.php` (o la URL que corresponda según tu instalación).

Puntos de extensión y mejoras recomendadas
- Extraer estilos comunes a un archivo CSS y/o usar un framework ligero (Tailwind/Bootstrap) para consistencia.
- Añadir CSRF tokens y validaciones lado servidor más completas.
- Centralizar lógica de base de datos en un DAO/Repository para facilitar tests.
- Añadir tests unitarios/funcionales (PHPUnit) y comprobaciones automáticas.

License / Notas
Proyecto creado como ejercicio/práctica. Revisa la base de datos y los datos de ejemplo en `src/sql/crm.sql` antes de usar en producción.

