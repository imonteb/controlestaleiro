# Estructura de ficheros — ControlEstaleiro CME C016

## Directorios principales

```
C:\laragon\www\controlestaleiro\
├── app/
│   ├── Livewire/              ← componentes Livewire (lógica)
│   │   ├── Concerns/
│   │   │   └── HasLogin.php
│   │   └── ...
│   ├── Models/
│   │   └── User.php           ← roles como JSON array
│   ├── Http/Middleware/
│   │   ├── EnsureIsAdmin.php
│   │   ├── EnsureIsEpi.php
│   │   ├── EnsureIsLogi.php
│   │   └── EnsureIsSuperAdmin.php
│   └── ...
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app/
│   │   │       └── sidebar.blade.php   ← navegación principal
│   │   ├── livewire/                   ← vistas Livewire
│   │   │   ├── gestao-equipas.blade.php
│   │   │   └── ...
│   │   ├── components/                 ← componentes Blade reutilizables
│   │   └── partials/
│   │       └── head.blade.php
│   └── css/
│       └── app.css                     ← NO tocar sin motivo (524 líneas)
└── SECURITY.md                         ← auditoría de seguridad completa
```

## Ficheros que NO se deben tocar sin justificación
- `resources/css/app.css` — estilos globales, cambios de color NUNCA aquí
- `app/Models/User.php` — modelo crítico con roles JSON
- `app/Livewire/Concerns/HasLogin.php` — lógica de login móvil

## Convención de nombres
- Vistas Livewire: `kebab-case.blade.php`
- Componentes: `PascalCase.php` + `kebab-case.blade.php`
- Rutas con nombre: `recurso.accion` (ej: `epis.entregas.index`)
