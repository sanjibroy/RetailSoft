<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$voucherId  =   $_GET["id"];

$query_get_master   =   "SELECT m.`vchno`, m.`vchdate`, m.`subvoucherid`, m.`partycode`, m.`amount`, m.`narration`, m.`currentstatus`, m.`supplierinvno`, m.`supplierinvdate`,p.partyname FROM `tbl_transactionmaster` m JOIN tbl_partymaster p WHERE m.vchno='$voucherId'";
$masterData   =   GetData($query_get_master,$dbh);

$query_get_items    =   "SELECT i.`vchno`, i.`itemcode`, p.`itemname`, i.`uom`, i.`itembatch`, i.`itemmrp`, DATE_FORMAT(i.`mfgdate`, '%d-%m-%Y'), DATE_FORMAT(i.`expdate`, '%d-%m-%Y'), i.`itemqty`, i.`itemaltqty`, i.`itemcost`, i.`salerate`, i.`itemamount`, i.`stocktype`, i.`recstatus`, i.`txndate`, i.`category` FROM `tbl_transactionitemdesc` i INNER JOIN tbl_productmaster p ON i.itemcode=p.itemcode WHERE i.vchno='$voucherId'";
$itemData   =   GetData($query_get_items,$dbh);
//echo $query_get_items;

$query_get_ledger   =   "SELECT `vchno`, `ledgerid`, `ledrate`, `ledamount` FROM `tbl_transactionledgerdesc` WHERE vchno='$voucherId'";
$ledgerData   =   GetData($query_get_ledger,$dbh);

$vchNo=$masterData[0][0];
$vchDate=$masterData[0][1];
$voucherId=$masterData[0][2];
$partyId=$masterData[0][3];
$total=$masterData[0][4];
$narration=$masterData[0][5];
$status=$masterData[0][6];
$supplierInv=$masterData[0][7];
$supplierDate=$masterData[0][8];
$partyName=$masterData[0][9];



$query_vouchers =   "SELECT slno,subvchtype FROM tbl_vouchertype WHERE primaryvchtype='Purchase'";
$vouchers   =   GetData($query_vouchers,$dbh);

//$vouchers=[['PUR1','purchase']];
//$uoms=['packet','tabs'];
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
    <link rel="stylesheet" href="../assets/css/main.css">

    <title>Edit Purchase</title>

</head>


