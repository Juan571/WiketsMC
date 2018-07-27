
$(document).ready(function() {

  $.fn.datepicker.dates['es'] = {
    days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    daysShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
    daysMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthsShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
  };

  $('#xFecha').datepicker({ format: 'dd-mm-yyyy',altFormat: "yyyy-mm-dd",language: 'es' });

  $("#xCedula").mask('000.000.000',{reverse: true});

  $("#xCedula").focus();

  $('#xCedula').keypress(function (e) {

  });

  $(document).on("focusin", "#xFecha", function() {
   $(this).prop('readonly', true);  
  });

  $(document).on("focusout", "#xFecha", function() {
   $(this).prop('readonly', false); 
  });

  $("#bEliminar").on("confirmed.bs.confirmation", function() {
   eliminarCliente();
  }), 
  $("#bEliminar").on("canceled.bs.confirmation", function() {

  })

  $("#formMantenedorClientes").validate({
    rules:{
      xCedula:{
        required:true,
        minlength: 7,
        maxlength: 11                
      }, 
      xNombre:{
        required:true,
        minlength: 3,
        maxlength: 25                
      }, 
      xApellido:{
        required:true,
        minlength: 3,
        maxlength: 25                
      },
      xFecha:{
        required:true,
      },
      cCargo:{
        required:true,
      },
      cSexo:{
        required:true,
      },
    },
    messages:{
      xCedula:{
        required:"Ingresa una Cedula Válida",
        minlength: "Ingresa el una Cédula Válida 10.100.100",
        maxlength: "Maximo 11 caracteres para una cédula"
      },  
      xNombre:{
        required:"Ingresa al menos un nombre válido",
        minlength: "El nombre debe de tener minimo 3 caracteres ",
        maxlength: "Maximo 25 caracteres para el Nombre"
      },
      xApellido:{
        required:"Ingresa al menos un Apellido válido",
        minlength: "El Apellido debe de tener minimo 3 caracteres ",
        maxlength: "Máximo 25 caracteres para el Apellido"
      },
      xFecha:{
        required:"Ingresa una fecha de Nacimiento",
      },
      cCargo:{
        required:"Seleccione un Cargo",
      },
      cSexo:{
        required:"Seleccione el Sexo",
      }
    },
    debug:true,
    submitHandler:function(){
      App.blockUI({
        target: "#formMantenedorClientes",
        animate: !0
      })
      var datosformulario1 = $("#formMantenedorClientes").serializeArray();
      var data={};
      var datos={};
      $.each(datosformulario1, function (i, a) {
        if(a.name=='xCedula'){
          a.value = a.value.replace(/\./g, "");
        }
        if(a.value===""){
          a.value=null;
        }
        datos[a.name]=a.value;
      });

      if (!datos['xid']){
        data["action"]="GuardarCliente";
        data["data"]=datos;

      }else{
        data["action"]="EditarCliente";
        data["data"]=datos;
      }

      $.ajax({
        url: "../Controllers/Modulo1.controller.php",
        async: false,
        data: data,
        dataType: "JSON",
        type: "post",
        beforeSend:function(){},

        error: function (err) {
          App.unblockUI('#formMantenedorClientes');
          toastr.error("Ha ocurrido un error", "Error", {
            "timeOut": "2000",
            "extendedTImeout": "0"
          });
          error = err;
          console.log(error.responseText);
        },
        success: function (dataRes) {
         App.unblockUI('#formMantenedorClientes');     

         if (dataRes=='existe'){

           toastr.error("La cedula ya existe", "Error", {
            "timeOut": "2000",
            "extendedTImeout": "0"
          });
         }
         if (dataRes==true){

           limpiar();
           toastr.success("Exito al Guardar", "Información", {
            "timeOut": "2000",
            "extendedTImeout": "0"
          });
         }
        }
      }) 
    }
  });

  datos={};
  datos["action"]="obtenerCargos";
  datos["id_usuario"]=$("#id_usuario").val();
  ajax(datos);

});

function formatearNumero(nStr) {
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? ',' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + '.' + '$2');
  }
  return x1 + x2;
}
function limpiar(){

  $('#formMantenedorClientes')[0].reset();
  $("#xid").val("");
  $("#bEnvio").text("Guardar").removeClass('yellow').addClass('green');
  $("#bEliminar").hide();

}


