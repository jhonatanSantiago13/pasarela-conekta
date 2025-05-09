<?php
require 'vendor/autoload.php';
header('Content-Type: application/json');

\Conekta\Conekta::setApiKey('key_h8NtXzTBadEsQhzgxZmTyn9'); // Usa tu clave real
\Conekta\Conekta::setApiVersion("2.0.0");

header('Content-Type: application/json; charset=utf-8');

try {
    if (empty($_POST['token_id']) || empty($_POST['email']) || empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['amount'])) {
        throw new Exception("Faltan datos necesarios para procesar el pago.");
    }

    $amount = (float) $_POST['amount'];
    $installments = isset($_POST['monthly_installments']) ? (int) $_POST['monthly_installments'] : 0;

    $orderData = [
        "line_items" => [[
            "name" => "Producto con MSI",
            "unit_price" => intval($amount * 100),
            "quantity" => 1
        ]],
        "currency" => "MXN",
        "customer_info" => [
            "name"  => $_POST['name'],
            "email" => $_POST['email'],
            "phone" => $_POST['phone']
        ],
        "charges" => [[
            "payment_method" => [
                "type" => "card",
                "token_id" => $_POST['token_id']
            ]
        ]]
    ];

    if ($installments > 0) {
        $orderData["charges"][0]["payment_method"]["monthly_installments"] = $installments;
    }

    $order = \Conekta\Order::create($orderData);

    echo json_encode([
        "status" => "success",
        "order_id" => $order->id
    ]);

} catch (\Conekta\ProcessingError $error) {
    echo json_encode(["status" => "error", "message" => $error->getMessage()]);
} catch (\Conekta\ParameterValidationError $error) {
    echo json_encode(["status" => "error", "message" => $error->getMessage()]);
} catch (\Conekta\Handler $error) {
    echo json_encode(["status" => "error", "message" => $error->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

