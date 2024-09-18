<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

    $error=0;

    if($_SERVER["REQUEST_METHOD"]=="POST"){

        mysqli_autocommit($dbh, FALSE);

        //$jsonData       = json_decode($_POST['jsonData']);
        $itemCode       = $_POST['itemcode'];
        $price          = $_POST['itemrate'];
        $date           = $_POST['pricedate'];

        $priceData      =   checkPriceList($itemCode,$date,$dbh);
        //echo $priceData."pp";
        if(count($priceData)==0){
            updateOldPriceDate($itemCode,$date,$dbh);
            $values     =   array($itemCode,$price,$date,$date);
            $data       =   addPriceList($values,$dbh);

            if($data!=1)
            {
                $error=1;
            }
        }
        else{
            updateOldPriceDate($itemCode,$date,$dbh);
            updatePriceList($itemCode,$price,$date,$dbh);
        }

        if ($error==1)
        {
            $rows   =   mysqli_affected_rows($dbh);
            $result =   "Failed";
            mysqli_rollback($dbh);
        }
        else
        {
            $rows   =   mysqli_affected_rows($dbh);
            $result =   "Success";
            mysqli_commit($dbh);
        }
        //echo $result;
    }
    
    $today  =   date('Y-m-d');
    $query_get_price    =   "SELECT p.itmid,m.itemname,DATE_FORMAT(p.plfromdate,'%d-%m-%Y'),DATE_FORMAT(p.pltodate,'%d-%m-%Y'),p.rate FROM tbl_pricelist p INNER JOIN tbl_productmaster m ON p.itmid=m.itemcode";
    $itemData   =   GetData($query_get_price,$dbh);
    //$msg            =   json_encode(array("result" => $result));
    
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
  <link rel="stylesheet" href="../assets/css/main.css">
  <title><?php echo $PRICELIST_TITLE; ?></title>
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
          <div class="heading">Price List</div>
        </div>
        <div class="card-mid">
            <form method="post">
                <div class="row">
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Select Item</label>
                            <input type="text" id="search-item" placeholder="Search Item" autocomplete="off">
                            <input type="hidden" id="itemcode" name="itemcode">
                            <input type="hidden" id="itemname">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Item Rate</label>
                            <input type="number" id="itemrate" name="itemrate" placeholder="Item rate">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Date</label>
                            <input type="date" id="pricedate" name="pricedate">
                        </div>
                    </div>
                    <div class="col-md-3 mt-auto mb-2">
                        <button class="btn">Save Price</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-mid">
            <div class="table-responsive text-nowrap" style="max-height: 60vh; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Item Name</th>
                            <th>Item Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=0;$i<count($itemData);$i++){?>
                        <tr>
                            <td><?php echo $i+1;?></td>
                            <th><?php echo $itemData[$i][2];?></th>
                            <th><?php echo $itemData[$i][3];?></th>
                            <td><?php echo $itemData[$i][1];?></td>
                            <td><?php echo $itemData[$i][4];?></td>
                        </tr>
                        <?php }?>
                        
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/nav.js"></script>
    <script src="../js/index.js"></script>
    <script src="../js/core-utilities.js"></script>
    <script src="../js/core-ajax.js"></script>
    <script>
        var party;
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        $( "select" ).selectmenu();
    
        // Get End Points
        const apiUrl = '../core-api/api-gets.php';

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
                    //createUoms();
                    return false;
                }
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                .append("<div>" + item.label + "</div>")
                .appendTo(ul);
            };
        });

        

        
    </script>
  </body>

</html>