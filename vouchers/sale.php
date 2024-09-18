<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$query_vouchers =   "SELECT slno,subvchtype FROM tbl_vouchertype WHERE primaryvchtype='Sale'";
$vouchers   =   GetData($query_vouchers,$dbh);

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
    <title><?php echo $SALE_TITLE; ?></title>


    <style>
        .dialog {
        display: none;
        position: fixed;
        top: 50;
        left: 30%;
        width: 50%;
        height: 50%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        }

        #iframeContent {
        width: 100%;
        height: 100%;
        border: 2px solid #ccc; 
        box-sizing: border-box; 
        }

        .highlighted {
        background-color: orange;
        color:white;
        }

    </style>

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
                <div class="heading">Manage Sale</div>
            </div>
            <div class="card-mid">
                <div class="row">
                    <div class="sub-heading mt-2">Sale Voucher & Other Details</div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Date</label>
                            <input type="date" id="vchdate" autofocus
                                onkeyup="focusInput('','#vouchername',event)" value="<?php echo date('Y-m-d');?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Voucher Type</label>
                            <select class="form-control" id="vouchername" name="vouchername">
                                <option value="" selected disabled>Select</option>
                                <?php for($i=0; $i<count($vouchers);$i++){?>
                                    <option value="<?php echo $vouchers[$i][0];?>"><?php echo $vouchers[$i][1];?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Party</label>
                            <input type="text" id="supplier" placeholder="Select Party"
                                onkeyup="focusInput('#vouchername','#search-item',event)">
                            <input type="hidden" id="partycode" name="partycode">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Current Balance</label>
                            <input type="number" id="current-balance" value="0.00" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!--
            <div class="card-mid">
                <div class="row">                
                    <div class="col-md-2"></div>
                    <div class="sub-heading mt-2">Sale Item Details</div>
                    <div class="col-md-4">
                        <div class="f-control flex-column align-items-start">
                            <label>Search Item</label>
                            <input type="text" id="search-item" placeholder="Search Item"
                                onkeyup="focusInput('#supplier','#item-batch',event)">
                            
                        </div>
                    </div>
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="f-control flex-column align-items-start">
                            <label>Item Name</label>
                            <input type="text" id="itemname" name="itemname" placeholder="Item Name" disabled>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Batch No.</label>
                            <select class="form-control" id="item-batch" name="item-batch">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Mfg. Date</label>
                            <input type="date" id="item-mfg" onkeyup="focusInput('#item-batch','#item-exp',event)">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Exp. Date</label>
                            <input type="date" id="item-exp" onkeyup="focusInput('#item-mfg','#item-rate',event)">
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Rate</label>
                            <input type="number" id="item-rate" placeholder="Rate"
                                onkeyup="setItemTotal()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Quantity</label>
                            <input type="number" id="item-qty" placeholder="Quantity"
                                onkeyup="setItemTotal()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Uom</label>
                            <select class="form-control" id="uom" name="uom">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Total</label>
                            <input type="number" id="item-total" placeholder="Total" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn my-2" id="item-add"
                            onkeyup="focusInput('#item-total','#search-purchase-item',event)"  onclick="callAddItem()">Add Item</button>
                    </div>
                </div>
            </div>-->
            

            <div class="row">
                <div class="col-md-5">
                    <div class="card-mid" style="height:100%;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="f-control flex-column align-items-start">
                                    <input type="text" id="search-item" placeholder="Search Item">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table no-padding" id="tblSaleItems">
                                <thead style="font-size: 1rem !important;">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Exp Date</th>
                                        <th>Uom</th>
                                        <th>Batch No</th>
                                        <th>Rate</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 1.1rem !important;" id="bodySaleItems">
                                    
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card-mid">
                        <div class="row">
                            <div class="sub-heading mt-2">Selected Item Details</div>

                            <input type="hidden" id="itemcode" name="itemcode">
                            <input type="hidden" id="itemuom" value="">
                            <input type="hidden" id="itemaltuom" value="">
                            <input type="hidden" id="conversion" value="">

                            <div class="col-md-6">
                                <div class="f-control flex-column align-items-start">
                                    <label>Item Name</label>
                                    <input type="text" id="itemname" name="itemname" placeholder="Item Name" disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Batch No.</label>
                                    <select class="form-control" id="item-batch" name="item-batch">

                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <input type="date" style="display:none" type="date" id="item-mfg">
                                    <label>Exp. Date</label>
                                    <input type="date" id="item-exp" >
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Rate</label>
                                    <input type="number" id="item-rate" placeholder="Rate"  onfocus="this.select();"
                                        onkeyup="setItemTotal()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Uom</label>
                                    <select class="form-control" id="uom" name="uom">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Quantity</label>
                                    <input type="number" id="item-qty" placeholder="Quantity" onfocus="this.select();"
                                        onkeyup="setItemTotal()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Total</label>
                                    <input type="number" id="item-total" placeholder="Total" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn my-2" id="item-add"  onclick="callAddItem()">Add Item</button>
                            </div>

                        </div>
                    </div>
                    <div class="card-mid">
                        <div class="table-responsive">
                            <table class="table no-padding" id="tblItems">
                                <thead>
                                    <th>Item Name</th>
                                    <th>Batch No.</th>
                                    <th>Exp Date</th>
                                    <th>Qty</th>
                                    <th>Uom</th>
                                    <th>Rate</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="bodyItems">
                                    
                                </tbody>
                                <tbody id="bodyItemTotal">
                                    <tr>
                                        <th colspan="6">Net Total</th>
                                        <th id="nettotal">₹0</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="card-mid">
                                <div class="row">
                                    <div class="sub-heading">Notes</div>
                                    <div class="col-md-12">
                                        <div class="f-control flex-column align-items-start">
                                            <label></label>
                                            <textarea name="" id="notes" placeholder="Add Notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card-mid">
                                <div class="row">
                                
                                    <!-- <div class="sub-heading">Payment Details</div> -->
                                    <div class="col-md-12">
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
                        <div class="col-md-12" align="right">
                            <button class="btn my-2" onclick="callSave()">Create Sale</button>
                        </div>
                    </div>

                </div>
            </div>

            <div id="dialogWindow" class="dialog">
                <iframe id="iframeContent" src="" frameborder="0"></iframe>
            </div>
            
        </div>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/nav.js">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        $("select").selectmenu();
        
    </script>
    <script src="../js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>
    <script src="../js/keymapping.js"></script>
    <script>

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

        // Get End Points
        const apiUrl = '../core-api/api-gets.php';

        //Get Voucher Types


        //Get Party
        getDataFromAPI({"action":"get-party"},apiUrl).then(data => {
            //console.log(data);

            //Autocomplete Party
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
                    definePaymentType(ui.item.transactiontype);
                    getPartyBalance(ui.item.value,ui.item.label);
                    return false;
                }
            })
        });

        function definePaymentType(transactionType){
            console.log(transactionType);
            if(transactionType=="Cash"){
                $("#credit-btn").prop("disabled", true);
                $("#cheque-btn").prop("disabled", true);
            }
        }

        function getPartyBalance(partycode,partyname){
            getDataFromAPI({"action":"get-party-balance","partycode":partycode},apiUrl).then(data => {
                //console.log(partyname);
                if(partyname!=="cash" && partyname!=="Cash" && partyname!=="CASH"){
                    document.getElementById("current-balance").value = data[0].balance;
                }
                
            });
        }


        //Get Items
        function getAllItems(){
            getDataFromAPI({"action": "get-sale-items"}, apiUrl).then(data => {
                console.log(data);
                addSaleItemsToTable(data);
                //var result  =   JSON.parse(data);
                
            });
        }
        
        function addSaleItemsToTable(data){
            var tbody = document.getElementById("bodySaleItems");

            var newRowHtml="";
            data.forEach(element => {
                newRowHtml += '<tr onclick="fillItems(this)">';
                newRowHtml += '<td style="display: none;">'+element.itemcode+'</td>';
                newRowHtml += '<td>'+element.itemname+'</td>';
                newRowHtml += '<td>'+element.expdate+'</td>';
                newRowHtml += '<td>'+element.uom+'</td>';
                newRowHtml += '<td>'+element.itembatch+'</td>';
                newRowHtml += '<td>'+element.itemmrp+'</td>';
                newRowHtml += '<td style="display: none;">'+element.puom+'</td>';
                newRowHtml += '<td style="display: none;">'+element.altuom+'</td>';
                newRowHtml += '<td style="display: none;">'+element.baseconv+'</td>';
                newRowHtml += '<td style="display: none;">'+element.mfgdate+'</td>';
                newRowHtml += '</tr>';
                
            });
            console.log(newRowHtml);
            

            tbody.innerHTML += newRowHtml;
        }

        function fillItems(row) {
            var cells = row.getElementsByTagName("td");
            var itemnameInput = document.getElementById("itemname");
            var itembatchInput = document.getElementById("item-batch");
            var expDateInput = document.getElementById("item-exp");
            var mfgDateInput = document.getElementById("item-mfg");
            var itemRate = document.getElementById("item-rate");
            var itemQty = document.getElementById("item-qty");
            
            // Fill the input fields with the data from the clicked row
            itemnameInput.value = cells[1].textContent;
            //itembatchInput.value = cells[4].textContent;
            expDateInput.value = cells[2].textContent;
            mfgDateInput.value = cells[9].textContent;
            itemRate.value  =   cells[5].textContent;

            document.getElementById("itemcode").value   =   cells[0].textContent;
            document.getElementById("itemuom").value   =   cells[6].textContent;
            document.getElementById("itemaltuom").value   =   cells[7].textContent;
            document.getElementById("conversion").value   =   cells[8].textContent;

            createBatches(cells[0].textContent,cells[4].textContent,cells[3].textContent);

            //highlight row color
            var tableRows = document.querySelectorAll("#tblSaleItems tbody tr");
            tableRows.forEach(function(row) {
                row.classList.remove("highlighted");
            });

            // Add the 'highlighted' class to the clicked row
            row.classList.add("highlighted");
        }

        

        function filterTableRows() {
            var searchValue = document.getElementById("search-item").value.toLowerCase();
            var tableRows = document.querySelectorAll("#tblSaleItems tbody tr");

            tableRows.forEach(function(row) {
                var itemName = row.cells[1].textContent.toLowerCase();
                if (itemName.includes(searchValue)) {
                row.style.display = "";
                } else {
                row.style.display = "none";
                }
            });
        }

        var searchInput = document.getElementById("search-item");
        searchInput.addEventListener("input", filterTableRows);


        function createBatches(itemid,selectedBatch,selectedUom){
            //console.log(selectedBatch);
            //create batches
            const selectElement = document.querySelector('#item-batch');

            let optionsHTML = '';

            getDataFromAPI({"action": "get-sale-batches","itemid":itemid}, apiUrl).then(data => {
                //console.log(data);
                data.forEach(element => {
                    //console.log(element.itembatch);
                    if(selectedBatch==element.itembatch){
                        optionsHTML += `<option selected value="${element.itembatch}">${element.itembatch}</option>`;
                    }else{
                        optionsHTML += `<option value="${element.itembatch}">${element.itembatch}</option>`;
                    }
                    
                });

                selectElement.innerHTML = optionsHTML;
                $('#item-batch').selectmenu('destroy').selectmenu();
                document.querySelector('#item-batch-button').focus();

                //uom
                createUoms(selectedUom);

            });

        }

        //Set event listener for item batch
        $( "#item-batch" ).selectmenu({
            select: function( event, ui ) {
                var itemId = document.querySelector('#itemcode').value;
                var batchId    =   ui.item.value;

                //Get Ledgers
                getDataFromAPI({"action":"get-batch-details","item":itemId,"batch":batchId},apiUrl).then(data => {
                    console.log(data);
                    document.querySelector('#item-mfg').value = data[0].mfgdate;
                    document.querySelector('#item-exp').value = data[0].expdate;
                    document.querySelector('#item-rate').value = data[0].itemmrp;
                    createUoms();
                    setItemTotal();
                });

                

                
            }
        });

        function setItemTotal(){
            var itemqty = document.getElementById("item-qty").value;
            var itemcost = document.getElementById("item-rate").value;
            if(itemqty.length==0){
                itemqty=0;
            }
            if(itemcost.length==0){
                itemcost=0;
            }
            //console.log(parseFloat(itemqty)*parseFloat(itemcost));
            document.getElementById("item-total").value = parseFloat(itemqty)*parseFloat(itemcost);
        }

        function createUoms(selectedUom){
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
            
            let optionsHTML = '';

            for (var option of options) {
                if(selectedUom==option){
                    optionsHTML += `<option selected>${option}</option>`;
                }else{
                    optionsHTML += `<option>${option}</option>`;
                }
                
            }

            selectElement.innerHTML = optionsHTML;

            $('#uom').selectmenu('refresh');

            //document.querySelector('#uom-button').focus();
        }

        function validateItems(){

            var itemName = document.getElementById("itemname").value;
            var batchNo = document.getElementById("item-batch").value;
            var mfgDate = document.getElementById("item-mfg").value;
            var expDate = document.getElementById("item-exp").value;
            var rate = document.getElementById("item-rate").value;
            var quantity = document.getElementById("item-qty").value;
            var uom = document.getElementById("uom").value;

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
            /* if (mfgDate === "") {
                alert("Please select the Mfg. Date");
                return false;
            } */

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


            // All inputs are valid
            return true;
        }

        function callAddItem(){

            var isValid =   validateItems();

            if(isValid){
                addItem();
            }
        }

        function addItem(){

            //Get all the values
            var itemcode = document.getElementById("itemcode").value;
            var itemname = document.getElementById("itemname").value;
            var uom = document.getElementById("uom").value;
            var itembatch = document.getElementById("item-batch").value;
            var itemmrp = document.getElementById("item-rate").value;
            var mfgdate = document.getElementById("item-mfg").value;
            var expdate = document.getElementById("item-exp").value;
            var itemqty = document.getElementById("item-qty").value;
            var itemamount = document.getElementById("item-total").value;
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

            var tdIdNames  =   ["itemcode","itemname","itembatch","mfgdate","expdate","itemqty","uom","itemmrp","itemamount","itemaltqty","itemcost","btndelete"];
            var columns =   [itemcode,itemname,itembatch,mfgdate,expdate,itemqty,uom,itemmrp,itemamount,itemaltqty,"",""];
            var columnTypes   =   [1,0,0,1,0,0,0,0,0,1,1,3];
            var colSpans    =   [1,1,1,1,1,1,1,1,1,1,1,1];
            /* itemslno +=1;

            //add rows
            addTableRow('bodyItems', columns, columnTypes, tdIdNames, colSpans, itemslno); */

            var existingRow = Array.from(document.querySelectorAll("#bodyItems tr")).find(function(row) {
                return row.querySelector('[data-td-id="itemcode"]').textContent === itemcode;
            });

            console.log(itemcode,existingRow);

            if (existingRow) {
                var itemqtyCell = existingRow.querySelector('[data-td-id="itemqty"]');
                itemqtyCell.textContent = itemqty;
                itemqtyCell.dataset.tdValue = itemqty;

                var itemTotalCell = existingRow.querySelector('[data-td-id="itemamount"]');
                itemTotalCell.textContent = itemamount;
                itemTotalCell.dataset.tdValue = itemamount;

                var itemBatchCell = existingRow.querySelector('[data-td-id="itembatch"]');
                itemBatchCell.textContent = itembatch;
                itemBatchCell.dataset.tdValue = itembatch;

                var itemUomCell = existingRow.querySelector('[data-td-id="uom"]');
                itemUomCell.textContent = uom;
                itemUomCell.dataset.tdValue = uom;

                var itemMrpCell = existingRow.querySelector('[data-td-id="itemmrp"]');
                itemMrpCell.textContent = itemmrp;
                itemMrpCell.dataset.tdValue = itemmrp;

                /* tdIdNames.forEach(function(tdId, index) {
                    existingRow.querySelector(`[data-td-id="${tdId}"]`).textContent = columns[index];
                }); */

            } else {
                // Add a new row
                itemslno += 1;
                addTableRow("bodyItems", columns, columnTypes, tdIdNames, colSpans, itemslno);
            }

            //sum total
            calculateItemTotalAmount();

            //clear form
            clearItems();

        }

        function deleteRow(id, event) {
            //console.log("delete");
            tr = event.target.parentElement.parentElement.parentElement;
            tr.remove();
            calculateItemTotalAmount();
            
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

        function clearItems() {
            document.getElementById("search-item").value = "";
            document.getElementById("itemname").value = "";
            document.getElementById("item-batch").value = "";
            //document.getElementById("item-mfg").value = "";
            document.getElementById("item-exp").value = "";
            document.getElementById("item-rate").value = "";
            document.getElementById("item-qty").value = "";
            
            document.getElementById("itemcode").value = "";
            document.getElementById("item-total").value = "";
            document.getElementById("conversion").value = "";

            $('#item-batch').empty();
            $('#item-batch').selectmenu('refresh');

            $('#uom').empty();
            $('#uom').selectmenu('refresh');

            //Remove Highlight color from Table Items
            var tableRows = document.querySelectorAll("#tblSaleItems tbody tr");
            tableRows.forEach(function(row) {
                row.classList.remove("highlighted");
            });

            //Focus on Search Item
            document.querySelector("#search-item").focus();
        }

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

        function createSale(){

            //var userId  =   "USR12";
            var today   =   todaysDate();

            //Get Purchase Info
            var vchdate = $('#vchdate').val();
            /* var supplierinvno = $('#supplierinvno').val();
            var supplierinvdate = $('#supplierinvdate').val(); */
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
                "subvchtype": "Receipt",
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
                "action":"add-voucher",
                "vchdate":vchdate,
                "supplierinvno":'',
                "supplierinvdate":'',
                "subvoucherid":subVoucherId,
                "vouchername":subVoucherName,
                "partycode":partycode,
                "narration":narration,
                "amount":-grandtotal,
                "currentstatus":currentstatus,
                "createdby":userId,
                "createdon":today,
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
                    alert("Sale saved successfully.");
                }else{
                    alert("Sale could not be saved.");
                }
            });

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

        function validateForm() {

            var voucherType = document.getElementById("vouchername").value;
            var supplier = document.getElementById("supplier").value;
            var currentBalance = document.getElementById("current-balance").value;
            /* var invoiceNo = document.getElementById("supplierinvno").value;
            var invoiceDate = document.getElementById("supplierinvdate").value; */
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
            /* if (isNaN(parseFloat(currentBalance))) {
                alert("Current Balance must be a number");
                return false;
            } */


            if(modeOfPayment===""){
                alert("Payment mode not selected");
                return false;
            }

            //Grand Total
            if (total ==0) {
                alert("Grand Total is not valid");
                return false;
            }

            return true;
        }

        function callSave(){
            var isValid =   validateForm();
            if(isValid){
                createSale();
            }
        }


        window.onkeydown = keydown;
        function keydown(evt) {

            if (!evt) evt = event;

            if (event.altKey && event.key === "s") {
                callSave();
            }

            if (event.altKey && event.key === "v") {
                loadPage("../settings/voucher-master.php");
            }

            if (event.altKey && event.key === "a") {
                loadPage("../party/party.php");
            }

            if (event.altKey && event.key === "t") {
                loadPage("../items/items.php");
            }

            if (event.keyCode === 27) {
                closeDialog();
            }

        }

        function loadPage(url){
            var dialog = document.getElementById("dialogWindow");
            var iframe = document.getElementById("iframeContent");
            
            // Set the source URL for the iframe
            iframe.src = url;
            
            // Show the dialog
            dialog.style.display = "block";
        }

        function closeDialog() {
            var dialog = document.getElementById("dialogWindow");
            var iframe = document.getElementById("iframeContent");
            
            // Clear the source URL of the iframe
            iframe.src = "";
            
            // Hide the dialog
            dialog.style.display = "none";
        }


        var bt = document.getElementById('item-batch-button');
        var itemQty = document.getElementById('item-qty');
        bt.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                itemQty.focus();
            }
        });


        //UOM Enter Key
        var uom = document.getElementById('uom-button');
        var itemQty = document.getElementById('item-qty');
        uom.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                itemQty.focus();
            }
        });

        //Qty Enter Key
        var itemAdd = document.getElementById('item-add');
        itemQty.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                itemAdd.focus();
            }
        });

        //Edit Items
        document.getElementById('bodyItems').addEventListener('click', function(event) {
            const clickedRow = event.target.closest('tr');
            
            const itemId = clickedRow.querySelector('[data-td-id="itemcode"]').textContent;
            const itemName = clickedRow.querySelector('[data-td-id="itemname"]').textContent;
            const batchNo = clickedRow.querySelector('[data-td-id="itembatch"]').textContent;
            const expDate = clickedRow.querySelector('[data-td-id="expdate"]').textContent;
            const uom = clickedRow.querySelector('[data-td-id="uom"]').textContent;
            const rate = clickedRow.querySelector('[data-td-id="itemmrp"]').textContent;
            const qty = clickedRow.querySelector('[data-td-id="itemqty"]').textContent;
            const total = clickedRow.querySelector('[data-td-id="itemamount"]').textContent;

            document.getElementById('itemcode').value = itemId;
            document.getElementById('itemname').value = itemName;
            document.getElementById('item-rate').value = rate;
            document.getElementById('item-qty').value = qty;
            document.getElementById('item-total').value = total;

            // Convert the date to the required format "yyyy-MM-dd"
            const formattedExpDate = convertDateFormat(expDate, 'dd-MM-yyyy', 'yyyy-MM-dd');
            document.getElementById('item-exp').value = formattedExpDate;

            createBatches(itemId,batchNo,uom);

            

            //document.getElementById('itemuom').value = uom;
            });

        // Function to convert date format
        function convertDateFormat(dateString, currentFormat, targetFormat) {
            const dateObject = new Date(dateString);
            const options = { year: targetFormat.includes('yyyy') ? 'numeric' : undefined, month: '2-digit', day: '2-digit' };
            const formattedDate = dateObject.toLocaleDateString('en-GB', options);

            if (currentFormat.includes('dd')) {
                const day = formattedDate.substring(0, 2);
                const month = formattedDate.substring(3, 5);
                const year = formattedDate.substring(6, 10);
                return targetFormat.replace('dd', day).replace('MM', month).replace('yyyy', year);
            }

            return formattedDate;
        }



        getAllItems();
    </script>
</body>

</html>