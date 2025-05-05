<?php
require 'vendor/autoload.php';

\Conekta\Conekta::setApiKey('key_h8NtXzTBadEsQhzgxZmTyn9'); // Usa tu clave real
\Conekta\Conekta::setApiVersion("2.0.0");

header('Content-Type: text/plain; charset=utf-8');

try {
    // Validación básica
    if (empty($_POST['token_id']) || empty($_POST['email']) || empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['amount'])) {
        throw new Exception("Faltan datos necesarios para procesar el pago.");
    }

    $amount = (float) $_POST['amount'];
    $installments = isset($_POST['monthly_installments']) ? (int) $_POST['monthly_installments'] : 0;

    // Estructura base de la orden
    $orderData = [
        "line_items" => [
            [
                "name" => "Producto con MSI",
                "unit_price" => intval($amount * 100), // Convertir a centavos
                "quantity" => 1
            ]
        ],
        "currency" => "MXN",
        "customer_info" => [
            "name"  => $_POST['name'],
            "email" => $_POST['email'],
            "phone" => $_POST['phone']
        ],
        "charges" => [
            [
                "payment_method" => [
                    "type" => "card",
                    "token_id" => $_POST['token_id']
                ]
            ]
        ]
    ];

    // Agrega MSI solo si es mayor a 0
    if ($installments > 0) {
        $orderData["charges"][0]["payment_method"]["monthly_installments"] = $installments;
    }

    // Crear orden
    $order = \Conekta\Order::create($orderData);

    echo "Pago realizado con éxito ✅. ID de orden: " . $order->id;

} catch (\Conekta\ProcessingError $error) {
    echo "Error de procesamiento: " . $error->getMessage();
} catch (\Conekta\ParameterValidationError $error) {
    echo "Error de validación: " . $error->getMessage();
} catch (\Conekta\Handler $error) {
    echo "Error general: " . $error->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
