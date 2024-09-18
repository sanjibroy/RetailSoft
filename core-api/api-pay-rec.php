<?php
require_once 'core-php.php';

// Create an instance of CorePHP
$database = new CorePHP();
$database->connect();

// Get the JSON data from the request body
$jsonData = file_get_contents('php://input');

// Convert JSON data to an associative array
$data = json_decode($jsonData, true);

//var_dump($data);

// Check if the 'action' parameter exists
if (isset($data['action'])) {
    $action = $data['action'];

    // Start an array to hold the queries for transaction
    $queries = [];
    switch ($action) {
    case 'add-voucher':
        //Get and set values for primary key id  
        $idName = $data['vouchername'];  
        $uid = '';  

        //Get Transaction Id  
        $uidData    =   $database->select("SELECT prefix,startvalue,suffix FROM tbl_voucherno WHERE vchtype='$idName'"); 
        foreach ($uidData as $row) { 
            $uid  =   $row['prefix'] . $row['startvalue'] . $row['suffix']; 
        } 

        //Modify query as per the table  
        $queriesInsert1 = "INSERT INTO tbl_transactionmaster (vchno, vchdate, subvoucherid, partycode, amount, narration, currentstatus, createdby, createdon, supplierinvno, supplierinvdate) VALUES ('{$uid}','{$data['vchdate']}','{$data['subvoucherid']}','{$data['partycode']}','{$data['amount']}','{$data['narration']}','{$data['currentstatus']}','{$data['createdby']}','{$data['createdon']}','{$data['supplierinvno']}','{$data['supplierinvdate']}')";

        // Add the query to the array
        $queries[] = $queriesInsert1;

        //Items
        $paymentData = $data['paymentData'];


        //Payment/Receipt

        $paymentData = $data['paymentData'];

        if(count($paymentData)>0){
            $payId  =   "";

            //Payment Id 
            $payIdData    =   $database->select("SELECT prefix,startvalue,suffix FROM tbl_voucherno WHERE vchtype='{$paymentData['subvchtype']}'"); 
            foreach ($payIdData as $row) { 
                $payId  =   $row['prefix'] . $row['startvalue'] . $row['suffix']; 
            } 

            //payment master
            $queryPay   ="INSERT INTO `tbl_payrecmaster`(`vchno`, `vchdate`, `subvchtype`, `partycode`, `receivedamt`, `narration`, `currentstatus`, `createdby`, `createdon`, `txntype`, `issettled`) VALUES ('{$payId}','{$data['vchdate']}','{$paymentData['subvchtype']}','{$data['partycode']}','{$paymentData['receivedamt']}','','{$paymentData['currentstatus']}','{$data['createdby']}','{$data['createdon']}','{$paymentData['txntype']}','{$paymentData['issettled']}')";

            //mode of payment
            $queryMop   ="INSERT INTO `tbl_payrecmop`(`vchno`, `mopamt`, `mop`, `instno`, `instdate`, `instbank`, `txnno`, `clearingbank`, `bankcardno`) VALUES ('{$payId}','{$paymentData['receivedamt']}','{$paymentData['mop']}','{$paymentData['instno']}','{$paymentData['instdate']}','{$paymentData['instbank']}','{$paymentData['txnno']}','{$paymentData['clearingbank']}','')";

            //payment reference
            $queryRef   ="INSERT INTO `tbl_payrecrefnodt`(`vchno`, `refvchnos`, `refvchdates`, `revdamount`, `firsttxn`) VALUES ('{$payId}','{$uid}','{$data['vchdate']}','{$paymentData['receivedamt']}','{$paymentData['firsttxn']}')";

            // Add the query to the array
            $queries[] = $queryPay;
            $queries[] = $queryMop;
            $queries[] = $queryRef;

            $queryUpdatePayId = "UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='Payment'";

            // Add the query to the array
            $queries[] = $queryUpdatePayId;
        }

        

        $queryUpdateUid = "UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='$idName'";

        // Add the query to the array
        $queries[] = $queryUpdateUid;

        break;

    case 'update-voucher':

        $queriesUpdate1 = "UPDATE tbl_transactionmaster SET slno='{$data['slno']}',vchno='{$data['vchno']}',vchdate='{$data['vchdate']}',subvoucherid='{$data['subvoucherid']}',partycode='{$data['partycode']}',amount='{$data['amount']}',narration='{$data['narration']}',currentstatus='{$data['currentstatus']}',cancelled='{$data['cancelled']}',createdby='{$data['createdby']}',createdon='{$data['createdon']}',modifiedby='{$data['modifiedby']}',modifiedon='{$data['modifiedon']}',supplierinvno='{$data['supplierinvno']}',supplierinvdate='{$data['supplierinvdate']}',stype='{$data['stype']}',srnoifany='{$data['srnoifany']}' WHERE id = '{$data['id']}'";

        //Modify query as per the table  
        $queriesInsert2 = "INSERT INTO tbl_transactionitemdesc (slno, vchno, itemcode, uom, itembatch, itemmrp, mfgdate, expdate, itemqty, itemaltqty, itemcost, salerate, itemamount, stocktype, recstatus, txndate, category) VALUES ('{$data['slno']}','{$data['vchno']}','{$data['itemcode']}','{$data['uom']}','{$data['itembatch']}','{$data['itemmrp']}','{$data['mfgdate']}','{$data['expdate']}','{$data['itemqty']}','{$data['itemaltqty']}','{$data['itemcost']}','{$data['salerate']}','{$data['itemamount']}','{$data['stocktype']}','{$data['recstatus']}','{$data['txndate']}','{$data['category']}')";

        //Modify query as per the table
        $queriesInsert1 = "INSERT INTO tbl_transactionledgerdesc (slno, vchno, ledgerid, ledrate, ledamount) VALUES ('{$data['slno']}','{$data['vchno']}','{$data['ledgerid']}','{$data['ledrate']}','{$data['ledamount']}')";

        $queries = [$queriesUpdate1,queriesInsert2,queriesInsert1];

        break;

    case 'delete-voucher':
        $queriesDelete1 = "DELETE FROM tbl_transactionmaster WHERE id = '{$data['id']}'";

        $queriesDelete2 = "DELETE FROM tbl_transactionitemdesc WHERE id = '{$data['id']}'";

        $queriesDelete3 = "DELETE FROM tbl_transactionledgerdesc WHERE id = '{$data['id']}'";

        $queries = [$queriesDelete1,queriesDelete2,queriesDelete3];

        break;

    default:
        $response = array('status' => 'error', 'message' => 'Invalid action.');
        break;
}
try {
        if (!empty($queries)) {
            // Execute the transaction
            $success = $database->executeTransaction($queries);

            if ($success) {
                $response = array('status' => 'success', 'message' => 'Transaction executed successfully.');
            } else {
                $response = array('status' => 'error', 'message' => 'Error executing transaction.');
            }
        } else {
            $response = array('status' => 'error', 'message' => 'No valid operations found for the action.');
        }
    } catch (Exception $e) {
        $response = array('status' => 'error', 'message' => $e->getMessage());
    }
} else {
    $response = array('status' => 'error', 'message' => 'Action parameter is missing.');
}

// Convert the response to JSON format
$jsonResponse = json_encode($response);

// Set the content type header
header('Content-Type: application/json');

// Return the JSON response
echo $jsonResponse;

$database->close();?>