function eliminarCliente(){

  idq= $("#xid").val();

  datos={};
  datos["action"]="eliminarCliente";
  datos["id_cliente"]=idq;

  $.ajax({
    url: "../Controllers/Modulo1.controller.php",
    async: false,
    data: datos,
    dataType: "JSON",
    type: "post",
    beforeSend:function(){

      App.blockUI({
        target: "#formMantenedorClientes",
        animate: !0
      })
    },
    error: function (err) {
      App.unblockUI('#formMantenedorClientes');
      toastr.error("Ha ocurrido un error", "Error", {
        "timeOut": "2000",
        "extendedTImeout": "0"
      });
      error = err;
      console.log(error.responseText);
    },
    success: function (dataRes) {
     App.unblockUI('#formMantenedorClientes');     


     if (dataRes==true){

       limpiar();
       toastr.success("Cliente eliminado satisfactoriamente", "Información", {
        "timeOut": "2000",
        "extendedTImeout": "0"
      });
     }
                  //console.log(dataRes)

                }
              }) 

}
function editarCliente(aData){

  datos={};
  datos["action"]="obtenerCiente";
  datos["id_cliente"]=aData[0];

  $.ajax({
    url: "../Controllers/Modulo1.controller.php",
    async: false,
    data: datos,
    dataType: "JSON",
    type: "post",
    error: function (err) {
      error = err;
      console.log(error.responseText);
    },
    success: function (data) {

      data.respuesta= JSON.parse(data.respuesta);
      data.respuesta=data.respuesta[0];
      limpiar();
      $('#modal_busqueda_clientes').modal('hide');

      $("#xCedula").val(formatearNumero(data.respuesta.clientes_Cedula));
      $("#xNombre").val(data.respuesta.clientes_Nombres);
      $("#xApellido").val(data.respuesta.clientes_Apellidos);
      $("#xFecha").val(data.respuesta.clientes_FechaNac);
      $("#cCargo").val(data.respuesta.clientes_Cargo);
      $("#cSexo").val(data.respuesta.clientes_Sexo);
      $("#xid").val(data.respuesta.id_clientes);


      $inputBox = jQuery('#xCedula').eq(1);
      var e = jQuery.Event("keypress");
                e.which = 32; // add spacebar
                $inputBox.trigger(e)

                $("#bEnvio").text("Editar").removeClass('green').addClass('yellow');
                $("#bEliminar").show();

              }
            })
}

$('#modal_busqueda_clientes').on('shown.bs.modal', function (e) {

  if ($("#tabla_busqueda_clientes").children().length > 0) {
    $("#tabla_busqueda_clientes").dataTable().fnClearTable();
    $("#tabla_busqueda_clientes").dataTable().fnDestroy();
    $("#tabla_busqueda_clientes thead > tr >  th").hide();
  }


  var cambiarDiseno = {};
  cambiarDiseno['tamano']= null;
  cambiarDiseno['bPaginate'] = true;
  cambiarDiseno['bScrollCollapse'] = false;
  cambiarDiseno['searching'] = true;
  cambiarDiseno['bLengthChange'] = false;
  cambiarDiseno['iDisplayLength'] = 8;
  cambiarDiseno['ancho'] = [
  { "width": "5%" },
  { "width": "30%" },
  { "width": "10%" },                
  { "width": "20%" },
  { "width": "15%" },
  { "width": "10%" },
  { "width": "5%" }

  ];
  sDefaultContent ="<button title='Seleccionar' style='padding:3px' class='btnEditar  btn btn-warning'>" +
  "<span class='glyphicon glyphicon-pencil'></span>" +
  "</button>";
  cargarTablas(
    "obtenerClientes",
    "",
    "#tabla_busqueda_clientes",
    cambiarDiseno, 
    [0,0],
    "../Controllers/Modulo1.controller.php",
    null,
    sDefaultContent,
    "#divModal"
  );

})
function alphaOnly(event) {
  var key = event.keyCode;
  return ((key >= 65 && key <= 90) || key == 9 || key == 8 || key ==32 );
};

function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function utf8_decode(str_data) {

  var tmp_arr = [],
  i = 0,
  ac = 0,
  c1 = 0,
  c2 = 0,
  c3 = 0,
  c4 = 0;

  str_data += '';

  while (i < str_data.length) {
    c1 = str_data.charCodeAt(i);
    if (c1 <= 191) {
      tmp_arr[ac++] = String.fromCharCode(c1);
      i++;
    } else if (c1 <= 223) {
      c2 = str_data.charCodeAt(i + 1);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
      i += 2;
    } else if (c1 <= 239) {
      // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    } else {
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      c4 = str_data.charCodeAt(i + 3);
      c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
      c1 -= 0x10000;
      tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
      tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
      i += 4;
    }
  }
  return tmp_arr.join('');
}
function ajax(data){

 $.ajax({
  url: "../Controllers/Modulo1.controller.php",
  async: false,
  data: data,
  dataType: "JSON",
  type: "post",
  error: function (err) {
    error = err;
    console.log(error.responseText);
            //alert("Este Registro ya existe..");
          },
          success: function (data) {

            respuesta = JSON.parse(data.respuesta);

            $(respuesta).each(function( index,val ) {
              $('#cCargo').append($('<option>', {
                value: val.id_Cargos,
                text: val.cargos_Nombre
              }));
            });          
        }
      });


}

