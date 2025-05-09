<?php 

session_start();

// Aquí se marca la sesión como pago exitoso
$_SESSION['pago_exitoso'] = true;

$id = $_GET['id'] ;// ID de la transacción

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago realizado exitosamente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Confetti JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #eef5fb;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
      color: #2262c6;
    }

    .container {
      background: white;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 90%;
    }

    h1 {
      font-size: 22px;
      margin-bottom: 10px;
    }

    .order-id {
      font-weight: 500;
      margin-bottom: 20px;
      font-size: 15px;
      color: #1a4ca0;
    }

    .check-icon {
      font-size: 60px;
      color: #22c55e;
      margin-bottom: 20px;
    }

    .btn {
      display: inline-block;
      background-color: #0070f3;
      color: white;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      font-size: 14px;
      transition: background 0.3s;
    }

    .btn:hover {
      background-color: #0059c1;
    }

    .check-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 30px auto;
    width: 150px;
    height: 150px;
    }

    .check-icon svg {
    width: 100%;
    height: 100%;
    }

    /*  */
    @keyframes pop-in {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        60% {
            transform: scale(1.2);
            opacity: 1;
        }
        100% {
            transform: scale(1);
        }
        }

        .check-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 30px auto;
        width: 150px;
        height: 150px;
        animation: pop-in 0.6s ease-out;
    }

    

  </style>
</head>
<body>

  <div class="container">
    <h1>✅ Pago realizado exitosamente</h1>
    <div class="order-id">ID de transacción: <span id="order-id"><?php echo $id; ?></span></div>
    <!-- <div class="check-icon">✔️</div> -->
    <div class="check-icon">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" fill="#22c55e" />
          <path d="M7 12l3 3 7-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    <a href="https://clarity.com.mx/" class="btn">Continuar</a>
  </div>

  <script>
    // Simula ID dinámico (reemplaza con PHP o query string si es necesario)
    /* const orderId = new URLSearchParams(window.location.search).get("order_id") || "N/D";
    document.getElementById("order-id").textContent = orderId; */

    // Confeti al cargar
    window.onload = () => {
      confetti({
        particleCount: 150,
        spread: 70,
        origin: { y: 0.6 }
      });
    };
  </script>

</body>
</html>
