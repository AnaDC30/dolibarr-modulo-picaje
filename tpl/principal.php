<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';

?>


<header class="page-header">
    <h1>Bienvenido al Módulo Picaje</h1>
</header>

<?php if ($user->admin): ?>
    <a href="<?php echo dol_buildpath('/custom/picaje/admin/setup.php', 1); ?>" class="icon-ajustes" title="Ajustes del módulo">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09c.7 0 1.3-.4 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09c0 .7.4 1.3 1 1.51a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09c0 .7.4 1.3 1 1.51a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09c-.7 0-1.3.4-1.51 1z"/>
        </svg>
    </a>
<?php endif; ?>


<div class="container-flex">
    <div class="main-content">
        <p>Realizar Picaje</p>
        <a class="mainButton" href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=picaje', 1); ?>">Picar</a>
    </div>

    <div class="main-content">
        <p>Registro de picadas</p>
        <a class="mainButton" href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=historial', 1); ?>">Ver Registro</a>
    </div>
</div>

<?php if ($user->admin && $user->id == 1): ?>
    <div class="container-flex">
        <div class="main-content">
            <p>Incidencias</p>
            <a class="mainButton" href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias', 1); ?>">Incidencias</a>
        </div>
    </div>
<?php endif; ?>

<?php if (!$user->admin) : ?>
  <div style="text-align:center; margin-top:2rem;">
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias_user', 1); ?>" class="btn-historial">
      📋 Mis incidencias
    </a>
  </div>
<?php endif; ?>

