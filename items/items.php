<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

$uoms = array("Case","Btl");

$party           =   getPartyId($dbh);




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
  <title><?php echo $ITEMS_TITLE; ?></title>
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
          <div class="heading">Manage Item</div>
        </div>
        <div class="card-mid">
          <div class="row">
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Item Name</label>
                <input type="text" placeholder="Item Name" id="txtItemName">
              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>
                  Item Short Name</label>
                <input type="text" placeholder="Item Short Name" id="txtItemShort">
              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Brand Name</label>
                <input type="text" placeholder="Brand Name" id="txtBrand">
              </div>
            </div>
            <div class="col-md-6">
              <div class="f-control flex-column align-items-start">
                <label>Item Description</label>
                <textarea placeholder="Item Description" id="txtDescription"></textarea>
              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Select Category</label>
                <select id="txtCategory">
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Sale Price</label>
                <input type="text" placeholder="Sale Price" id="txtPrice">
              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>UOM</label>
                <!-- <select id="txtUom">
                    <option value="" selected disabled>Select</option>
                  <?php //for($i=0; $i<count($uoms);$i++){?>
                    <option><?php //echo $uoms[$i];?></option>
                  <?php //}?>
                </select> -->

                <input placeholder="UOM" type="text" name="txtUom" id="txtUom">

              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Alt UOM</label>
                <!-- <select id="txtAltUom">
                    <option value="" selected disabled>Select</option>
                  <?php //for($i=0; $i<count($uoms);$i++){?>
                    <option><?php //echo $uoms[$i];?></option>
                  <?php //}?>
                </select> -->

                <input placeholder="Alt UOM" type="text" name="txtAltUom" id="txtAltUom">

              </div>
            </div>
            <div class="col-md-2">
              <div class="f-control flex-column align-items-start">
                <label>Conversion</label>
                <input type="number" placeholder="Conversion" id="txtConversion">
              </div>
            </div>
            <!-- <div class="col-md-2 mt-auto mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onclick="focusInput(event)">
                <div class="f-control d-inline">
                  <label class="form-check-label" for="flexCheckDefault">
                    Batch No.
                  </label>
                </div>
              </div>
            </div> -->
          </div>
      </div>
      <div class="card-mid">
        <div class="sub-heading mb-2">Opening Balance Details</div>
        <div class="row">
          <div class="col-md-2">
            <div class="f-control flex-column align-items-start">
              <label>Quantity</label>
              <input type="number" id="txtQty" placeholder="Quantity" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="f-control flex-column align-items-start">
              <label>Alt Quantity</label>
              <input type="number" id="txtAltQty" placeholder="Alt Quantity" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="f-control flex-column align-items-start">
              <label>Rate</label>
              <input type="number" id="txtRate" placeholder="Rate" readonly>
            </div>
          </div>
          <div class="col-md-2">
            <div class="f-control flex-column align-items-start">
              <label>Amount</label>
              <input type="number" id="txtAmt" placeholder="Amount" readonly>
            </div>
          </div>
          <div class="col-md-2 mt-auto mb-2">
            <button class="btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Next</button>
          </div>
        </div>
      </div>
      <div class="card-bot">
        <button class="btn mt-3" onclick="saveItem()">Save Item</button>
        
      </div>
      </div>
    </main>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Opening Balance Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row" id="opDivRow">
              
              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                  <label for="batchNo">Batch No.</label>
                  <input type="text" id="batchNo" name="batchNo" placeholder="">
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label for="mfg">Mfg. Date</label>
                    <input type="month" id="mfg" name="mfg" placeholder="">
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label for="exp">Exp. Date</label>
                    <input type="month" id="exp" name="exp" placeholder="">
                </div>
              </div>
              
              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                    <label for="qty">Qty</label>
                    <input type="number" id="qty" name="qty" placeholder="">
                </div>
              </div>
              
              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                    <label for="alt qty">Alt Qty</label>
                    <input type="number" id="altQty" name="altQty" placeholder="">
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label for="party">Party</label>
                    <select class="form-control" id="opParty">
                      <option value="" selected disabled>Select</option>
                      <?php for($i=0; $i<count($party);$i++){?>
                        <option value="<?php echo $party[$i][0];?>"><?php echo $party[$i][1];?></option>
                      <?php }?>
                    </select>
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label for="party">Vch Date</label>
                    <input type="date" id="date" name="date" placeholder="">
                </div>
              </div>
              

              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                  <label for="">Vch No.</label>
                  <input type="text" id="vchNo" name="vchNo" placeholder="">
                </div>
              </div>
              
              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                  <label for="">Amount</label>
                  <input type="number" id="amount" name="amount" placeholder="">
                </div>
              </div>


              <div class="col-md-1">
                <div class="f-control flex-column align-items-start">
                  <label for="">&nbsp;</label>
                  <button class="btn" onclick="addOpItems()">Add</button>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive mt-4">
                  <table class="table" id="opTable">
                      <thead>
                          <tr>
                              <th>Batch No</th>
                              <th>Mfg Date</th>
                              <th>Exp Date</th>
                              <th>Qty</th>
                              <th>Alt Qty</th>
                              <th>Particulars</th>
                              <th>Voucher Date</th>
                              <th>Voucher #</th>
                              <th>Amount</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                </div>  
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="../js/nav.js"></script>
    <script src="../js/index.js"></script>
    <script src="../js/ajaxcalls.js"></script>
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
      $( "select" ).selectmenu();

      var totalOpQty,totalOpAltQty,totalOpAmount;

      totalOpQty    = 0;
      totalOpAltQty = 0;
      totalOpAmount = 0;

        getCategory();
      
      
        async function callFetch(){
            //get
            let arr =   [["abc",20],["xyz",25]];
            let x   =   await fetch(`fetch-api.php?action=edit&data=${JSON.stringify(arr)}`);
            let result = await x.text();
            document.getElementById("result").innerHTML=result;
        }

        function getCategory() {

            var url = "../api/api-item.php";
            ajax(
                url,
                "categories",
                "",
                getCategory_callback,
                (vchType = null)
            );
        }

        function getCategory_callback(result){
          var result = JSON.parse(result);
          var items = result.data;

          var options="<option selected disabled value=''>Select</option>";

          items.forEach(element => {
            options += `<option>${element[0]}</option>`;
          });

          document.getElementById("txtCategory").innerHTML = options;
          
          document.querySelector(".ui-selectmenu-text").innerHTML = "Select";
          //console.log(document.querySelector(".ui-selectmenu-text").innerHTML);
        }

        function saveItem(){

              var itemName      = document.getElementById("txtItemName").value;
              var itemShortName = document.getElementById("txtItemShort").value;
              var brandName     = document.getElementById("txtBrand").value;
              var description   = document.getElementById("txtDescription").value;
              var category      = document.getElementById("txtCategory").value;
              var salePrice     = document.getElementById("txtPrice").value;
              var uom           = document.getElementById("txtUom").value;
              var altUom        = document.getElementById("txtAltUom").value;
              var conversion    = document.getElementById("txtConversion").value;
              var qty           = document.getElementById("txtQty").value;
              var altQty        = document.getElementById("txtAltQty").value;
              var rate          = document.getElementById("txtRate").value;
              var amount        = document.getElementById("txtAmt").value;

              //console.log(itemName);

              var batchNo = document.getElementById("batchNo").value;
              var mfg = document.getElementById("mfg").value;
              var exp = document.getElementById("exp").value;
              var party = document.getElementById("opParty").value;
              var date = document.getElementById("date").value;
              //var vchType = document.getElementById("vchType").value;
              var vchNo = document.getElementById("vchNo").value;

              //console.log(mfg);

              var opJsonData = opDataInJson();

              var jsonData = {
              itemName: itemName,
              itemShortName: itemShortName,
              brandName: brandName,
              description: description,
              category: category,
              salePrice: salePrice,
              uom: uom,
              altUom: altUom,
              conversion: conversion,
              batchNo: batchNo,
              mfg: mfg,
              exp: exp,
              qty: qty,
              altQty: altQty,
              party: party,
              date: date,
              rate: rate,
              amount: amount,
              vchType: vchType,
              vchNo: vchNo
          };

          $.ajax({
              type: "POST",
              url: "../api/api-item.php",
              data: { jsonData: JSON.stringify(jsonData), opJsonData:opJsonData, action:"add-item"},
              success: function(response) {
                  console.log(response);
                  var output = JSON.parse(this.response);

                  if(output.result=="Success"){
                        msg="Item Added.";
                    }else{
                        msg="Item Could Not Be Added.";
                    }

                    $.alert({
                        title: "Message",
                        content: msg,
                       
                    });
              }
          });

        }
    
        function addOpItems() {
          // get the specific div element
          var myDiv = document.querySelector('#opDivRow');
          
          // get all input and select elements within the specific div
          var inputs = myDiv.querySelectorAll('input, select');
          
          // get the tbody of the existing table element
          var tbody = document.querySelector('#opTable tbody');
          
          // create table row HTML string
          var trHTML = '<tr>';
          
          // iterate over inputs
          for (var i = 0; i < inputs.length; i++) {
            // add table cell for value to row HTML string
            trHTML += '<td>' + inputs[i].value + '</td>';
          }
          
          // close table row HTML string
          trHTML += '<td><button>Delete</button></td></tr>';
          
          // append row HTML string to tbody
          tbody.insertAdjacentHTML('beforeend', trHTML);

          //sum of op values
          sumOpValues();

          //clear form
          clearOpForm();

          //delete functionality
          addDeleteFunctionality();

        }

        function sumOpValues(){
          // Get all the cells in column 4 (the Qty column)
          var cells4 = document.querySelectorAll("#opTable tbody tr td:nth-child(4)");
          var cells5 = document.querySelectorAll("#opTable tbody tr td:nth-child(5)");
          var cells9 = document.querySelectorAll("#opTable tbody tr td:nth-child(9)");

          // Initialize a variable to store the sum
          var qty = 0;
          var altQty = 0;
          var amount = 0;

          // Loop through each cell and add its value to the sum
          cells4.forEach(function(cell) {
            qty += parseInt(cell.textContent);
          });

          cells5.forEach(function(cell) {
            altQty += parseInt(cell.textContent);
          });

          cells9.forEach(function(cell) {
            amount += parseInt(cell.textContent);
          });

          //Calculate rate
          var rate  = parseFloat(amount)/parseFloat(qty);

          //Display data
          document.querySelector('#txtQty').value=qty;
          document.querySelector('#txtAltQty').value=altQty;
          document.querySelector('#txtAmt').value=amount;
          document.querySelector('#txtRate').value=rate;
          

        }

        function getTableRows(){

          // Get all the rows in the table body
          var rows = document.querySelectorAll("#opTable tbody tr");

          // Get the number of rows
          var rowCount = rows.length;

          return rowCount;
        }

        function opDataInJson(){

          // Get all the rows in the table body
          var rows = document.querySelectorAll("#opTable tbody tr");

          // Initialize an array to store the row data
          var rowData = [];

          // Loop through each row
          rows.forEach(function(row) {
              // Get all the cells in the row
              var cells = row.querySelectorAll("td");

              // Initialize an object to store the cell data for the row
              var cellData = {};

              // Set the cell data for each column
              cellData.batchNo = cells[0].textContent;
              cellData.mfgDate = cells[1].textContent;
              cellData.expDate = cells[2].textContent;
              cellData.qty = parseInt(cells[3].textContent);
              cellData.altQty = parseInt(cells[4].textContent);
              cellData.particulars = cells[5].textContent;
              cellData.voucherDate = cells[6].textContent;
              cellData.voucherNo = cells[7].textContent;
              cellData.amount = parseFloat(cells[8].textContent.replace("$", ""));

              // Add the cell data for the row to the row data array
              rowData.push(cellData);
          });

          // Convert the row data array to JSON
          var json = JSON.stringify(rowData, null, 2);

          return json;
          // Output the JSON
          //console.log(json);

        }

        function clearForm(){
          
          // Get all the input and select elements
          var inputs = document.querySelectorAll("input, select");

          // Loop through each element and clear its value
          inputs.forEach(function(input) {
              input.value = "";
          });

        }

        function clearOpForm(){
            // Get the div element with the specified id
            var div = document.querySelector("#opDivRow");

            // Get all the input and select elements within the div
            var inputs = div.querySelectorAll("input, select");

            // Loop through each element and clear its value
            inputs.forEach(function(input) {
                input.value = "";
            });

        }

        function addDeleteFunctionality(){
          var deleteButtons = document.querySelectorAll("#opTable tbody tr td:nth-child(10) button");

          // Add a click event listener to each Delete button
          deleteButtons.forEach(function(button) {
              button.addEventListener("click", function() {
                  deleteRow(this);
              });
          });
        }

        function deleteRow(button) {
            // Get the row containing the clicked button
            var row = button.closest("tr");

            // Remove the row from the table
            row.parentNode.removeChild(row);

            //sum of op values
            sumOpValues();
        }

    </script>
    
  </body>

</html>