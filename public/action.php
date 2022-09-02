<?php
	error_reporting(E_ALL);
	
	$fields = 'formData={
        "description": {
            "Nombre del establecimiento o Razón social": "sanson bar",
            "Identificación del establecimiento (NIT o cedula propietario)": "4584621258-7",
            "Características del lugar (local, fachada física, puerta de ingreso)": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
            "Días y Horas en qué funciona": "lun - dom de 8pm - 3am",
            "Departamento": "sospec_sit_frec_ant",
            "Municipio": "frec_child_options_med",
            "Barrio": "bello",
            "Municipio ant otro": "",
            "Barrio ant otro": "",
            "Dirección exacta o indicaciones del lugar donde compran/venden objetos robados": "calle 456 # 234-67",
            "Información sobre los responsables del lugar": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt",
            "Días y horas de entrega/recibo de objetos robados": "lun - dom de 8pm - 3am",
            "Tipo de objetos": "motos",
            "Tipo de bien": "",
            "¿Por qué sospecha que los bienes son robados?": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod",
            "Características o descripción del bien": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
            "Nombres del sospechoso": "carlos",
            "Apellidos del sospechoso": "mendez",
            "Alias": "pepe el grillo",
            "Descripción del sospechoso": "hombre alto,negro,,flaco",
            "Tipo de identificación del sospechoso": "cc",
            "Número de identificación del sospechoso": "8956471",
            "De qué está encargado en el negocio": "recibir los hurtos",
            "Número de teléfono del sospechoso": "4568974",
            "Número de celular del sospechoso": "3008965478",
            "Tipo red social": "fb",
            "Cuál red social": "",
            "Información de la red social (usuario, correo, nombre…)": "elnregodel whatsapp",
            "Tipo de vehículo (moto, automóvil, camioneta… )": "taxi",
            "Marca y referencia": "hyundai i25",
            "Placa": "HT89",
            "Color": "amarillo",
            "Otras Características del vehículo": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
            "Información adicional": "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum."
        }
}';

	


$ch = curl_init();
 
// definimos la URL a la que hacemos la petición
curl_setopt($ch, CURLOPT_URL,"https://seguridadenlinea.com/crime-reports/create-anonymous/format/json");
// indicamos el tipo de petición: POST
curl_setopt($ch, CURLOPT_POST, TRUE);
// definimos cada uno de los parámetros
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields.'&type=anonymous');
// recibimos la respuesta y la guardamos en una variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Host: seguridadenlinea.com",
			"User-Agent: PostmanRuntime/7.15.2",
			"Content-Type: application/x-www-form-urlencoded"
		));
$remote_server_output = curl_exec ($ch);
 


 
echo "remote ";
print_r($remote_server_output);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
}
curl_close($ch);

if (isset($error_msg)) {
    // TODO - Handle cURL error accordingly
	echo $error_msg;
}
?>
