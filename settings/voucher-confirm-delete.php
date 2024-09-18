<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

$isPosted=false;

$id=$_GET["id"];
$voucher=$_GET["voucher"];

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $isPosted=true;
    mysqli_autocommit($dbh, FALSE);
    $error=0;
    $msg="";

    $id= $_POST["voucherId"];
        //echo $id;

    $query_get_assigned="SELECT subvoucherid FROM tbl_voucher_ledger_assoc WHERE subvoucherid='$id'";
    $data   =   GetData($query_get_assigned,$dbh);

    $query_get_sub_voucher="SELECT subvchtype FROM tbl_vouchertype WHERE slno='$id'";
    $dataSubVch   =   GetData($query_get_sub_voucher,$dbh);
    $vch=$dataSubVch[0][0];

    if(count($data)==0){
        $query_delete_ledger="DELETE FROM tbl_vouchertype WHERE slno='$id'";
        mysqli_query($dbh,$query_delete_ledger);
        $result=mysqli_affected_rows($dbh);
        if($result==0){
            $error=1;
        }

        $query_delete_unique_id="DELETE FROM tbl_voucherno WHERE vchtype='$vch'";
        mysqli_query($dbh,$query_delete_unique_id);
        $result=mysqli_affected_rows($dbh);
        if($result==0){
            $error=1;
        }

        if ($error==1)
        {
            mysqli_rollback($dbh);
            $msg="Voucher could not be deleted.";
        }
        else
        {
            $msg="Voucher deleted.";
            mysqli_commit($dbh);
        } 
    }
    else{
        $msg="Voucher is assigned to ledger. Cannot delete voucher.";
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <link rel="stylesheet" href="../assets/css/main.css">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

  <title>Confirm Delete</title>
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
                buttons: {
                ok: function () {
                    backToMaster();
                }
            }
            }); 
            //alert("");
    </script>

    <?php $isPosted=false; }?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
      <div class="card" id="dashboard">
      <div class="card-mid">
          <!-- <select id="item-wise">
            <option selected>Item Wise</option>
            <option>Category Wise</option>
          </select> -->
          <div class="heading">Confirm Delete</div>
          
          <form method="POST">
            <p>Are you sure want to delete the voucher <strong>"<?php echo $voucher;?>"</strong> ?</p>
            <input type="hidden" name="voucherId" value="<?php echo $id;?>">
            <div class="d-flex ms-auto gap-4">
                <button name="btnSave" class="btn" style="background-color:red">Yes</button>
                <button type="button" class="btn" onclick="backToMaster()">No</button>
            </div>
          </form>

        </div>
        
      </div>
    </main>
    <!--Container Main end-->
    
    <script src="js/nav.js">
    </script>
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
      $( "select" ).selectmenu();

      function backToMaster(){
        window.location.href='voucher-master.php';
      }
    </script>
    <script src="../js/nav.js"></script>
    <script src="../js/settings.js"></script>

  </body>

</html>