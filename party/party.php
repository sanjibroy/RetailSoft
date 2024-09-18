<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$isPosted   =   false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error          =   0;
    $errorMsg       =   "";
    $voucherType    =   "Party";
    $today=date('Y-m-d');
    $isPosted   =   true;
    mysqli_autocommit($dbh, FALSE);

    // Get form values and sanitize them with htmlspecialchars
    $partyName = htmlspecialchars($_POST['partyName'], ENT_QUOTES, 'UTF-8');
    //$balanceDate = htmlspecialchars($_POST['balanceDate'], ENT_QUOTES, 'UTF-8');
    $balanceAmount = htmlspecialchars($_POST['balanceAmount'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $pincode = htmlspecialchars($_POST['pincode'], ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
    $state = htmlspecialchars($_POST['state'], ENT_QUOTES, 'UTF-8');
    $landline = htmlspecialchars($_POST['landline'], ENT_QUOTES, 'UTF-8');
    $mobile = htmlspecialchars($_POST['mobile'], ENT_QUOTES, 'UTF-8');
    $pan = htmlspecialchars($_POST['pan'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $gstin = htmlspecialchars($_POST['gstin'], ENT_QUOTES, 'UTF-8');
    $partyType = htmlspecialchars($_POST['partyType'], ENT_QUOTES, 'UTF-8');
    $transType = htmlspecialchars($_POST['transType'], ENT_QUOTES, 'UTF-8');

    if(date('m')>3){
        $currentYear = date('Y');
        $balanceDate = $currentYear.'-04-01';
    }else{
        $previousYear = date('Y', strtotime('-1 year'));
        $balanceDate = $previousYear.'-04-01';
    }

    // Validate the required fields
    $errors = array();
    if (empty($partyName)) {
        $errors[] = "Party Name is required.";
    }

    /* if (empty($mobile)) {
        $errors[] = "Mobile Number is required.";
    } */

    $allErrors="";
    // Check if there are any validation errors
    if (!empty($errors)) {
        foreach ($errors as $err) {
            $allErrors = $err . "<br>";
        }
        $msg    =   $allErrors;
    } else {

    //partyId
    $query_voucher  =   "SELECT prefix,startvalue,suffix FROM tbl_voucherno WHERE vchtype='$voucherType'";
    $vchData=GetData($query_voucher,$dbh);
    foreach ($vchData as $row) { 
        $partyId  =   $row[0] . $row[1] . $row[2]; 
    }

    // Prepare the insert query
    $query_party = "INSERT INTO `tbl_partymaster`(`partycode`, `partyname`, `partyadd`, `partycity`, `partystate`, `partypincode`, `partylandlineno`, `partymobileno`, `partyemailid`, `partypan`, `gstno`, `partytype`,`transactiontype`) 
            VALUES ('$partyId','$partyName','$address', '$city', '$state', '$pincode', '$landline','$mobile','$email','$pan','$gstin','$partyType','$transType')";

    $partyMaster    =   mysqli_query($dbh,$query_party);

    if($partyMaster!=1)
    {
        $error=1;
        $errorMsg      .=   "Error in receipt transaction master";
    }


    //Opening balance
    $opIdName          =   "opbalparty";

    $query_opbal  =   "SELECT prefix,startvalue,suffix FROM tbl_voucherno WHERE vchtype='$opIdName'";
    $opData=GetData($query_opbal,$dbh);
    foreach ($opData as $row) { 
        $opId  =   $row[0] . $row[1] . $row[2]; 
    }

    $query_get_subvch  =   "SELECT slno FROM tbl_vouchertype WHERE subvchtype='$opIdName'";
    $subVchData        =   GetData($query_get_subvch,$dbh);
    $subVchId          =   $subVchData[0][0];

    if($balanceAmount>0){
        $opQueryTrans        =   "INSERT INTO tbl_transactionmaster(vchno,vchdate,subvoucherid,partycode,amount,createdon) VALUES('$opId','$balanceDate','$subVchId','$partyId','$balanceAmount','$today')";
        //echo $opQueryTrans;
        $data               =   mysqli_query($dbh,$opQueryTrans);
        if($data!=1)
        {
            $error=1;
            $errorMsg .="Error in transaction master";
        }
    }

    //Update  voucher Id

    $queryUpdateUid = "UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='$voucherType'";
    mysqli_query($dbh,$queryUpdateUid);
    $rows   =   mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg    .=   "Error in payment id update";
    }

    $queryUpdateOp = "UPDATE tbl_voucherno SET startvalue=startvalue+1 WHERE vchtype='$opIdName'";
    mysqli_query($dbh,$queryUpdateOp);
    $rows   =   mysqli_affected_rows($dbh);
    if($rows==0)
    {
        $error=1;
        $errorMsg    .=   "Error in opening balance id";
    }


    if ($error==1)
    {
        $msg="Party could not be added.";
        mysqli_rollback($dbh);
    }
    else
    {
        $msg="Party added successfully.";
        mysqli_commit($dbh);
    } 

    //$dbh->close();

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
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <title><?php echo $PARTY_TITLE; ?></title>
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
          <div class="heading">Add Party</div>
        </div>
        
        <form method="post">
    <div class="card-mid">
        <div class="row">
            <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label>Transaction Type</label>
                    <select class="form-control" id="transType" name="transType">
                        <option>Cash</option>
                        <option>Regular</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Party Name</label>
                    <input type="text" name="partyName" placeholder="Party Name" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start" required>
                    <label>Party Type</label>
                    <select name="partyType">
                        <option value="">Select</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Customer">Customer</option>
                    </select>
                </div>
            </div>
            <!-- <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label>Opening Balance Date</label>
                    <input type="date" name="balanceDate">
                </div>
            </div> -->
            <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label>Opening Balance Amount</label>
                    <input type="number" name="balanceAmount" placeholder="Opening Balance">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Address</label>
                    <textarea name="address" placeholder="Address"></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Pincode</label>
                    <input type="number" name="pincode" placeholder="Pincode">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>City</label>
                    <input type="text" name="city" placeholder="City">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>State</label>
                    <input type="text" name="state" placeholder="State">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Landline#</label>
                    <input type="number" name="landline" placeholder="Landline">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Mobile#</label>
                    <input type="number" name="mobile" placeholder="Mobile" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Pan#</label>
                    <input type="text" name="pan" placeholder="Pan">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>GSTIN#</label>
                    <input type="text" name="gstin" placeholder="GSTIN">
                </div>
            </div>
            <div class="col-md-12">
                <button class="btn my-2" type="submit" name="saveParty">Save Party</button>
            </div>
        </div>
    </div>
</form>

        
      </div>
    </main>
    <!--Container Main end-->
    
    <script src="../js/nav.js">
    </script>
    <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
      $( "select" ).selectmenu();
    </script>
    <script src="js/index.js"></script>
  </body>

</html>