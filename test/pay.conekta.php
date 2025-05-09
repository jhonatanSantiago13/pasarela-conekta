<?php 

require_once("conekta-php-master/lib/Conekta.php");

class Payment{

    private $ApiKey="key_h8NtXzTBadEsQhzgxZmTyn9"; // Cambia a tu clave real
    private $ApiVersion="2.0.0";

    public function __construct($token,$card,$name,$description,$total,$email,$monthly){
    	$this->token=$token;
        $this->card=$card;
        $this->name=$name;
        $this->description=$description;
        $this->total=$total;
        $this->email=$email;
        $this->monthly=$monthly;
    }	

    public function pay(){
    	\Conekta\Conekta::setApiKey($this->ApiKey);
        \Conekta\Conekta::setApiVersion($this->ApiVersion);

        /*=============================================
        CREAR CLIENTE
        =============================================*/      
        // Crear cliente
        if (!$this->CreateCustomer()) {
            return [
                'success' => false,
                'error' => $this->error ?? 'Error al crear cliente'
            ];
        }

        /*=============================================
        CREAR ORDEN
        =============================================*/

       
        $orderId = $this->CreateOrder();

        if (!$orderId) {

            return [
                'success' => false,
                'error' => $this->error ?? 'Error al crear orden'
            ];

        } else {
             return [
                'success' => true,
                'charge_id' => $orderId,
                'message' => 'Pago realizado con éxito ✅'
            ];
        }


        
    }//pay

    /*=============================================
    CREAR ORDEN
    =============================================*/

    /*crear la orden del pago*/

    /* public function CreateOrder(){
        try{
          $this->order = \Conekta\Order::create(
            array(
              "amount"=>$this->total,
              "line_items" => array(
                array(
                  "name" => $this->description,
                  "unit_price" => $this->total*100, //se multiplica por 100 conekta
                  "quantity" => 1
                )//first line_item
              ), //line_items
              "currency" => "MXN",
              "customer_info" => array(
                "customer_id" => $this->customer->id
              ), //customer_info
              "charges" => array(
                  array(
                      "payment_method" => array(
                              "type" => "default", //, SOLO MESES
						      "monthly_installments" => $this->monthly
                      )
                  ) //first charge
              ) //charges
            )//order
          );

            // ✅ Retorna el ID del cargo (primer charge)
            return $this->order->charges[0]->id;

        } catch (\Conekta\ProcessingError $error){
          $this->error=$error->getMessage();
          return false;
        } catch (\Conekta\ParameterValidationError $error){
          $this->error=$error->getMessage();
          return false;
        } catch (\Conekta\Handler $error){
          $this->error=$error->getMessage();
          return false;
        }

        // return true;

    }// CreateOrder */

    public function CreateOrder() {
        try {
            // Construir el arreglo del método de pago condicionalmente
            $paymentMethod = [
                "type" => "default"
            ];
    
            if ($this->monthly > 0) {
                $paymentMethod["monthly_installments"] = $this->monthly;
            }
    
            $this->order = \Conekta\Order::create([
                "amount" => $this->total,
                "line_items" => [
                    [
                        "name" => $this->description,
                        "unit_price" => $this->total * 100, // se multiplica por 100
                        "quantity" => 1
                    ]
                ],
                "currency" => "MXN",
                "customer_info" => [
                    "customer_id" => $this->customer->id
                ],
                "charges" => [
                    [
                        "payment_method" => $paymentMethod
                    ]
                ]
            ]);
    
            // ✅ Retorna el ID del cargo (primer charge)
            return $this->order->charges[0]->id;
    
        } catch (\Conekta\ProcessingError $error) {
            $this->error = $error->getMessage();
            return false;
        } catch (\Conekta\ParameterValidationError $error) {
            $this->error = $error->getMessage();
            return false;
        } catch (\Conekta\Handler $error) {
            $this->error = $error->getMessage();
            return false;
        }

    }// CreateOrder
  

    /*=============================================
    CREAR CLIENTE
    =============================================*/

    /*por defecto la pai nos pide que creaeemos un clinte*/

    public function CreateCustomer(){
        try {
          $this->customer = \Conekta\Customer::create(
            array(
              "name" => $this->name,
              "email" => $this->email,
              //"phone" => "+52181818181",
              "payment_sources" => array(
                array(
                    "type" => "card",
                    "token_id" => $this->token
                )
              )//payment_sources
            )//customer
          );

        } catch (\Conekta\ProccessingError $error){
          $this->error=$error->getMesage();
          return false;

        } catch (\Conekta\ParameterValidationError $error){
          $this->error=$error->getMessage();
          return false;

        } catch (\Conekta\Handler $error){
          $this->error=$error->getMessage();
          return false;
        }

        return true;

    }//CreateCustomer
      

}//class Payment