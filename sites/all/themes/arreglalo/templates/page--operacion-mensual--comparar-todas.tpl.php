<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['corechart', 'table']}]}"></script>
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
      <section class="block block-views contextual-links-region clearfix" id="chart">
          <div id="chartComparativo"></div>
      </section>
      <section class="block block-views contextual-links-region clearfix" id="chart3">
          <div id="chartComparativo3"></div>
          <div id="chartComparativoImg" class="btn btn-primary descarga"></div>
          <div id="chartComparativoCsv"  class="btn btn-success descarga"></div>
      </section>
      <section class="block block-views contextual-links-region clearfix" id="chart2">
          <div id="chartComparativo2"></div>
      </section>
      <section class="block block-views contextual-links-region clearfix" id="chart4">
          <div id="chartComparativo4"></div>
          <div id="chartComparativoImg2" class="btn btn-primary descarga"></div>
          <div id="chartComparativoCsv2"  class="btn btn-success descarga"></div>
      </section>
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
    
    var drawChartComparativo = function (objeto, contenedor) {   
        var x = 0;
        var anio1 = 0;
        var anio2 = 0;
        var anio3 = 0;
        do{
            if(objeto[x]){
                if(anio1 == 0)
                    anio1 = objeto[x].y1;
                if(anio2 == 0)
                    anio2 = objeto[x].y2;
                if(anio3 == 0)
                    anio3 = objeto[x].y3;
            }
            x++;
            if(x>300)break;
            
        }while((anio1 == 0) || (anio2 == 0) || (anio3 == 0));

        var data = [['Mes', anio1.toString(), anio2.toString(), anio3.toString()]];

        for (var key in objeto) {
            if (objeto.hasOwnProperty(key)) {
                var diferencia = parseFloat(objeto[key].vn2) - parseFloat(objeto[key].vn1);
                data.push([objeto[key].mes, parseFloat(objeto[key].vn1),parseFloat(objeto[key].vn2),parseFloat(objeto[key].vn3)]);
            }
        }

        var options = {
                title: 'Venta Neta',
                colors: ['blue', 'orange', 'green']
          };
        data = google.visualization.arrayToDataTable(data);

        var chart = new google.visualization.ColumnChart(document.getElementById(contenedor));
        var table = new google.visualization.Table(document.getElementById('chartComparativo3'));
        
        var csv = google.visualization.dataTableToCsv(data);
        var my_div = document.getElementById('chartComparativoImg');
        var $div = $("#chartComparativoImg");
        var $div2 = $("#chartComparativoCsv");
        console.log(csv);
        
        csv = csv.replace(/\r\n/g, '%0A').replace(/[\r\n]/g, '%0A');

        google.visualization.events.addListener(chart, 'ready', function () {
          my_div.innerHTML = '<i class="fa fa-bar-chart"></i><a href="' + chart.getImageURI() + '" download>Descargar gráfico</a>';
        });
        google.visualization.events.addListener(table, 'ready', function () {
            var a         = document.createElement('a');
                a.href        = 'data:attachment/csv,' + csv;
                a.target      = '_blank';
                a.download    = 'Venta_Neta.csv';
                a.setAttribute('class','links-graficas');
                a.innerHTML = "Obtener csv";
                $div2.empty();
                $div2.append("<i class='fa fa-table'></i>",a);
        });
        
        chart.draw(data, options);
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
    };
    
    var drawChartComparativo2 = function (objeto, contenedor) {   
        var x = 0;
        var anio1 = 0;
        var anio2 = 0;
        var anio3 = 0;
        do{
            if(objeto[x]){
                if(anio1 == 0)
                    anio1 = objeto[x].y1;
                if(anio2 == 0)
                    anio2 = objeto[x].y2;
                if(anio3 == 0)
                    anio3 = objeto[x].y3;
            }
            x++;
            if(x>300)break;
            
        }while((anio1 == 0) || (anio2 == 0) || (anio3 == 0));

        var data = [['Mes', anio1.toString(), anio2.toString(), anio3.toString()]];

        for (var key in objeto) {
            if (objeto.hasOwnProperty(key)) {
                var diferencia = parseFloat(objeto[key].vb2) - parseFloat(objeto[key].vb1);
                data.push([objeto[key].mes, parseFloat(objeto[key].vb1),parseFloat(objeto[key].vb2),parseFloat(objeto[key].vb3)]);
            }
        }

        var options = {
                title: 'Venta Bruta',
                colors: ['blue', 'orange', 'green']
          };
        data = google.visualization.arrayToDataTable(data);

        var chart = new google.visualization.ColumnChart(document.getElementById(contenedor));
        var table = new google.visualization.Table(document.getElementById('chartComparativo4'));
        
        var csv = google.visualization.dataTableToCsv(data);
        var my_div = document.getElementById('chartComparativoImg2');
        var $div = $("#chartComparativoImg2");
        var $div2 = $("#chartComparativoCsv2");
        console.log(csv);
        
        csv = csv.replace(/\r\n/g, '%0A').replace(/[\r\n]/g, '%0A');

        google.visualization.events.addListener(chart, 'ready', function () {
          my_div.innerHTML = '<i class="fa fa-bar-chart"></i><a href="' + chart.getImageURI() + '" download>Descargar gráfico</a>';
        });
        google.visualization.events.addListener(table, 'ready', function () {
            var a         = document.createElement('a');
                a.href        = 'data:attachment/csv,' + csv;
                a.target      = '_blank';
                a.download    = 'Venta_Bruta.csv';
                a.setAttribute('class','links-graficas');
                a.innerHTML = "Obtener csv";
                $div2.empty();
                $div2.append("<i class='fa fa-table'></i>",a);
        });
        
        chart.draw(data, options);
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
    };
    
    $(document).ready(function(){
        
        drawChartComparativo(anioMes, "chartComparativo");
        drawChartComparativo2(anioMes, "chartComparativo2");
        
    });
    $( document ).ajaxComplete(function() {
        setTimeout(function () {
            drawChartComparativo(anioMes, "chartComparativo");
            drawChartComparativo2(anioMes, "chartComparativo2");
        }, 3000);
    });
})( jQuery );
</script>