<?php
include('library/CardPayment.php');
use Flutterwave\Card;
//The data variable holds the payload
$data = array(
    "card_number"=> "5531886652142950",
    "cvv"=> "564",
    "expiry_month"=> "09",
    "expiry_year"=> "22",
    "currency"=> "NGN",
    "amount" => "1000",
    "fullname"=> "Ekene Eze",
    "email"=> "ekene@flw.com",
    "phone_number"=> "0902620185",
    "fullname" => "temi desola",
    //"tx_ref"=> "MC-3243e",// should be unique for every transaction
    "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
           
    );


$payment = new Card();
$res = $payment->cardCharge($data);//This call is to figure out the authmodel
$data['authorization']['mode'] = $res['meta']['authorization']['mode'];

if($res['meta']['authorization']['mode'] == 'pin'){

    //Supply authorization pin here
    $data['authorization']['pin'] = '3310';
}

if($res['meta']['authorization']['mode'] == 'avs_noauth'){
    //supply avs details here
    $data["authorization"] = array(
            "mode" => "avs_noauth",
            "city"=> "Sampleville",
            "address"=> "3310 sample street ",
            "state"=> "Simplicity",
            "country"=> "Simple",
            "zipcode"=> "000000",
        );
}

$result = $payment->cardCharge($data);//charge with new fields


if($result['data']['auth_mode'] == 'otp'){
    $id = $result['data']['id'];
    $flw_ref = $result['data']['flw_ref'];

   echo '<p>Please enter an OTP pin sent to your phone</p><form method="POST" action="">
   <input type="text" name="otp"/>
   <button name="submit" type="submit">submit</button>
   </form>';
   if (isset($_POST['submit'])) {
   	$otp = $_POST['otp'];
   	$validate = $payment->validateTransaction($otp,$flw_ref);// you can print_r($validate) to see the response
    $verify = $payment->verifyTransaction($id);
    if ($verify['status'] == 'success') {
    	echo "Success!!!!";
    }



   
   }
    

    

}


