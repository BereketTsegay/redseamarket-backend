<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Invoice</title>
</head>
<body>
    Hi, {{ $details['name'] }}
    confirmation mail for the payment of {{ $details['amount'] }} on {{ date('d-M-Y H:i A', strtotime($details['date'])) }} at Jamal al bahr. 
    Your payment id is {{ $details['id'] }}
</body>
</html>