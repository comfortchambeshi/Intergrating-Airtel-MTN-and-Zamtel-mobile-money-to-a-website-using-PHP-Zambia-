<?php

namespace Flutterwave;

require_once('rave.php');
require_once('raveEventHandlerInterface.php');
require_once('EventTracker.php');

class billEventHandler implements EventHandlerInterface
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
        if ($transactionData["data"]["chargecode"] === '00' || $transactionData["data"]["chargecode"] === '0') {
            self::sendAnalytics("Bills");
            echo "Transaction Completed";
        } else {
            $this->onFailure($transactionData);
        }
    }

    /**
     * This is called only when a transaction failed
     * */
    function onFailure($transactionData)
    {
        self::sendAnalytics("Bills-error");
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

class Bill
{
    protected $payment;

    function __construct()
    {
        $this->payment = new Rave($_ENV['SECRET_KEY']);
        $this->type = array('AIRTIME', 'DSTV', 'DSTV BOX OFFICE', 'Postpaid', 'Prepaid', 'AIRTEL', 'IKEDC TOP UP', 'EKEDC POSTPAID TOPUP', 'EKEDC PREPAID TOPUP', 'LCC', 'KADUNA TOP UP');
    }

    function payBill($array)
    {
        if (gettype($array['amount']) !== 'integer') {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Specified Amount should be an integer and not a string.
          </div>';
        }

        if (!in_array($array['type'], $this->type, true)) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            The Type specified in the payload  is not <b> "' . $this->type[0] . ' , ' . $this->type[1] . ' , ' . $this->type[2] . ' or ' . $this->type[3] . '"</b>
          </div>';
        }
        switch ($array['type']) {
            case 'DSTV':
                //set type to dstv

                $this->type = 'DSTV';

                break;

            case 'EKEDC POSTPAID TOPUP':
                //set type to ekedc

                $this->type = 'EKEDC POSTPAID TOPUP';

                break;
            case 'LCC':
                //set type to lcc

                $this->type = 'LCC';

                break;
            case 'AIRTEL':
                //set type to airtel

                $this->type = 'AIRTEL';

                break;
            case 'Postpaid':
                //set type to postpaid

                $this->type = 'Postpaid';

                break;
            case 'IKEDC TOP UP':
                //set type to ikedc

                $this->type = 'IKEDC TOP UP';

                break;
            case 'KADUNA TOP UP':
                //set type to kaduna top up

                $this->type = 'KADUNA TOP UP';

                break;

            case 'DSTV BOX OFFICE':
                //set type to dstv box office
                $this->type = 'DSTV BOX OFFICE';

                break;

            default:
                //set type to airtime
                $this->type = 'AIRTIME';

                break;
        }

        $this->payment->eventHandler(new billEventHandler)
            //set the endpoint for the api call
            ->setEndPoint("v3/bills");

        billEventHandler::startRecording();
        $response = $this->payment->bill($array);
        billEventHandler::sendAnalytics("Pay-Bills");

        return $response;
    }

    function bulkBill($array)
    {
        if (!array_key_exists('bulk_reference', $array) || !array_key_exists('callback_url', $array) || !array_key_exists('bulk_data', $array)) {
            return '<div class="alert alert-danger" role="alert"> <b>Error:</b> 
            Please Enter the required body parameters for the request.
          </div>';
        }

        $this->payment->eventHandler(new billEventHandler)
            ->setEndPoint('v3/bulk-bills');

        billEventHandler::startRecording();
        $response = $this->payment->bulkBills($array);
        billEventHandler::sendAnalytics("Pay-Bulk-Bills");

        return $response;
    }

    function getBill($array)
    {

        $this->payment->eventHandler(new billEventHandler);

        if (array_key_exists('reference', $array) && !array_key_exists('from', $array)) {
            echo "Im here";
            $this->payment->setEndPoint('v3/bills/' . $array['reference']);
        } else if (array_key_exists('code', $array) && !array_key_exists('customer', $array)) {
            $this->payment->setEndPoint('v3/bill-items');
        } else if (array_key_exists('id', $array) && array_key_exists('product_id', $array)) {
            $this->payment->setEndPoint('v3/billers');
        } else if (array_key_exists('from', $array) && array_key_exists('to', $array)) {
            if (isset($array['page']) && isset($array['reference'])) {
                $this->payment->setEndPoint('v3/bills');
            } else {
                $this->payment->setEndPoint('v3/bills');
            }
        }

        billEventHandler::startRecording();
        $response = $this->payment->getBill($array);
        billEventHandler::sendAnalytics("Get-Bills");

        return $response;
    }

    function getBillCategories()
    {


        $this->payment->eventHandler(new billEventHandler)
            ->setEndPoint('v3');

        billEventHandler::startRecording();
        $response = $this->payment->getBillCategories();
        billEventHandler::sendAnalytics("Get-Bill-Categories");

        return $response;
    }

    function getAgencies()
    {
        $this->payment->eventHandler(new billEventHandler)
            ->setEndPoint('v3');

        billEventHandler::startRecording();
        $response = $this->payment->getBillers();
        billEventHandler::sendAnalytics("Get-Billing-Agencies");

        return $response;
    }
}








