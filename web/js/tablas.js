
var bPaginate = true ;
var bScrollCollapse= false;
var sScrollY= null;
var searching = true;
var bLengthChange =true;
var bSort = true ;
var iDisplayLength  = 10 ;
var cambiarDiseno = {};

/*
 var cambiarDiseno['tamano'];
 var cambiarDiseno['bPaginate'];
 var cambiarDiseno['bScrollCollapse'];
 var cambiarDiseno['searching'];
 var cambiarDiseno['bLengthChange'];
 var cambiarDiseno['iDisplayLength'];
 */

function crearTh(datos,tabla){
    var tablatemp=tabla;
    var tabla=tabla;
    var classOpc ="indice";
    var indice=null;
    $(tabla).html("<thead ><tr class=\"rowtabla\">");
    tabla+=" thead tr";
    $.each(datos,function(k,v){
        var th = $("<th>",{
            css:{
                "padding-left":"4px",
                "padding-right":"4px",
                "padding-bottom":"4px",
                "padding-top":"4px",
                "text-align":"center"
            },
            html : v
        });
        if(k===indice){
            $(th).addClass(classOpc);
        }
        $(tabla).append(th);
        //    $(tabla).append("<th style=\"padding-left: 4px;padding-right: 4px;padding-top: 2px;padding-bottom: 2px;\">"+v+"</th>");
    });

    switch (tablatemp){
        case '#tabla_busqueda_clientes':
            $(tabla).append("<th style='text-align: center'>Seleccionar</th>");
            break;
       
        default:
            $(tabla).append("<th style='text-align: center'>Acción</th>");


    }

}

