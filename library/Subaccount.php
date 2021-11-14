<?php
namespace Flutterwave;

//uncomment if you need this
//define("BASEPATH", 1);//Allow direct access to rave.php and raveEventHandler.php

require_once('rave.php');
require_once('raveEventHandlerInterface.php');
require_once('EventTracker.php');

class subaccountEventHandler implements EventHandlerInterface
{
    use EventTracker;

    /**
     * This is called only when a transaction is successful
     * */
    function onSuccessful($transactionData)
    {
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
        self::sendAnalytics("Initiate-Subaccount");
    }

    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData)
    {
        self::sendAnalytics("Initiate-Subaccount");
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here

    }

    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    function onRequery($transactionReference)
    {
        // Do something, anything!
    }

    /**
     * This is called a transaction requery returns with an error
     * */
    function onRequeryError($requeryResponse)
    {
        // Do something, anything!
    }

    /**
     * This is called when a transaction is canceled by the user
     * */
    function onCancel($transactionReference)
    {
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution

    }

    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    function onTimeout($transactionReference, $data)
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects

    }
}

class Subaccount
{
    protected $subaccount;

    function __construct()
    {
        $this->subaccount = new Rave($_ENV['SECRET_KEY']);
    }

    function createSubaccount($array)
    {
        //set the payment handler
        $this->subaccount->eventHandler(new subaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts");
        //returns the value from the results
        subaccountEventHandler::startRecording();
        $response = $this->subaccount->createSubaccount($array);
        subaccountEventHandler::sendAnalytics('Create-Subaccount');

        return $response;
    }

    function getSubaccounts()
    {

        $this->subaccount->eventHandler(new subaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts");
        //returns the value from the results
        subaccountEventHandler::startRecording();
        $response = $this->subaccount->getSubaccounts();
        subaccountEventHandler::sendAnalytics('Get-Subaccounts');

        return $response;
    }

    function fetchSubaccount($array)
    {

        $this->subaccount->eventHandler(new subaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results
        subaccountEventHandler::startRecording();
        $response = $this->subaccount->fetchSubaccount();
        subaccountEventHandler::sendAnalytics('Fetch-Subaccount');

        return $response;

    }

    function updateSubaccount($array)
    {

        if (!isset($array['id'])) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
                Missing <b> id </b> Parameter in the payload
              </div>';
        }

        $this->subaccount->eventHandler(new subaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results
        subaccountEventHandler::startRecording();
        $response = $this->subaccount->updateSubaccount($array);
        subaccountEventHandler::sendAnalytics('Update-Subaccount');

        return $response;

    }

    function deleteSubaccount($array)
    {
        $this->subaccount->eventHandler(new subaccountEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/subaccounts/" . $array['id']);
        //returns the value from the results

        subaccountEventHandler::startRecording();
        $response = $this->subaccount->deleteSubaccount();
        subaccountEventHandler::sendAnalytics('Delete-Subaccount');

        return $response;
    }
}


?>
