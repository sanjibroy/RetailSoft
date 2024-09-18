//Create Dynamic Table Rows
//columnTypes -   0 : General,  1 :  Hidden,  2 : Input-Number, 3 : Input Button-Delete

function addTableRow(tableBody, columns, columnTypes, tdIdNames, colSpans,slno) {
   // var table = document.getElementById(tableName);
    var tbody = document.getElementById(tableBody);

    var newRowHtml = '<tr>';

    for (var i = 0; i < columns.length; i++) {
      if (columnTypes[i] === 1) {
        newRowHtml += '<td colspan="'+colSpans[i]+'" style="display: none;" data-td-type="td" data-td-id="' + tdIdNames[i] + '">' + columns[i] + '</td>';
      }else if (columnTypes[i] === 2) {
        newRowHtml += '<td colspan="'+colSpans[i]+'" data-td-type="input" data-td-id="' + tdIdNames[i] + '">' + '<input type="number" value="'+columns[i]+'" onkeyup="calculateGrandTotal()">' + '</td>';
      }
      else if (columnTypes[i] === 3) {
        newRowHtml += '<td colspan="'+colSpans[i]+'" data-td-type="button" data-td-id="' + tdIdNames[i] + '">' + '<button onclick="deleteRow('+slno+', event)" class="btn-r"><i class="bx bxs-trash"></i></button>' + '</td>';
      }  
      else {
        newRowHtml += '<td colspan="'+colSpans[i]+'" data-td-type="td" data-td-id="' + tdIdNames[i] + '">' + columns[i] + '</td>';
      }
    }

    newRowHtml += '</tr>';

    tbody.innerHTML += newRowHtml;
  }


function addMultipleRows() {
    var data = [
      { tableName: "myTable", tableBody: "tbody", columns: ["Row 3, Column 1", "Row 3, Column 2", "Row 3, Column 3"], hiddenColumns: [0, 1, 0], tdIdNames: ["td1", "td2", "td3"] },
      { tableName: "myTable", tableBody: "tbody", columns: ["Row 4, Column 1", "Row 4, Column 2", "Row 4, Column 3"], hiddenColumns: [0, 0, 1], tdIdNames: ["td4", "td5", "td6"] },
      { tableName: "myTable", tableBody: "tbody", columns: ["Row 5, Column 1", "Row 5, Column 2", "Row 5, Column 3"], hiddenColumns: [1, 0, 0], tdIdNames: ["td7", "td8", "td9"] }
    ];

    for (var i = 0; i < data.length; i++) {
      addTableRow(data[i].tableName, data[i].tableBody, data[i].columns, data[i].hiddenColumns, data[i].tdIdNames);
    }
  }

  function getElementNames(ids) {
    var elementNames = [];

    for (var i = 0; i < ids.length; i++) {
      var element = document.getElementById(ids[i]);

      if (element) {
        var name = element.getAttribute('name');
        elementNames.push(name);
      }
    }

    return elementNames;
  }

function formatDate(dateStr){
  var parts = dateStr.split("-");
  var formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];

  return formattedDate;
}

function todaysDate(){
  var today = new Date();
  var currentDate = today.toISOString().slice(0, 10);
  return currentDate;
}

function convertTableToJSON(tableName,tableBody) {
  var table = document.getElementById(tableName);
  var bodyItems = table.querySelector(tableBody);
  var rows = bodyItems.getElementsByTagName('tr');
  var jsonData = [];

  for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var rowData = {};

      var tds = row.getElementsByTagName('td');
      for (var j = 0; j < tds.length; j++) {
      var td = tds[j];
      var tdType = td.getAttribute('data-td-type');
      var tdId = td.getAttribute('data-td-id');
      
        if (tdType === 'td') {
          rowData[tdId] = td.textContent;
        } else if (tdType === 'input') {
          var input = td.querySelector('input');
          rowData[tdId] = input.value;
        }

      }

      jsonData.push(rowData);
  }

  return jsonData;
  //return JSON.stringify(jsonData);
}

function jsonCreate(key, val) {
  var json = "{";
  for (var x = 0; x < key.length; x++) {
      if (x == key.length - 1) {
          json = json + `"${key[x]}":"${val[x]}"`;
      } else {
          json = json + `"${key[x]}":"${val[x]}",`;
      }
  }
  json = json + "}";
  return json;
}