var itemArr = [];

$(document).ready(function () {
  $("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#dashboard table tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
});

function pageSwitch(start, end) {
  document.querySelector(`#${start}`).classList.add("d-none");
  document.querySelector(`#${end}`).classList.remove("d-none");
  itemArr = createArray();
  if (itemArr.length != 0) {
    document.querySelector("#itemAdd").innerHTML = createTable(itemArr);
  } else {
    return;
  }
  console.log(itemArr);
  calculate(itemArr);
}

function focusInput(e) {
  var tr = e.target.parentElement.parentElement.parentElement;
  var qtyInput = tr.querySelector(".qty-input");
  var caseBtl = tr.querySelector(".case-btl");
  if (qtyInput.value == "") {
    qtyInput.value = 0;
  }
  if (e.target.checked) {
    qtyInput.disabled = false;
    caseBtl.disabled = false;
    qtyInput.focus();
    qtyInput.select();
  } else {
    qtyInput.value = 0;
    qtyInput.disabled = true;
    caseBtl.disabled = true;
  }
}

function createArray() {
  var tempArr = [];
  var checkedInputs = document.querySelectorAll(".item-select input:checked");
  checkedInputs.forEach((element) => {
    var tr = element.parentElement.parentElement.parentElement;
    var td = tr.querySelectorAll("td");
    var itemName = td[2].innerHTML;
    var itemCategory = td[1].innerHTML;
    var itemSize = td[3].innerHTML;
    var itemRate = td[6].innerHTML;
    var qtyInput = tr.querySelector(".qty-input");
    var caseBtl = tr.querySelector(".case-btl");
    var itemId = tr.getAttribute("data-item-id");
    var itemUnit = parseFloat(tr.getAttribute("data-item-unit"));
    //taxes
    var importFee = tr.getAttribute("data-import-fee");
    var advalorem = tr.getAttribute("data-advalorem");
    var transitFee = tr.getAttribute("data-transit-fee");
    var tcsFee = tr.getAttribute("data-tcs-fee");

    if (caseBtl.checked) {
      var unit = "BTL";
      var btlQty = parseFloat(qtyInput.value);
      var caseQty = parseFloat(btlQty / itemUnit).toFixed(2);
    } else {
      var unit = "CASE";
      var caseQty = parseFloat(qtyInput.value);
      var btlQty = parseFloat(caseQty * itemUnit);
    }
    var total = parseFloat(itemRate) * caseQty;
    total = total.toFixed(2);
    tempArr.push([
      itemId,
      itemName,
      itemCategory,
      itemSize,
      itemRate,
      caseQty,
      btlQty,
      unit,
      total,
      importFee,
      advalorem,
      transitFee,
      tcsFee,
      percentCalc(importFee, total),
      percentCalc(advalorem, total),
      percentCalc(transitFee, total),
      percentCalc(tcsFee, total),
    ]);
  });
  return tempArr;
}

function createTable(arr) {
  var tbody = ``;
  arr.forEach((element, index) => {
    if (element[5] != "0" || element[5] != 0) {
      var tr = `<tr data-item-id="${element[0]}">
        <td>${element[1]}</td>
        <td>${element[2]}</td>
        <td>${element[3]}</td>
        <td>${element[4]}</td>
        <td>${element[5]}</td>
        <td>${element[6]}</td>
        <td>${element[7]}</td>
        <td>${element[8]}</td>
        <td><button class="btn-r mx-auto" onclick="deleteRow(${index})"><i class="bx bx-trash"></i></button></td>
    </tr>`;
      tbody = tbody + tr;
    }
  });
  return tbody;
}

function deleteRow(index) {
  deleteSelection(index);
  itemArr.splice(parseInt(index), 1);
  document.querySelector("#itemAdd").innerHTML = createTable(itemArr);
  calculate(itemArr);
}

function deleteSelection(index) {
  var itemId = itemArr[parseInt(index)][0];
  var tr = document.querySelector(`tr[data-item-id="${itemId}"]`);
  var itemSelect = tr.querySelector(".item-select input");
  var qtyInput = tr.querySelector(".qty-input");
  var caseBtl = tr.querySelector(".case-btl");
  qtyInput.value = 0;
  qtyInput.disabled = true;
  caseBtl.checked = false;
  caseBtl.disabled = true;
  itemSelect.checked = false;
}

function percentCalc(percent, total) {
  return ((parseFloat(percent) * parseFloat(total)) / 100).toFixed(2);
}

function addTotal(arr, index) {
  var temp = 0;
  arr.forEach((element) => {
    temp = parseFloat(element[index]) + temp;
  });
  temp = temp.toFixed(2);
  return temp;
}

function calculate(itemArr) {
  var runningTotal = addTotal(itemArr, 8);
  var importFee = addTotal(itemArr, 13);
  var advalorem = addTotal(itemArr, 14);
  var transitFee = addTotal(itemArr, 15);
  var tcsFee = addTotal(itemArr, 16);
  var grandTotal = parseFloat(runningTotal)+(parseFloat(importFee)+parseFloat(advalorem)-parseFloat(transitFee)+parseFloat(tcsFee));
  console.log(grandTotal);
  grandTotal = Math.round(grandTotal);
  grandTotal = grandTotal.toFixed(2);
  var calculateText = `<tr>
    <td><b>Running Total</b></td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;"><b>${runningTotal}</b></td>
</tr>
<tr>
    <td>Import Pass Fee/Literage Fee/Transport Fee</td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;">${importFee}</td>
</tr>
<tr>
    <td>Advalorem</td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;">${advalorem}</td>
</tr>
<tr>
    <td>Transit Breakage Allowance @ 0.50%</td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;">${transitFee}</td>
</tr>
<tr>
    <td>TCS@1%</td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;">${tcsFee}</td>
</tr>
<tr>
    <td><b>Grand Total</b></td>
    <td colspan="6"></td>
    <td style="text-align:right; padding-right:1rem;"><b>${grandTotal}</b></td>
</tr>`;
document.querySelector("#calculate").innerHTML = calculateText;
}
