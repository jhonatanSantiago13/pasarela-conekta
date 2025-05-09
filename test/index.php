<?php
session_start();

// Si el pago ya se realizó, no permitir regresar aquí
if (isset($_SESSION['pago_exitoso']) && $_SESSION['pago_exitoso'] === true) {
    unset($_SESSION['pago_exitoso']); // Elimina la variable
    header('Location: https://clarity.com.mx/'); // Redirige
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>

    <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    #card-form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 300px;
    }

    #card-form h2 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 18px;
      text-align: center;
      color: #333;
    }

    .form-group {
      margin-bottom: 10px;
    }

    label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
      color: #555;
    }

    input {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 100%;
      box-sizing: border-box;
      font-size: 13px;
      transition: border 0.3s ease;
    }

    .input {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 100%;
      box-sizing: border-box;
      font-size: 13px;
      transition: border 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: #0070f3;
      box-shadow: 0 0 0 2px rgba(0,112,243,0.1);
    }

    button {
      margin-top: 12px;
      background-color: #0070f3;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
      width: 100%;
      cursor: pointer;
    }

    button:hover {
      background-color: #005fd1;
    }

    .hidden {
      display: none;
    }

    button.disabled {
        background-color: #ccc !important;
        cursor: not-allowed;
    }

    #summary {
      border: 1px solid #ccc;
      padding: 15px;
      margin-top: 20px;
      width: 320px;
      background: #f9f9f9;
    }
    #summary h3 { margin-top: 0; }

    #msi-info{
      font-size: 12px;
      color: #555;
    }

    li{
      /* padding-left: 20px; */
      margin: 3px;
      font-size: 12px;
      color: #555;
    }

  </style>


    <title>Document</title>
</head>
<body>



<form id="card-form">

<!-- Resumen del pago -->

<p>Monto total: <strong id="monto">$1,500.00 MXN</strong></p>
<p id="msi-info">
        Para meses sin intereses:<br>
        <ul>
          <li>3 meses:  compra Miníma de <strong>$1,500.00 MXN</strong></li>
          <li>6 meses: compras  de <strong>$3000.00 MXN</strong> o más</li>
          <li>12 meses: compras  de <strong>$10,000.00 MXN</strong> </li>
        </ul>
  
      </p>

<h2>Formulario de pago</h2>

<div class="form-group">
  <label for="name">Nombre en la tarjeta</label>
  <input value="Taylor Swift" name="name" id="name" type="text" data-conekta="card[name]">
</div>

<div class="form-group">
  <label for="card">Número de tarjeta</label>
  <input name="card" id="card" data-conekta="card[number]" type="text" maxlength="16">
</div>

<div class="form-group">
  <label for="cvc">CVC</label>
  <input id="cvc" data-conekta="card[cvc]" type="text" maxlength="4" value="123">
</div>

<div class="form-group">
  <label for="exp_month">Mes de expiración (MM)</label>
  <input id="exp_month" value="12" data-conekta="card[exp_month]" type="text" maxlength="2">
</div>

<div class="form-group">
  <label for="exp_year">Año de expiración (AA)</label>
  <input id="exp_year" value="25" data-conekta="card[exp_year]" type="text" maxlength="2">
</div>

<div class="form-group">
  <label for="email">Correo electrónico</label>
  <input type="text" name="email" id="email" maxlength="200" value="jhonale44@hotmail.com">
</div>

<div class="form-group">
  <label for="description">Descripción</label>
  <input type="text" name="description" id="description" maxlength="100" value="Ticket No. 52254551212141414141">
</div>

<div class="form-group">
  <label for="total">Total a pagar</label>
  <input type="number" name="total" id="total" value="15000" >
</div>

<div class="form-group">
  <label for="monthly">Meses sin intereses</label>
  <!-- <input type="number" name="monthly" id="monthly" value="3"> -->
  <select name="monthly" id="monthly" class="input" required>
  </select>
</div>
<input type="text" id="conektaTokenId" name="conektaTokenId" class="hidden">


<button type="submit" id="submit-button">Pagar</button>

<div id="payment-message" style="margin-top: 10px; font-size: 13px; text-align: center;"></div>


    </form>
    
</body>

