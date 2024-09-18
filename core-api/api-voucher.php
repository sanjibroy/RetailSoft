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
        $itemData = $data['itemData'];
        $paymentData = $data['paymentData'];

        foreach ($itemData as $item) {

            /* $date = DateTime::createFromFormat('d-m-Y', $item['mfgdate']);
            if ($date) {
                $mfg  = $date->format('Y-m-d');
            } else {
                $date = DateTime::createFromFormat('Y-m-d', $item['mfgdate']);
                $mfg  = $date->format('Y-m-d');
            } */
            $date = DateTime::createFromFormat('d-m-Y', $item['mfgdate']);
            $mfg  = $date->format('Y-m-d');

            $date = DateTime::createFromFormat('d-m-Y', $item['expdate']);
            $exp  = $date->format('Y-m-d');

            if($paymentData['subvchtype']=="Receipt"){
                $queryInsert2 = "INSERT INTO tbl_transactionitemdesc (vchno, itemcode, uom, itembatch, itemmrp, mfgdate, expdate, itemqty, itemaltqty, itemcost, itemamount,subvoucherid) VALUES ('{$uid}','{$item['itemcode']}','{$item['uom']}','{$item['itembatch']}','{$item['itemmrp']}','{$mfg}','{$exp}','-{$item['itemqty']}','-{$item['itemaltqty']}','{$item['itemcost']}','{$item['itemamount']}','{$data['subvoucherid']}')";
            }elseif($paymentData['subvchtype']=="Payment"){
                $queryInsert2 = "INSERT INTO tbl_transactionitemdesc (vchno, itemcode, uom, itembatch, itemmrp, mfgdate, expdate, itemqty, itemaltqty, itemcost, itemamount,subvoucherid) VALUES ('{$uid}','{$item['itemcode']}','{$item['uom']}','{$item['itembatch']}','{$item['itemmrp']}','{$mfg}','{$exp}','{$item['itemqty']}','{$item['itemaltqty']}','{$item['itemcost']}','{$item['itemamount']}','{$data['subvoucherid']}')";
            }

            

            // Add the query to the array
            $queries[] = $queryInsert2;
        }

        //Ledgers
        $ledgerData = $data['ledgerData'];

        foreach ($ledgerData as $item) {
            $queriesInsert3 = "INSERT INTO tbl_transactionledgerdesc (vchno, ledgerid, ledrate, ledamount) VALUES ('{$uid}','{$item['ledgerid']}','{$item['ledrate']}','{$item['ledamount']}')";

            // Add the query to the array
            $queries[] = $queriesInsert3;
        }


        //Payment

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
 
        //$uid = $data['vchno'];

        $queriesUpdate1 = "UPDATE tbl_transactionmaster SET vchdate='{$data['vchdate']}',subvoucherid='{$data['subvoucherid']}',partycode='{$data['partycode']}',amount='{$data['amount']}',narration='{$data['narration']}',modifiedby='{$data['modifiedby']}',modifiedon='{$data['modifiedon']}',supplierinvno='{$data['supplierinvno']}',supplierinvdate='{$data['supplierinvdate']}' WHERE vchno='{$data['vchno']}'";

        $itemData = $data['itemData'];
        $paymentData = $data['paymentData'];
        //$ledgerData = $data['ledgerData'];

        $queriesDelete2 = "DELETE FROM tbl_transactionitemdesc WHERE vchno = '{$data['vchno']}'";
        $queries[] = $queriesDelete2;

        $ledgers    =   $database->select("SELECT * FROM tbl_transactionledgerdesc WHERE vchno='{$data['vchno']}'"); 
        foreach ($ledgers as $row) { 
            if($row[4]>0){
                $queriesDelete3 = "DELETE FROM tbl_transactionledgerdesc WHERE vchno = '{$data['vchno']}'";
                $queries[] = $queriesDelete3;
            }
        } 

        
        foreach ($itemData as $item) {

            $date = DateTime::createFromFormat('d-m-Y', $item['mfgdate']);
            $mfg  = $date->format('Y-m-d');

            $date = DateTime::createFromFormat('d-m-Y', $item['expdate']);
            $exp  = $date->format('Y-m-d');

            if($paymentData['subvchtype']=="Receipt"){
                $queryInsert2 = "INSERT INTO tbl_transactionitemdesc (vchno, itemcode, uom, itembatch, itemmrp, mfgdate, expdate, itemqty, itemaltqty, itemcost, itemamount) VALUES ('{$data['vchno']}','{$item['itemcode']}','{$item['uom']}','{$item['itembatch']}','{$item['itemmrp']}','{$mfg}','{$exp}','-{$item['itemqty']}','-{$item['itemaltqty']}','{$item['itemcost']}','{$item['itemamount']}')";
            }elseif($paymentData['subvchtype']=="Payment"){
                $queryInsert2 = "INSERT INTO tbl_transactionitemdesc (vchno, itemcode, uom, itembatch, itemmrp, mfgdate, expdate, itemqty, itemaltqty, itemcost, itemamount) VALUES ('{$data['vchno']}','{$item['itemcode']}','{$item['uom']}','{$item['itembatch']}','{$item['itemmrp']}','{$mfg}','{$exp}','{$item['itemqty']}','{$item['itemaltqty']}','{$item['itemcost']}','{$item['itemamount']}')";
            }

            // Add the query to the array
            $queries[] = $queryInsert2;
        }

        //Ledgers
        $ledgerData = $data['ledgerData'];

        foreach ($ledgerData as $item) {

            if($item['ledamount']>0 || $item['ledamount']!=""){

                $queriesInsert3 = "INSERT INTO tbl_transactionledgerdesc (vchno, ledgerid, ledrate, ledamount) VALUES ('{$data['vchno']}','{$item['ledgerid']}','{$item['ledrate']}','{$item['ledamount']}')";

                // Add the query to the array
                $queries[] = $queriesInsert3;

            }
        }

        $paymentData = $data['paymentData'];

        /* if(count($paymentData)>0){
            $payId  =   "";

            //Payment Id 
            $payIdData    =   $database->select("SELECT prefix,startvalue,suffix FROM tbl_voucherno WHERE vchtype='{$paymentData['subvchtype']}'"); 
            foreach ($payIdData as $row) { 
                $payId  =   $row['prefix'] . $row['startvalue'] . $row['suffix']; 
            } 

            //payment master
            $queryPay   ="INSERT INTO `tbl_payrecmaster`(`vchno`, `vchdate`, `subvchtype`, `partycode`, `receivedamt`, `narration`, `currentstatus`, `createdby`, `createdon`, `txntype`, `issettled`) VALUES ('{$payId}','{$data['vchdate']}','{$paymentData['subvchtype']}','{$data['partycode']}','{$paymentData['receivedamt']}','','{$paymentData['currentstatus']}','{$data['modifiedby']}','{$data['modifiedon']}','{$paymentData['txntype']}','{$paymentData['issettled']}')";

            //mode of payment
            $queryMop   ="INSERT INTO `tbl_payrecmop`(`vchno`, `mopamt`, `mop`, `instno`, `instdate`, `instbank`, `txnno`, `clearingbank`, `bankcardno`) VALUES ('{$payId}','{$paymentData['receivedamt']}','{$paymentData['mop']}','{$paymentData['instno']}','{$paymentData['instdate']}','{$paymentData['instbank']}','{$paymentData['txnno']}','{$paymentData['clearingbank']}','')";

            //payment reference
            $queryRef   ="INSERT INTO `tbl_payrecrefnodt`(`vchno`, `refvchnos`, `refvchdates`, `revdamount`, `firsttxn`) VALUES ('{$payId}','{$data['vchno']}','{$data['vchdate']}','{$paymentData['receivedamt']}','{$paymentData['firsttxn']}')";

            // Add the query to the array
            $queries[] = $queryPay;
            $queries[] = $queryMop;
            $queries[] = $queryRef;

            $queryUpdatePayId = "UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='{$paymentData['subvchtype']}'";

            // Add the query to the array
            $queries[] = $queryUpdatePayId;
        } */

        //$queries = [$queriesUpdate1,queriesInsert2,queriesInsert1];

        //var_dump($queries);

        break;

    case 'delete-voucher':
        $queriesDelete1 = "DELETE FROM tbl_transactionmaster WHERE vchno = '{$data['vchno']}'";

        $queriesDelete2 = "DELETE FROM tbl_transactionitemdesc WHERE vchno = '{$data['vchno']}'";

        $queries = [$queriesDelete1,$queriesDelete2];

        $rows    =   $database->select("SELECT * FROM tbl_transactionledgerdesc WHERE vchno = '{$data['vchno']}'"); 
        if(count($rows)>0){
            $queriesDelete3 = "DELETE FROM tbl_transactionledgerdesc WHERE vchno = '{$data['vchno']}'";
            $queries[] = $queriesDelete3;
        }

        break;

    case 'delete-payrec':
        $queriesDelete1 = "DELETE FROM tbl_payrecmaster WHERE vchno = '{$data['vchno']}'";

        $queriesDelete2 = "DELETE FROM tbl_payrecmop WHERE vchno = '{$data['vchno']}'";

        $queries = [$queriesDelete1,$queriesDelete2];

        $rows    =   $database->select("SELECT * FROM tbl_payrecrefnodt WHERE vchno = '{$data['vchno']}'"); 
        if(count($rows)>0){
            $queriesDelete3 = "DELETE FROM tbl_payrecrefnodt WHERE vchno = '{$data['vchno']}'";
            $queries[] = $queriesDelete3;
        }

        $queriesDelete4 = "DELETE FROM tbl_transactionmaster WHERE vchno = '{$data['vchno']}'";
        $queries[] = $queriesDelete4;

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