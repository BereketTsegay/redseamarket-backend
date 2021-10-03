<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ad Enquiry</title>
</head>
<body>
    <h3>Customer detaisl</h3>
    <hr>
    <p>Name : {{ $enquiry['customer_name'] }}</p>
    <p>Email : {{ $enquiry['email'] }}</p>
    <p>Phone : {{ $enquiry['phone'] }}</p>
    <p>Message : {{ $enquiry['message'] }}</p>

</body>
</html>
