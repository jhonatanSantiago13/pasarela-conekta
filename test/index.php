<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>
    <title>Document</title>
</head>
<body>

<form id="card-form" style="display:none;">

		<input value="Taylor Swift" name="name" id="name"  type="text" data-conekta="card[name]">
		<input name="card" id="card" data-conekta="card[number]" type="text" maxlength="16" >
		<input  data-conekta="card[cvc]" type="text" maxlength="4" placeholder="CVC" required value="123">
		<input value="12>" data-conekta="card[exp_month]" type="text" maxlength="2" placeholder="Mes de expiración (MM)">
        <input value="25" data-conekta="card[exp_year]" type="text" maxlength="2" placeholder="Año de expiración (AA)">
        <input type="text" name="email" id="email" maxlength="200" value="jhonale44@hotmail.com">
        <input type="text" name="description" id="description" maxlength="100" value="Ticket No. 52254551212141414141">
        <input type="number" name="total" id="total" value="1500">
        <input type="number" name="monthly" id="monthly" value="3">
        <input type="hidden" id="conektaTokenId" name="conektaTokenId" >

        <button type="submit" id="submit-button">Pagar</button>

    </form>
    
</body>
</html>