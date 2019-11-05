<?php if (!empty($user->name)): ?>
    <div id="adminmenu">
        <?php print render($page['adminmenu']); ?>
        <div id="holausuario">Hola: <span class="datos"><?php print l($user->name, 'user/' . $user->uid); ?></span></div>
        <div id="userLogout"><?php if ($user->uid != 0): ?><a class="logout" href="/user/logout?redirect=true">Cerrar Sesión</a><?php endif; ?></div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>
<?php if (!empty($user->name)): ?>
<header id="navbar" role="banner" class="navbar navbar-default">
  <div class="topMenu">
    <div class="navbar-header">
      <?php if ($logo): ?>
      <a class="logo navbar-btn pull-left" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
      </a>
      <?php endif; ?>

      <?php if (!empty($site_name)): ?>
      <a class="name navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a>
      <?php endif; ?>

      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
      <div class="navbar-collapse collapse">
        <nav role="navigation">
          <?php if (!empty($primary_nav)): ?>
            <?php print render($primary_nav); ?>
          <?php endif; ?>
          <?php if (!empty($secondary_nav)): ?>
            <?php print render($secondary_nav); ?>
          <?php endif; ?>
          <?php if (!empty($page['navigation'])): ?>
            <?php print render($page['navigation']); ?>
          <?php endif; ?>
        </nav>
      </div>
    <?php endif; ?>
  </div>
</header>
<?php endif; ?>
<div class="main-container container">

  <header role="banner" id="page-header">
    <?php if (!empty($site_slogan)): ?>
      <p class="lead"><?php print $site_slogan; ?></p>
    <?php endif; ?>

    <div class="col-md-2 col-sm-2 col-xs-3">
          <?php print render($page['marca1']); ?>
      </div>  
      <div class="col-md-8 col-sm-8 col-xs-6">
          <?php print render($page['header']); ?>
      </div> 
      <div class="col-md-2 col-sm-2 col-xs-3">
          <?php print render($page['marca2']); ?>
      </div>
  </header> <!-- /#page-header -->

  <div class="row">
    <section<?php print $content_column_class; ?>>
      <?php if (!empty($page['highlighted'])): ?>
        <div class="highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
      <?php endif; ?>
      <?php if (!empty($breadcrumb)): print $breadcrumb; endif;?>
      <a id="main-content"></a>
      <?php print render($title_prefix); ?>
      <?php if (!empty($title)): ?>
        <h1 class="content-page-header"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
      <?php print $messages; ?>
      <?php if (!empty($tabs)): ?>
        <?php print render($tabs); ?>
      <?php endif; ?>
      <?php if (!empty($page['help'])): ?>
        <?php print render($page['help']); ?>
      <?php endif; ?>
      <?php if (!empty($action_links)): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      <?php print render($page['content']); ?>
        <a href="" id="gmaps">Buscar en google maps</a>
    </section>

    <?php if (!empty($page['sidebar_second'])): ?>
      <aside class="col-sm-3" role="complementary">
        <?php print render($page['sidebar_second']); ?>
      </aside>  <!-- /#sidebar-second -->
    <?php endif; ?>

  </div>
</div>
<footer class="footer container">
    <div class="copyright">
        <div class="region region-footer">©2016 Arreglalo.</div> <?php print render($page['footer']); ?>
    </div>
</footer>
<script>
(function($){
    $(document).ready(function(){
        var calle = $(".field-name-field-calle .field-item.even").text();
        var cp = $(".field-name-field-c-p- .field-item.even").text();
        var delegacion = $(".field-name-field-delegaci-n-o-municipio .field-item.even").text();
        var colonia = $(".field-name-field-colonia-o-asentamiento .field-item.even").text();
        var estado = $(".field-name-field-estado-o-provincia .field-item.even").text();
        var pais = $(".field-name-field-pa-s .field-item.even").text();

        if(calle)
            calle = calle.split(' ').join('+');
        if(delegacion)
            delegacion = delegacion.split(' ').join('+');
        if(colonia)
            colonia = colonia.split(' ').join('+');
        if(estado)
            estado = estado.split(' ').join('+');
        if(pais)
            pais = pais.split(' ').join('+');

        var href= "https://www.google.com.mx/maps/place/"+calle+",+"+colonia+",+"+delegacion+"+"+cp+",+"+estado+",+"+pais;

        $("#gmaps").attr("href", href); 
    });
})( jQuery );
</script>