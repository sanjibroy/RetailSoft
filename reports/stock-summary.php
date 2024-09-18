<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");


$partyName  ="";
$party      ="";
$today      =date('Y-m-d');
$data   =array();
$fromDate = $today;
$toDate = $today;

$query_get_data    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.amount) FROM tbl_transactionmaster m INNER JOIN tbl_vouchertype v ON v.slno=m.subvoucherid INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype in ('Purchase','Sale','Payment','Receipt') AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";
$data   =   GetData($query_get_data,$dbh);

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $party   =   $_POST["party"];
    $fromDate   =   $_POST["from-date"];
    $toDate   =   $_POST["to-date"];
    $partyName  =   $_POST["txtSupp"];


    if($party=="All" || $partyName==""){

        //Purchase
        $query_get_data    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.amount) FROM tbl_transactionmaster m INNER JOIN tbl_vouchertype v ON v.slno=m.subvoucherid INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype in ('Purchase','Sale','Payment','Receipt') AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        //Sales
       /*  $query_get_sales    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.amount) FROM tbl_transactionmaster m INNER JOIN tbl_vouchertype v ON v.slno=m.subvoucherid INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype='Sale' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        //Payment
        $query_get_payment    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype='Payment' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        //Receipt
        $query_get_receipt    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype='Receipt' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";
 */
    }
    else{

        //Purchase
        $query_get_data    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.amount) FROM tbl_transactionmaster m INNER JOIN tbl_vouchertype v ON v.slno=m.subvoucherid INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE m.partycode='$party' AND  v.primaryvchtype in ('Purchase','Sale','Payment','Receipt') AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        /* //Sales
        $query_get_sales    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.amount) FROM tbl_transactionmaster m INNER JOIN tbl_vouchertype v ON v.slno=m.subvoucherid INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE m.partycode='$party' AND v.primaryvchtype='Sale' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        //Payment
        $query_get_payment    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE m.partycode='$party' AND v.primaryvchtype='Payment' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";

        //Receipt
        $query_get_receipt    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE m.partycode='$party' AND v.primaryvchtype='Receipt' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc"; */

    }
    
    //echo $query_get_data;

    $data   =   GetData($query_get_data,$dbh);/* 
    $saleData       =   GetData($query_get_sales,$dbh);
    $paymentData    =   GetData($query_get_payment,$dbh);
    $receiptData    =   GetData($query_get_receipt,$dbh); */



    if(count($data)==0){
        $isPosted=true;
        $msg="No Records Found";
    }else{
        $isPosted=false;
    }

    //var_dump($data);
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <title><?php echo $REPORT_TITLE; ?></title>
</head>


