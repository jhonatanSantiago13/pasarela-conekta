<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago con Conekta - MSI</title>
  <script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>
  <style>

    button#submit-button:disabled {
      background-color: #888 !important;
      cursor: not-allowed;
      opacity: 0.8;
    }

    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      width: 100%;
      max-width: 550px;
      padding: 20px;
    }
    #summary {
      border: 1px solid #ccc;
      padding: 15px;
      background: #fff;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    #summary h3 {
      margin-top: 153px;
    }
    #payment-form {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input, select {
      display: block;
      margin-top: 5px;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    button#submit-button {
      background-color: #28a745;
      color: white;
      font-size: 18px;
      font-weight: bold;
      padding: 15px;
      border: none;
      border-radius: 5px;
      width: 100%;
      margin-top: 20px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button#submit-button:hover {
      background-color: #218838;
    }
    #response {
      margin-top: 20px;
      font-weight: bold;
    }
    
    /* #msi-info, li {
      font-size: 13px;
      color: #555;
    } */

    #summary ul {
      padding-left: 20px;
      margin-top: 5px;
      color: #555;
      font-size: 13px;
    }

    /*  #summary h3 {
      display: block;
      margin: 0 0 10px 0;
      font-size: 20px;
      color: #333;
    } */



  </style>
</head>
<body>

<div class="container">

      <!-- Resumen del pago -->
    <div id="summary">
          <h3>Resumen de Pago</h3>
          <p>Monto total: <strong id="monto">$1,500.00 MXN</strong></p>
          <p id="msi-info">Para meses sin intereses:</p>
          <ul>
            <li>3 meses: compra mínima de <strong>$1,500.00 MXN</strong></li>
            <li>6 meses: compras de <strong>$3,000.00 MXN</strong> o más</li>
            <li>12 meses: compras de <strong>$10,000.00 MXN</strong></li>
          </ul>
    </div>



      <h2 style="text-align: center;">Formulario de Pago</h2>

      <form id="payment-form">

            <label for="card-name">Nombre del titular</label>
            <input type="text" id="card-name" data-conekta="card[name]" required value="Jhonatan Santiago">

            <label for="card-number">Número de tarjeta</label>
            <input type="text" id="card-number" data-conekta="card[number]" required>

            <label for="card-exp-month">Mes de expiración (MM)</label>
            <input type="text" id="card-exp-month" data-conekta="card[exp_month]" required value="12">

            <label for="card-exp-year">Año de expiración (AA)</label>
            <input type="text" id="card-exp-year" data-conekta="card[exp_year]" required value="25">

            <label for="card-cvc">CVC</label>
            <input type="text" id="card-cvc" data-conekta="card[cvc]" required value="123">

            <label for="amount">Monto a pagar (MXN)</label>
            <input type="text" name="amount" value="1500" id="amount" required>

            <label for="monthly_installments">Meses sin intereses</label>
            <select name="monthly_installments" id="monthly_installments" required></select>

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" placeholder="Correo electrónico" required>

            <label for="name">Nombre completo</label>
            <input type="text" name="name" placeholder="Nombre completo" required>

            <label for="phone">Teléfono</label>
            <input type="tel" name="phone" placeholder="Teléfono" required>

            <input type="hidden" name="token_id" id="token_id">

            <button type="submit" id="submit-button">💳 Pagar</button>
            
      </form>

      <div id="response"></div>

</div>

  <script>

    /*Formato moneda*/
    const formatoMoneda = new Intl.NumberFormat('es-MX', {
          style: 'currency',
          currency: 'MXN',
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
    });

    const totalInput = document.getElementById('amount');

    totalInput.addEventListener('change', function() {
        
        selectMsi();

        const monto = document.getElementById('monto');
        const amount  = document.getElementById('amount').value;

        monto.textContent = formatoMoneda.format(amount);

    });

    // Actualiza el resumen cuando se selecciona MSI
    const selectMsi = () =>{

          const amount  = document.getElementById('amount').value;

          const montoUnico = formatoMoneda.format(amount);
          const msi3       = formatoMoneda.format(amount / 3);
          const msi6       = formatoMoneda.format(amount / 6);
          const msi12      = formatoMoneda.format(amount / 12);

          const msiSelect   = document.getElementById("monthly_installments");

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




    Conekta.setPublicKey('key_DGjl2SLd8oDQJzRnusZMYr0'); // Reemplaza con tu llave pública

    const form = document.getElementById("payment-form");
    const responseDiv = document.getElementById("response");
    const submitBtn   = document.getElementById("submit-button");
    const msiSelect   = document.getElementById("monthly_installments");
    const msiInfo     = document.getElementById("msi-info");
    const amount      = document.getElementById('amount').value;

    // Actualiza el resumen cuando se selecciona MSI

    /* const montoUnico =formatoMoneda.format(amount);
    const msi3 = formatoMoneda.format(amount / 3);
    const msi6 = formatoMoneda.format(amount / 6);
    const msi12 = formatoMoneda.format(amount / 12); */


    /* if(amount == 1500){
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
    } */
    
   
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

  // Deshabilitar y cambiar diseño del botón
  submitBtn.disabled = true;
  submitBtn.style.backgroundColor = "#888";
  submitBtn.innerHTML = "⏳ Procesando pago...";

  // Mostrar mensaje opcional
  responseDiv.innerHTML = "⏳ Procesando...";

  // Esperar 5 segundos antes de continuar
  setTimeout(() => {
    Conekta.Token.create(form, function(tokenObject) {
      document.getElementById("token_id").value = tokenObject.id;

      const formData = new FormData(form);

      fetch("crear_cargo.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(data => {
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

        // Restaurar el botón
        submitBtn.disabled = false;
        submitBtn.style.backgroundColor = "#28a745";
        submitBtn.innerHTML = "💳 Pagar";
      })
      .catch(error => {
        responseDiv.innerHTML = `<p style="color:red;">❌ Error de red: ${error.message}</p>`;
        submitBtn.disabled = false;
        submitBtn.style.backgroundColor = "#28a745";
        submitBtn.innerHTML = "💳 Pagar";
      });

    }, function(errorResponse) {
      responseDiv.innerHTML = `<p style="color:red;">❌ ${errorResponse.message_to_purchaser || "Error al generar el token."}</p>`;
      submitBtn.disabled = false;
      submitBtn.style.backgroundColor = "#28a745";
      submitBtn.innerHTML = "💳 Pagar";
    });
  }, 5000); // Espera de 5 segundos
});

   
  </script>
</body>
</html>
