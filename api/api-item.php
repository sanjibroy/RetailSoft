<?php
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

if($_POST["action"]  ==  "categories"){
    $data           =   getCategory($dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));
    echo $msg;
}
elseif($_POST["action"]  ==  "items"){

    $data           =   getItems($dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "add-item"){

    $error=0;
    $errorMsg="";
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $opJsonData       = json_decode($_POST['opJsonData']);

    $itemName = $jsonData->itemName;
    $itemShortName = $jsonData->itemShortName;
    $brandName = $jsonData->brandName;
    $description = $jsonData->description;
    $category = $jsonData->category;
    $salePrice = $jsonData->salePrice;
    $uom = $jsonData->uom;
    $altUom = $jsonData->altUom;
    $conversion = $jsonData->conversion;
    $qty = $jsonData->qty;
    $altQty = $jsonData->altQty;
    $rate = $jsonData->rate;
    $amount = $jsonData->amount;
    //$batchNo = $jsonData->batchNo;
    //$mfg = $jsonData->mfg;
    //$exp = $jsonData->exp;
    $party = $jsonData->party;
    $date = $jsonData->date;
    $vchType = $jsonData->vchType;
    $vchNo = $jsonData->vchNo;

    

    

    $today=date('Y-m-d');

    $vchType = "";
    $altConv    =   1;


    //Get Item Id
    $idName         =   "item";
    $itemId         =   getVoucherNo($idName,$dbh);
        
    //Insert item
    $values         =   array($itemId,$itemName,$itemShortName,$brandName,$category,$uom,$altUom,$altConv,$conversion,$description);
    $data           =   addNewItem($values,$dbh);
    if($data!=1)
    {
        $error=1;
        $errorMsg .="Error in item";
    }

     //Get Op Id
     $opIdName         =   "opbalitem";
     $opId             =   getVoucherNo($opIdName,$dbh);

     //Get sub voucher id
     $query_get_subvch  =   "SELECT slno FROM tbl_vouchertype WHERE subvchtype='$opIdName'";
     $subVchData        =   GetData($query_get_subvch,$dbh);
     $subVchId          =   $subVchData[0][0];

    //Insert OP Balance
    if($amount>0){


        $opQueryTrans        =   "INSERT INTO tbl_transactionmaster(vchno,vchdate,subvoucherid,amount,createdon,supplierinvno,supplierinvdate) VALUES('$opId','$date','$subVchId','$amount','$today','$vchNo','$date')";
        //echo $opQueryTrans;
        $data               =   mysqli_query($dbh,$opQueryTrans);
        if($data!=1)
        {
            $error=1;
            $errorMsg .="Error in transaction master";
        }

        for($i=0;$i<count($opJsonData);$i++)
        {
            $arr = $opJsonData[$i];

            $batchNo       = $arr->batchNo;
            $mfg       = $arr->mfgDate;
            $exp       = $arr->expDate;
            $qty           = $arr->qty;
            $altQty        = $arr->altQty;
            $particulars   = $arr->particulars;
            $voucherDate   = $arr->voucherDate;
            $voucherNo     = $arr->voucherNo;
            $opAmount        = $arr->amount;

            $mfgDate = DateTime::createFromFormat("Y-m", $mfg);
            $mfgDate = $mfgDate->format("Y-m-d");
            $expDate = DateTime::createFromFormat("Y-m", $exp);
            $expDate = $expDate->format("Y-m-d");

            $opRate = $amount/$qty;

    
            //item description
            $opQueryDesc        =   "INSERT INTO tbl_transactionitemdesc(vchno,itemcode,uom,itembatch,mfgdate,expdate,itemqty,itemaltqty,salerate,itemamount,txndate,category) VALUES('$opId','$itemId','$uom','$batchNo','$mfgDate',' $expDate','$qty','$altQty','$opRate','$opAmount','$today','$category')";
            //echo $opQueryDesc;
            $data               =   mysqli_query($dbh,$opQueryDesc);
            if($data!=1)
            {
                $error=1;
                $errorMsg .="Error in transaction items";
            }


            //Update Op Id
            $data3          =   updateVoucherNo($opIdName,$dbh);
            if($data3!=1)
            {
                $error=1;
                $errorMsg .="Error in voucher op update";
            }
        }


        

    }


    //Update Item Id
    $data3          =   updateVoucherNo($idName,$dbh);
    if($data3!=1)
    {
        $error=1;
        $errorMsg .="Error in voucher item update";
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

    $msg            =   json_encode(array("result" => $result,"itemId"=>$itemId, "error"=>$errorMsg));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-item"){

    $errorMsg="";
    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;
    $itemName       = $jsonData->itemName;
    $displayName    = $jsonData->itemShortName;
    $brand          = $jsonData->brandName;
    $category       = $jsonData->category;
    $uom            = $jsonData->uom;
    $altUom         = $jsonData->altUom;
    $altConv        = $jsonData->conversion;
    //$baseConv       = $jsonData->baseConv;
    $description    = $jsonData->description;
    $vchNo          = $jsonData->vchNo;
    $obQty          = $jsonData->qty;
    $obAltQty       = $jsonData->altQty;
    $obRate         = $jsonData->rate;
    $obAmount       = $jsonData->amount;

    //Update item
    $q      =   "UPDATE tbl_productmaster SET itemname='$itemName',displayname='$displayName',brandname='$brand',categoryname='$category',uom='$uom',altuom='$altUom',altconv='$altConv',description='$description' WHERE itemcode='$itemCode'";
    //echo $q;
    mysqli_query($dbh,$q);
    
    //$data           =   updateItem($itemCode,$itemName,$displayName,$brand,$category,$uom,$altUom,$altConv,$baseConv,$description,$dbh);
    /* $rows=mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg.="error in productmaster";
    } */

    //Update Opening Balance
    //$data           =   updateItemOpBal($vchNo,$itemCode,$obQty,$obAltQty,$obRate,$obAmount,$dbh);

    $query  =   "UPDATE tbl_transactionmaster SET amount='$obAmount' WHERE vchno='$vchNo'";
    //echo $query;
    mysqli_query($dbh,$query);
    /* $rows=mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg.="error in transmaster";
    } */

    $query  =   "UPDATE tbl_transactionitemdesc SET itemqty='$obQty',itemaltqty='$obAltQty',salerate='$obRate',itemamount='$obAmount' WHERE itemcode='$itemCode' AND vchno='$vchNo'";
    //echo $query;
    mysqli_query($dbh,$query);
    /* $rows=mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg.="error in item description";
    } */

    if ($error==1)
    {
        $result =   "Failed";
        mysqli_rollback($dbh);
    }
    else
    {
        $result =   "Success";
        mysqli_commit($dbh);
    }

    $msg            =   json_encode(array("result" => $result,"error"=>$errorMsg));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-item-op-balance"){

    /* $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;
    $obQty          = $jsonData->obQty;
    $obAltQty       = $jsonData->obAltQty;
    $obRate         = $jsonData->obRate;
    $obAmount       = $jsonData->obAmount;
    $description    = $jsonData->description;

    //Update item
    $data           =   updateItemOpBal($itemCode,$obQty,$obAltQty,$obRate,$obAmount,$description,$dbh);
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

    $msg            =   json_encode(array("result" => $result,"itemId"=>$itemCode,"rows"=>$rows)); */

    echo $msg;
}
elseif($_POST["action"]  ==  "delete-item"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;

    //Delete item
    $data           =   deleteItem($itemCode,$dbh);
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

    $msg            =   json_encode(array("result" => $result,"itemId"=>$itemCode,"rows"=>$rows));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-price-list"){
    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;
    $price          = $jsonData->price;
    $date           = $jsonData->date;

    $priceData      =   checkPriceList($itemCode,$date,$dbh);
    //echo $priceData."pp";
    if(count($priceData)==0){
        updateOldPriceDate($itemCode,$date,$dbh);
        $values     =   array($itemCode,$price,$date,$date);
        $data       =   addPriceList($values,$dbh);

        if($data!=1)
        {
            $error=1;
        }
    }
    else{
        updateOldPriceDate($itemCode,$date,$dbh);
        updatePriceList($itemCode,$price,$date,$dbh);
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

    $msg            =   json_encode(array("result" => $result));
    echo $msg;
}
elseif($_POST["action"]  ==  "item-price"){

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;

    $data           =   getCurrentPriceById($itemCode,$dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
/* if($_POST["action"]=="getItems")
{
    $opBalType      =   'opbalitem';

    $selectedDate   =   $_POST['selectedDate'];
    $qItem          =   "SELECT itemcode,itemname FROM tbl_productmasterstorewise";
    $arrItem        =   GetData($qItem,$dbh);

    $qInward        =   "SELECT count(itemid),itemid,itemqty,SUM(itemqty) FROM tbl_transactionitemdesc WHERE  subvchtype<>'$opBalType' GROUP BY itemid HAVING itemqty>0";
    $arrInward      =   GetData($qInward,$dbh);

    $qOutward       =   "SELECT count(itemid),itemid,itemqty,SUM(itemqty) FROM tbl_transactionitemdesc  WHERE  subvchtype<>'$opBalType' GROUP BY itemid HAVING itemqty<0";
    $arrOutward     =   GetData($qOutward,$dbh);
    
    $qOpening       =   "SELECT count(itemid),itemid,itemqty,SUM(itemqty) FROM tbl_transactionitemdesc WHERE  subvchtype='$opBalType' GROUP BY itemid";
    $arrOpening     =   GetData($qOpening,$dbh);

    $mainArray      = array('items' => $arrItem,'inward' => $arrInward,'outward' => $arrOutward,'opening' => $arrOpening);
    
    echo json_encode($mainArray);
} */
elseif($_POST["action"]=="getStockSummary")
{
    //$opBalType      =   'opbalitem';

    $query_vch      =   "SELECT slno FROM tbl_vouchertype WHERE subvchtype='opbalitem'";
    $arrOpItem      =   GetData($query_vch,$dbh);
    $opBalItem      =   $arrOpItem[0][0];

    $query_vch      =   "SELECT slno FROM tbl_vouchertype WHERE subvchtype='opbalparty'";
    $arrOpParty     =   GetData($query_vch,$dbh);
    $opBalParty     =   $arrOpParty[0][0];

    $selectedDate   =   $_POST['selectedDate'];

    $qItem          =   "SELECT itemcode,itemname FROM tbl_productmaster";
    $arrItem        =   GetData($qItem,$dbh);

    $qInward        =   "SELECT count(d.itemcode),d.itemcode,d.itemqty,SUM(d.itemqty) FROM tbl_transactionitemdesc d INNER JOIN tbl_transactionmaster m ON m.vchno=d.vchno WHERE m.vchdate<'$selectedDate' AND m.subvoucherid NOT IN ('$opBalItem','$opBalParty') AND d.itemqty>0 GROUP BY d.itemcode";
    $arrInward      =   GetData($qInward,$dbh);

   // echo $qInward;

    $qOutward       =   "SELECT count(d.itemcode),d.itemcode,d.itemqty,SUM(d.itemqty) FROM tbl_transactionitemdesc d INNER JOIN tbl_transactionmaster m ON m.vchno=d.vchno WHERE m.vchdate<'$selectedDate' AND m.subvoucherid NOT IN ('$opBalItem','$opBalParty') AND d.itemqty<0 GROUP BY d.itemcode";
    $arrOutward     =   GetData($qOutward,$dbh);
    
    $qOpening       =   "SELECT count(d.itemcode),d.itemcode,d.itemqty,SUM(d.itemqty) FROM tbl_transactionitemdesc d INNER JOIN tbl_transactionmaster m ON m.vchno=d.vchno WHERE m.vchdate<='$selectedDate' AND m.subvoucherid='$opBalItem' GROUP BY d.itemcode";
    $arrOpening     =   GetData($qOpening,$dbh);

    $qCurrentInward =   "SELECT count(d.itemcode),d.itemcode,d.itemqty,SUM(d.itemqty) FROM tbl_transactionitemdesc d INNER JOIN tbl_transactionmaster m ON m.vchno=d.vchno WHERE m.vchdate='$selectedDate' AND m.subvoucherid NOT IN ('$opBalItem','$opBalParty') AND d.itemqty>0 GROUP BY d.itemcode";
    $arrCurrentInward      =   GetData($qCurrentInward,$dbh);

    //echo $qCurrentInward;

    $qCurrentOutward       =   "SELECT count(d.itemcode),d.itemcode,d.itemqty,SUM(d.itemqty) FROM tbl_transactionitemdesc d INNER JOIN tbl_transactionmaster m ON m.vchno=d.vchno WHERE m.vchdate='$selectedDate' AND m.subvoucherid NOT IN ('$opBalItem','$opBalParty') AND d.itemqty<0 GROUP BY d.itemcode";
    $arrCurrentOutward     =   GetData($qCurrentOutward,$dbh);
    

    $mainArray      = array('items' => $arrItem,'inward' => $arrInward,'outward' => $arrOutward,'opening' => $arrOpening,'currentInward' => $arrCurrentInward,'currentOutward' => $arrCurrentOutward);
    
    echo json_encode($mainArray);
}
else
{
    echo "Invalid Request";
}
?>