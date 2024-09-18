<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$today  = date('Y-m-d');
$id =   $_GET['id'];

$query_get_master   =   "SELECT m.`vchno`, m.`vchdate`, m.`subvoucherid`, m.`partycode`, m.`amount`, m.`narration`, m.`currentstatus`, m.`supplierinvno`, m.`supplierinvdate`,p.partyname FROM `tbl_transactionmaster` m JOIN tbl_partymaster p WHERE m.vchno='$id'";
$masterData   =   GetData($query_get_master,$dbh);

$query_get_items    =   "SELECT i.`vchno`, i.`itemcode`, p.`itemname`, i.`uom`, i.`itembatch`, i.`itemmrp`, DATE_FORMAT(i.`mfgdate`, '%d-%m-%Y'), DATE_FORMAT(i.`expdate`, '%d-%m-%Y'), i.`itemqty`, i.`itemaltqty`, i.`itemcost`, i.`salerate`, i.`itemamount`, i.`stocktype`, i.`recstatus`, i.`txndate`, i.`category` FROM `tbl_transactionitemdesc` i INNER JOIN tbl_productmaster p ON i.itemcode=p.itemcode WHERE i.vchno='$id'";
$itemData   =   GetData($query_get_items,$dbh);

$vchNo=$masterData[0][0];
$vchDate=$masterData[0][1];
$voucherId=$masterData[0][2];
$partyId=$masterData[0][3];
$total=$masterData[0][4];
$narration=$masterData[0][5];
$status=$masterData[0][6];
$partyName=$masterData[0][9];

$query_get_voucher  =   "SELECT slno,subvchtype FROM tbl_vouchertype WHERE primaryvchtype='Stock Journal'";
$vouchers   =   GetData($query_get_voucher,$dbh);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <title>Stock Journal</title>
</head>


