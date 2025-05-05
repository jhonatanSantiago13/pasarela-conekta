<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago con Conekta - MSI</title>
  <script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>
  <style>
    body { font-family: sans-serif; padding: 20px; }
    input, select { display: block; margin-bottom: 10px; padding: 8px; width: 300px; }
    button { padding: 10px 20px; }
    #response { margin-top: 20px; font-weight: bold; }
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
</head>
<body>

<!-- Resumen del pago -->
<div id="summary">
      <h3>Resumen de Pago</h3>
      <p>Monto total: <strong>$1,500.00 MXN</strong></p>
      <p id="msi-info">
        Para meses sin intereses:<br>
        <ul>
          <li>3 meses:  compra Miníma de <strong>$1,500.00 MXN</strong></li>
          <li>6 meses: compras  de <strong>$3000.00 MXN</strong> o más</li>
          <li>12 meses: compras  de <strong>$10,000.00 MXN</strong> </li>
        </ul>
  
      </p>
    </div>

  <h2>Formulario de Pago</h2>

  <form id="payment-form">
    <input type="hidden" name="amount" value="1500" id="amount">
    <input type="text" data-conekta="card[name]" placeholder="Nombre del titular" required value="Jhonatan Santiago ">
    <input type="text" data-conekta="card[number]" placeholder="Número de tarjeta" required >
    <input type="text" data-conekta="card[exp_month]" placeholder="Mes de expiración (MM)" required value="12">
    <input type="text" data-conekta="card[exp_year]" placeholder="Año de expiración (AA)" required  value="25">
    <input type="text" data-conekta="card[cvc]" placeholder="CVC" required value="123">

    <select name="monthly_installments" id="monthly_installments" required>
      <!-- <option value="">Selecciona meses sin intereses</option>
      <option value="0">Pago único ()</option>
      <option value="3">3 meses</option>
      <option value="6">6 meses</option>
      <option value="9">9 meses</option>
      <option value="12">12 meses</option> -->
    </select>

    <input type="email" name="email" placeholder="Correo electrónico" required>
    <input type="text" name="name" placeholder="Nombre completo" required>
    <input type="tel" name="phone" placeholder="Teléfono" required>

    <input type="hidden" name="token_id" id="token_id">

    <button type="submit" id="submit-button">Pagar</button>
  </form>

  <div id="response"></div>

  <script>

    /*Formato moneda*/
    const formatoMoneda = new Intl.NumberFormat('es-MX', {
          style: 'currency',
          currency: 'MXN',
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
    });




    Conekta.setPublicKey('key_DGjl2SLd8oDQJzRnusZMYr0'); // Reemplaza con tu llave pública

    const form = document.getElementById("payment-form");
    const responseDiv = document.getElementById("response");
    const submitBtn   = document.getElementById("submit-button");
    const msiSelect   = document.getElementById("monthly_installments");
    const msiInfo     = document.getElementById("msi-info");
    const amount      = document.getElementById('amount').value;

    // Actualiza el resumen cuando se selecciona MSI

    const montoUnico =formatoMoneda.format(amount);
    const msi3 = formatoMoneda.format(amount / 3);
    const msi6 = formatoMoneda.format(amount / 6);
    const msi12 = formatoMoneda.format(amount / 12);


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
    
   
    // Actualiza el resumen cuando se selecciona MSI
    /* msiSelect.addEventListener("change", function() {
      const msi = parseInt(this.value);
      if (msi > 0) {
        const mensualidad = (1500 / msi).toFixed(2);
        msiInfo.innerHTML = `Pago en <strong>${msi}</strong> mensualidades de <strong>$${mensualidad} MXN</strong>`;
      } else {
        msiInfo.innerHTML = "Selecciona los meses sin intereses";
      }
    }); */

    form.addEventListener("submit", function(event) {
      event.preventDefault();

      responseDiv.innerHTML = "⏳ Procesando...";
      submitBtn.disabled = true;

      Conekta.Token.create(form, function(tokenObject) {
        document.getElementById("token_id").value = tokenObject.id;

        const formData = new FormData(form);

        fetch("crear_cargo.php", {
          method: "POST",
          body: formData
        })
        .then(response => response.text())
        .then(data => {
          submitBtn.disabled = false;
          const msg = data.toLowerCase();

          if (msg.includes("card_over_limit")) {
            responseDiv.innerHTML = `<p style="color:red;">❌ La tarjeta no cuenta con el límite de crédito suficiente para realizar el pago.</p>`;
          } else if (msg.includes("not valid for installments")) {
            responseDiv.innerHTML = `<p style="color:red;">❌ Tu tarjeta no permite pagos a meses sin intereses. Usa una tarjeta de crédito.</p>`;
          } else if (msg.includes("error")) {
            responseDiv.innerHTML = `<p style="color:red;">❌ ${data}</p>`;
          } else {
            responseDiv.innerHTML = `<p style="color:green;">✅ ${data}</p>`;
          }
        })
        .catch(error => {
          submitBtn.disabled = false;
          responseDiv.innerHTML = `<p style="color:red;">❌ Error de red: ${error.message}</p>`;
        });

      }, function(errorResponse) {
        submitBtn.disabled = false;
        responseDiv.innerHTML = `<p style="color:red;">❌ ${errorResponse.message_to_purchaser || "Error al generar el token."}</p>`;
      });
    });
  </script>
</body>
</html>
