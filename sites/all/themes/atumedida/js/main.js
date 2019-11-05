var c1 = [];
var c2 = [];
var compararList = [];
var anioMes = [];

(function($){

    $(document).ready(function(){
        $("#edit-field-total-de-gastos-und-0-value, #edit-field-precio-promedio-ticket-und-0-value, #edit-field-precio-promedio-arreglo-und-0-value, #edit-field-precio-promedio-cliente-und-0-value, #edit-field-prendas-promedio-ticket-und-0-value, #edit-field-arreglos-prenda-promedio-und-0-value, #edit-field-arreglos-ticket-promedio-und-0-value, #edit-field-arreglos-cliente-promedio-und-0-value").keydown(function(event) {event.preventDefault();});
        $("table").addClass("table-bordered table-hover table-condensed");
        $( "table" ).parent().addClass("table-responsive");
        $(".node-operacionmensual-form #edit-field-venta-bruta").before("<h1>Ventas</h1><br/>");
        $(".node-operacionmensual-form #edit-field-renta").before("<h1 class='clearfix'>Gastos</h1><br/>");
        $(".node-operacionmensual-form #edit-field-n-mero-de-clientes").before("<h1 class='clearfix'>Productividad</h1><br/>");
        
        $('#block-menu-menu-crear-operacion li.first a').on("click", function(e){
           $("#block-views-operacion-mensual-block-1 .views-field-field-intervalo-de-fechas").each(function(){
               var date = $(this).find(".date-display-start").attr("content");
               var now = new Date();
               var parseDate = new Date(date);
               if((now.getMonth() == parseDate.getMonth())&&(now.getYear()==parseDate.getYear())){
                   var cont = confirm("Ya agregaste este mes una operación mensual. ¿Deseas agregar otra?");
                   if (! cont == true){
                       e.preventDefault();
                       return false;
                   }
               }
           });
        });
        
        if (/messages/.test(self.location.href)){
            $("#block-views-user-franquicias-block-1").hide(); 
            $("#block-views-user-franquicias-block").hide();
        }
        
        $(".page-operaciones-mensuales .feed-icon").before("<h1>Exportar:</h1><br/>");
        
        if ((/operacionmensual/.test(self.location.href))||(/edit/.test(self.location.href))){
            $("#edit-field-total-de-gastos-und-0-value, #edit-field-precio-promedio-ticket-und-0-value, #edit-field-precio-promedio-arreglo-und-0-value, #edit-field-precio-promedio-cliente-und-0-value, #edit-field-prendas-promedio-ticket-und-0-value, #edit-field-arreglos-prenda-promedio-und-0-value, #edit-field-arreglos-ticket-promedio-und-0-value, #edit-field-arreglos-cliente-promedio-und-0-value").keypress(function(event) {event.preventDefault();});
            
            $(".node-operacionmensual-form #edit-field-renta-und-0-value, .node-operacionmensual-form #edit-field-mantenimiento-und-0-value,  .node-operacionmensual-form #edit-field-sueldos-und-0-value,  .node-operacionmensual-form #edit-field-cuotas-patronales-und-0-value,  .node-operacionmensual-form #edit-field-comisiones-del-personal-und-0-value,  .node-operacionmensual-form #edit-field-comisiones-bancarias-und-0-value,  .node-operacionmensual-form #edit-field-publicidad-und-0-value,  .node-operacionmensual-form #edit-field-luz-und-0-value,  .node-operacionmensual-form #edit-field-tel-fono-opmens-und-0-value,  .node-operacionmensual-form #edit-field-insumos-und-0-value,  .node-operacionmensual-form #edit-field-agua-und-0-value,  .node-operacionmensual-form #edit-field-mantenimiento-local-und-0-value,  .node-operacionmensual-form #edit-field-mantenimiento-equipo-und-0-value,  .node-operacionmensual-form #edit-field-regal-as-und-0-value,  .node-operacionmensual-form #edit-field-capacitacion-opmen-und-0-value,  .node-operacionmensual-form #edit-field-papeler-a-und-0-value,  .node-operacionmensual-form #edit-field-tintorer-a-und-0-value,  .node-operacionmensual-form #edit-field-maquila-bordados-und-0-value,  .node-operacionmensual-form #edit-field-maquila-etiquetas-und-0-value,  .node-operacionmensual-form #edit-field-maquila-zurcidos-und-0-value,  .node-operacionmensual-form #edit-field-transporte-und-0-value,  .node-operacionmensual-form #edit-field-contabilidad-und-0-value,  .node-operacionmensual-form #edit-field-isr-und-0-value, .node-operacionmensual-form #edit-field-mensajeria-opmens-und-0-value, .node-operacionmensual-form #edit-field-varios-und-0-value").on("change", function(){
                var val1 = parseFloat($(".node-operacionmensual-form #edit-field-renta-und-0-value").val());
                var val2 = parseFloat($(".node-operacionmensual-form #edit-field-mantenimiento-und-0-value").val());
                var val3 = parseFloat($(".node-operacionmensual-form #edit-field-sueldos-und-0-value").val());
                var val4 = parseFloat($(".node-operacionmensual-form #edit-field-cuotas-patronales-und-0-value").val());
                var val5 = parseFloat($(".node-operacionmensual-form #edit-field-comisiones-del-personal-und-0-value").val());
                var val6 = parseFloat($(".node-operacionmensual-form #edit-field-comisiones-bancarias-und-0-value").val());
                var val7 = parseFloat($(".node-operacionmensual-form #edit-field-publicidad-und-0-value").val());
                var val8 = parseFloat($(".node-operacionmensual-form #edit-field-luz-und-0-value").val());
                var val9 = parseFloat($(".node-operacionmensual-form #edit-field-tel-fono-opmens-und-0-value").val());
                var val10 = parseFloat($(".node-operacionmensual-form #edit-field-insumos-und-0-value").val());
                var val11 = parseFloat($(".node-operacionmensual-form #edit-field-agua-und-0-value").val());
                var val12 = parseFloat($(".node-operacionmensual-form #edit-field-mantenimiento-local-und-0-value").val());
                var val13 = parseFloat($(".node-operacionmensual-form #edit-field-mantenimiento-equipo-und-0-value").val());
                var val14 = parseFloat($(".node-operacionmensual-form #edit-field-regal-as-und-0-value").val());
                var val15 = parseFloat($(".node-operacionmensual-form #edit-field-capacitacion-opmen-und-0-value").val());
                var val16 = parseFloat($(".node-operacionmensual-form #edit-field-papeler-a-und-0-value").val());
                var val17 = parseFloat($(".node-operacionmensual-form #edit-field-tintorer-a-und-0-value").val());
                var val18 = parseFloat($(".node-operacionmensual-form #edit-field-maquila-bordados-und-0-value").val());
                var val19 = parseFloat($(".node-operacionmensual-form #edit-field-maquila-etiquetas-und-0-value").val());
                var val20 = parseFloat($(".node-operacionmensual-form #edit-field-maquila-zurcidos-und-0-value").val());
                var val21 = parseFloat($(".node-operacionmensual-form #edit-field-transporte-und-0-value").val());
                var val22 = parseFloat($(".node-operacionmensual-form #edit-field-contabilidad-und-0-value").val());
                var val23 = parseFloat($(".node-operacionmensual-form #edit-field-isr-und-0-value").val());
                var val24 = parseFloat($(".node-operacionmensual-form #edit-field-mensajeria-opmens-und-0-value").val());
                var val25 = parseFloat($(".node-operacionmensual-form #edit-field-varios-und-0-value").val());
                
                val1 = val1 ? val1 : 0;
                val2 = val2 ? val2 : 0;
                val3 = val3 ? val3 : 0;
                val4 = val4 ? val4 : 0;
                val5 = val5 ? val5 : 0;
                val6 = val6 ? val6 : 0;
                val7 = val7 ? val7 : 0;
                val8 = val8 ? val8 : 0;
                val9 = val9 ? val9 : 0;
                val10 = val10 ? val10 : 0;
                val11 = val11 ? val11 : 0;
                val12 = val12 ? val12 : 0;
                val13 = val13 ? val13 : 0;
                val14 = val14 ? val14 : 0;
                val15 = val15 ? val15 : 0;
                val16 = val16 ? val16 : 0;
                val17 = val17 ? val17 : 0;
                val18 = val18 ? val18 : 0;
                val19 = val19 ? val19 : 0;
                val20 = val20 ? val20 : 0;
                val21 = val21 ? val21 : 0;
                val22 = val22 ? val22 : 0;
                val23 = val23 ? val23 : 0;
                val24 = val24 ? val24 : 0;
                val25 = val25 ? val25 : 0;
                
                var total = val1+val2+val3+val4+val5+val6+val7+val8+val9+val10+val11+val12+val13+val14+val15+val16+val17+val18+val19+val20+val21+val22+val23+val24+val25;
                total = total.toFixed(2);
                $(".node-operacionmensual-form #edit-field-total-de-gastos-und-0-value").val(total);
            });
            $("#edit-field-venta-neta-und-0-value, #edit-field-n-mero-de-tickets-emitidos-und-0-value, #edit-field-n-mero-de-arreglos-und-0-value, #edit-field-n-mero-de-clientes-und-0-value, #edit-field-n-mero-de-prendas-und-0-value").on("change", function(){
                var ventaneta = parseFloat($(".node-operacionmensual-form #edit-field-venta-neta-und-0-value").val());
                var ticketsem = parseFloat($(".node-operacionmensual-form #edit-field-n-mero-de-tickets-emitidos-und-0-value").val());
                var numarreg = parseFloat($(".node-operacionmensual-form #edit-field-n-mero-de-arreglos-und-0-value").val());
                var numclientes = parseFloat($(".node-operacionmensual-form #edit-field-n-mero-de-clientes-und-0-value").val());
                var numprendas = parseFloat($(".node-operacionmensual-form #edit-field-n-mero-de-prendas-und-0-value").val());
                
                ventaneta = ventaneta ? ventaneta : 0;
                ticketsem = ticketsem ? ticketsem : 0;
                numarreg = numarreg ? numarreg : 0;
                numclientes = numclientes ? numclientes : 0;
                numprendas = numprendas ? numprendas : 0;
                
                $(".node-operacionmensual-form #edit-field-precio-promedio-ticket-und-0-value").val((ticketsem > 0)?(ventaneta/ticketsem).toFixed(2):0);
                $(".node-operacionmensual-form #edit-field-precio-promedio-arreglo-und-0-value").val((numarreg > 0)?(ventaneta/numarreg).toFixed(2):0);
                $(".node-operacionmensual-form #edit-field-precio-promedio-cliente-und-0-value").val((numclientes > 0)?(ventaneta/numclientes).toFixed(2):0);
                $(".node-operacionmensual-form #edit-field-prendas-promedio-ticket-und-0-value").val((ticketsem > 0)?(numprendas/ticketsem).toFixed(2):0); 
                $(".node-operacionmensual-form #edit-field-arreglos-prenda-promedio-und-0-value").val((numprendas > 0)?(numarreg/numprendas).toFixed(2):0); 
                $(".node-operacionmensual-form #edit-field-arreglos-ticket-promedio-und-0-value").val((ticketsem > 0)?(numarreg/ticketsem).toFixed(2):0); 
                $(".node-operacionmensual-form #edit-field-arreglos-cliente-promedio-und-0-value").val((numclientes > 0)?(numarreg/numclientes).toFixed(2):0); 
            });
        }
        
        var month = new Array();
        month[0] = "Enero";
        month[1] = "Febrero";
        month[2] = "Marzo";
        month[3] = "Abril";
        month[4] = "Mayo";
        month[5] = "Junio";
        month[6] = "Julio";
        month[7] = "Agosto";
        month[8] = "Septiembre";
        month[9] = "Octubre";
        month[10] = "Noviembre";
        month[11] = "Diciembre";
        
        function sortear(comp){
            c3 = comp.sort(function (a, b) {
                if (a.mes > b.mes) {
                  return 1;
                }
                if (a.mes < b.mes) {
                  return -1;
                }
                return 0;
            });
            return c3;
        };
        
        
        
        function acomodar(comp){
            c3 = comp.sort(function (a, b) {
                if (a.unidad > b.unidad) {
                  return 1;
                }
                if (a.unidad < b.unidad) {
                  return -1;
                }
                return 0;
            });
            return c3;
        };
        
        
        function compararMeses(c1){
            anioMes=[];
            
            var y0 = $("#edit-field-intervalo-de-fechas-value-value-year").val();
            var y1 = $("#edit-field-intervalo-de-fechas-value-1-value-year").val();
            var y2 = $("#edit-field-intervalo-de-fechas-value-2-value-year").val();
            
            var year =[y0,y1,y2];
            
            for(var y = 0; y<12; y++){
                var mes;
                var val1 = "-1";
                var val2 = "-1";
                var name = "-1";
                for(var x=0; x<c1.length; x++){
                    if(c1[x].mes == y){
                        mes = month[y];
                        if(val1 == "-1")
                            val1 = c1[x].vn;
                        else
                            val1 = val1+"-"+c1[x].vn;
                        if(val2 == "-1")
                            val2 = c1[x].vb;
                        else
                            val2 = val2+"-"+c1[x].vb;
                        if(name == "-1")
                            name = c1[x].anio;
                        else
                            name = name+"-"+c1[x].anio;
                    }
                    console.log("mes: "+c1[x].mes+" y: "+y);
                    console.log(mes, val1, val2, name);
                }
                val1 = val1.split("-");
                val2 = val2.split("-");
                name = name.split("-");
                
                var v1=[];
                var v2=[];
                var n=[];
                
                for(var x=0; x<name.length; x++){
                    if(name[x]==year[0]){
                        n[0] = name[x];
                        v1[0] = val1[x];
                        v2[0] = val2[x];
                    } else if(name[x]==year[1]){
                        n[1] = name[x];
                        v1[1] = val1[x];
                        v2[1] = val2[x];
                    } else if(name[x]==year[2]){
                        n[2] = name[x];
                        v1[2] = val1[x];
                        v2[2] = val2[x];
                    } 
                    console.log(year[0],year[1],year[2], name[x]);
                    console.log(name[x]==year[1]);
                }
                
                
                
                if(mes){
                   var objeto = {
                        mes: mes,
                        vn1:v1[0]?v1[0]:0,
                        vn2:v1[1]?v1[1]:0,
                        vn3:v1[2]?v1[2]:0,
                        vb1:v2[0]?v2[0]:0,
                        vb2:v2[1]?v2[1]:0,
                        vb3:v2[2]?v2[2]:0,
                        y1:n[0]?n[0]:0,
                        y2:n[1]?n[1]:0,
                        y3:n[2]?n[2]:0,
                    };
                    anioMes.push(objeto);
                }
                mes="";
                console.log(objeto);
            }
        };
        
        function objetosComparados(co1, co2){
            compararList = [];
            var matched = 0;
            if(co1.length >= co2.length){
                for(var x = 0; x < co1.length; x++){
                    for(var y = 0; y < co2.length; y++){
                        if(co1[x].unidad===co2[y].unidad){
                            var objeto = {
                                unidad: co1[x].unidad,
                                vn1: co1[x].vn,
                                vn2: co2[y].vn,
                                vb1: co1[x].vb,
                                vb2: co2[y].vb,
                                anio1: co1[x].anio,
                                anio2: co2[y].anio
                            };
                            compararList.push(objeto);
                            matched = 1;
                        }else if(((y+1)>=co2.length)&& matched == 0){
                            var objeto = {
                                unidad: co1[x].unidad,
                                vn1: co1[x].vn,
                                vn2: 0,
                                vb1: co1[x].vb,
                                vb2: 0,
                                anio1: co1[x].anio,
                                anio2: 0
                            };
                            compararList.push(objeto);
                        }
                    };  
                    matched = 0;
                };
                matched = 0;
                for(var x = 0; x < co2.length; x++){
                    for(var y = 0; y < compararList.length; y++){
                        if(co2[x].unidad===compararList[y].unidad){
                            matched = 1;
                        }else if(((y+1)>=compararList.length)&& matched == 0){
                            var objeto = {
                                unidad: co2[x].unidad,
                                vn2: co2[x].vn,
                                vn1: 0,
                                vb2: co2[x].vb,
                                vb1: 0,
                                anio2: co2[x].anio,
                                anio1: 0
                            };
                            compararList.push(objeto);
                            console.log("else if 1");
                        }
                    };
                    matched = 0;
                };
            }else{
                for(var x = 0; x < co2.length; x++){
                    for(var y = 0; y < co1.length; y++){
                        if(co2[x].unidad===co1[y].unidad){
                            var objeto = {
                                unidad: co2[x].unidad,
                                vn2: co2[x].vn,
                                vn1: co1[y].vn,
                                vb2: co2[x].vb,
                                vb1: co1[y].vb,
                                anio2: co2[x].anio,
                                anio1: co1[y].anio
                            };
                            compararList.push(objeto);
                            matched = 1;
                        }else if(((y+1)>=co1.length)&& matched == 0){
                            var objeto = {
                                unidad: co2[x].unidad,
                                vn2: co2[x].vn,
                                vn1: 0,
                                vb2: co2[x].vb,
                                vb1: 0,
                                anio2: co2[x].anio,
                                anio1: 0
                            };
                            compararList.push(objeto);
                        }
                    };
                    matched = 0;
                };
                matched = 0;
                for(var x = 0; x < co1.length; x++){
                    for(var y = 0; y < compararList.length; y++){
                        if(co1[x].unidad===compararList[y].unidad){
                            matched = 1;
                        }else if(((y+1)>=compararList.length)&& matched == 0){
                            var objeto = {
                                unidad: co1[x].unidad,
                                vn1: co1[x].vn,
                                vn2: 0,
                                vb1: co1[x].vb,
                                vb2: 0,
                                anio1: co1[x].anio,
                                anio2: 0
                            };
                            compararList.push(objeto);
                            console.log("else if 2");
                        }
                    };
                    matched = 0;
                };
            }
        };
        
        
        if(/comparar/.test(self.location.href)){
            $("#block-system-main tbody tr").each(function(){
               if($(this).find(".views-field-field-unidad").html())
                    var unidad = $(this).find(".views-field-field-unidad").html().trim();
                else
                    var unidad = "";
               var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
               var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
               var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim(); 

               var comparador1 = {
                 unidad: unidad,
                 vn: ventaNeta,
                 vb: ventaBruta,
                 anio: anio
               };
               
               c1.push(comparador1);
            });
            $(".view-contenido tbody tr").each(function(){
                if($(this).find(".views-field-field-unidad").html())
                    var unidad = $(this).find(".views-field-field-unidad").html().trim();
                else
                    var unidad = "";
               var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
               var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
               var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim(); 

               var comparador2 = {
                 unidad: unidad,
                 vn: ventaNeta,
                 vb: ventaBruta,
                 anio: anio
               };
               
               c2.push(comparador2);
            });
            
            c1 = acomodar(c1);
            c2 = acomodar(c2);
            
            objetosComparados(c1,c2);
            
            console.log(compararList);
            
            $("#chartComparativo").empty();
        };
        
        if(/comparar-todas/.test(self.location.href)){
            c1 = [];
            $("#block-system-main tbody tr").each(function(){
               var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
               var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
               var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim();
               
               var date = anio.split("-");

               var comparador1 = {
                 vn: ventaNeta,
                 vb: ventaBruta,
                 anio: date[0],
                 mes: date[1]-1
               };
               
               c1.push(comparador1);
            });
            
            c1 = sortear(c1);
            compararMeses(c1);
            
            $("#chartComparativo").empty();
        };
        
        
        if(/utilidad/.test(self.location.href)){
            c1 = [];
            $("#block-system-main tbody tr").each(function(){
               var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
               var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
               var gastos = $(this).find(".views-field-field-total-de-gastos").html().trim();
                if($(this).find(".views-field-field-unidad").html())
                    var unidad = $(this).find(".views-field-field-unidad").html().trim();
                else
                    var unidad = "";
                   
                   var comparador1 = {
                     vn: ventaNeta,
                     vb: ventaBruta,
                     gastos: gastos,
                     unidad:unidad
                   };
               
               c1.push(comparador1);
            });
            
            $("#chartComparativo").empty();
        };
        
        if(/ingreso-utilidad/.test(self.location.href)){
            c1 = [];
            $("#block-system-main tbody tr").each(function(){
               var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
               var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
               var gastos = $(this).find(".views-field-field-total-de-gastos").html().trim();
                var mes = $(this).find(".views-field-field-intervalo-de-fechas").html().trim();
                mes = parseFloat(mes-1);

                var comparador1 = {
                  vn: ventaNeta,
                  vb: ventaBruta,
                  gastos: gastos,
                  mes:month[mes]
                };
               
               c1.push(comparador1);
            });
            
            $("#chartComparativo").empty();
        };
        
        $( document ).ajaxComplete(function() {
            $("table").addClass("table-bordered table-hover table-condensed");
            $(".page-operacion-mensual .views-table").each(function(){
                $(this).parent().addClass("table-responsive");
            });
            
            if(/comparar/.test(self.location.href)){
                c1=[];
                $("#block-system-main tbody tr").each(function(){
                   if($(this).find(".views-field-field-unidad").html())
                        var unidad = $(this).find(".views-field-field-unidad").html().trim();
                    else
                        var unidad = "";
                   var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
                   var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
                   var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim(); 

                   var comparador1 = {
                     unidad: unidad,
                     vn: ventaNeta,
                     vb: ventaBruta,
                     anio: anio
                   };

                   c1.push(comparador1);
                });
                c2=[];
                $(".view-contenido tbody tr").each(function(){
                    if($(this).find(".views-field-field-unidad").html())
                        var unidad = $(this).find(".views-field-field-unidad").html().trim();
                    else
                        var unidad = "";
                    var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
                    var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
                    var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim(); 

                    var comparador2 = {
                      unidad: unidad,
                      vn: ventaNeta,
                      vb: ventaBruta,
                      anio: anio
                    };

                    c2.push(comparador2);
                });
                
                c1 = acomodar(c1);
                c2 = acomodar(c2);

                objetosComparados(c1,c2);

                console.log(compararList);
                $("#chartComparativo").empty();
            
            };
            if(/comparar-todas/.test(self.location.href)){
                c1 = [];
                $("#block-system-main tbody tr").each(function(){
                   var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
                   var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
                   var anio = $(this).find(".views-field-field-intervalo-de-fechas").html().trim();

                   var date = anio.split("-");

                   var comparador1 = {
                     vn: ventaNeta,
                     vb: ventaBruta,
                     anio: date[0],
                     mes: date[1]-1
                   };

                   c1.push(comparador1);
                });

                c1 = sortear(c1);
                compararMeses(c1);

                $("#chartComparativo").empty();
            };
            
            if(/utilidad/.test(self.location.href)){
                c1 = [];
                $("#block-system-main tbody tr").each(function(){
                   var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
                   var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
                   var gastos = $(this).find(".views-field-field-total-de-gastos").html().trim();
                   if($(this).find(".views-field-field-unidad").html())
                        var unidad = $(this).find(".views-field-field-unidad").html().trim();
                    else
                        var unidad = "";
                   
                   var comparador1 = {
                     vn: ventaNeta,
                     vb: ventaBruta,
                     gastos: gastos,
                     unidad:unidad
                   };

                   c1.push(comparador1);
                });

                $("#chartComparativo").empty();
            };
            
            if(/ingreso-utilidad/.test(self.location.href)){
                c1 = [];
                $("#block-system-main tbody tr").each(function(){
                   var ventaNeta = $(this).find(".views-field-field-venta-bruta").html().trim(); 
                   var ventaBruta = $(this).find(".views-field-field-venta-neta").html().trim(); 
                   var gastos = $(this).find(".views-field-field-total-de-gastos").html().trim();
                    var mes = $(this).find(".views-field-field-intervalo-de-fechas").html().trim();
                    mes = parseFloat(mes-1);

                       var comparador1 = {
                         vn: ventaNeta,
                         vb: ventaBruta,
                         gastos: gastos,
                         mes:month[mes]
                       };

                   c1.push(comparador1);
                });

                $("#chartComparativo").empty();
            };
            
        });
        
        
    });
})( jQuery );


(function($){
})( jQuery );