<body id="body-pd" class="body-pd">
    
    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">
            
       
            
            <div class="card-top d-flex align-item-center">
                <div class="heading">Stock Journal</div>
            </div>

            <div class="card-mid">
                <div class="row">

                    <!--PURCHASE VOUCHER DETAILS-->
                    <input type="hidden" id="userId" value="<?php echo $_SESSION["PH_USER_ID"];?>">

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Journal</label>
                            <input type="text" id="journal-id" value="<?php echo $vchNo;?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Date</label>
                            <input type="date" id="purchase-date" value="<?php echo $today;?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Party</label>
                            <input type="text" id="supplier" placeholder="Select Party" value="<?php echo $partyName;?>">
                            <input type="hidden" id="supplier-id" value="<?php echo $partyId;?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div  class="f-control flex-column align-items-start">
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
                    
                    <!-- <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Current Balance</label>
                            <input type="number" id="current-balance" value="0.00" disabled>
                        </div>
                    </div> -->
                    <div class="col-md-2"></div>

                    <!--ITEM DETAILS-->

                    <!-- <div class="sub-heading mt-2">Item Details</div>
                    <div class="col-md-3">
                        <div class="f-control">
                            <input type="text" id="items-search" placeholder="Search Item">
                            <input type="hidden" id="item-id">
                            <input type="hidden" id="item-uom">
                            <input type="hidden" id="item-alt-uom">
                            <input type="hidden" id="item-conversion-rate">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="f-control">
                            <input type="text" id="item-name" placeholder="Item Name" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-2"></div>
                    
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Quantity</label>
                            <input type="number" id="item-qty" placeholder="Quantity">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>UOM</label>
                            <select id="selected-uom">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <div class="f-control flex-column align-items-start">
                            <label>&nbsp;</label>
                            <button type="button" class="btn" onclick="addItem()">Add</button>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="f-control flex-column">
                            
                        </div>
                    </div>-->
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-7">
                    <div class="card-mid">
                        <div class="row">
                            <div class="sub-heading">List Of Items</div>
                            <div class="col-md-12 my-2">
                                <div class="table-responsive">
                                    <table class="table no-padding">
                                        <thead style="font-size: 1.2rem !important;">
                                            <tr>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Uom</th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-body" style="font-size: 1.1rem !important;">
                                            <?php for ($i=0; $i <count($itemData) ; $i++) { ?>
                                                <tr>
                                                    <td><?php echo $itemData[$i][2];?></td>
                                                    <td><?php echo $itemData[$i][8];?></td>
                                                    <td><?php echo $itemData[$i][3];?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>

                                        <input type="hidden" id="item-qty-total">
                                        <!-- <tbody style="font-size: 1.1rem !important;">
                                            <tr>
                                                <td>Total Qty</td>
                                                <td><span id="item-qty-total">0.00</span></td>
                                                
                                            </tr>
                                        </tbody> -->

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
                                <div class="f-control">
                                    <textarea name="" id="notes" placeholder="Add Notes"><?php echo $narration;?></textarea>
                                </div>
                            </div>
                            <div class="d-flex w-100">
                                <button class="btn my-2 ms-auto" onclick="confirmDelete()">Delete</button>
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
                
            </div>
        </div>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script src="../js/nav.js"></script>
    <script src="js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>
    <!--<script src="../js/index.js"></script>-->

    <script> 
        var itemSerialNo = 0;
        var purchaseItems = [];
        //var userId  =   "<?php //echo $_SESSION["CBW_USER_ID"];?>";

        const apiUrl = '../core-api/api-gets.php';

        getDataFromAPI({"action":"all-party"},apiUrl).then(data => {
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
                    $("#supplier-id").val(ui.item.value);
                    return false;
                }
            })
        });

        //Get Items
        getDataFromAPI({"action":"get-items"},apiUrl).then(data => {
            console.log(data);

            //Autocomplete Item
            $("#items-search").autocomplete({
                minLength: 0,
                source: data,
                focus: function(event, ui) {
                    $("#items-search").val(ui.item.label);
                    return false;
                },
                select: function(event, ui) {
                    $("#items-search").val(ui.item.label);
                    $("#item-id").val(ui.item.value);
                    $("#item-name").val(ui.item.label);
                    $("#item-uom").val(ui.item.uom);
                    $("#item-alt-uom").val(ui.item.altuom);
                    $("#item-conversion-rate").val(ui.item.baseconv);
                    createUoms();
                    return false;
                }
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<div>" + item.label + "</div>")
                .appendTo(ul);
            };
        });

        function addItem() {
            var itemId = document.getElementById("item-id").value;
            var itemName = document.getElementById("item-name").value;
            var qty = document.getElementById("item-qty").value;
            var searchItem = document.querySelector("#items-search").value;
            var conversion = document.querySelector("#item-conversion-rate").value;
            var uom = document.querySelector("#item-uom").value;
            var altUom = document.querySelector("#item-alt-uom").value;
            var selectedUom =   document.querySelector("#selected-uom").value;

            if (searchItem == "") {
                $.alert({
                title: WARNING_TITLE,
                content: "Please Select An Item",
                buttons: {
                    ok: document.querySelector("#items-search").focus(),
                },
                });
                return;
            }

            if (qty == "") {
                //checkValidation("items-search", "Please Select An Item");
                $.alert({
                title: WARNING_TITLE,
                content: "Please Add Quantity",
                buttons: {
                    ok: document.querySelector("#item-qty").focus(),
                },
                });
                return;
            }

            if (selectedUom == "") {
                //checkValidation("items-search", "Please Select An Item");
                $.alert({
                title: WARNING_TITLE,
                content: "Please Select UOM",
                buttons: {
                    ok: document.querySelector("#selected-uom").focus(),
                },
                });
                return;
            }

            itemSerialNo += 1;

            var itemArray = [
                itemId,
                itemName,
                qty,
                itemSerialNo,
                conversion,
                uom,
                altUom,
                selectedUom
            ];

            purchaseItems.push(itemArray);
            addItemToTable(itemArray);
            updateTotalItemQty(qty);

            console.log(itemArray);

            clearItemInputs();
        }

        function addItemToTable(arr) {
            var tbody = ``;
            tbody = `<tr data-item-id="${arr[0]}" data-sl-no="${arr[3]}">
                        <td>${arr[1]}</td>
                        <td>${arr[2]}</td>
                        <td><button class="btn-r mx-auto" onclick="deleteRow(${arr[3]}, event)"><i class="bx bx-trash" style="pointer-events: none;"></i></button></td>
                    </tr>`;

            //  console.log(tbody);
            document.querySelector("#item-body").innerHTML += tbody;
        }

        function updateTotalItemQty(value) {
            var qtyTotal = document.getElementById("item-qty-total").value;
            document.getElementById("item-qty-total").value =   parseFloat(qtyTotal) + parseFloat(value);
        }

        function clearItemInputs() {
            document.getElementById("items-search").value = "";
            document.getElementById("item-id").value = "";
            document.getElementById("item-name").value = "";
            document.getElementById("item-qty").value = "";
            document.getElementById("item-uom").value = "";
        }

        function createUoms(){
        //create uoms
            const selectElement = document.querySelector('#selected-uom');

            var uom1    =   $("#item-uom").val();
            var uom2    =   $("#item-alt-uom").val();

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

        function deleteRow(id, event) {
            purchaseItems.forEach((element, index) => {
                if (element[3] == id) {
                    purchaseItems.splice(index, 1);
                }
            });

            tr = event.target.parentElement.parentElement;
            tr.remove();

            var updatedQty = 0;

            purchaseItems.forEach((element) => {
                updatedQty = updatedQty + parseFloat(element[2]);
            });

            //updatedQty = updatedQty;

            document.querySelector("#item-qty-total").value = updatedQty;
        }

        function saveJournal(){
            var totalQty    =   document.querySelector("#item-qty-total").value;
            var suppId    =   document.querySelector("#supplier-id").value;
            var voucherDate    =   document.querySelector("#purchase-date").value;
            var voucherSelect    =   document.querySelector("#sub-vouchers-search");
            var narration    =   document.querySelector("#notes").value;
            var userId    =   document.querySelector("#userId").value;

            var selectedOption = voucherSelect.options[voucherSelect.selectedIndex];
  
            var voucherType = selectedOption.text;
            var voucherId = selectedOption.value;

            if(suppId==""){
                $.alert({
                title: "Information",
                content: "Please select supplier.",
                buttons: {
                    ok: document.querySelector("#supplier").focus(),
                },
                });
                return;
            }

            if(totalQty==0){
                $.alert({
                title: "Information",
                content: "Add item before saving.",
                buttons: {
                    ok: document.querySelector("#items-search").focus(),
                },
                });
                return;
            }

            var jsonDataItems = [];

            purchaseItems.forEach((item) => {
                jsonDataItems.push(
                jsonCreate(
                    [
                    "itemId",
                    "itemName",
                    "qty",
                    "conversion",
                    "uom",
                    "altUom",
                    "selectedUom"
                    ],
                    [
                    item[0],
                    item[1],
                    item[2],
                    item[4],
                    item[5],
                    item[6],
                    item[7]
                    ]
                )
                );
            });

            var jsonData = `{"voucherDate":"${voucherDate}","supplierId":"${suppId}","voucherType":"${voucherType}","voucherId":"${voucherId}","narration":"${narration}","userId":"${userId}"}`;
            //var url = "../../ucwapi/api/voucher.php";
            //ajaxMultiJson(url, token, "save-journal", jsonData,`[${jsonDataItems}]`,'', addJournal_callback, (vchType = null));

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() 
            {
                    if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                    var output = JSON.parse(this.responseText);
                    console.log(output);
                    var msg="";

                    if(output.result=="Success"){
                        msg="Stock Journal Saved.";
                    }else{
                        msg="Stock Journal Could Not Be Saved.";
                    }

                    $.alert({
                        title: "Message",
                        content: msg,
                        buttons: {
                            ok: function () {
                                location.reload();
                            }
                        }
                    });

                }
            };
    
            xmlhttp.open("POST", "../api/api-ledger.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(`jsonDataMaster=${jsonData}&jsonDataItems=[${jsonDataItems}]&action=save-journal`);
        }

        function confirmDelete(){
            $.alert({
                title: "Information",
                content: "Are you sure want to delete?",
                buttons: {
                    yes: function(){
                        deleteJournal();
                    },
                    no: function(){

                    }
                },
                });
        }

        function deleteJournal(){
            var id  =   document.getElementById("journal-id").value;

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() 
            {
                    if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                    var output = JSON.parse(this.responseText);
                    console.log(output);
                    var msg="";

                    if(output.result=="Success"){
                        msg="Stock Journal Deleted.";
                    }else{
                        msg="Stock Journal Could Not Be Deleted.";
                    }

                    $.alert({
                        title: "Message",
                        content: msg,
                        buttons: {
                            ok: function () {
                                window.location.assign("../reports/stock-journal-register.php");
                            }
                        }
                    });

                }
            };
    
            xmlhttp.open("POST", "../api/api-ledger.php", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(`journalid=${id}&action=delete-journal`);
        }
        
    </script>

</body>

</html>