<?php
require 'vendor/autoload.php';

\Conekta\Conekta::setApiKey('key_h8NtXzTBadEsQhzgxZmTyn9'); // Reemplaza con tu llave privada real
\Conekta\Conekta::setApiVersion("2.0.0");

header('Content-Type: text/plain; charset=utf-8');

$amount = $_POST['amount'];

try {
    // Validación básica
    if (empty($_POST['token_id']) || empty($_POST['email']) || empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['monthly_installments'])) {
        throw new Exception("Faltan datos necesarios para procesar el pago.");
    }

    $order = \Conekta\Order::create([
        "line_items" => [
            [
                "name" => "Producto con MSI",
                // "unit_price" => 150000, // $1500.00 MXN en centavos
                "unit_price" => $amount * 100, // Convertir a centavos
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
                    "token_id" => $_POST['token_id'],
                    "monthly_installments" => (int) $_POST['monthly_installments']
                ]
            ]
        ]
    ]);

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