<body id="body-pd" class="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">

            <div class="card-top">
                <div class="heading">Stock Summary</div>
            </div>

            <div class="card-mid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="f-control flex-column align-items-start">
                            <label>Party Name</label>
                            <input type="date" id="toDate">
                            <input type="hidden" id="partycode" name="party" value="<?php echo $party;?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>&nbsp;</label>
                            <input type="button" value="Go" onclick="getItemData()">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-mid">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <th>Sl No</th>
                            <th>Item Name</th>
                            <th>Opening Stock</th>
                            <th>Inward Stock</th>
                            <th>Outward Stock</th>
                            <th>Closing Stock</th>
                        </thead>
                        <tbody id="stockTableBody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!--Container Main end-->
    
    <script src="../js/nav.js"></script>
    <script src="js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>
    <script>
        
        function getItemData(){
            var toDate  =   document.getElementById("toDate").value;
            //console.log(toDate);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() 
            {
                    if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                    var data = JSON.parse(this.responseText);
                    var itemList = data.items;
                    var inward   = data.inward;
                    var outward  = data.outward;
                    var opening  = data.opening;
                    var currentInward  = data.currentInward;
                    var currentOutward  = data.currentOutward;

                    createItems(itemList,inward,outward,opening,currentInward,currentOutward);

                }
            };
    
            xmlhttp.open("POST", "../api/api-item.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(`selectedDate=${toDate}&action=getStockSummary`);
        }

        function createItems(items,inward,outward,opening,currentInward,currentOutward){

            var stocks  =   items;

            for (let index = 0; index < items.length; index++) {
                const element   =   items[index];
                let itemId      =   element[0];
                
                //inward
                for(let x=0;x<inward.length;x++){
                    let inElement   =   inward[x];
                    let inId        =   inElement[1];
                    if(itemId==inId)
                    {
                        stocks[index][2]=inElement[3];
                        //console.log("matched");
                        break;
                    }
                    else
                    {
                        stocks[index][2]=0;
                    }
                } 

                //outward
                for(let x=0;x<outward.length;x++){
                    let inElement   =   outward[x];
                    let inId        =   inElement[1];
                    if(itemId==inId)
                    {
                        stocks[index][3]=inElement[3];
                        //console.log("matched");
                        break;
                    }
                    else
                    {
                        stocks[index][3]=0;
                    }
                } 
                
                //opening
                for(let x=0;x<opening.length;x++){
                    let inElement   =   opening[x];
                    let inId        =   inElement[1];
                    if(itemId==inId)
                    {
                        stocks[index][4]=inElement[3];
                        //console.log("matched");
                        break;
                    }
                    else
                    {
                        stocks[index][4]=0;
                    }
                }


                //current inward
                for(let x=0;x<currentInward.length;x++){
                    let inElement   =   currentInward[x];
                    let inId        =   inElement[1];
                    if(itemId==inId)
                    {
                        stocks[index][5]=inElement[3];
                        //console.log("matched");
                        break;
                    }
                    else
                    {
                        stocks[index][5]=0;
                    }
                } 

                //current outward
                for(let x=0;x<currentOutward.length;x++){
                    let inElement   =   currentOutward[x];
                    let inId        =   inElement[1];
                    if(itemId==inId)
                    {
                        stocks[index][6]=inElement[3];
                        //console.log("matched");
                        break;
                    }
                    else
                    {
                        stocks[index][6]=0;
                    }
                } 

            }

            console.log(stocks);

            createSummaryTable(stocks);

        }

        function createSummaryTable(stocks){
            
            var stockBody   =   document.getElementById("stockTableBody");
            var rows    ="";
            for(let i=0;i<stocks.length;i++){
                var opQty,inQty,outQty,closeQty,currentInward,currentOutward;

                if(stocks[i][2]==undefined){
                    inQty=0;
                }
                else
                {
                    inQty=stocks[i][2];
                }

                if(stocks[i][3]==undefined){
                    outQty=0;
                }
                else
                {
                    outQty=-stocks[i][3];
                }

                if(stocks[i][4]==undefined){
                    opQty=0;
                }
                else
                {
                    opQty=stocks[i][4];
                }

                if(stocks[i][5]==undefined){
                    currentInward=0;
                }
                else
                {
                    currentInward=stocks[i][5];
                }

                if(stocks[i][6]==undefined){
                    currentOutward=0;
                }
                else
                {
                    currentOutward=-stocks[i][6];
                }

                prevClosingQty    =   parseInt(opQty)+parseInt(inQty)-parseInt(outQty);
                
                var closingQty    =   parseInt(prevClosingQty)+parseInt(currentInward)-parseInt(currentOutward);

                rows +=   `<tr><td>${i+1}</td><td>${stocks[i][1]}</td><td style="text-align:center">${prevClosingQty}</td><td style="text-align:center">${currentInward}</td><td style="text-align:center">${currentOutward}</td><td style="text-align:center">${closingQty}</td></tr>`;

                //console.log(stocks[i][1],stocks[i][2],stocks[i][3],stocks[i][4]);

            }

            stockBody.innerHTML=rows;
        }
    </script>
   
</body>

</html>