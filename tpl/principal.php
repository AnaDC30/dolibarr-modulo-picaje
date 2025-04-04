<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';

?>

<header class="page-header">
    <h1>Bienvenido al Módulo Picaje</h1>
</header>

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