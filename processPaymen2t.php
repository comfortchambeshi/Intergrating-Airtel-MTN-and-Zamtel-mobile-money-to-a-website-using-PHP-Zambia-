<?php

session_start() ;
// session_destroy();
// Prevent direct access to this class



define("BASEPATH", 1);

//including main files
include '../../functions/header.php';
include '../../inc/staff_login.php';
include '../../inc/dbconnect.inc.php';
include '../../inc/login_function.php';
include '../../inc/mains.php';
include($_SERVER['DOCUMENT_ROOT'].'/classes/mail.php');
//include flutterwave files

include('library/rave.php');
include('library/raveEventHandlerInterface.php');



use Flutterwave\Rave;
use Flutterwave\EventHandlerInterface;
//Payments
if(isset($_GET['product']) && isset($_GET['type']) && isset($_GET['method']) && isset($_GET['pid'])) {
    $product_url = $_GET['product'];

if ($_GET['type'] == 'music') {
   $product = $main->all_query_nolimit('uploads', '', 'id', $product_url);
   $product_cost = 'upload_price';
   $product_name = 'upload_title';
   $product_owner = 'uploaded_by';
   $descr = 'Digital audio download';
   $upload_image = 'upload_image';
   $image_directory = '';
}elseif ($_GET['type'] == 'upgrade') {
   $product = $main->all_query_nolimit('fees', '', 'id', $product_url);
   $product_cost = 'amount';
   $product_name = 'name';
   $product_owner = 'user_id';
}elseif ($_GET['type'] == 'album') {
   $product = $main->all_query_nolimit('albums', '', 'id', $product_url);
   $product_cost = 'price';
   $product_name = 'name';
   $product_owner = 'user_id';
   $descr = 'Digital album download';
   $upload_image = 'album_cover';
   $image_directory = 'albums/';
}
foreach ($product[0] as $row_p) {


$URL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$getData = $_GET;
$postData = $_POST;
$publicKey = $_SERVER['PUBLIC_KEY'];
$secretKey = $_SERVER['SECRET_KEY'];
    $success_url = '';
    $failure_url = '';

$env = $_SERVER['ENV'];

if(isset($postData['amount'])){
    $_SESSION['publicKey'] = $publicKey;
    $_SESSION['secretKey'] = $secretKey;
    $_SESSION['env'] = $env;
    $_SESSION['successurl'] = $success_url;
    $_SESSION['failureurl'] = $failure_url;
    $_SESSION['currency'] = $postData['currency'];
    $_SESSION['amount'] = $postData['amount'];
}

$prefix = 'RV'; // Change this to the name of your business or app
$overrideRef = false;

// Uncomment here to enforce the useage of your own ref else a ref will be generated for you automatically
if(isset($postData['ref'])){
    $prefix = $postData['ref'];
    $overrideRef = true;
}

$payment = new Rave($_SESSION['secretKey'], $prefix, $overrideRef);

function getURL($url,$data = array()){
    $urlArr = explode('?',$url);
    $params = array_merge($_GET, $data);
    $new_query_string = http_build_query($params).'&'.$urlArr[1];
    $newUrl = $urlArr[0].'?'.$new_query_string;
    return $newUrl;
};


// This is where you set how you want to handle the transaction at different stages
class myEventHandler implements EventHandlerInterface{
    /**
     * This is called when the Rave class is initialized
     * */
    function onInit($initializationData){
        // Save the transaction to your DB.
    }
    
    /**
     * This is called only when a transaction is successful
     * */
    function onSuccessful($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here
        if($transactionData->status === 'successful'){
          if($transactionData->currency == $_SESSION['currency'] && $transactionData->amount == $_SESSION['amount']){
              
              if($_SESSION['publicKey']){
                        //inserting an order after card payemnt
            $insertPayment = $accounts->insert_order($product_url, $row_p[$product_cost], 'ZMW', 'approved', $user_id, $_GET['method'], 'system', $_GET['type'], $row_p[$product_owner]);


            //send an email
            $mail_template = $email_templates->email_invoice($user_name, 'approved', $insertPayment[0], $row_p[$product_name], 1, $row_p[$product_cost], $site_name);
            Mail::sendMail('Payment successfull', $mail_template, $user_email);
            header("LOCATION: /studio/order.php?id=".$insertPayment[1]."", array('event' => 'successful'));
                    $_SESSION = array();
                    session_destroy();
                }
          }else{
              if($_SESSION['publicKey']){
                    header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'suspicious')));
                    $_SESSION = array();
                    session_destroy();
                }
          }
      }else{
          $this->onFailure($transactionData);
      }
    }
    
    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData){
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
        if($_SESSION['publicKey']){
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'failed')));
            $_SESSION = array();
            session_destroy();
        }
    }
    
    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference){
        // Do something, anything!
    }
    
    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse){
        echo 'the transaction was not found';
    }
    
    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference){
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
        if($_SESSION['publicKey']){
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'canceled')));
            $_SESSION = array();
            session_destroy();
        }
    }
    
    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data){
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
        if($_SESSION['publicKey']){
            header('Location: '.getURL($_SESSION['failureurl'], array('event' => 'timedout')));
            $_SESSION = array();
            session_destroy();
        }
    }
}

if(isset($postData['amount'])){
    // Make payment
    $payment
    ->eventHandler(new myEventHandler)
    ->setAmount(5)
    ->setPaymentOptions('account') // value can be card, account or both
    ->setDescription($row_p[$product_name])
    ->setLogo($site_url.'/uploads/file_images/'.$image_directory.$row_p[$upload_image])
    ->setTitle($row_p[$product_name])
    ->setCountry('Zambia')
    ->setCurrency('ZMW')
    ->setEmail($user_email)
    ->setFirstname($postData['firstname'])
    ->setLastname($postData['lastname'])
    ->setPhoneNumber($postData['phonenumber'])
    ->setPayButtonText($postData['pay_button_text'])
    ->setRedirectUrl($URL)
    // ->setMetaData(array('metaname' => 'SomeDataName', 'metavalue' => 'SomeValue')) // can be called multiple times. Uncomment this to add meta datas
    // ->setMetaData(array('metaname' => 'SomeOtherDataName', 'metavalue' => 'SomeOtherValue')) // can be called multiple times. Uncomment this to add meta datas
    ->initialize();
}else{
    if(isset($getData['cancelled'])){
        // Handle canceled payments
        $payment
        ->eventHandler(new myEventHandler)
        ->paymentCanceled($getData['cancelled']);
    }elseif(isset($getData['tx_ref'])){
        // Handle completed payments
        $payment->logger->notice('Payment completed. Now requerying payment.');
        $payment
        ->eventHandler(new myEventHandler)
        ->requeryTransaction($getData['transaction_id']);
    }else{
        $payment->logger->warn('Stop!!! Please pass the txref parameter!');
        echo 'Stop!!! Please pass the txref parameter!';
    }
}
}
}

?>
