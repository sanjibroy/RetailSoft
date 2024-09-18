<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

$ledgerGroups           =   getLedgerGroup($dbh);

$q="SELECT l.ledgername,l.displayledgername,g.ledgergroup,l.ledgergroup,l.slno FROM tbl_ledgermaster l INNER JOIN tbl_ledgergroup g ON l.ledgergroup=g.ledgergroup";
$ledgerData = GetData($q,$dbh);

$isPosted   =   false;

if($_SERVER["REQUEST_METHOD"]=="POST"){

  $isPosted   =   true;

  $error=0;
  mysqli_autocommit($dbh, FALSE);

    $ledgerName     = $_POST["ledgerName"];
    $displayName    = $_POST["ledgerDisplayName"];
    $ledgerGroup    = $_POST["ledgerGroups"];
   
    //Insert item
    $values         =   array($ledgerName,$ledgerGroup,$displayName);
    $data           =   addNewLedger($values,$dbh);
    if($data!=1)
    {
        $error=1;
    }

    if ($error==1)
    {
        $result="Failed";
        mysqli_rollback($dbh);
        $msg="Ledger could not be saved.";
    }
    else
    {
        $result="Success";
        mysqli_commit($dbh);
        $msg="Ledger saved.";
    }
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <link rel="stylesheet" href="../assets/css/main.css">
    <title><?php echo $LEDGER_TITLE; ?></title>
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
            <div class="card-top d-flex align-item-center">
                <!-- <select id="item-wise">
            <option selected>Item Wise</option>
            <option>Category Wise</option>
          </select> -->
                <div class="heading">Manage Ledger</div>
            </div>
            <div class="card-mid">
              <form method="post">
                <div class="row">
                  <div class="col-md-3">
                    <div class="f-control flex-column align-items-start">
                      <label>Ledger Name</label>
                      <input type="text" placeholder="Ledger Name" id="ledgerName" name="ledgerName">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="f-control flex-column align-items-start">
                      <label>Ledger Print Name</label>
                      <input type="text" placeholder="Ledger Print Name" id="ledgerDisplayName" name="ledgerDisplayName">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="f-control flex-column align-items-start">
                      <label>Ledger Group</label>
                      <select class="form-control" id="ledgerGroups" name="ledgerGroups">
                        <option value="" selected disabled>Select</option>
                        <?php for($i=0; $i<count($ledgerGroups);$i++){?>
                          <option value="<?php echo $ledgerGroups[$i][0];?>"><?php echo $ledgerGroups[$i][1];?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 mt-auto mb-2">
                    <button class="btn">Proceed</button>
                  </div>
                </div>
              </form>
            </div>
            <div class="card-mid">
              <div class="sub-heading">List Of Ledger Under The Selected Voucher</div>
              <div class="table-responsive">
                <table class="table no-padding">
                    <thead>
                      <tr>
                        <th>Ledger Name</th>
                        <th>Display Name</th>
                        <th>Ledger Group</th>
                        <th></th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php for($i=0; $i<count($ledgerData);$i++){?>
                      <tr>
                        <td><?php echo $ledgerData[$i][0];?></td>
                        <td><?php echo $ledgerData[$i][1];?></td>
                        <td><?php echo $ledgerData[$i][2];?></td>
                        <td><a href="edit-ledger.php?id=<?php echo $ledgerData[$i][4]; ?>">Edit</a></td>
                        <td><a href="ledger-confirm-delete.php?id=<?php echo $ledgerData[$i][4]; ?>&ledger=<?php echo $ledgerData[$i][0];?>">Delete</a></td>
                      </tr>
                    <?php }?>
                      
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </main>
    <!--Container Main end-->
    
    <script src="../js/nav.js">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        $("select").selectmenu();
        $(function () {
            var purchase = [
                {
                    value: "PUR1",
                    label: "Purchase Type 1"
                },
                {
                    value: "PUR2",
                    label: "Purchase Type 2"
                },
                {
                    value: "PUR3",
                    label: "Purchase Type 3"
                }
            ];

            $("#purchase-search").autocomplete({
                minLength: 0,
                source: purchase,
                focus: function (event, ui) {
                    $("#purchase-search").val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    $("#purchase-search").val(ui.item.label);
                    $("#purchase-search-id").val(ui.item.value);

                    return false;
                }
            })
        });
    </script>
    <script src="../js/index.js"></script>
</body>

</html>