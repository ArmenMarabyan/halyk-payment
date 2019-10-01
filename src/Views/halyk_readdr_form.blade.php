<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Merchant post template to ECOMM</title>
</head>

{{--<body>--}}
<body>
    <form id='returnform' action='https://ecommerce.ufc.ge/ecomm2/ClientHandler' method='{{$formMethod}}'>
        <input type='hidden' name='trans_id' value='{{$transactionId}}'>
        <input type='submit' name='submit' value='{{$submitText}}'>
    </form>
</body>
</html>
