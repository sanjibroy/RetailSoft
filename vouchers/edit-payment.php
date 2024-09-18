<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$query_vouchers =   "SELECT slno,subvchtype FROM tbl_vouchertype WHERE primaryvchtype='Payment'";
$vouchers   =   GetData($query_vouchers,$dbh);
//var_dump($vouchers);
$isPosted   =   false;

$id=$_GET["id"];



if($_SERVER["REQUEST_METHOD"]=="POST"){

    $error          =   0;
    $errorMsg       =   "";
    $isPosted   =   true;
    mysqli_autocommit($dbh, FALSE);

    $partyId        =$_POST["partyId"];
    $voucherDate    =$_POST["paymentDate"];
    $voucherId      =$_POST["voucherType"];
    $paymentType    =$_POST["paymentType"];
    $totalAmount    =$_POST["totalAmount"];
    $paymentMode    =$_POST["paymentMode"];
    $today          =date('Y-m-d');
    $userId         =$_SESSION["PH_USER_ID"];

   // echo $partyId;

    foreach ($vouchers as $option) {
        if ($option[0] === $voucherId) {
            $voucherType = $option[1];
            break;
        }
    }

    if($paymentMode!="Credit")
    {

        $currentStatus      =1;
        $isSettled          =0;

        $query_delete_mop   =   "DELETE FROM tbl_payrecmop WHERE vchno='$id'";
        mysqli_query($dbh,$query_delete_mop);
        $rows_mop   =   mysqli_affected_rows($dbh);
        if($rows_mop==0){
            $error=1;
            $errorMsg    .=   "Error in mop";
        }

        $query_get_ref  =   "SELECT * FROM tbl_payrecrefnodt WHERE vchno='$id'";
        $refData    =   GetData($query_get_ref,$dbh);
        if(count($refData)>0){
            $query_delete_ref   =   "DELETE FROM tbl_payrecrefnodt WHERE vchno='$id'";
            mysqli_query($dbh,$query_delete_ref);
            $rows_ref   =   mysqli_affected_rows($dbh);
            if($rows_ref==0){
                $error=1;
                $errorMsg    .=   "Error in ref";
            }
        }

        

        $query_delete_trans   =   "DELETE FROM tbl_transactionmaster WHERE vchno='$id'";
        mysqli_query($dbh,$query_delete_trans);
        $rows_trans   =   mysqli_affected_rows($dbh);
        if($rows_trans==0){
            $error=1;
            $errorMsg    .=   "Error in master";
        }

        $query  =   "UPDATE tbl_payrecmaster SET vchdate='$voucherDate',subvchtype='$voucherType', partycode='$partyId', receivedamt='$totalAmount' WHERE vchno='$id'";
        mysqli_query($dbh,$query);
        /* $rows_master   =   mysqli_affected_rows($dbh);
        if($rows_master==0){
            $error=1;
            $errorMsg    .=   "Error in master";
        } */

        $chequeNo   ="";
        $chequeDate ="";
        $chequeBank ="";
        $bank="";
        $utr="";

        //$dataPayment        =   addPaymentReceivedMaster($paymentMaster,$dbh);

        if($paymentMode=="Cheque"){
            $chequeNo       =$_POST["chequeNo"];
            $chequeDate     =$_POST["chequeDate"];
            $chequeBank     =$_POST["clearingBank"];
        }

        if($paymentMode=="UPI"){
            $bank           =$_POST["clearingBank"];
            $utr            =$_POST["utr"];
        }

        if($paymentMode=="Online"){
            $bank           =$_POST["clearingBank"];
            $utr            =$_POST["utr"];
        }

        $q2 =   "INSERT INTO `tbl_payrecmop`(`vchno`, `mopamt`, `mop`, `instno`, `instdate`, `instbank`, `txnno`, `clearingbank`, `bankcardno`) VALUES ('$id','$totalAmount','$paymentMode','$chequeNo','$chequeDate','$chequeBank','$utr','$bank','')";
        $dataMop    =   mysqli_query($dbh,$q2);
        //$dataMop            =   addPaymentReceivedMop($paymentMop,$dbh);
        if($dataMop!=1)
        {
            $error=1;
            $errorMsg    .=   "Error in payment mop";
        } 
        


        //add transaction master
        $currentStatus          =   0;

        //insert payment transaction master
        $queriesTrans = "INSERT INTO tbl_transactionmaster (vchno, vchdate, subvoucherid, partycode, amount, narration, currentstatus, createdby, createdon, supplierinvno, supplierinvdate) VALUES ('$id','$voucherDate','$voucherId','$partyId','-$totalAmount','','$currentStatus','$userId','$today','','')";
        $paymentMaster    =   mysqli_query($dbh,$queriesTrans);
        if($paymentMaster!=1)
        {
            $error=1;
            $errorMsg      .=   "Error in payment transaction master";
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
    
    echo $errorMsg;
    echo $result;



}

$query_master  =   "SELECT m.`vchno`, m.`vchdate`, m.`subvchtype`, m.`partycode`, p.partyname, m.`receivedamt` FROM `tbl_payrecmaster` m INNER JOIN tbl_partymaster p ON m.partycode=p.partycode WHERE vchno='$id'";
$masterData     =   GetData($query_master,$dbh);

$vchNo  =   $masterData[0][0];
$voucherDate  =   $masterData[0][1];
$subVoucher  =   $masterData[0][2];
$partyCode  =   $masterData[0][3];
$partyName  =   $masterData[0][4];
$amount  =   $masterData[0][5];

$query_mop  =   "SELECT `vchno`, `mopamt`, `mop`, `instno`, `instdate`, `instbank`, `txnno`, `clearingbank`, `bankcardno` FROM `tbl_payrecmop` WHERE `vchno`='$id'";
$mopData    =   GetData($query_mop,$dbh);

$mop    =   $mopData[0][2];
$instNo    =   $mopData[0][3];
$instDate    =   $mopData[0][4];
$instBank    =   $mopData[0][5];
$txnNo    =   $mopData[0][6];
$bank    =   $mopData[0][7];

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <link rel="stylesheet" href="../assets/css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <title>Payment Voucher</title>
</head>


  <body id="body-pd" class="body-pd">
    
  <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <?php
        if($isPosted){
    ?>
    
    <script>
         $.alert({
                title: "Message",
                content: "<?php echo $msg;?>",
            }); 
            //alert("");
    </script>

    <?php $isPosted=false; }?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
      <div class="card" id="dashboard">

        <form method="POST" onsubmit="return validateForm();">

        <div class="card-top d-flex align-item-center">
          <!-- <select id="item-wise">
            <option selected>Item Wise</option>
            <option>Category Wise</option>
          </select> -->
          <div class="heading">Edit Payment</div>
          <!-- <div class="d-flex ms-auto gap-4">
            <div class="f-control">
                <label><b>New</b></label>
                <input type="radio" name="item" value="new">
              </div>
              <div class="f-control">
                <label><b>Update</b></label>
                <input type="radio" name="item" value="update">
              </div>
              <div class="f-control">
                <label><b>Delete</b></label>
                <input type="radio" name="item" value="delete">
              </div>
          </div> -->
        </div>
        <div class="card-mid">
            <div class="row">
                <div class="sub-heading">Select Party</div>
                <div class="col-md-6 mt-2">
                    <div class="f-control">
                        <input type="text" id="supplier" name="party-search" placeholder="Search Party" value="<?php echo $partyName;?>" required>
                        <input type="hidden" id="partycode" name="partyId" value="<?php echo $partyCode;?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <p></p>
                </div>
                <div class="sub-heading mt-2">Payment Voucher & Other Details</div>

                <div class="col-md-2">
                    <div class="f-control d-flex flex-column align-items-start">
                        <label>Voucher No</label>
                        <input type="text" id="voucherNo" name="voucherNo" value="<?php echo $vchNo;?>" readonly>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="f-control d-flex flex-column align-items-start">
                        <label>Date</label>
                        <input type="date" id="paymentDate" name="paymentDate" value="<?php echo $voucherDate;?>" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="f-control d-flex flex-column align-items-start">
                        <label>Voucher Type</label>
                        <select name="voucherType" required>
                            <?php
                                for($i=0;$i<count($vouchers);$i++){
                                    if($vouchers[$i][1]==$subVoucher){?>
                                        <option selected value="<?php echo $vouchers[$i][0];?>"><?php echo $vouchers[$i][1];?></option>
                                <?php }else{?>
                                        <option value="<?php echo $vouchers[$i][0];?>"><?php echo $vouchers[$i][1];?></option>
                                <?php }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="f-control d-flex flex-column align-items-start">
                        <label>Payment Type</label>
                        <select name="paymentType">
                            <option value="onaccount">On Account</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="f-control d-flex flex-column align-items-start">
                        <label>Amount</label>
                        <input type="number" placeholder="Amount" name="totalAmount" value="<?php echo $amount;?>" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 d-none">
                <div class="card-mid">
                    <div class="row">
                        <div class="sub-heading">Voucher Selection</div>
                        <div class="col-md-6 mt-2">
                            <div class="f-control">
                                <input type="text" id="invoice-search" placeholder="Search Invoices (Party Pending Invoices)">
                                <input type="hidden" id="invoice-search-id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p></p>
                        </div>
                        <div class="col-md-3">
                            <div class="f-control">
                                <input type="text" id="receipt-inv" disabled placeholder="Selected Invoice Number">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="f-control">
                                <input type="number" id="receipt-bal" disabled placeholder="Balance Amount">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="f-control">
                                <input type="number" id="receipt-amt" disabled placeholder="Amount To Be Paid">
                            </div>
                        </div>
                        <div class="col-md-12 d-flex gap-2">
                            <button class="btn mt-1">Add To List</button>
                            <button class="btn mt-1">Remove From List</button>
                            <button class="btn mt-1">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 d-none">
                <div class="card-mid">
                    <div class="table-responsive" style="max-height: 30vh; overflow-y: auto;">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>Invoice#</th>
                              <th>Balance Amount</th>
                              <th class="text-center">Paying Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                              <tr>
                                <td colspan="2">Grand Total</td>
                                <td class="text-center">0.00</td>
                              </tr>
                          </tbody>
                        </table>
                      </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card-mid">
                    <div class="row">
                        <div class="sub-heading">Payment Details</div>
                        <div class="col-md-8">
                            <div class="payment">
                                <button type="button" class="payment-btn" id="cash-btn" onclick="createInputs('Cash')">
                                    <i class="bx bx-money"></i>
                                    <span>Cash</span>
                                </button>
                                <!-- <button type="button" class="payment-btn" id="credit-btn" onclick="createInputs('Credit')">
                                    <i class="bx bx-credit-card"></i>
                                    <span>Credit</span>
                                </button> -->
                                <button type="button" class="payment-btn" id="cheque-btn" onclick="createInputs('Cheque')">
                                    <i class="bx bx-credit-card-front"></i>
                                    <span>Cheque</span>
                                </button>
                                <button type="button" class="payment-btn" id="upi-btn" onclick="createInputs('UPI')">
                                    <i class="fab fa-google-pay"></i>
                                    <span>BHIM UPI</span>
                                </button>
                                <button type="button" class="payment-btn" id="net-banking-btn" onclick="createInputs('Online')">
                                    <i class="bx bxs-bank"></i>
                                    <span>Net Banking</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row" id="payment-inputs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-bot">
          <div class="d-flex w-100">
            <button class="btn my-3">Save</button>
          </div>
        </div>

        </form>

      </div>
    </main>
    <!--Container Main end-->
    
    <script src="../js/nav.js">
    </script>
    <script src="../js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
      $( "select" ).selectmenu();
      

        var supplier=[];

        var modeOfPayment = "";

        const apiUrl = '../core-api/api-gets.php';

        getDataFromAPI({"action":"get-suppliers"},apiUrl).then(data => {
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

        var inputType   =   "<?php echo $mop;?>";
        //console.log(inputType);
        createInputs(inputType);

        function createInputs(inputType){
            var paymentInput = '';
            if(inputType == "Cash"){
                modeOfPayment="Cash";
                paymentInput = `<div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    <label>Cash</label>
                                    <input type="hidden" name="paymentMode" value="Cash"/>
                                </div>
                            </div>`;
            }
            else if(inputType == "Credit"){
                modeOfPayment="Credit";
                paymentInput = `<div class="col-md-12"><br>
                <label>Credit Mode Selected</label>
                <input type="hidden" name="paymentMode" value="Credit"/>
                </div>`;
            }
            else if(inputType == "Cheque"){
                modeOfPayment="Cheque";
                paymentInput = `<div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    
                                    <input type="hidden" name="paymentMode" value="Cheque"/>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="f-control flex-column align-items-start">
                                    <label>Cheque No.</label>
                                    <input type="text" id="cheque-no" name="chequeNo" placeholder="Cheque No." value="<?php echo $instNo;?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="f-control flex-column align-items-start">
                                    <label>Cheque Date</label>
                                    <input type="date" id="cheque-date" name="chequeDate" value="<?php echo $instDate;?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                            <div class="f-control flex-column align-items-start">
                                    <label>Clearing Bank</label>
                                    <input type="text" id="clearing-bank" name="clearingBank" placeholder="Clearing Bank" value="<?php echo $instBank;?>">
                                </div>
                            </div>`;
            }
            else if(inputType == "UPI"){
                modeOfPayment="UPI";
                paymentInput = `<div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    
                                    <input type="hidden" name="paymentMode" value="UPI"/>
                                </div>
                                </div>
                            <div class="col-md-6">
                                <div class="f-control flex-column align-items-start">
                                    <label>UTR#</label>
                                    <input type="text" id="utr" name="utr" placeholder="UTR#" value="<?php echo $txnNo;?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                    <label>Clearing Bank</label>
                                    <input type="text" id="clearing-bank" name="clearingBank" placeholder="Clearing Bank" value="<?php echo $bank;?>">
                                </div>
                            </div>`;
            }
            else if(inputType == "Online"){
                modeOfPayment="Online";
                paymentInput = `<div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    
                                    <input type="hidden" name="paymentMode" value="Online"/>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="f-control flex-column align-items-start">
                                    <label>UTR#</label>
                                    <input type="text" id="utr" name="utr" placeholder="UTR#" value="<?php echo $txnNo;?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                    <label>Clearing Bank</label>
                                    <input type="text" id="clearing-bank" name="clearingBank" placeholder="Clearing Bank" value="<?php echo $bank;?>">
                                </div>
                            </div>`;
            }
            document.querySelector("#payment-inputs").innerHTML = paymentInput;
        }
    </script>
    
    <script>
        function validateForm() {

            var party = document.querySelector("#party-search");
            var date = document.querySelector("#paymentDate");
            var voucherType = document.querySelector("#vchType");
            var paymentType = document.querySelector("#paymentType");
            var amount = document.querySelector("#amt");

            if(party.value == ""){
                $.alert({
                    title: "Warning!",
                    content: "Please Add Party",
                    buttons: {
                        ok: party.focus(),
                    }
                });
                return false;
            }

            if(!Date.parse(date.value)){
                $.alert({
                    title: "Warning!",
                    content: "Please Add Date",
                    buttons: {
                        ok: date.focus(),
                    }
                });
                return false;
            }


            if(voucherType.value == ""){
                $.alert({
                    title: "Warning!",
                    content: "Please Select Voucher Type",
                    buttons: {
                        ok: voucherType.focus(),
                    }
                });
                return false;
            }

            if(paymentType.value == ""){
                $.alert({
                    title: "Warning!",
                    content: "Please Select Payment Type",
                    buttons: {
                        ok: paymentType.focus(),
                    }
                });
                return false;
            }

            if(amount.value == ""){
                $.alert({
                    title: "Warning!",
                    content: "Please Add Amount",
                    buttons: {
                        ok: amount.focus(),
                    }
                });
                return false;
            }

            if (modeOfPayment == "") {
                $.alert({
                    title: "Warning!",
                    content: "Select Payment Mode",
                });
                return false;
            }

            //Payment
            if (modeOfPayment == "Credit") {
                var jsonPayment = jsonCreate(
                    [
                        "subVoucherType",
                        "amountPaid",
                        "modeOfPay",
                        "instNo",
                        "instDate",
                        "instBank",
                        "txnNo",
                    ],
                    [paymentVoucher, "", "Credit", "", "", "", ""]
                );
            }

            if (modeOfPayment == "Cash") {
                var paidAmount = document.getElementById("paid-amount").value;

                if (paidAmount.length == 0) {
                    $.alert({
                        title: "Warning!",
                        content: "Enter amount paid",
                    });
                    return false;
                }

                var jsonPayment = jsonCreate(
                    [
                        "subVoucherType",
                        "amountPaid",
                        "modeOfPay",
                        "instNo",
                        "instDate",
                        "instBank",
                        "txnNo",
                    ],
                    [paymentVoucher, paidAmount, modeOfPayment, "", "", "", ""]
                );
            }

            if (modeOfPayment == "Cheque") {
                var paidAmount = document.getElementById("paid-amount").value;

                if (paidAmount.length == 0) {
                    $.alert({
                        title: "Warning!",
                        content: "Enter amount paid",
                    });
                    return false;
                }

                var chequeNo = document.getElementById("cheque-no").value;
                var chequeDate = document.getElementById("cheque-date").value;
                var clearingBank = document.getElementById("clearing-bank").value;
                var jsonPayment = jsonCreate(
                    [
                        "subVoucherType",
                        "amountPaid",
                        "modeOfPay",
                        "instNo",
                        "instDate",
                        "clearingBank",
                        "txnNo",
                    ],
                    [
                        paymentVoucher,
                        paidAmount,
                        modeOfPayment,
                        chequeNo,
                        chequeDate,
                        clearingBank,
                        "",
                    ]
                );
            }

            if (modeOfPayment == "UPI") {
                var paidAmount = document.getElementById("paid-amount").value;

                if (paidAmount.length == 0) {
                    $.alert({
                        title: "Warning!",
                        content: "Enter amount paid",
                    });
                    return false;
                }

                var utr = document.getElementById("utr").value;
                var clearingBank = document.getElementById("clearing-bank").value;
                var jsonPayment = jsonCreate(
                    [
                        "subVoucherType",
                        "amountPaid",
                        "modeOfPay",
                        "instNo",
                        "instDate",
                        "clearingBank",
                        "txnNo",
                    ],
                    [paymentVoucher, paidAmount, modeOfPayment, "", "", clearingBank, utr]
                );
            }

            if (modeOfPayment == "Online") {
                var paidAmount = document.getElementById("paid-amount").value;

                if (paidAmount.length == 0) {
                    $.alert({
                        title: "Warning!",
                        content: "Enter amount paid",
                    });
                    return false;
                }

                var utr = document.getElementById("utr").value;
                var clearingBank = document.getElementById("clearing-bank").value;
                var jsonPayment = jsonCreate(
                    [
                        "subVoucherType",
                        "amountPaid",
                        "modeOfPay",
                        "instNo",
                        "instDate",
                        "clearingBank",
                        "txnNo",
                    ],
                    [paymentVoucher, paidAmount, modeOfPayment, "", "", clearingBank, utr]
                );
            }
        }
    </script>
  </body>

</html>