<body id="body-pd" class="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>
    
    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">
            <div class="card-top d-flex align-item-center">
                <!-- <select id="item-wise">
            <option selected>Item Wise</option>
            <option>Category Wise</option>
          </select> -->
                <div class="heading">Edit Purchase</div>
            </div>
            <div class="card-mid">
                <div class="row">
                    <div class="sub-heading mt-2">Purchase Voucher & Other Details</div>

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Voucher No</label>
                            <input type="text" id="vchno" name="vchno" value="<?php echo $vchNo;?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Date</label>
                            <input type="date" name="vchdate" id="vchdate" value="<?php echo $vchDate;?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Voucher Type</label>
                            <select class="form-control" id="vouchername" name="vouchername">
                                <option value="" selected disabled>Select</option>
                                <?php for($i=0; $i<count($vouchers);$i++){
                                    if($vouchers[$i][0]==$voucherId){
                                    ?>
                                    <option selected value="<?php echo $vouchers[$i][0];?>"><?php echo $vouchers[$i][1];?></option>
                                <?php 
                                    }else{?>
                                    <option value="<?php echo $vouchers[$i][0];?>"><?php echo $vouchers[$i][1];?></option>
                                <?php }
                            }?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Supplier</label>
                            <input type="text" id="supplier" placeholder="Select Supplier" value="<?php echo $partyName;?>">
                            <input type="hidden" id="partycode" name="partycode" value="<?php echo $partyId;?>">
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Current Balance</label>
                            <input type="number" id="current-balance" name="" value="0.00" disabled>
                        </div>
                    </div>
                    <div class="col-md-2"></div>

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Invoice No</label>
                            <input type="text" id="supplierinvno" placeholder="Invoice No" value="<?php echo $supplierInv;?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Invoice Date</label>
                            <input type="date" id="supplierinvdate" placeholder="Invoice Date" value="<?php echo $supplierDate;?>">
                        </div>
                    </div>

                    <div class="sub-heading mt-2">Item Details</div>
                    <div class="col-md-4">
                        <div class="f-control flex-column align-items-start">
                            <label>Search Item</label>
                            <input type="text" id="search-item" placeholder="Search Item">
                            <input type="hidden" id="itemcode" name="itemcode">
                            <input type="hidden" id="itemuom" value="">
                            <input type="hidden" id="itemaltuom" value="">
                            <input type="hidden" id="conversion" value="">
                        </div>
                    </div>
                    <div class="col-md-8">

                    </div>
                    <div class="col-md-4">
                        <div class="f-control flex-column align-items-start">
                            <label>Item Name</label>
                            <input type="text" id="itemname" name="itemname" placeholder="Item Name" disabled>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Batch No.</label>
                            <input type="text" id="itembatch" name="itembatch" placeholder="Batch No">
                        </div>
                    </div>
                    <div class="col-md-2">
                      <div class="f-control flex-column align-items-start">
                          <label>Mfg. Date</label>
                        <input type="date" id="mfgdate" name="mfgdate">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="f-control flex-column align-items-start">
                        <label>Exp. Date</label>
                        <input type="date" id="expdate" name="expdate">
                        </div>
                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Rate</label>
                            <input type="number" id="itemcost" name="itemcost" placeholder="Rate" onkeyup="setItemTotal()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Quantity</label>
                            <input type="number" id="itemqty" name="itemqty" placeholder="Quantity" onkeyup="setItemTotal()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Uom</label>
                            <select class="form-control" id="uom" name="uom">
                                
                            </select>
                        </div>
                    </div>

                    

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>MRP</label>
                            <input type="number" id="itemmrp" name="itemmrp" placeholder="MRP">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Total</label>
                            <input type="number" id="itemamount" name="itemamount" placeholder="Total" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn my-2" onclick="callAddItem()">Add Item</button>
                    </div>
                </div>
            </div>
            <div class="card-mid">
              <div class="table-responsive">
                <table class="table no-padding" id="tblItems">
                    <thead>
                        <th>Item Name</th>
                        <th>Batch No.</th>
                        <th>Mfg Date</th>
                        <th>Exp Date</th>
                        <th>Qty</th>
                        <th>Uom</th>
                        <th>Rate</th>
                        <th>Total</th>
                        <th>Action</th>
                    </thead>
                    <tbody id="bodyItems">
                      <?php 
                      for ($i=0; $i <count($itemData) ; $i++) { ?>
                        <tr>
                            <td data-td-type="td" data-td-id="itemcode" style="display: none;"><?php echo $itemData[$i][1];?></td>
                            <td data-td-type="td" data-td-id="itemname"><?php echo $itemData[$i][2];?></td>
                            <td data-td-type="td" data-td-id="itembatch"><?php echo $itemData[$i][4];?></td>
                            <td data-td-type="td" data-td-id="mfgdate"><?php echo $itemData[$i][6];?></td>
                            <td data-td-type="td" data-td-id="expdate"><?php echo $itemData[$i][7];?></td>
                            <td data-td-type="td" data-td-id="itemqty"><?php echo $itemData[$i][8];?></td>
                            <td data-td-type="td" data-td-id="uom"><?php echo $itemData[$i][3];?></td>
                            <td data-td-type="td" data-td-id="itemcost"><?php echo $itemData[$i][10];?></td>
                            <td data-td-type="td" data-td-id="itemamount"><?php echo $itemData[$i][12];?></td>
                            <td data-td-type="td" data-td-id="itemaltqty" style="display: none;"><?php echo $itemData[$i][9];?></td>
                            <td data-td-type="td" data-td-id="itemmrp" style="display: none;"><?php echo $itemData[$i][5];?></td>
                            <td data-td-type="button"><button onclick="deleteRow(1, event)" class="btn-r"><i class="bx bxs-trash"></i></button></td>
                        </tr>
                    <?php  }
                      ?>
                    </tbody>
                    <tbody id="bodyItemTotal">
                      <tr>
                        <th colspan="7">Net Total</th>
                        <th id="nettotal">₹0</th>
                      </tr>
                    </tbody>
                </table>
            </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="card-mid">
                        <div class="row">
                            <div class="col-md-12 my-2">
                                <div class="table-responsive">
                                    <table class="table no-padding" id="tblLedgers">
                                        <thead style="font-size: 1.2rem !important;">
                                            <tr>
                                                <th colspan="3">Ledger Name</th>
                                                <th>&nbsp;</th>
                                                <th width="15%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 1.1rem !important;" id="bodyLedgers">
                                            <!-- <tr>
                                                <td colspan="3">Disc</td>
                                                <td>-</td>
                                                <td><div class="f-control flex-column align-items-start">
                                                    <label></label>
                                                    <input type="number" name="" value="0" disabled>
                                                </div></td>
                                            </tr> -->
                                        </tbody>
                                        <tbody id="bodyLedgerTotal">
                                            <tr>
                                                <td colspan="3">Grand Total</td>
                                                <td></td>
                                                <td><div class="f-control flex-column align-items-start">
                                                    <label></label>
                                                    <input type="number" id="grandtotal" name="" value="0" disabled>
                                                </div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card-mid">
                        <div class="row">
                            <div class="sub-heading">Notes</div>
                            <div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    <label></label>
                                    <textarea name="" id="notes" placeholder="Add Notes"></textarea>
                                </div>
                            </div>
                            <div class="sub-heading" style="display:none">Payment Details</div>
                            <div class="col-md-12" style="display:none">
                                <div class="payment">
                                    <button class="payment-btn" id="cash-btn" onclick="createInputs('Cash')">
                                        <i class="bx bx-money"></i>
                                        <span>Cash</span>
                                    </button>
                                    <button class="payment-btn" id="credit-btn" onclick="createInputs('Credit')">
                                        <i class="bx bx-credit-card"></i>
                                        <span>Credit</span>
                                    </button>
                                    <button class="payment-btn" id="cheque-btn" onclick="createInputs('Cheque')">
                                        <i class="bx bx-credit-card-front"></i>
                                        <span>Cheque</span>
                                    </button>
                                    <button class="payment-btn" id="upi-btn" onclick="createInputs('UPI')">
                                        <i class="fab fa-google-pay"></i>
                                        <span>BHIM UPI</span>
                                    </button>
                                    <button class="payment-btn" id="net-banking-btn" onclick="createInputs('Net Banking')">
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
                    <button class="btn my-2" onclick="callSave()">Update Purchase</button>
                </div>
            </div>
        </div>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/nav.js"></script>
    <script src="../js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        $("select").selectmenu();

        //Variables

        var subVoucherName  =   "";
        var subVoucherId    =   "";
        var itemslno =   0;
        var ledgerslno  =   0;
        var netTotal = 0;
        var grandTotal  =   0;
        var modeOfPayment   =   "";
        var userId  =   "<?php echo $_SESSION["PH_USER_ID"];?>";
        var ledgers =   [];

        calculateItemTotalAmount();


    // Get End Points
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
        

    //Get Items
    getDataFromAPI({"action":"get-items"},apiUrl).then(data => {
        console.log(data);

        //Autocomplete Item
        $("#search-item").autocomplete({
            minLength: 0,
            source: data,
            focus: function(event, ui) {
                $("#search-item").val(ui.item.label);
                return false;
            },
            select: function(event, ui) {
                $("#search-item").val(ui.item.label);
                $("#itemcode").val(ui.item.value);
                $("#itemname").val(ui.item.label);
                $("#itemuom").val(ui.item.uom);
                $("#itemaltuom").val(ui.item.altuom);
                $("#conversion").val(ui.item.baseconv);
                createUoms();
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            return $("<li>")
            .append("<div>" + item.label + "</div>")
            .appendTo(ul);
        };
    });


    //Set event listener for voucher
    $( "#vouchername" ).selectmenu({
        select: function( event, ui ) {
            var selectedValue = $(this).val();
            subVoucherName  =   ui.item.label;
            subVoucherId    =   ui.item.value;

            //Get Ledgers
            getDataFromAPI({"action":"get-ledgers","voucher":subVoucherId},apiUrl).then(data => {
                ledgers =data;
                addLedger(ledgers);
            });

            

            
        }
    });


    //Sample Datas

    function createUoms(){
        //create uoms
        const selectElement = document.querySelector('#uom');

        var uom1    =   $("#itemuom").val();
        var uom2    =   $("#itemaltuom").val();

        var options=[];

        if(uom1===uom2){
            // Create an array of options
            options = [uom1];
        }else{
            // Create an array of options
            options = [uom1, uom2];
        }

        console.log(options);
        
        // Create an empty string to hold the option tags
        let optionsHTML = '';

        // Loop over the options array
        for (var option of options) {
        // Concatenate an option tag to the optionsHTML string
        optionsHTML += `<option>${option}</option>`;
        }

        // Set the innerHTML of the select element to the optionsHTML string
        selectElement.innerHTML = optionsHTML;

        $('#uom').selectmenu('refresh');
    }

    function setItemTotal(){
        var itemqty = document.getElementById("itemqty").value;
        var itemcost = document.getElementById("itemcost").value;
        if(itemqty.length==0){
            itemqty=0;
        }
        if(itemcost.length==0){
            itemcost=0;
        }
        //console.log(parseFloat(itemqty)*parseFloat(itemcost));
        document.getElementById("itemamount").value = parseFloat(itemqty)*parseFloat(itemcost);
    }


    function addItem(){

        //Get all the values
        var itemcode = document.getElementById("itemcode").value;
        var itemname = document.getElementById("itemname").value;
        var uom = document.getElementById("uom").value;
        var itembatch = document.getElementById("itembatch").value;
        var itemmrp = document.getElementById("itemmrp").value;
        var mfgdate = document.getElementById("mfgdate").value;
        var expdate = document.getElementById("expdate").value;
        var itemqty = document.getElementById("itemqty").value;
        var itemcost = document.getElementById("itemcost").value;
        var itemamount = document.getElementById("itemamount").value;
        var conversion = document.getElementById("conversion").value;
        var itemuom = document.getElementById("itemuom").value;
        var itemaltuom = document.getElementById("itemaltuom").value;

        if(uom==itemuom){
            var itemaltqty  =   parseFloat(itemqty)*parseFloat(conversion);
        }else{
            var itemaltqty  =   itemqty;
        }

        //format date
        mfgdate=formatDate(mfgdate);
        expdate=formatDate(expdate);
        
        var tdIdNames  =   ["itemcode","itemname","itembatch","mfgdate","expdate","itemqty","uom","itemcost","itemamount","itemaltqty","itemmrp","btndelete"];
        var columns =   [itemcode,itemname,itembatch,mfgdate,expdate,itemqty,uom,itemcost,itemamount,itemaltqty,itemmrp,""];
        var columnTypes   =   [1,0,0,0,0,0,0,0,0,1,1,3];
        var colSpans    =   [1,1,1,1,1,1,1,1,1,1,1,1];
        itemslno +=1;

        //add rows
        addTableRow('bodyItems', columns, columnTypes, tdIdNames, colSpans, itemslno);

        //sum total
        calculateItemTotalAmount();

        //clear form
        clearItems();

    }

    function addLedger(ledger){
        //console.log(id);
        console.log(ledgers);

        document.getElementById('bodyLedgers').innerHTML="";

        ledger.forEach(row => {
            var tdIdNames   =   ["ledgerid","ledgername","caltype","ledrate","ledamount"];
            var columns =   [row.id,row.name,row.caltype,row.rate,""];
            var columnTypes   =   [1,0,0,1,2];
            var colSpans    =   [1,3,1,1,1];
            ledgerslno +=1;
            //add rows
            addTableRow('bodyLedgers', columns, columnTypes, tdIdNames,colSpans,ledgerslno);

        });
    }

    var table = document.getElementById("tblItems");
    
    table.addEventListener("click", function(e) {
    if (e.target && e.target.matches("button.btn-r")) {
        var row = e.target.closest("tr");
        row.parentNode.removeChild(row);
    }
    });

    function deleteRow(id, event) {
        //console.log("delete");
        tr = event.target.parentElement.parentElement.parentElement;
        tr.remove();
        calculateItemTotalAmount();
        /*  purchaseItems.forEach((element, index) => {
            if (element[16] == id) {
            purchaseItems.splice(index, 1);
            }
        });
        */
    }

    function calculateItemTotalAmount(){
        netTotal = 0;
        $("#bodyItems tr").each(function() {
            var cellValue = $(this).find("td:nth-child(9)").text();
            var amount = parseFloat(cellValue.trim().replace(/[^0-9.-]+/g, ""));

            if (!isNaN(amount)) {
                netTotal += amount;
            }
        });

        //console.log(sum);
        document.getElementById("nettotal").innerHTML =  "₹"+netTotal;

        calculateGrandTotal();
    }

    function calculateGrandTotal(){
        grandTotal  =   0;
        $("#bodyLedgers tr").each(function() {
        var symbol = $(this).find("td:nth-child(3)").text().trim();
        var inputValue = $(this).find("td:nth-child(5) input").val();
        var amount = parseFloat(inputValue.trim());

        if (!isNaN(amount)) {
            if (symbol === "+") {
                grandTotal += amount;
            } else if (symbol === "-") {
                grandTotal -= amount;
            }
        }
        });
        console.log(grandTotal);
        grandTotal    += parseFloat(netTotal);

        $("#bodyLedgerTotal input").val(grandTotal);
    }

    function createInputs(inputType) {
        var paymentInput = '';
        var grandTotalInput = document.querySelector("#grandtotal");
        var gTotal = parseFloat(grandTotalInput.value); // Parse the value as a float

        if (isNaN(gTotal)) {
            gTotal = 0; // Set default value to 0 if parsing fails
        }

        if (inputType == "Cash") {
            modeOfPayment = "Cash";
            paymentInput = `<div class="col-md-12">
                            <div class="f-control flex-column align-items-start">
                                <label>Amount Paid</label>
                                <input type="number" id="paid-amount" placeholder="Amount Paid" value="${gTotal}" onfocus="this.select()">
                            </div>
                        </div>`;
        } else if (inputType == "Credit") {
            modeOfPayment = "Credit";
            paymentInput = `<div class="col-md-12"><br>
            <label>Credit Mode Selected</label>
            </div>`;
        } else if (inputType == "Cheque") {
            modeOfPayment = "Cheque";
            paymentInput = `<div class="col-md-12">
                            <div class="f-control flex-column align-items-start">
                                <label>Amount Paid</label>
                                <input type="number" id="paid-amount" placeholder="Amount Paid" value="${gTotal}" onfocus="this.select()">
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                <label>Cheque No.</label>
                                <input type="text" id="cheque-no" placeholder="Cheque No.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                <label>Cheque Date</label>
                                <input type="date" id="cheque-date">
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="f-control flex-column align-items-start">
                                <label>Clearing Bank</label>
                                <input type="text" id="clearing-bank" placeholder="Clearing Bank">
                            </div>
                        </div>`;
        } else if (inputType == "UPI") {
            modeOfPayment = "UPI";
            paymentInput = `<div class="col-md-12">
                            <div class="f-control flex-column align-items-start">
                                <label>Amount Paid</label>
                                <input type="number" id="paid-amount" placeholder="Amount Paid" value="${gTotal}" onfocus="this.select()">
                            </div>
                            </div>
                        <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                <label>UTR#</label>
                                <input type="text" id="utr" placeholder="UTR#">
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="f-control flex-column align-items-start">
                                <label>Clearing Bank</label>
                                <input type="text" id="clearing-bank" placeholder="Clearing Bank">
                            </div>
                        </div>`;
        } else if (inputType == "Net Banking") {
            modeOfPayment = "Online";
            paymentInput = `<div class="col-md-12">
                            <div class="f-control flex-column align-items-start">
                                <label>Amount Paid</label>
                                <input type="number" id="paid-amount" placeholder="Amount Paid" value="${gTotal}" onfocus="this.select()">
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="f-control flex-column align-items-start">
                                <label>UTR#</label>
                                <input type="text" id="utr" placeholder="UTR#">
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="f-control flex-column align-items-start">
                                <label>Clearing Bank</label>
                                <input type="text" id="clearing-bank" placeholder="Clearing Bank">
                            </div>
                        </div>`;
        }
        document.querySelector("#payment-inputs").innerHTML = paymentInput;
    }

    function createPurchase(){

        //var userId  =   "USR12";
        var today   =   todaysDate();

        //Get Purchase Info
        var vchno = $('#vchno').val();
        var vchdate = $('#vchdate').val();
        var supplierinvno = $('#supplierinvno').val();
        var supplierinvdate = $('#supplierinvdate').val();
        var grandtotal  = $('#grandtotal').val();
        var partycode = $('#partycode').val();
        var narration = $('#notes').val();
        var currentstatus   =   "1";

        //Get Items
        var itemData = convertTableToJSON('tblItems','#bodyItems');

        //Get Ledgers
        var ledgerData = convertTableToJSON('tblLedgers','#bodyLedgers');
        
        //payment
        var currentstatus = "1";
        var txntype = "againstref";
        var issettled = "";
        var firsttxn = "1";
        var receivedamt = "";
        var instno = "";
        var instdate = "";
        var instbank = "";
        var clearingbank = "";
        var txnno = "";

        
        // Variables with the same name as the keys
        if (modeOfPayment == "Cash") {
            receivedamt = document.getElementById("paid-amount").value;
        } else if (modeOfPayment == "Cheque") {
            receivedamt = document.getElementById("paid-amount").value;
            instno = document.getElementById("cheque-no").value;
            instdate = document.getElementById("cheque-date").value;
            instbank = document.getElementById("clearing-bank").value;
        } else if (modeOfPayment == "UPI") {
            receivedamt = document.getElementById("paid-amount").value;
            txnno = document.getElementById("utr").value;
            clearingbank = document.getElementById("clearing-bank").value;
        } else if (modeOfPayment == "Net Banking") {
            receivedamt = document.getElementById("paid-amount").value;
            txnno = document.getElementById("utr").value;
            clearingbank = document.getElementById("clearing-bank").value;
        }
        
        

        var paymentData ={
            "subvchtype": "Payment",
            "receivedamt": receivedamt,
            "narration": "",
            "currentstatus": currentstatus,
            "txntype": txntype,
            "issettled": issettled,
            "mop": modeOfPayment,
            "instno": instno,
            "instdate": instdate,
            "instbank": instbank,
            "txnno": txnno,
            "clearingbank": clearingbank,
            "firsttxn": firsttxn
        };

        //console.log(itemData,ledgerData);

        //Set JSON data to be sent
        var data    =   {
            "action":"update-voucher",
            "vchno":vchno,
            "vchdate":vchdate,
            "supplierinvno":supplierinvno,
            "supplierinvdate":supplierinvdate,
            "subvoucherid":subVoucherId,
            "vouchername":subVoucherName,
            "partycode":partycode,
            "narration":narration,
            "amount":grandtotal,
            "modifiedby":userId,
            "modifiedon":today,
            "itemData":itemData,
            "ledgerData":ledgerData,
            "paymentData":paymentData
        };

        console.log(data);

        // API endpoint URL
        const apiUrl = '../core-api/api-voucher.php';

        sendDataToAPI(data,apiUrl).then(result=>{
            console.log("Result",result);
            if(result.status=="success"){
                alert("Purchase updated successfully.");
            }else{
                alert("Purchase could not be updated.");
            }
        });

    }

    function validateForm() {

        var voucherType = document.getElementById("vouchername").value;
        var supplier = document.getElementById("supplier").value;
        var currentBalance = document.getElementById("current-balance").value;
        var invoiceNo = document.getElementById("supplierinvno").value;
        var invoiceDate = document.getElementById("supplierinvdate").value;
        var total = document.getElementById("grandtotal").value;

        // Check if Voucher Type is selected
        if (voucherType === "") {
            alert("Please select a Voucher Type");
            return false;
        }

        // Check if Supplier is entered
        if (supplier === "") {
            alert("Please enter the Supplier");
            return false;
        }

        // Check if Current Balance is a valid number
        if (isNaN(parseFloat(currentBalance))) {
            alert("Current Balance must be a number");
            return false;
        }

        // Check if Invoice No is entered
        if (invoiceNo === "") {
            alert("Please enter the Invoice No");
            return  false;
        }

        // Check if Invoice Date is selected
        if (invoiceDate === "") {
            alert("Please select the Invoice Date");
            return false;
        }

        /* if(modeOfPayment===""){
            alert("Payment mode not selected");
            return false;
        } */

        //Grand Total
        if (total ==0) {
            alert("Grand Total is not valid");
            return false;
        }

        return true;
    }

    function validateItems() {

        var itemName = document.getElementById("itemname").value;
        var batchNo = document.getElementById("itembatch").value;
        var mfgDate = document.getElementById("mfgdate").value;
        var expDate = document.getElementById("expdate").value;
        var rate = document.getElementById("itemcost").value;
        var quantity = document.getElementById("itemqty").value;
        var uom = document.getElementById("uom").value;
        var mrp = document.getElementById("itemmrp").value;

        // Check if Item Name is entered
        if (itemName === "") {
            alert("Please enter the Item Name");
            return false;
        }

        // Check if Batch No. is entered
        if (batchNo === "") {
            alert("Please enter the Batch No.");
            return false;
        }

        // Check if Mfg. Date is selected
        if (mfgDate === "") {
            alert("Please select the Mfg. Date");
            return false;
        }

        // Check if Exp. Date is selected
        if (expDate === "") {
            alert("Please select the Exp. Date");
            return false;
        }

        // Check if Rate is a valid number
        if (isNaN(parseFloat(rate))) {
            alert("Rate must be a number");
            return false;
        }

        // Check if Quantity is a valid number
        if (isNaN(parseInt(quantity))) {
            alert("Quantity must be a number");
            return false;
        }

        // Check if Uom is selected
        if (uom === "") {
            alert("Please select the Uom");
            return false;
        }

        // Check if MRP is a valid number
        if (isNaN(parseFloat(mrp))) {
            alert("MRP must be a number");
            return false;
        }

        // All inputs are valid
        return true;
    }

    function callAddItem(){

        var isValid =   validateItems();

        if(isValid){
            addItem();
        }
    }


    function callSave(){

        var isValid =   validateForm();

        if(isValid){
            createPurchase();
        }
    }

    // Function to clear the item inputs
    function clearItems() {
        document.getElementById("itemname").value = "";
        document.getElementById("itembatch").value = "";
        document.getElementById("mfgdate").value = "";
        document.getElementById("expdate").value = "";
        document.getElementById("itemcost").value = "";
        document.getElementById("itemqty").value = "";
        document.getElementById("uom").value = "";
        document.getElementById("itemmrp").value = "";
        document.getElementById("itemamount").value = "";
    }

    

    </script>
    
</body>

</html>