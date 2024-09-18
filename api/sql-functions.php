<?php
/**
 * Utilities Functions
 */
function concatValues($values)
{
    $temp="(";
    for($i=0;$i<count($values);$i++)
    {
        $temp=$temp."'".$values[$i]."',";
    }
    $temp=rtrim($temp,",");
    $temp=$temp.")";
    return $temp;
}

function GetData($strQuery,$dbh)
{
	$data=array();
	$sqlr=mysqli_query($dbh,$strQuery);
	if($sqlr)
	{	 
        $col=mysqli_num_fields($sqlr);
        $no=0;
        while ($row = mysqli_fetch_array($sqlr)) 
		{	
            for ($index = 0; $index < $col; $index++) 
			{
                $data[$no][$index]=$row[$index];
            }
            $no++;	
        }
        return $data;
	 }
}

/** Utilities Functions Ends */

function getCurrentFinancialYear($dbh){
    $q="SELECT finyearstart,finyearend FROM tbl_company";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


function getSideMenuHeader($dbh){
    $q="SELECT DISTINCT(menu_title) FROM tbl_app_menu";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getSideMenu($header,$dbh){
    $q="SELECT menu_sub_title,menu_icon,menu_link FROM tbl_app_menu WHERE menu_title='$header'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getVoucherNo($vchType,$dbh){
    $q="SELECT prefix,suffix,startvalue,lengthval FROM tbl_voucherno WHERE vchtype='$vchType'";
    try {
        $result = GetData($q,$dbh);
        $id=$result[0][0].$result[0][2].$result[0][1];
    } catch (\Throwable $th) {
        $id = 0;
    }
    return $id;
}

function updateVoucherNo($vchType,$dbh)
{
    $q="UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='$vchType'";
    try {
        $result = mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        return 0;
    }
    
    return $result;
}

function addNewTransactionMaster($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_transactionmaster(vchno,vchdate,subvoucherid,partycode,amount,narration,createdby,createdon,supplierinvno,supplierinvdate) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addNewTransactionItems($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_transactionitemdesc(vchno,itemcode,uom,itembatch,itemmrp,mfgdate,expdate,itemqty,itemaltqty,itemcost,salerate,itemamount,stocktype,txndate,category) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addNewTransactionLedger($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_transactionledgerdesc(vchno,ledgerid,ledrate,ledamount) VALUES".$values;

    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


/** ITEMS */


function getCategory($dbh){
    $q="SELECT categoryname FROM tbl_categorymaster";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addNewItem($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_productmaster(itemcode,itemname,displayname,brandname,categoryname,uom,altuom,altconv,baseconv,description) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getItems($dbh){
    $q="SELECT itemcode,itemname,displayname,brandname,categoryname,uom,altuom,altconv,baseconv,obqty,obaltqty,obrate,obamount,description FROM tbl_productmaster";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getItemById($id,$dbh){
    $q="SELECT itemcode,itemname,displayname,brandname,categoryname,uom,altuom,altconv,baseconv,obqty,obaltqty,obrate,obamount,description FROM tbl_productmaster WHERE itemcode='$id'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updateItem($itemCode,$itemName,$displayName,$brandName,$categoryName,$uom,$altUom,$altConv,$baseConv,$description,$dbh){
    $q      =   "UPDATE tbl_productmaster SET itemname='$itemName',displayname='$displayName',brandname='$brandName',categoryname='$categoryName',uom='$uom',altuom='$altUom',altconv='$altConv',baseconv=$baseConv,description='$description' WHERE itemcode='$itemCode'";
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updateItemOpMaster($vchNo,$obAmount,$dbh){
    $query  =   "UPDATE tbl_transactionmaster SET amount='$obAmount' WHERE vchno='$vchNo'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updateItemOpBal($vchNo,$itemCode,$obQty,$obAltQty,$obRate,$obAmount,$dbh){
    $query  =   "UPDATE tbl_transactionitemdesc SET itemqty='$obQty',itemaltqty='$obAltQty',salerate='$obRate',itemamount='$obAmount' WHERE itemcode='$itemCode' AND vchno='$vchNo'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function deleteItem($itemCode,$dbh){
    $q      =   "DELETE FROM tbl_productmaster WHERE itemcode='$itemCode'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addItemOpeningBalance($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_openingbalance_items(itemcode,opdate,opbalance,fromdate,todate) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addPriceList($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_pricelist(itmid,rate,plfromdate,pltodate) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updatePriceList($itemCode,$rate,$date,$dbh){
    //$values =   concatValues($data);
    $q      =   "UPDATE tbl_pricelist SET rate='$rate' WHERE itmid='$itemCode' AND plfromdate='$date' AND plfromdate=pltodate";
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updateOldPriceDate($itemCode,$date,$dbh){
    //$values =   concatValues($data);
    $q      =   "UPDATE tbl_pricelist SET pltodate='$date' WHERE itmid='$itemCode' AND plfromdate=pltodate";
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function checkPriceList($itemCode,$date,$dbh){
    $q="SELECT itmid FROM tbl_pricelist WHERE itmid='$itemCode' AND plfromdate='$date' AND plfromdate=pltodate";
    //echo $q;
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getCurrentPriceById($itemCode,$dbh){
    $today  =   date('Y-m-d');
    $q      =   "SELECT rate FROM tbl_pricelist WHERE itmid='$itemCode' AND plfromdate<='$today' AND pltodate>='$today'";
    //echo $q;
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


/** LEDGER */


function getLedgerGroup($dbh){
    $q="SELECT slno,ledgergroup FROM tbl_ledgergroup";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addLedgerGroup($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_ledgergroup(ledgergroup) VALUES".$values;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function deleteLedgerGroup($ledgergroup,$dbh){
    $q      =   "DELETE FROM tbl_ledgergroup WHERE ledgergroup='$ledgergroup'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getLedgerByGroup($ledgergroup,$dbh){
    $q="SELECT ledgername,displayledgername FROM tbl_ledgermaster WHERE ledgergroup='$ledgergroup'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


function getLedgers($dbh){
    $q="SELECT ledgername,displayledgername,ledgergroup FROM tbl_ledgermaster";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addNewLedger($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_ledgermaster(ledgername,ledgergroup,displayledgername) VALUES".$values;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updateLedger($ledgername,$ledgergroup,$displayledgername,$slno,$dbh){
    $q      =   "UPDATE tbl_ledgermaster SET ledgername='$ledgername',ledgergroup='$ledgergroup',displayledgername='$displayledgername' WHERE slno='$slno'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function deleteLedger($id,$dbh){
    $q      =   "DELETE FROM tbl_ledgermaster WHERE slno='$id'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


/** VOUCHER */


function getVoucherGroup($dbh){
    $q="SELECT slno,primaryvoucher FROM tbl_primaryvoucher";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addSubVoucher($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_vouchertype(primaryvchtype,subvchtype,frequentuse,txtnature,commonvchnum) VALUES".$values;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getSubVoucherByPrimaryVch($primaryvch,$dbh){
    $q="SELECT slno,subvchtype,frequentuse,txtnature,commonvchnum FROM tbl_vouchertype WHERE primaryvchtype='$primaryvch'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addVoucherNumber($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_voucherno(prefix,suffix,startvalue,lengthval,vchtype) VALUES".$values;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function assignLedger($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_voucher_ledger_assoc(subvoucherid,ledgerid,rate,caltype,ledgerposition) VALUES".$values;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getAssignedLedgerBySubVoucherId($id,$dbh){
    $q      =   "SELECT a.subvoucherid,a.ledgerid,m.displayledgername,a.rate,a.caltype,a.ledgerposition FROM tbl_voucher_ledger_assoc a INNER JOIN tbl_ledgermaster m ON a.ledgerid=m.slno WHERE a.subvoucherid='$id'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getTotalVoucherLedgerById($id,$dbh){
    $q      =   "SELECT COUNT(slno) FROM tbl_transactionledgerdesc WHERE ledgerid='$id'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function deleteAssignedLedger($id,$dbh){
    $q      =   "DELETE FROM tbl_voucher_ledger_assoc WHERE ledgerid='$id'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getTotalSubVoucherById($id,$dbh){
    $q      =   "SELECT COUNT(slno) FROM tbl_transactionmaster WHERE subvoucherid='$id'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function deleteSubVoucher($id,$dbh){
    $q      =   "DELETE FROM tbl_vouchertype WHERE slno='$id'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}


/** PARTY */


function getPartyId($dbh){
    $q="SELECT partycode,partyname FROM tbl_partymaster";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function getPartyDetailById($id,$dbh){
    $q="SELECT partycode,partyname,partyadd,partycity,partystate,partystatecode,partypincode,partylandlineno,partymobileno,partyemailid,partypan,partytin,gstenable,gstno,partytype,opbalance,obdate FROM tbl_partymaster WHERE partycode='$id'";
    try {
        $result = GetData($q,$dbh);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addNewParty($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_partymaster(partycode,partyname,partyadd,partycity,partystate,partystatecode,partypincode,partylandlineno,partymobileno,partyemailid,partypan,partytin,gstenable,gstno,partytype) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function addPartyOpeningBalance($data,$dbh){
    $values =   concatValues($data);
    $q      =   "INSERT INTO tbl_openingbalance_party(partycode,opdate,opbalance,fromdate,todate) VALUES".$values;
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updatePartyProfile($partyCode,$partyName,$partyAdd,$partyCity,$partyState,$partyStateCode,$partyPinCode,$partyLandLineNo,$partyMobileNo,$partyEmailId,$partyPan,$partyTin,$gstEnable,$gstNo,$partyType,$dbh){
    $q      =   "UPDATE tbl_partymaster SET partyname='$partyName',partyadd='$partyAdd',partycity='$partyCity',partystate='$partyState',partystatecode='$partyStateCode',partypincode='$partyPinCode',partylandlineno='$partyLandLineNo',partymobileno='$partyMobileNo',partyemailid='$partyEmailId',partypan='$partyPan',partytin='$partyTin',gstenable='$gstEnable',gstno='$gstNo',partytype='$partyType' WHERE partycode='$partyCode'";
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

function updatePartyOpBalance($partyCode,$opBalance,$opDate,$dbh){
    /* $q      =   "UPDATE tbl_partymaster SET opbalance='$opBalance',obdate='$opDate' WHERE partycode='$partyCode'";
    //echo $q;
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result; */
}

function deleteParty($partyCode,$dbh){
    $q      =   "DELETE FROM tbl_partymaster WHERE partycode='$partyCode'";
    try {
        $result   =   mysqli_query($dbh,$q);
    } catch (\Throwable $th) {
        $result = 0;
    }
    return $result;
}

