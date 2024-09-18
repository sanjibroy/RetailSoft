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
    $query = '';
    switch ($action) {
    case 'get-suppliers':  

        //Modify query as per id table  
        $response    =   $database->select("SELECT partycode as value,partyname as label,transactiontype FROM tbl_partymaster WHERE partytype='Supplier'"); 
       
        break;

    case 'get-party':  

        //Modify query as per id table  
        $response    =   $database->select("SELECT partycode as value,partyname as label,transactiontype FROM tbl_partymaster WHERE partytype='Customer'"); 
        
        break;
    
    case 'all-party':  

        //Modify query as per id table  
        $response    =   $database->select("SELECT partycode as value,partyname as label FROM tbl_partymaster"); 
        
        break;

    case 'get-items':

        $response    =   $database->select("SELECT itemcode as value,itemname as label,uom,altuom,baseconv FROM tbl_productmaster");

        break;

    case 'get-ledgers':

        $voucher    =   $data['voucher'];

        $response    =   $database->select("SELECT a.ledgerid as id,l.displayledgername as name,a.caltype,a.rate FROM tbl_voucher_ledger_assoc a INNER JOIN tbl_ledgermaster l ON a.ledgerid=l.slno WHERE a.subvoucherid='$voucher'");

        break;

    case 'get-sale-batches':

        $itemid    =   $data['itemid'];

        $response    =   $database->select("SELECT itemcode,itembatch,SUM(itemqty) as qty FROM `tbl_transactionitemdesc` WHERE itemcode='$itemid' GROUP BY itembatch HAVING qty>0 ORDER BY expdate;");

        break;

    case 'get-batch-details':
        $itemid    =   $data['item'];
        $batchid    =   $data['batch'];

        $response    =   $database->select("SELECT uom,itemmrp,mfgdate,expdate,itemqty FROM `tbl_transactionitemdesc` WHERE itemcode='$itemid' AND itembatch='$batchid';");

        break;

    case 'get-sale-items':

        $response    =   $database->select("SELECT d.itemcode, p.itemname, d.itembatch, d.uom, d.mfgdate, d.expdate, CASE WHEN pl.rate IS NOT NULL THEN pl.rate ELSE d.itemmrp END AS itemmrp, p.uom AS puom, p.altuom, p.baseconv FROM `tbl_transactionitemdesc` d INNER JOIN tbl_productmaster p ON d.itemcode = p.itemcode INNER JOIN tbl_vouchertype v ON d.subvoucherid = v.slno LEFT JOIN tbl_pricelist pl ON d.itemcode = pl.itmid AND pl.plfromdate = pl.pltodate WHERE v.primaryvchtype = 'Purchase' ORDER BY d.expdate");

        break;

    case 'get-party-balance':
        $partycode    =   $data['partycode'];
        $response    =   $database->select("SELECT SUM(amount) as balance FROM tbl_transactionmaster WHERE partycode='$partycode'");

        break;

    default:
        $response = array('status' => 'error', 'message' => 'Invalid action.');
        break;
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