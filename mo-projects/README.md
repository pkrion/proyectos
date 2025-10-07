# Plugin mo-projects

Plugin de gestión de proyectos para FacturaScripts con integración de clientes, documentos y tablero Kanban.

## Características principales

- Gestión de proyectos con relación a clientes y documentos de venta (facturas, albaranes y presupuestos).
- Sistema de credenciales por proyecto con opciones rápidas de copiado.
- Gestión de archivos y creatividades asociados a cada proyecto.
- Integración opcional con Google Calendar para sincronizar eventos de proyecto.
- Enlace directo a carpeta de Google Drive configurada a nivel de proyecto.
- Tablero Kanban para organizar tareas y estados configurables.

## Estructura

- `Controller/`: Controladores con prefijo `mo-` que gestionan listas, edición y API del Kanban.
- `Model/`: Modelos persistentes (`mo-project`, `mo-project-task`, etc.) que crean automáticamente sus tablas (`mo_…`).
- `Service/`: Servicios de dominio para documentos, tablero Kanban y sincronización con Google Calendar.
- `SQL/install.sql`: Script inicial de creación de tablas para instalaciones manuales.
- `Assets/`: Recursos CSS y JavaScript para credenciales y tablero Kanban.
- `Resources/views/`: Plantillas Twig para la interfaz de proyectos.

## Integraciones

### Documentos de cliente

El servicio `MoProjectDocumentService` permite vincular facturas, albaranes y presupuestos existentes a cada proyecto. Las vistas muestran resúmenes y enlazan con los documentos originales.

### Credenciales

`MoProjectCredential` almacena credenciales etiquetadas por proyecto con indicadores de sensibilidad y botones para copiado rápido.

### Archivos y creatividades

`MoProjectFile` permite relacionar archivos con la ficha del proyecto. El plugin expone rutas de descarga y deja preparado el punto de integración con cualquier servicio de almacenamiento.

### Calendario y Google Calendar

`MoProjectCalendarService` se integra con Google Calendar utilizando la librería oficial de Google. Configure las credenciales OAuth en `configure()` y asocie el `calendar_id` a cada proyecto para sincronizar eventos.

### Kanban

`MoProjectKanbanService` genera columnas por defecto (`Backlog`, `En progreso`, `Revisión`, `Finalizado`) y gestiona la reorganización de tareas mediante la API `MoProjectsKanbanMove`.

## Instalación

1. Copie la carpeta `mo-projects` dentro de `FacturaScripts/Plugins/`.
2. Active el plugin desde la administración de FacturaScripts.
3. Ejecute las migraciones para crear las tablas `mo_…` o importe `SQL/install.sql`.

## Licencia

Publicación bajo la licencia LGPL v3, en línea con la licencia de FacturaScripts.

Consulte la documentación interna en el código para conocer los puntos de extensión y servicios disponibles.
