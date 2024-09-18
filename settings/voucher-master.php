<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

$isPosted=false;

$query_get_primary="SELECT primaryvoucher FROM tbl_primaryvoucher ORDER BY slno";
$primary=GetData($query_get_primary,$dbh);

//var_dump($primary);

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $isPosted=true;
    mysqli_autocommit($dbh, FALSE);
    $error=0;
    $msg="";

    if(isset($_POST["btnSave"])){

        $primaryVoucher     =   addslashes($_POST["primaryVoucher"]);
        $subVoucher         =   addslashes($_POST["subVoucher"]);
        $frequentUse        =   addslashes($_POST["frequentUse"]);
        $transactionNature  =   addslashes($_POST["transactionNature"]);
        $prefix             =   addslashes($_POST["prefix"]);
        $suffix             =   addslashes($_POST["suffix"]);
        $initialValue       =   addslashes($_POST["initialValue"]);
        $paddingValue       =   0;

        if(isset($_POST["commonVoucherNumber"])){
            $commonVoucherNumber=   "1";
        }
        else{
            $commonVoucherNumber=   "0";
        }

        

        //$query_check_voucher =   "SELECT sl_no,sub_vch_type FROM tbl_voucher_type WHERE sub_vch_type='$subVoucher'";
        //echo $query_check_ledger;
        //$data   =   GetData($query_check_voucher,$dbh);

        //var_dump($data);

        //if(count($data)==0){
            //echo "insert";
            $query_insert_voucher="INSERT INTO tbl_vouchertype(`primaryvchtype`, `subvchtype`, `frequentuse`, `txtnature`, `commonvchnum`) VALUES('$primaryVoucher','$subVoucher','$frequentUse','$transactionNature','$commonVoucherNumber')";
            $result=mysqli_query($dbh,$query_insert_voucher);
            if($result!=1){
                $error=1;
            }
            
            $query_insert_unique_id="INSERT INTO tbl_voucherno(`prefix`, `suffix`, `startvalue`, `lengthval`, `vchtype`) VALUES('$prefix','$suffix','$initialValue','$paddingValue','$subVoucher')";
            $result=mysqli_query($dbh,$query_insert_unique_id);
            if($result!=1){
                $error=1;
            }
        //}
        

        if ($error==1)
        {
            mysqli_rollback($dbh);
            $msg="Voucher could not be saved";
        }
        else
        {
            $msg="Voucher saved";
            mysqli_commit($dbh);
        } 

    }

    if(isset($_POST["btnUpdate"])){

        $primaryVoucher     =   addslashes($_POST["primaryVoucher"]);
        $subVoucher         =   addslashes($_POST["subVoucher"]);
        $frequentUse        =   addslashes($_POST["frequentUse"]);
        $transactionNature  =   addslashes($_POST["transactionNature"]);
        $prefix             =   addslashes($_POST["prefix"]);
        $suffix             =   addslashes($_POST["suffix"]);
        $initialValue       =   addslashes($_POST["initialValue"]);
        $paddingValue       =   addslashes($_POST["paddingValue"]);

        if(isset($_POST["commonVoucherNumber"])){
            $commonVoucherNumber=   "1";
        }
        else{
            $commonVoucherNumber=   "0";
        }

        $query_check_voucher =   "SELECT slno FROM tbl_vouchertype WHERE subvchtype='$subVoucher'";
        //echo $query_check_ledger;
        $data   =   GetData($query_check_voucher,$dbh);
        $id=$data[0][0];
        
        
            //echo "update";
            
            $query_get_unique =   "SELECT slno FROM tbl_voucherno WHERE vchtype='$subVoucher'";
            //echo $query_check_ledger;
            $dataUniqueId   =   GetData($query_get_unique,$dbh);
            $uid=$dataUniqueId[0][0];

            $query_update_voucher="UPDATE tbl_vouchertype SET `primaryvchtype`='$primaryVoucher', `subvchtype`='$subVoucher', `frequentuse`='$frequentUse', `txtnature`='$transactionNature', `commonvchnum`='$commonVoucherNumber' WHERE `slno`='$id'";
            //echo $query_update_voucher;
            mysqli_query($dbh,$query_update_voucher);
            $result=mysqli_affected_rows($dbh);
            /* if($result==0){
                $error=1;
                echo "error 1";
            } */

            $query_update_unique_id="UPDATE tbl_voucherno SET `prefix`='$prefix', `suffix`='$suffix', `startvalue`='$initialValue', `lengthval`='$paddingValue', `vchtype`='$subVoucher' WHERE `slno`='$uid'";
            mysqli_query($dbh,$query_update_unique_id);
            $result=mysqli_affected_rows($dbh);
            /* if($result==0){
                $error=1;
                echo "error 2";
            } */

            if ($error==1)
            {
                mysqli_rollback($dbh);
                $msg="Voucher could not be updated.";
            }
            else
            {
                $msg="Voucher updated.";
                mysqli_commit($dbh);
            } 


        
    }

    

}

$query_get_vouchers="SELECT v.primaryvchtype,v.subvchtype,v.frequentuse,v.txtnature,v.commonvchnum,u.prefix,u.suffix,u.startvalue,u.lengthval,v.slno FROM tbl_vouchertype v INNER JOIN tbl_voucherno u ON v.subvchtype=u.vchtype";
$vouchers=GetData($query_get_vouchers,$dbh);