<script>

    
    /*Formato moneda*/
    const formatoMoneda = new Intl.NumberFormat('es-MX', {
          style: 'currency',
          currency: 'MXN',
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
    });

    const totalInput = document.getElementById('total');

    totalInput.addEventListener('change', function() {
        
        selectMsi();

        const monto = document.getElementById('monto');
        const amount  = document.getElementById('total').value;

        monto.textContent = formatoMoneda.format(amount);

    });

    // Actualiza el resumen cuando se selecciona MSI
    const selectMsi = () =>{

          const amount  = document.getElementById('total').value;

          const montoUnico = formatoMoneda.format(amount);
          const msi3       = formatoMoneda.format(amount / 3);
          const msi6       = formatoMoneda.format(amount / 6);
          const msi12      = formatoMoneda.format(amount / 12);

          const msiSelect   = document.getElementById("monthly");

          if(amount == 1500){
            msiSelect.innerHTML = `
              <option value="">Selecciona meses sin intereses</option>
              <option value="0">Pago único (${montoUnico})</option>
              <option value="3">3 meses (${msi3})</option>`;
          }else if(amount >= 3000 && amount < 10000){
            msiSelect.innerHTML = `
              <option value="">Selecciona meses sin intereses</option>
              <option value="0">Pago único (${montoUnico})</option>
              <option value="3">3 meses (${msi3})</option>
              <option value="6">6 meses (${msi6})</option>`;
          }else if(amount >= 10000){   
            msiSelect.innerHTML = `
              <option value="">Selecciona meses sin intereses</option>
              <option value="0">Pago único (${montoUnico})</option>
              <option value="3">3 meses (${msi3})</option>
              <option value="6">6 meses (${msi6})</option>
              <option value="12">12 meses (${msi12})</option>`;
          }else{
            msiSelect.innerHTML = `
              <option value="0">Pago único (${montoUnico})</option>`;
          }

    }; // selectMsi

     selectMsi();

    //CONEKTA
    Conekta.setPublicKey('key_DGjl2SLd8oDQJzRnusZMYr0'); // Reemplaza con tu llave pública

    /*=============================================
    TOKENIZAR LA TARJETA
    =============================================*/

    const tokenizeCard = (event) => {
    
        event.preventDefault(); // Evita el envío del formulario por defecto

        var conektaSuccessResponseHandler= function(token){
            $("#conektaTokenId").val(token.id);
            jsPay();
        };
        
        var conektaErrorResponseHandler =function(response){
            var $form=$("#card-form");
            alert(response.message_to_purchaser);
        }
            
        $(document).ready(function(){
        // setTimeout( ()=>{
        //     var $form=$("#card-form");
        //     /*=============================================
        //     TOKENIZAR LA TARJETA
        //     =============================================*/
        //     Conekta.Token.create($form,conektaSuccessResponseHandler,conektaErrorResponseHandler);
        // },3000);

        var $form=$("#card-form");
        /*=============================================
        TOKENIZAR LA TARJETA
        =============================================*/
        Conekta.Token.create($form,conektaSuccessResponseHandler,conektaErrorResponseHandler);

        })
        
    };// tokenizeCard

    /*=============================================
	LLAMADA AL BACKEND
	=============================================*/
    /*Mandamos los parametros por post al back para realizar el pago*/

    const jsPay = () =>{
        
        const $button = $("#submit-button");
        const $message = $("#payment-message");

        // Desactivar botón y mostrar “procesando”
        $button.prop("disabled", true).addClass("disabled").html("⏳ Procesando...");

        let params=$("#card-form").serialize();
        let url="pay.php";

        

        $.post(url,params,function(data){

            console.log(data);

            // Intentar parsear JSON
            let res;
            try {
                res = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                res = { success: false, error: "Respuesta inválida del servidor" };
            }

            if (res.success) {
                // $message.css("color", "green").text("✅ " + res.message + " (ID: " + res.charge_id + ")");
                const redirectUrl = "success-payment.php?id=" + res.charge_id;
                window.location.href = redirectUrl;


            } else {
                $message.css("color", "red").text("❌ Error: " + (res.error || "Ocurrió un error inesperado"));
            }

             // Restaurar el botón
            $button.prop("disabled", false).removeClass("disabled").html("Pagar");
           
        })

    }; //jsPay

    $(document).on('submit', '#card-form', function(event) {

        const $message = $("#payment-message");
        $message.text('');
          

        tokenizeCard(event);
       
    });


</script>

</html>