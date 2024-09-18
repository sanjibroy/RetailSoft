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

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $party   =   $_POST["party"];
    $fromDate   =   $_POST["from-date"];
    $toDate   =   $_POST["to-date"];
    $partyName  =   $_POST["txtSupp"];

    if($party=="All" || $partyName==""){
        $query_get_data    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE v.primaryvchtype='Receipt' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";
    }
    else{
        $query_get_data    =   "SELECT DATE_FORMAT(m.vchdate,'%d-%m-%Y'),m.partycode,p.partyname,v.subvchtype,m.vchno,ABS(m.receivedamt) FROM tbl_payrecmaster m INNER JOIN tbl_vouchertype v ON v.subvchtype=m.subvchtype INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE m.partycode='$party' AND v.primaryvchtype='Receipt' AND m.vchdate BETWEEN '$fromDate' AND '$toDate' ORDER BY m.slno desc";
        
    }
    
    //echo $query_get_data;
    $data  =   GetData($query_get_data,$dbh);
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
    <title>Receipt Register</title>
</head>


<body id="body-pd" class="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">

            <div class="card-top d-flex align-item-center">
                <div class="heading">Receipt Register</div>
            </div>

            <div class="card-mid">
                <form method="post" class="w-100">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="f-control flex-column align-items-start">
                                <label>Party Name</label>
                                <input type="text" id="supplier" name="txtSupp" placeholder="Select Supplier" class="ui-autocomplete-input" autocomplete="off" value="<?php echo $partyName;?>">
                                <input type="hidden" id="partycode" name="party" value="<?php echo $party;?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>From Date</label>
                                <input type="date" id="from-date" name="from-date" value="<?php echo $fromDate;?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>To Date</label>
                                <input type="date" id="to-date" name="to-date" value="<?php echo $toDate;?>">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="f-control flex-column align-items-start">
                                <label>&nbsp;</label>
                                <input type="submit" name="Proceed" value="Proceed" class="btn py-0 py-2">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="f-control flex-column align-items-start">
                                <label>&nbsp;</label>
                                <input type="submit" name="Export" value="Export" class="btn py-0 py-2">
                            </div>
                        </div>
                    </div>

                </form>

            </div>
            <div class="card-mid">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Voucher No</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="display-indent-list" style="cursor:pointer">
                            <?php
                                for($i=0;$i<count($data);$i++){
                            ?>
                                <tr >
                                    <td onclick="editPurchase('<?php echo $data[$i][4]; ?>')"><?php echo $i+1; ?></td>
                                    <td onclick="editPurchase('<?php echo $data[$i][4]; ?>')"><?php echo $data[$i][0]; ?></td>
                                    <td onclick="editPurchase('<?php echo $data[$i][4]; ?>')"><?php echo $data[$i][2]; ?></td>
                                    <td onclick="editPurchase('<?php echo $data[$i][4]; ?>')"><?php echo $data[$i][4]; ?></td>
                                    <td onclick="editPurchase('<?php echo $data[$i][4]; ?>')"><?php echo $data[$i][5]; ?></td>
                                    <td><button onclick="deleteReceipt('<?php echo $data[$i][4];?>')" class="btn-r"><i class="bx bxs-trash"></i></button></td>
                                </tr>
                            <?php
                                }
                            ?>
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
        const apiUrl = '../core-api/api-gets.php';

        getDataFromAPI({"action":"get-party"},apiUrl).then(data => {
            //console.log(data);

            //Autocomplete Supplier
            $("#supplier").autocomplete({
                minLength: 0,
                source: data,
                focus: function (event, ui) {
                    $("#supplier").val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    $("#supplier").val(ui.item.label);
                    $("#partycode").val(ui.item.value);

                    return false;
                }
            })
        });

        function editPurchase(id){
            //console.log(id);
            let hostName    = window.location.hostname;
            let url =   `../vouchers/edit-receipt.php?id=${id}`;

            // Push the details page URL with the identifier to the browser's history
            //history.pushState({ page: 'edit-purchase', id: voucherId }, '', url);

            // Redirect the user to the details page
            window.location.assign(url);
        }

        function deleteReceipt(id){
            //Set JSON data to be sent
            var data    =   {
                "action":"delete-payrec",
                "vchno":id
            };

            $.alert({
                title: "Message",
                content: "Are you sure want to delete?",
                buttons: {
                yes: function () {
                    // API endpoint URL
                    const apiUrl = '../core-api/api-voucher.php';

                    sendDataToAPI(data,apiUrl).then(result=>{
                        console.log("Result",result);
                        if(result.status=="success"){
                            alert("Receipt deleted successfully.");
                            location.reload();
                        }else{
                            alert("Receipt could not be deleted.");
                        }
                    });
                },
                no:function(){
                    console.log("no");
                }
            }
            }); 
        }
    </script>
   
</body>

</html>