//var_dump($vouchers);


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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <title>Voucher Master</title>
</head>

    <body id="body-pd">

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
            <div class="card">

                <form method="POST">
                    <div class="card-mid">
                        
                            <div class="row">
                                <div class="col-md-3">
                                <div class="f-control flex-column align-items-start">
                                    <label>Primary Voucher</label>
                                    <select id="primaryVoucher" name="primaryVoucher">
                                        <option value="" disabled selected>Select Primary Voucher</option>
                                        <?php
                                            for($i=0;$i<count($primary);$i++){
                                        ?>
                                            <option value="<?php echo $primary[$i][0];?>"><?php echo $primary[$i][0];?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Sub Voucher</label>
                                        <input type="text" placeholder="Sub Voucher" id="subVoucher" name="subVoucher">
                                    </div> 
                                </div>
                                <div class="col-md-3 d-none">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Frequent Use</label>
                                        <select id="frequentUse" name="frequentUse">
                                            <!-- <option value="" disabled selected>Select Frequent Use</option> -->
                                            <option selected value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Transaction Nature</label>
                                        <select id="transactionNature" name="transactionNature">
                                            <option value="" disabled selected>Select Transaction Nature</option>
                                            <option value="Dr">Dr</option>
                                            <option value="Cr">Cr</option>
                                            <option value="NA">NA</option>
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Prefix</label>
                                        <input type="text" placeholder="Prefix" id="prefix" name="prefix">
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Suffix</label>
                                        <input type="text" placeholder="Suffix" id="suffix" name="suffix">
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Initial Value</label>
                                        <input type="number" placeholder="Initial Value" id="initialValue" name="initialValue">
                                    </div> 
                                </div>
                                <div class="col-md-3 d-none">
                                    <div class="f-control flex-column align-items-start">
                                        <label>Padding Value</label>
                                        <input type="number" placeholder="Padding Value" id="paddingValue" name="paddingValue">
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="commonVoucherNumber" name="commonVoucherNumber">
                                        <div class="f-control">
                                            <label class="me-auto mt-1">
                                                Common Voucher Number
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3"></div>
                                <div class="col-md-1">
                                    <button name="btnSave" class="btn">Save</button>
                                </div>
                                <div class="col-md-1">
                                    <button name="btnUpdate" class="btn">Update</button>
                                </div>
                            </div>

                            <input type="hidden" value="save" name="saveType" id="saveType">
                        
                    </div>
                    <div class="card-mid">
                        <div class="table-responsive text-nowrap"  style="max-height: 40vh; overflow-y: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Primary Voucher</th>
                                        <th>Sub Voucher</th>
                                        <th>Frequent Use</th>
                                        <th>Transaction Nature</th>
                                        <th>Prefix</th>
                                        <th>Suffix</th>
                                        <th>Initial Value</th>
                                        <!-- <th>Padding Value</th> -->
                                        <!-- <th class="text-center">Common Voucher Number</th> -->
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                        for($i=0;$i<count($vouchers);$i++){
                                    ?>
                                    <tr style="cursor:pointer" onclick="addToInputs(event)">
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $vouchers[$i][0];?></td>
                                        <td><?php echo $vouchers[$i][1];?></td>
                                        <td><?php echo $vouchers[$i][2];?></td>
                                        <td><?php echo $vouchers[$i][3];?></td>
                                        <td><?php echo $vouchers[$i][5];?></td>
                                        <td><?php echo $vouchers[$i][6];?></td>
                                        <td><?php echo $vouchers[$i][7];?></td>
                                        <!-- <td><?php //echo $vouchers[$i][8];?></td> -->
                                        <!-- <td><div class="form-check w-100 d-flex">
                                            <?php
                                            //if($vouchers[$i][4]==1){
                                            ?>
                                            <input class="form-check-input mx-auto" checked type="checkbox" disabled>
                                            <?php
                                            //}else{
                                                ?>
                                            <input class="form-check-input mx-auto" type="checkbox" disabled>
                                            <?php
                                            //}
                                            ?>
                                        </div></td> -->
                                        <td><a class="btn-r mx-auto" href="voucher-confirm-delete.php?id=<?php echo $vouchers[$i][9];?>&voucher=<?php echo $vouchers[$i][1];?>"><i class="bx bx-trash"></i></a></td>
                                    </tr>
                                    <?php }?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </main>
        <!--Container Main end-->
        
        <script src="../js/nav.js">
        </script>
        <script>
            $("select").selectmenu();
            function addToInputs(e){
                var tr = e.target.parentElement;
                $("#primaryVoucher").val(tr.cells[1].innerHTML).change();
                document.querySelector("#primaryVoucher-button .ui-selectmenu-text").innerHTML = tr.cells[1].innerHTML;
                document.querySelector("#subVoucher").value = tr.cells[2].innerHTML;
                $("#frequentUse").val(tr.cells[3].innerHTML).change();
                document.querySelector("#frequentUse-button .ui-selectmenu-text").innerHTML = tr.cells[3].innerHTML;
                $("#transactionNature").val(tr.cells[4].innerHTML).change();
                document.querySelector("#transactionNature-button .ui-selectmenu-text").innerHTML = tr.cells[4].innerHTML;
                document.querySelector("#prefix").value = tr.cells[5].innerHTML;
                document.querySelector("#suffix").value = tr.cells[6].innerHTML;
                document.querySelector("#initialValue").value = tr.cells[7].innerHTML;
                document.querySelector("#paddingValue").value = tr.cells[8].innerHTML;
                document.querySelector("#commonVoucherNumber").checked = tr.cells[9].children[0].children[0].checked;

            }
        </script>
    </body>

</html>