<?php
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

if($_POST["action"]  ==  "ledgers"){

    $data           =   getLedgers($dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "primary-vouchers"){

    $data           =   getVoucherGroup($dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "sub-vouchers"){

    $jsonData       =   json_decode($_POST['jsonData']);
    $primaryVoucher =   $jsonData->primaryVoucher;

    $data           =   getSubVoucherByPrimaryVch($primaryVoucher,$dbh);    
    $total          =   count($data);
    $msg            =   json_encode(array("count" => $total,"data" => $data));

    echo $msg;
}
elseif($_POST["action"]  ==  "add-ledger"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);

    $ledgerName     = $jsonData->ledgerName;
    $ledgerGroup    = $jsonData->ledgerGroup;
    $displayName    = $jsonData->displayName;
   
    //Insert item
    $values         =   array($ledgerName,$ledgerGroup,$displayName);
    $data           =   addNewLedger($values,$dbh);
    if($data!=1)
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

    $msg            =   json_encode(array("result" => $result));

    echo $msg;
}
elseif($_POST["action"]  ==  "add-sub-voucher"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);

    $primaryVoucher     = $jsonData->primaryVoucher;
    $subVoucher         = $jsonData->subVoucher;
    $frequentUse        = $jsonData->frequentUse;
    $txtNature          = $jsonData->txtNature;
    $commonVchNum       = $jsonData->commonVchNum;
    $prefix             = $jsonData->prefix;
    $suffix             = $jsonData->suffix;
    $startvalue         = $jsonData->startValue;
    $valueLength        = $jsonData->valueLength;
    $vchType            = $jsonData->vchType;
   
    //Insert Sub Voucher
    $values             =   array($primaryVoucher,$subVoucher,$frequentUse,$txtNature,$commonVchNum);
    $data               =   addSubVoucher($values,$dbh);
    if($data!=1)
    {
        $error=1;
    }

    //Insert Sub Voucher Number
    $values2             =   array($prefix,$suffix,$startvalue,$valueLength,$vchType);
    $data2               =   addVoucherNumber($values2,$dbh);
    if($data2!=1)
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

    $msg            =   json_encode(array("result" => $result));

    echo $msg;
}
elseif($_POST["action"]  ==  "assign-ledger-voucher"){

    $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);

    for($i=0;$i<count($jsonData);$i++)
    {
        $arr = $jsonData[$i];

        $subVoucherId       = $arr->subVoucherId;
        $ledgerId           = $arr->ledgerId;
        $rate               = $arr->rate;
        $calType            = $arr->calType;
        $ledgerPosition     = $arr->ledgerPosition;
   
        //Assign Ledger to Sub Voucher
        $values             =   array($subVoucherId,$ledgerId,$rate,$calType,$ledgerPosition);
        $data               =   assignLedger($values,$dbh);
        if($data!=1)
        {
            $error=1;
        }
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

    $msg            =   json_encode(array("result" => $result));

    echo $msg;
}
elseif($_POST["action"]  ==  "save-journal"){
    $error          =   0;
    $errorMsg       =   "";
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonDataMaster']);
    $jsonItems       = json_decode($_POST['jsonDataItems']);

    $voucherDate      = $jsonData->voucherDate;
    $supplierId       = $jsonData->supplierId;
    $voucherType      = $jsonData->voucherType;
    $vchId      = $jsonData->voucherId;
    $narration      = $jsonData->narration;
    $userId      = $jsonData->userId;
    $today  =   date('Y-m-d');
    //echo $voucherDate;

    //$idName             =   "";
    $voucherId         =   getVoucherNo($voucherType,$dbh);

    $query_save_journal   =   "INSERT INTO `tbl_transactionmaster`(`vchno`, `vchdate`, `subvoucherid`, `partycode`, `amount`, `narration`, `createdby`, `createdon`) VALUES('$voucherId','$voucherDate','$vchId','$supplierId','0','$narration','$userId','$today')";

    $result=mysqli_query($dbh,$query_save_journal);
    //$rows=mysqli_affected_rows($dbh);
    if($result!=1)
    {
        $error=1;
        $errorMsg    .=   "Error in master";
    }

   // var_dump($jsonItems);

    //echo count($jsonItems);

    for($i=0;$i<count($jsonItems);$i++){
        $arr            = $jsonItems[$i];

        $itemId         = $arr->itemId;
        $qty            = $arr->qty;
        $conversion     = $arr->conversion;
        $uom            = $arr->uom;
        $altUom         = $arr->altUom;
        $selectedUom    = $arr->selectedUom;

        if($selectedUom==$uom){
            $altQty =   $conversion*$qty;
        }else{
            $altQty =   $qty;
            $qty    =   $qty/$conversion;
            
        }

        $query_items    =   "INSERT INTO `tbl_transactionitemdesc`(`vchno`, `itemcode`, `itemqty`, `itemaltqty`, `uom`) VALUES ('$voucherId','$itemId','$qty','$altQty','$selectedUom')";

        //echo $query_items;

        $result=mysqli_query($dbh,$query_items);
        if($result!=1)
        {
            $error=1;
            $errorMsg    .=   "Error in items";
        }

    }

    $rows   = updateVoucherNo($voucherType,$dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg    .=   "Error in id update";
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
    

    $msg            =   json_encode(array("result" => $result,"error" => $errorMsg));

    echo $msg;
}
elseif($_POST["action"]  ==  "delete-journal"){
    $error          =   0;
    $errorMsg       =   "";
    mysqli_autocommit($dbh, FALSE);

    $id= $_POST['journalid'];

    $query1  =   "DELETE FROM tbl_transactionmaster WHERE vchno='$id'";
    mysqli_query($dbh,$query1);
    $rows   =   mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg    .=   "Error in master";
    }

    $query2  =   "DELETE FROM tbl_transactionitemdesc WHERE vchno='$id'";
    mysqli_query($dbh,$query2);
    $rows   =   mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg    .=   "Error in items";
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

    $msg    =   json_encode(array("result" => $result,"error" => $errorMsg));

    echo $msg;
}
elseif($_POST["action"]  ==  "update-ledger"){

    /* $error=0;
    mysqli_autocommit($dbh, FALSE);

    $jsonData       = json_decode($_POST['jsonData']);
    $itemCode       = $jsonData->itemCode;
    $itemName       = $jsonData->itemName;
    $displayName    = $jsonData->displayName;
    $brand          = $jsonData->brand;
    $category       = $jsonData->category;
    $uom            = $jsonData->uom;
    $altUom         = $jsonData->altUom;
    $altConv        = $jsonData->altConv;
    $baseConv       = $jsonData->baseConv;
    $description    = $jsonData->description;

    //Update item
    $data           =   updateItem($itemCode,$itemName,$displayName,$brand,$category,$uom,$altUom,$altConv,$baseConv,$description,$dbh);
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
elseif($_POST["action"]  ==  "delete-ledger"){

    /* $error=0;
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

    $msg            =   json_encode(array("result" => $result,"itemId"=>$itemCode,"rows"=>$rows)); */

    echo $msg;
}
else
{
    echo "Invalid Request";
}
?>