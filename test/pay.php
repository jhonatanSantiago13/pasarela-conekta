<?php 

header('Content-Type: application/json');

require("pay.conekta.php");

//extraer los datos de la solicitud
extract($_REQUEST);

/*Se crea un objeto de la clase Payment que importamos
debemos de colocar los parmetros del name de los inputs*/
$oPayment= new Payment($conektaTokenId,$card,$name,$description,$total,$email,$monthly);

$response = $oPayment->pay(); // <-- aquí guardas la respuesta

echo json_encode($response);

?>