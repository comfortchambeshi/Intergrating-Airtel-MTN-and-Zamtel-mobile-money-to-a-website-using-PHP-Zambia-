<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment for groundnuts </title>
    <style>
        #btn-of-destiny {
            margin-top: 2em;
        }

        #explain1 {
            padding: 10px;
            margin: 2em;
        }

    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body>


<form method="POST" action="processPayment.php" id="paymentForm">
    <input  name="amount" value="200"/> <!-- Replace the value with your transaction amount -->
    <input  name="payment_options" value=""/>
    <!-- Can be card, account, ussd, qr, mpesa, mobilemoneyzambia  (optional) -->
    <input  name="description" value="Groundnuts"/>
    <!-- Replace the value with your transaction description -->
    <input  name="logo" value="http://brandmark.io/logo-rank/random/apple.png"/>
    <!-- Replace the value with your logo url (optional) -->
    <input  name="title" value="Witlevels Store"/>
    <!-- Replace the value with your transaction title (optional) -->
    <input  name="country" value="ZM"/> <!-- Replace the value with your transaction country -->
    <input  name="currency" value="ZMW"/> <!-- Replace the value with your transaction currency -->
    <input  name="email" value="comfortbatcall@witlevels.com"/> <!-- Replace the value with your customer email -->
    <input  name="firstname" value="Comfort"/>
    <!-- Replace the value with your customer firstname (optional) -->
    <input  name="lastname" value="Chambeshi"/>
    <!-- Replace the value with your customer lastname (optional) -->
    <input  name="phonenumber" value="0968793843"/>
    <!-- Replace the value with your customer phonenumber (optional if email is passes) -->
    <input  name="pay_button_text" value="Complete Payment"/>
    <!-- Replace the value with the payment button text you prefer (optional) -->
    <input  name="ref" value="MY_NAME_5a22a7f270abc8879"/>
    <!-- Replace the value with your transaction reference. It must be unique per transaction. You can delete this line if you want one to be generated for you. -->
    <input type="hidden" name="successurl" value="success.php">
    <!-- Put your success url here -->
    <input type="hidden" name="failureurl" value="fail.php">
    <!-- Put your failure url here -->
    <center><input id="btn-of-destiny" class="btn btn-warning" type="submit" value="Pay Now"/></center>
</form>


<!--you can delete this if you no longer need the guide--->



<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
</body>
</html>
