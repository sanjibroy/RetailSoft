<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$isPosted   =   false;

$partyId    =   $_GET['id'];



//var_dump($partyData);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error          =   0;
    $errorMsg       =   "";
    $voucherType    =   "Party";
    $today=date('Y-m-d');
    $isPosted   =   true;
    mysqli_autocommit($dbh, FALSE);

    // Get form values and sanitize them with htmlspecialchars
    $partyName = htmlspecialchars($_POST['partyName'], ENT_QUOTES, 'UTF-8');
    $balanceDate = htmlspecialchars($_POST['balanceDate'], ENT_QUOTES, 'UTF-8');
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

    $opVoucher = htmlspecialchars($_POST['opVoucher'], ENT_QUOTES, 'UTF-8');

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

    
    $query_party = "UPDATE `tbl_partymaster` SET `partyname`='$partyName', `partyadd`='$address', `partycity`='$city', `partystate`='$state', `partypincode`='$pincode', `partylandlineno`='$landline', `partymobileno`='$mobile', `partyemailid`='$email', `partypan`='$pan', `gstno`='$gstin', `partytype`='$partyType' WHERE partycode='$partyId'";
    mysqli_query($dbh,$query_party);
    
    $opQueryTrans        =   "UPDATE tbl_transactionmaster SET vchdate='$balanceDate',amount='$balanceAmount' WHERE vchno='$opVoucher'";
    mysqli_query($dbh,$opQueryTrans);
    


    if ($error==1)
    {
        $msg="Failed";
        mysqli_rollback($dbh);
    }
    else
    {
        $msg="Party updated.";
        mysqli_commit($dbh);
    } 


    }
}


$query_party  =   "SELECT `partyname`, `partyadd`, `partycity`, `partystate`, `partypincode`, `partylandlineno`, `partymobileno`, `partyemailid`, `partypan`, `gstno`, `partytype` FROM `tbl_partymaster` WHERE `partycode`='$partyId'";
$partyData  =   GetData($query_party,$dbh);

$partyName  =   $partyData[0][0];
$partyAdd   =   $partyData[0][1];
$city   =   $partyData[0][2];
$state  =   $partyData[0][3];
$pin    =   $partyData[0][4];
$landLine   =   $partyData[0][5];
$mobile =   $partyData[0][6];
$email  =   $partyData[0][7];
$pan    =   $partyData[0][8];
$gst    =   $partyData[0][9];
$partyType  =   $partyData[0][10];

$query_op   =   "SELECT vchno,vchdate,amount FROM tbl_transactionmaster WHERE partycode='$partyId'";
$opData =   GetData($query_op,$dbh);

$opVoucher  =   $opData[0][0];
$opDate  =   $opData[0][1];
$opAmt  =   $opData[0][2];


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
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Party Name</label>
                    <input type="text" name="partyName" placeholder="Party Name" value="<?php echo $partyName;?>" required>
                </div>
            </div>
            <input type="hidden" name="opVoucher" value="<?php echo $opVoucher;?>">
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
            <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label>Opening Balance Date</label>
                    <input type="date" name="balanceDate" value="<?php echo $opDate;?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="f-control flex-column align-items-start">
                    <label>Opening Balance Amount</label>
                    <input type="number" name="balanceAmount" placeholder="Opening Balance" value="<?php echo $opAmt;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Address</label>
                    <textarea name="address" placeholder="Address"><?php echo $partyAdd;?></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Pincode</label>
                    <input type="number" name="pincode" placeholder="Pincode" value="<?php echo $pin;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>City</label>
                    <input type="text" name="city" placeholder="City" value="<?php echo $city;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>State</label>
                    <input type="text" name="state" placeholder="State" value="<?php echo $state;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Landline#</label>
                    <input type="number" name="landline" placeholder="Landline" value="<?php echo $landLine;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Mobile#</label>
                    <input type="number" name="mobile" placeholder="Mobile" value="<?php echo $mobile;?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Pan#</label>
                    <input type="text" name="pan" placeholder="Pan" value="<?php echo $pan;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email" value="<?php echo $email;?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="f-control flex-column align-items-start">
                    <label>GSTIN#</label>
                    <input type="text" name="gstin" placeholder="GSTIN" value="<?php echo $gst;?>">
                </div>
            </div>
        </div>
    </div>

        <div class="card-bot">
            <div class="d-flex w-50 gap-2">
                <button class="btn mt-2" >Update Party</button>
                <button class="btn mt-2" type="button" onclick="deleteParty('<?php echo $partyId;?>','<?php echo $partyName;?>')">Delete Party</button>
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

      function deleteParty(id,party){
          window.location.href='party-confirm-delete.php?id='+id+'&party='+party;
        }
    </script>
    <script src="js/index.js"></script>
  </body>

</html>