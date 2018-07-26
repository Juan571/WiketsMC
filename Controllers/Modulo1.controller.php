<?php
/**
 *
 * Archivo para Gestionar usuarios, crear y modicar usuarios,
 * Existen 2 tipo de usuarios "Administrador y Normal"
 * "El Usuario administrador tiene todos los privilegios al igual que el
 * usuario normal excepto poder gestionar usuarios(este modulo)
 *
 * @author Juan Romero  <jromero@vtelca.gob.ve>
 * @copyright 2015

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
     *  case $action == 'cambiarClave': --> si se cumple esta condicion, arma un sql y cambia la clave del usuario
     * del id_usuario
     * @var $acual = clave que tiene actualmente el usuario
     * @var $clave = clave nueva para establecer
     * @var $id_usuario = contiene el id del usuario en cuestion
     * @var $sql = contiene la la sentencia para aplicar a la BD
     * @return Objeto JSON
     *
     **/
    case $action == 'cambiarClave':


        $actual = md5($_REQUEST['actual']);
        $clave = md5($_REQUEST['password']);

        $id_usuario = $_REQUEST['id_usuario'];
        $sql = ("SELECT * from usuarios where id_usuario = '$id_usuario' and password= '$actual'");
        $rowcont=$ejecuta->numerodefilasAfectadas($sql,$action);
        if ( $rowcont>0){
            $sql1 = ("UPDATE usuarios  set password='$clave' WHERE id_usuario= '$id_usuario';");
            $sql= str_replace("''","null", $sql1);

            echo $ejecuta->ejecutar($sql,$action);
        }else{
            $result = array("respuesta"=>"claveInvalida","evento"=>$action,"row"=>"No conincide la contraseña Actual".$id_usuario);

            echo json_encode($result);
        }
        break;


    /**
     * case $action == 'guardarUsuario': --> si se cumple esta condicion, arma un sql y registra un usuario dependiendo
     * los parametros recibidos:
     *
     * @var $login = usuario del sistema para el logueo, se recibe por $_REQUEST['login']
     * @var $nombre = nombre del usuario , se recibe por $_REQUEST['nombre_usuario']
     * @var $apellido = apellido del usuario , se recibe por $_REQUEST['apellido_usuario']
     * @var $clave = clave del usuario para el logueo, recibido por $_REQUEST['password']
     * @var $tipo_usuario = define si el usuario es administrador o no, (true = S , FALSE = N ) recibido por $_REQUEST['tipo_usuario']
     * @var $sql = contiene la la sentencia para aplicar a la BD
     *
     * @return Objeto JSON
     *
     **/
    case $action == 'guardarUsuario':

        $login = $_REQUEST['login'];
        $nombre = strtoupper($_REQUEST['nombre_usuario']);
        $apellido = strtoupper($_REQUEST['apellido_usuario']);
        $cedula = $_REQUEST['cedula_usuario'];
        $clave = md5($_REQUEST['password']);
        $tipo_usuario = ($_REQUEST['tipo_usuario']=='true') ? "S":"N";

        $sql1 = ("INSERT INTO usuarios values ('','$login','$clave','$tipo_usuario','$cedula','$nombre','$apellido','0','0')");
        $sql= str_replace("''","null", $sql1);

        echo $ejecuta->ejecutar($sql,$action);
        break;
    /**
     * case $action == 'editarUsuario': --> si se cumple esta condicion, arma un sql y edita un usuario dependiendo
     * los parametros recibidos, especialmente el id_usuario
     *
     * @var $login = usuario del sistema para el logueo, se recibe por $_REQUEST['login']
     * @var $nombre = nombre del usuario , se recibe por $_REQUEST['nombre_usuario']
     * @var $apellido = apellido del usuario , se recibe por $_REQUEST['apellido_usuario']
     * @var $clave = clave del usuario para el logueo, recibido por $_REQUEST['password']
     * @var $tipo_usuario = define si el usuario es administrador o no, (true = S , FALSE = N ) recibido por $_REQUEST['tipo_usuario']
     * @var $sql = contiene la la sentencia para aplicar a la BD
     * @var $id_usuario = id del usuario para ser editado, recibido por $_REQUEST['id_usuario'];
     * @var $cedula = contiene la cedula del usuario recibido por $_REQUEST['cedula']
     *
     * @return Objeto JSON
     **/
    case $action == 'editarUsuario':
        $id_usuario = $_REQUEST['id_usuario'];
        $login = $_REQUEST['login'];
        $nombre = strtoupper($_REQUEST['nombre_usuario']);
        $apellido = strtoupper($_REQUEST['apellido_usuario']);
        $cedula = $_REQUEST['cedula_usuario'];
       // $clave = md5($_REQUEST['password']);
        $tipo_usuario = ($_REQUEST['tipo_usuario']=='true') ? "S":"N";

        $sql1 = ("UPDATE usuarios  set login='$login', tipo_usuario='$tipo_usuario',cedula_usuario='$cedula',nombre_usuario='$nombre',apellido_usuario='$apellido' WHERE id_usuario= '$id_usuario';");
        $sql= str_replace("''","null", $sql1);

        echo $ejecuta->ejecutar($sql,$action);
        break;

    /**
     * case $action == 'ResetClave': --> si se cumple esta condicion, arma un sql y resetea la clave de id_usuario
     * para que posteriormente el usuario resetee su clave desde el modulo cambiar clave
     *
     * @var $clave = clave del usuario para el logueo, se establece por defecto md5(123456), para que luego el usuario lo
     * @var $sql = contiene la la sentencia para aplicar a la BD
     * @var $id_usuario = id del usuario para ser editado, recibido por $_REQUEST['id_usuario'];
     *
     * @return Objeto JSON
     **/
    case $action == 'ResetClave':

        $id_usuario = (isset($_REQUEST['id_usuario'])) ? $_REQUEST['id_usuario'] : die("no se ha definido el Id de Usuario");
        $clave = md5("123456");
        $sql1 = ("UPDATE usuarios  set password='$clave' WHERE id_usuario= '$id_usuario';");
        $sql= str_replace("''","null", $sql1);

        echo $ejecuta->ejecutar($sql,$action);
        break;

    case $action == 'eliminarCliente':
     
        $xid = (isset($_REQUEST['id_cliente'])) ? $_REQUEST['id_cliente'] : die("no se ha definido xid");
       
        $stmt = $ejecuta->prepare("DELETE FROM clientes WHERE idclientes = ? ");
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
        
        $chk = $ejecuta->ver_num_registros("SELECT * FROM clientes WHERE clientesCedula = ".$clientesCedula.' and idclientes <> '.$xid);
        
        if($chk>0){
            die("existe");
        }
        
        $stmt = $ejecuta->prepare("UPDATE clientes SET
        `clientesNombres` = ? , `clientesApellidos` = ?, `clientesCedula` = ?, `clientesFechaNac` = ?, `clientesCargo` = ?, `clientesSexo` = ?
        WHERE idclientes = ? ");
        
        
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
        
        $chk = $ejecuta->ver_num_registros("SELECT * FROM clientes WHERE clientesCedula = ".$clientesCedula);
        
        if($chk>0){
            die("existe");
        }
        
        $stmt = $ejecuta->prepare("INSERT INTO clientes 
        (`idclientes`, `clientesNombres`, `clientesApellidos`, `clientesCedula`, `clientesFechaNac`, `clientesCargo`, `clientesSexo`) 
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
        $sql1 = ("SELECT *  FROM  clientes where idclientes = $id_cliente ");
        

        $sql1= str_replace("''","null", $sql1);

        
        $datos = $ejecuta->obtener($sql1,null);
       

        $result = array("respuesta"=>$datos,"evento"=>$action);


        echo json_encode($result);


        break;

    case $action == 'obtenerClientes':
        $sql1 = ("SELECT idclientes as id, 
            CONCAT(clientesNombres,' ',clientesApellidos) as 'Nombre\ Completo', 
            clientesCedula as Cédula, 
            clientesFechaNac as 'Fecha\ de\ Nacimiento', 
            Cargosnombre as Cargo, 
            clientesSexo as Sexo 
            FROM wiketstest.clientes
            INNER JOIN cargos on wiketstest.clientes.clientesCargo=wiketstest.cargos.idCargos");
        //die($sql1);

        $sql= str_replace("''","null", $sql1);

        echo $ejecuta->obtener($sql1,$action);
        break;


    default :
        break;

}
?>