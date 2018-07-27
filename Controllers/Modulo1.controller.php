<?php
/**
 *
 * @author Juan Romero  <jromero@vtelca.gob.ve>
 * @copyright 2018

 * @version 1
 *
 */
include ("../Funciones/conexion.php");
$date = date('Y-m-d ');
$ejecuta = new conexion();

if(isset($_REQUEST['action'])){
    $action = $_REQUEST['action'];
} else {
    die("Ninguna accion ha sido a definida");
}

/**
 * dependiendo la $action recibido por post se ejecuta la accion,
 * si no se recibe nada por $REQUEST['action'] cancela la operacion de este archivo
 * y devuelve "Ninguna accion ha sido a definida"
 *
 *
**/
switch ($action){

    /**
     *  case $action == 'eliminarCliente': --> si se cumple esta condicion, arma un sql elimina el Cliente
     *
     * @var $id_cliente = id del cliente a eliminar
     * 
     * @return Objeto Boolean
     *
     **/
    case $action == 'eliminarCliente':

    $xid = (isset($_REQUEST['id_cliente'])) ? $_REQUEST['id_cliente'] : die("no se ha definido xid");

    $stmt = $ejecuta->prepare("DELETE FROM clientes WHERE id_clientes = ? ");
    $stmt->bindParam(1, $xid, PDO::PARAM_INT);
    
    $result = $stmt->execute();

    echo json_encode($result);


    break;

    case $action == 'EditarCliente':

    $xid = (isset($_REQUEST['data']['xid'])) ? $_REQUEST['data']['xid'] : die("no se ha definido xid");
    $clientesNombres = (isset($_REQUEST['data']['xNombre'])) ? $_REQUEST['data']['xNombre'] : die("no se ha definido xNombre");
    $clientesApellidos = (isset($_REQUEST['data']['xApellido'])) ? $_REQUEST['data']['xApellido'] : die("no se ha definido xApellido");
    $clientesCedula = (isset($_REQUEST['data']['xCedula'])) ? $_REQUEST['data']['xCedula'] : die("no se ha definido Cedula");
    $clientesFechaNac = (isset($_REQUEST['data']['xFecha'])) ? $_REQUEST['data']['xFecha'] : die("no se ha definido xFecha");
    $clientesCargo = (isset($_REQUEST['data']['cCargo'])) ? $_REQUEST['data']['cCargo'] : die("no se ha definido el cCargo");
    $clientesSexo = (isset($_REQUEST['data']['cSexo'])) ? $_REQUEST['data']['cSexo'] : die("no se ha definido el cSexo");

    $chk = $ejecuta->ver_num_registros("SELECT * FROM clientes WHERE clientes_Cedula = ".$clientesCedula.' and id_clientes <> '.$xid);

    if($chk>0){
        die("existe");
    }

    $stmt = $ejecuta->prepare("UPDATE clientes SET
        `clientes_Nombres` = ? , `clientes_Apellidos` = ?, `clientes_Cedula` = ?, `clientes_FechaNac` = ?, `clientes_Cargo` = ?, `clientes_Sexo` = ?
        WHERE id_clientes = ? ");


    $sec = strtotime($clientesFechaNac);
    $clientesFechaNac = date("Y-m-d", $sec);

    $stmt->bindParam(1, $clientesNombres, PDO::PARAM_STR);
    $stmt->bindParam(2, $clientesApellidos, PDO::PARAM_STR);
    $stmt->bindParam(3, $clientesCedula, PDO::PARAM_INT);
    $stmt->bindParam(4, $clientesFechaNac, PDO::PARAM_STR);
    $stmt->bindParam(5, $clientesCargo, PDO::PARAM_STR);
    $stmt->bindParam(6, $clientesSexo, PDO::PARAM_STR);
    $stmt->bindParam(7, $xid, PDO::PARAM_INT);
    
    $result = $stmt->execute();

    echo json_encode($result);

    break;


    case $action == 'GuardarCliente':

    $clientesNombres = (isset($_REQUEST['data']['xNombre'])) ? $_REQUEST['data']['xNombre'] : die("no se ha definido xNombre");
    $clientesApellidos = (isset($_REQUEST['data']['xApellido'])) ? $_REQUEST['data']['xApellido'] : die("no se ha definido xApellido");
    $clientesCedula = (isset($_REQUEST['data']['xCedula'])) ? $_REQUEST['data']['xCedula'] : die("no se ha definido Cedula");
    $clientesFechaNac = (isset($_REQUEST['data']['xFecha'])) ? $_REQUEST['data']['xFecha'] : die("no se ha definido xFecha");
    $clientesCargo = (isset($_REQUEST['data']['cCargo'])) ? $_REQUEST['data']['cCargo'] : die("no se ha definido el cCargo");
    $clientesSexo = (isset($_REQUEST['data']['cSexo'])) ? $_REQUEST['data']['cSexo'] : die("no se ha definido el cSexo");

    $chk = $ejecuta->ver_num_registros("SELECT * FROM clientes WHERE clientes_Cedula = ".$clientesCedula);

    if($chk>0){
        die("existe");
    }

    $stmt = $ejecuta->prepare("INSERT INTO clientes 
        (`id_clientes`, `clientes_Nombres`, `clientes_Apellidos`, `clientes_Cedula`, `clientes_FechaNac`, `clientes_Cargo`, `clientes_Sexo`) 
        VALUES (NULL, ?, ?, ?, ?, ?, ?)");

    $sec = strtotime($clientesFechaNac);
    $clientesFechaNac = date("Y-m-d", $sec);

    $stmt->bindParam(1, $clientesNombres, PDO::PARAM_STR);
    $stmt->bindParam(2, $clientesApellidos, PDO::PARAM_STR);
    $stmt->bindParam(3, $clientesCedula, PDO::PARAM_INT);
    $stmt->bindParam(4, $clientesFechaNac, PDO::PARAM_STR);
    $stmt->bindParam(5, $clientesCargo, PDO::PARAM_STR);
    $stmt->bindParam(6, $clientesSexo, PDO::PARAM_STR);    
    $result = $stmt->execute();

    echo json_encode($result);
    break;

    case $action == 'obtenerCargos':
    $sql1 = ("SELECT *  FROM  cargos");
    $sql1= str_replace("''","null", $sql1);
    $datos = $ejecuta->obtener($sql1,null);
    $result = array("respuesta"=>$datos,"evento"=>$action);
    echo json_encode($result);

    break;

    case $action == 'obtenerCiente':
    $id_cliente = (isset($_REQUEST['id_cliente'])) ? $_REQUEST['id_cliente'] : die("no se ha definido el Id de cliente");
    $sql1 = ("SELECT *  FROM  clientes where id_clientes = $id_cliente ");
    $sql1= str_replace("''","null", $sql1);        
    $datos = $ejecuta->obtener($sql1,null);
    $result = array("respuesta"=>$datos,"evento"=>$action);
    echo json_encode($result);

    break;

    case $action == 'obtenerClientes':
    $sql1 = ("SELECT id_clientes as id, 
        CONCAT(clientes_Nombres,' ',clientes_Apellidos) as 'Nombre\ Completo', 
        clientes_Cedula as Cédula, 
        clientes_FechaNac as 'Fecha\ de\ Nacimiento', 
        cargos_nombre as Cargo, 
        clientes_Sexo as Sexo 
        FROM clientes
        INNER JOIN cargos on clientes.clientes_Cargo=cargos.id_Cargos");

    $sql= str_replace("''","null", $sql1);

    echo $ejecuta->obtener($sql1,$action);
    break;


    default :
    break;

}
?>