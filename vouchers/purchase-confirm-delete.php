<?php
include("../session-lock.php");

$isPosted=false;

$id=$_GET["id"];
$ledger=$_GET["ledger"];

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $isPosted=true;
    mysqli_autocommit($dbh, FALSE);
    $error=0;
    $msg="";

    $id= $_POST["ledgerId"];

    $query_get_assigned="SELECT ledger_id FROM tbl_ledger_assign WHERE ledger_id='$id'";
    $data   =   GetData($query_get_assigned,$dbh);

    if(count($data)==0){
        $query_delete_ledger="DELETE FROM tbl_ledger_master WHERE sl_no='$id'";
        mysqli_query($dbh,$query_delete_ledger);
        $result=mysqli_affected_rows($dbh);
        if($result==0){
            $error=1;
        }

        if ($error==1)
        {
            mysqli_rollback($dbh);
            $msg="Ledger could not be deleted";
        }
        else
        {
            $msg="Ledger deleted";
            mysqli_commit($dbh);
        } 
    }
    else{
        $msg="Ledger is assigned to voucher.Cannot delete ledger.";
    }
    

    if ($error==1)
    {
        mysqli_rollback($dbh);
    }
    else
    {
        mysqli_commit($dbh);
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

  <title>Custom Admin Panel</title>
</head>


  <body id="body-pd" class="body-pd">
    
    <?php include("../menu-top.php"); ?>
    <?php include("../menu-side.php"); ?>


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
            <p>Are you sure want to delete the ledger <strong>"<?php echo $ledger;?>"</strong> ?</p>
            <input type="hidden" name="ledgerId" value="<?php echo $id;?>">
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
        window.location.href='purchase-register.php';
      }
    </script>
    <script src="../js/nav.js"></script>
    <script src="../js/settings.js"></script>

  </body>

</html>