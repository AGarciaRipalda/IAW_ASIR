<?php
// /templates/sidebar.php
$userRol = $_SESSION['user']['role'] ?? 'viewer';

/*
 * Definimos las rutas base.
 * (Calculadas desde un archivo en /admin/)
*/
$logoPath = '../assets/wow_logo.png';
$iconPath = '../assets/'; // Ruta base para los nuevos iconos
?>
<aside class="sidebar">
  
  <div class="sidebar-logo-top">
    <img src="<?= $logoPath ?>" alt="WoW Logo">
  </div>
  
  <nav class="sidebar-nav">
    
    <a href="wow_dashboard.php" class="<?= ($paginaActual === 'dashboard') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_dashboard.png" class="sidebar-nav-icon" alt=""> Dashboard
    </a>
    
    <?php if ($userRol === 'admin' || $userRol === 'tester'): ?>
    <a href="wow_sesiones.php" class="<?= ($paginaActual === 'sesiones') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_sesiones.png" class="sidebar-nav-icon" alt=""> Sesiones
    </a>
    <a href="wow_contenido.php" class="<?= ($paginaActual === 'contenido') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_contenido.png" class="sidebar-nav-icon" alt=""> Contenido
    </a>
    <a href="wow_testers.php" class="<?= ($paginaActual === 'testers') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_testers.png" class="sidebar-nav-icon" alt=""> Testers
    </a>
    <?php endif; ?>
    
    <?php if ($userRol === 'admin'): ?>
    <a href="wow_usuarios.php" class="<?= ($paginaActual === 'usuarios') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_usuarios.png" class="sidebar-nav-icon" alt=""> Usuarios
    </a>
    <a href="wow_reportes.php" class="<?= ($paginaActual === 'reportes') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_reportes.png" class="sidebar-nav-icon" alt=""> Reportes
    </a>
    <a href="wow_configuracion.php" class="<?= ($paginaActual === 'configuracion') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_config.png" class="sidebar-nav-icon" alt=""> Configuración
    </a>
    <?php endif; ?>
    
    <a href="wow_perfil.php" class="<?= ($paginaActual === 'perfil') ? 'active' : '' ?>">
      <img src="<?= $iconPath ?>icon_perfil.png" class="sidebar-nav-icon" alt=""> Mi Perfil
    </a>
  </nav>
  
  <div class="sidebar-footer">
    <a href="../auth/wow_logout.php" class="btn-wow primary">
      Cerrar Sesión
    </a>
  </div>
</aside>