function cargarTablas(action,data,tabla,cambiarDiseno,columnasvisibles,url,urlIdioma,sDefaultContent,targetBlock){
    //console.log(data);
    var header=[];
    datos = {
        action          : action,
        accion          : action,
        data            : data
    }
    var tabla=tabla;
    if(urlIdioma==null){
        urlIdioma="../dataTables/spa_SPA.txt";
    }
    if(url==null){
        dir="./BD/swtichprepared.php";
    }else{
        dir=url;
    }
    if(sDefaultContent==null){
        sDefaultContent ="<buton style='padding:3px' class='botonRow  btn btn-danger '>Editar</span></buton>";
    }
    if(targetBlock==null){
        targetBlock=false;
    }

    $.ajax({
        url:dir,
        data:datos,
        dataType:"json",
        type:"POST",
        async:true,
        beforeSend:function(){
            if (targetBlock){
                App.blockUI({
                        target: targetBlock,
                        overlayColor: "none",
                        animate: !0
                    })
            }
            
        },
        error:function(req,err){
            if (targetBlock){
                App.unblockUI(targetBlock)
            }
            console.log(req);
            $(tabla).hide();
        },
        success: function(resp) {
            if (targetBlock){
                App.unblockUI(targetBlock)
            }
            var data =[];
            var data2=[];
            var ih=0;
            var op=0;
            var datostr=[];
            if(resp.length==0){

                switch (tabla){
                  
                }
                return 0;
            }
            Keys = Object.keys(resp[0]);
            var cont=0;
            var cont=0;
            Keys.map(function(v){
                Keys[cont]=v.charAt(0).toUpperCase()+v.slice(1);
                cont++;
            })
            cont=0;
            
            crearTh(Keys,tabla);//Añadir Thead a la Tabla, con los object Key obtenidos

            $.each(resp, function (ix, itemx) {
                op++;
                data=[];
                $.each(itemx, function (ixx, itemxx) {
                    ih++;
                    data.push(itemxx);
                });
                data2[ix]=data;//creo el array con el array del tr dentro..
                ih=0;
            });
            //console.log(data);
            if(cambiarDiseno!=null){
                sScrollY  = cambiarDiseno['tamano'];
                bPaginate = cambiarDiseno['bPaginate'];
                bScrollCollapse = cambiarDiseno['bScrollCollapse'];
                searching = cambiarDiseno['searching'];
                bLengthChange = cambiarDiseno['bLengthChange'];
                iDisplayLength = cambiarDiseno['iDisplayLength'];
                bJQueryUI: cambiarDiseno['bJQueryUI'];
                ancho= cambiarDiseno['ancho'];
               
                //bSort = cambiarDiseno['bSort'];
            }
            else{
                bJQueryUI: true,
                    bPaginate = true ;
                bScrollCollapse= false;
                sScrollY= "";
            }

            tabletools = {
                "aButtons": [

                ],
                "sSwfPath": "../../js/dataTables/TableTools-2.2.4/swf/copy_csv_xls_pdf.swf"

            };

            if (tabla==="#tabla_auditorias"){

                    tabletools = {

                            "aButtons": [

                                {
                                    "sExtends": "pdf",
                                    "sButtonText": "Generar PDF",
                                    "sPdfOrientation": "landscape",
                                    "sPdfMessage": "Reporte Generado por Sistema de Gestion de Red",
                                    "mColumns": [1,2,3,4,5],

                                },
                            ],
                                "sSwfPath": "../../js/dataTables/TableTools-2.2.4/swf/copy_csv_xls_pdf.swf"

                    }
            }
            console.log(tabla);

            var QTable = $(tabla).dataTable( {
                "language": {
                    "url": "../web/js/dataTables/spa_SPA.json"
                },
                "bRetrieve" :true,
                "iDisplayLength": iDisplayLength ,
                "bSort" :"true",
                "sScrollY": sScrollY,
                "bScrollCollapse": bScrollCollapse,
                "bPaginate":bPaginate,
                "searching": searching,
                "bLengthChange": bLengthChange,
                "data": data2,
                "bJQueryUI":false,
                "async": false,
                "pageLength":5,
                "columns": ancho,
                "scrollX": true ,
                "sDom": 'lfrtip<"clear spacer">T',
                tableTools:tabletools,
                "aoColumnDefs": [
                    {
                        "aTargets": [-1],
                        "mData": null,
                        "sDefaultContent" :sDefaultContent,
                        "mRender": function (data, type, full) {
                        }
                    },

                    {
                        "targets": columnasvisibles,
                        "visible": false,
                        "searchable": false
                    }
                ],
                "fnDrawCallback": function( oSettings ) {
                    console.log(tabla);
                    if(tabla=="#tabla_busqueda_clientes") {
                       $("#tabla_busqueda_clientes_filter").hide();
                       
                    }
                },


                "fnRowCallback":function(nRow,aData, iDisplayIndex, iDisplayIndexFull ){
                    

                    if(tabla==="#tabla_busqueda_clientes"){
                        $(nRow).children().each(function(index, td) {
                            if(index != 1)  {
                                $(td).attr("style","text-align: center;");
                            }
                        });
                        var boton = $(nRow).find(".botonRow");
                        $(boton).parent().attr('style','text-align:center');

                       var btnEditar = $(nRow).find(".btnEditar").off();
                       
                         $(btnEditar).on("click",function () {
                          editarCliente(aData);
                        });
                        
                        $(btnEditar).parent().attr('style','text-align:center');
                        
                        $(nRow).children().each(function(index, td) {
                            data=$(td).html();

                            if(index == 1){
                              // $(td).html(formatearNumero(data));
                            }
                            if(index == 4){
                                if (data=='M')
                                $(td).html("Masculino");
                                else
                                $(td).html("Femenino");
                                
                            }
                        });

                    }
                    if(tabla==="#tabla_usuarios"){


                        var btnEditar = $(nRow).find(".btnEditar").off();
                        var btnReset = $(nRow).find(".btnReset").off();

                        $(btnReset).on("click",function () {
                            $(nRow).removeClass("selected");
                            var r = confirm("Desea resetear la contreseña de "+aData[2]+" "+aData[3]+" la nueva contraseña sera: 123456");
                            if (r == true) {
                                ResetClave(aData);
                            }
                        });
                        $(btnEditar).on("click",function () {
                            $(nRow).removeClass("selected");
                            seleccionarUsuario(aData);
                        });

                        $(btnEditar).parent().attr('style','text-align:center');
                    }
                    if(tabla==="#tabla_auditorias"){

                        $(nRow).children().each(function(index, td) {

                            if((index == 0)||(index == 1)||(index == 3)||(index == 4))  {
                                $(td).attr("style","text-align: center;");

                                /* if ($(td).html() == "") {
                                 $(nRow).children().each(function(index, td) {
                                 console.log($(td).html());
                                 if(index == 1)  {
                                 $(td).attr("style","background-color:rgb(103, 255, 33)")
                                 }});
                                 }else{
                                 $(td).attr("style","background-color:rgb(255,151,142)")
                                 }*/
                            }
                            if(index == 1){
                                data = $(td).html();

                                if (!data.search("Eliminar ACL DNS")){
                                    $(nRow).attr("style","background-color:rgba(0, 255, 0, 0.2)")
                                }
                                if (!data.search("Asignar ACL DNS")){
                                    $(nRow).attr("style","background-color:rgba(0, 255, 0, 0.4)")
                                }
                                if (!data.search("Eliminar ACL SQUID")){
                                    $(nRow).attr("style","background-color:rgba(1, 65, 255, 0.2)")
                                }
                                if (!data.search("Asignar ACL SQUID")){
                                    $(nRow).attr("style","background-color:rgba(1, 65, 255, 0.4)")
                                }
                                if (!data.search("Login")){
                                    $(nRow).attr("style","background-color:rgba(255,0,0,0.2)")
                                }
                                if (!data.search("Logout")){
                                    $(nRow).attr("style","background-color:rgba(255,0,0,0.4)")
                                }
                                if (!data.search("Editar Host")){
                                    $(nRow).attr("style","background-color:rgba(255,128,0,0.2)")
                                }
                                if (!data.search("Guardar Host")){
                                    $(nRow).attr("style","background-color:rgba(255,128,0,0.4)")
                                }


                            }

                        });

                    }


                    if(tabla==="#tabla_redes"){


                        var btnEditar = $(nRow).find(".editip").off();
                        var btnEliminar = $(nRow).find(".eliminarip").off();

                        $(btnEditar).on("click",function () {
                            //console.log(aData);
                        //    abrirmodal();
                            editarSubNet(aData);
                        });
                        $(btnEliminar).on("click",function () {
                           eliminarSubNet(aData);
                        });

                        $(btnEditar).parent().attr('style','text-align:center');



                        $(nRow).children().each(function(index, td) {

                            if(index == 3)  {
                                if ($(td).html().length>0) {
                                }else{
                                    $(td).html("No Definido").attr("style","text-align: center;");
                                }
                               /* if ($(td).html() == "") {
                                    $(nRow).children().each(function(index, td) {
                                            console.log($(td).html());
                                        if(index == 1)  {
                                            $(td).attr("style","background-color:rgb(103, 255, 33)")
                                        }});
                                }else{
                                    $(td).attr("style","background-color:rgb(255,151,142)")
                                }*/
                            }
                            if(index == 4)  {
                                if ($(td).html().length>0) {
                                }else{
                                    $(td).html("No Definido").attr("style","text-align: center;");
                                }
                            }
                        });

                    }

                    var tabla1 = $(tabla).DataTable();
                    var cadenatabla = tabla + " tbody";
                    $(cadenatabla).on( 'click', 'tr', function () {
                        if ( $(this).hasClass('selected') ) {
                        }
                        else {
                            tabla1.$('tr.selected').removeClass('selected');
                            $(this).addClass('selected');
                        }
                    });
                }
                //aoColumns..
            });  // datatable

 
            $('#xBuscar').keyup(function(){
               QTable.api().search( $(this).val() ).draw();
            })
            $('#bModalBuscar').on('click',function(){
               QTable.api().search( $('#xBuscar').val() ).draw();
            })
           
        }//succes
    });//ajax
}
