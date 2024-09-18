<?php
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

if($_POST["action"]  ==  "party"){

    $data           =   getPartyId($dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "party-details"){

    $jsonData       =   json_decode($_POST['jsonData']);

    $partyId        =   $jsonData->partyId;
    $data           =   getPartyDetailById($partyId,$dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "add-party"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       =   json_decode($_POST['jsonData']);
    $partyName      =   $jsonData->partyName;
    $partyAdd       =   $jsonData->partyAdd;
    $partyCity      =   $jsonData->partyCity;
    $partyState     =   $jsonData->partyState;
    $partyStateCode =   $jsonData->partyStateCode;
    $partyPinCode   =   $jsonData->partyPinCode;
    $partyLandLineNo=   $jsonData->partyLandLineNo;
    $partyMobileNo  =   $jsonData->partyMobileNo;
    $partyEmailId   =   $jsonData->partyEmailId;
    $partyPan       =   $jsonData->partyPan;
    $partyTin       =   $jsonData->partyTin;
    $gstEnable      =   $jsonData->gstEnable;
    $gstNo          =   $jsonData->gstNo;
    $partyType      =   $jsonData->partyType;
    $partyOpDate    =   $jsonData->opDate;
    $partyOpBalance =   $jsonData->opBalance;

    //Get Party Id
    $idName         =   "party";
    $partyId        =   getVoucherNo($idName,$dbh);

    //Get Financial Year
    $yearData       =   getCurrentFinancialYear($dbh);
    $yearFrom       =   $yearData[0][0];
    $yearTo         =   $yearData[0][1];

    //Insert New Party
    $values         =   array($partyId,$partyName,$partyAdd,$partyCity,$partyState,$partyStateCode,$partyPinCode,$partyLandLineNo,$partyMobileNo,$partyEmailId,$partyPan,$partyTin,$gstEnable,$gstNo,$partyType);
    $data           =   addNewParty($values,$dbh);    
    
    //Insert Party Opening Balance
    $values2        =   array($partyId,$partyOpDate,$partyOpBalance,$yearFrom,$yearTo);
    $data2          =   addPartyOpeningBalance($values2,$dbh);

    //Update Party Id
    $data3          =   updateVoucherNo($idName,$dbh);


    if($data!=1)
    {
        $error=1;
    }

    if($data2!=1)
    {
        $error=1;
    }

    if($data3!=1)
    {
        $error=1;
    }

    if ($error==1)
    {
        $result="Failed";
        mysqli_rollback($dbh);
    }
    else
    {
        $result="Success";
        mysqli_commit($dbh);
    }

    $msg            =   json_encode(array("result" => $result,"partyId"=>$partyId));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-party-profile"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       =   json_decode($_POST['jsonData']);
    $partyId        =   $jsonData->partyId;
    $partyName      =   $jsonData->partyName;
    $partyAdd       =   $jsonData->partyAdd;
    $partyCity      =   $jsonData->partyCity;
    $partyState     =   $jsonData->partyState;
    $partyStateCode =   $jsonData->partyStateCode;
    $partyPinCode   =   $jsonData->partyPinCode;
    $partyLandLineNo=   $jsonData->partyLandLineNo;
    $partyMobileNo  =   $jsonData->partyMobileNo;
    $partyEmailId   =   $jsonData->partyEmailId;
    $partyPan       =   $jsonData->partyPan;
    $partyTin       =   $jsonData->partyTin;
    $gstEnable      =   $jsonData->gstEnable;
    $gstNo          =   $jsonData->gstNo;
    $partyType      =   $jsonData->partyType;
    //$partyOpDate    =   $jsonData->partyOpDate;
    //$partyOpBalance =   $jsonData->partyOpBalance;

    $data           =   updatePartyProfile($partyId,$partyName,$partyAdd,$partyCity,$partyState,$partyStateCode,$partyPinCode,$partyLandLineNo,$partyMobileNo,$partyEmailId,$partyPan,$partyTin,$gstEnable,$gstNo,$partyType,$dbh);
    
    if($data!=1)
    {
        $error=1;
    }

    if ($error==1)
    {
        $rows   =   mysqli_affected_rows($dbh);
        $result =   "Failed";
        mysqli_rollback($dbh);
    }
    else
    {
        $rows   =   mysqli_affected_rows($dbh);
        $result =   "Success";
        mysqli_commit($dbh);
        
    }

    $msg            =   json_encode(array("result" => $result,"partyId"=>$partyId,"rows"=>$rows));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-party-op-balance"){

    /* $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       =   json_decode($_POST['jsonData']);
    $partyId        =   $jsonData->partyId;
    $obAmount       =   $jsonData->obAmount;
    $obDate         =   $jsonData->obDate;

    //Get Opening Balance Id
    $idName         =   "opbalparty";
    $opId           =   getVoucherNo($idName,$dbh);
 */
    
    echo $msg;
}
elseif($_POST["action"]  ==  "delete-party"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $partyId        = $jsonData->partyId;

    //Delete party
    $data           =   deleteParty($partyId,$dbh);
    if($data!=1)
    {
        $error=1;
    }

    if ($error==1)
    {
        $rows       =   mysqli_affected_rows($dbh);
        $result     =   "Failed";
        mysqli_rollback($dbh);
    }
    else
    {
        $rows       =   mysqli_affected_rows($dbh);
        $result     =   "Success";
        mysqli_commit($dbh);
    }

    $msg            =   json_encode(array("result" => $result,"partyId"=>$partyId,"rows"=>$rows));

    echo $msg;
}
else
{
    echo "Invalid Request";
}

?>