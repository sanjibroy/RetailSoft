<?php
include("../session-lock.php");
include_once("../utilities/strings.php");
require_once '../api/configs.php';
require_once '../api/sql-functions.php';

$query_get_ledgers = "SELECT `ledgername`,`slno` FROM `tbl_ledgermaster`";
$ledgers = GetData($query_get_ledgers, $dbh);

$query_get_vouchers = "SELECT v.subvchtype,v.slno FROM tbl_vouchertype v INNER JOIN tbl_voucherno u ON v.subvchtype=u.vchtype ORDER BY v.slno";
$vouchers = GetData($query_get_vouchers, $dbh);

//var_dump($ledgers);

$isPosted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isPosted = true;
    mysqli_autocommit($dbh, FALSE);
    $error = 0;
    $msg = "";

    $ledgerId = $_POST["ledgerName"];
    $voucherId = $_POST["subVoucher"];
    $rate = $_POST["rate"];
    $calType = $_POST["calType"];

    $query_get_primary = "SELECT primaryvchtype FROM tbl_vouchertype WHERE slno='$voucherId'";
    $data   =   GetData($query_get_primary, $dbh);
    $primary = $data[0][0];

    $query_get_assigned = "SELECT ledgerid FROM tbl_voucher_ledger_assoc WHERE ledgerid='$ledgerId' AND subvoucherid='$voucherId'";
    $data   =   GetData($query_get_assigned, $dbh);

    if (count($data) == 0) {
        $query_assign_ledger = "INSERT INTO `tbl_voucher_ledger_assoc`(`ledgerid`, `subvoucherid`, `rate`,`caltype`) VALUES ('$ledgerId','$voucherId','$rate','$calType')";
        //echo $query_assign_ledger;
        $result = mysqli_query($dbh, $query_assign_ledger);
        if ($result != 1) {
            $error = 1;
        }

        if ($error == 1) {
            $msg = "Ledger could not be assigned to voucher";
            mysqli_rollback($dbh);
        } else {
            $msg = "Ledger assigned to voucher";
            mysqli_commit($dbh);
        }
    } else {
        $msg = "Already assigned";
    }
}

$query_ledgers = "SELECT a.`ledgerid`, a.`subvoucherid`, m.ledgername, t.subvchtype FROM `tbl_voucher_ledger_assoc` a INNER JOIN tbl_ledgermaster m ON a.ledgerid=m.slno INNER JOIN tbl_vouchertype t ON a.subvoucherid=t.slno ORDER BY t.subvchtype,a.slno";
$ledgerVoucher = GetData($query_ledgers, $dbh);
//$ledgerVoucher=array();

?>
<!doctype html>
<html lang="en">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <title>Assign Ledger</title>
</head>

<body id="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <?php
    if ($isPosted) {
    ?>
        <script>
            $.alert({
                title: "Message",
                content: "<?php echo $msg; ?>",
            });
            //alert("");
        </script>

    <?php $isPosted = false;
    } ?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">
            <div class="card-mid">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>Voucher Type</label>
                                <select id="subVoucher" name="subVoucher">
                                    <option disabled selected value="">Select Voucher Type</option>
                                    <?php
                                    for ($i = 0; $i < count($vouchers); $i++) {
                                    ?>
                                        <option value="<?php echo $vouchers[$i][1]; ?>"><?php echo $vouchers[$i][0]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>Ledger Name</label>
                                <select id="ledgerName" name="ledgerName">
                                    <option disabled selected value="">Select Ledger Name</option>
                                    <?php
                                    for ($i = 0; $i < count($ledgers); $i++) {
                                    ?>
                                        <option value="<?php echo $ledgers[$i][1]; ?>"><?php echo $ledgers[$i][0]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>Ledger Rate</label>
                                <input type="text" id="rate" name="rate">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="f-control flex-column align-items-start">
                                <label>Calculation Type</label>
                                <select id="calType" name="calType">
                                    <option disabled selected value="">Select</option>
                                    <option>+</option>
                                    <option>-</option>
                                    <option>NA</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 mt-auto mb-2">
                            <button class="btn">Assign </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-mid">
                <div class="table-responsive text-nowrap" style="max-height: 80vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Voucher Type</th>
                                <th>Ledger Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 0; $i < count($ledgerVoucher); $i++) {
                            ?>
                                <tr onclick="addToInputs(event)">
                                    <td><?php echo $i + 1; ?></td>
                                    <td><?php echo $ledgerVoucher[$i][3]; ?></td>
                                    <td><?php echo $ledgerVoucher[$i][2]; ?></td>
                                    <td><a class="btn-r mx-auto" href="ledger-assign-confirm-delete.php?ledgerId=<?php echo $ledgerVoucher[$i][0]; ?>&voucherId=<?php echo $ledgerVoucher[$i][1]; ?>&ledger=<?php echo $ledgerVoucher[$i][2]; ?>&voucher=<?php echo $ledgerVoucher[$i][3]; ?>"><i class="bx bx-trash"></i></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!--Container Main end-->
    <script src="../js/nav.js"></script>
    <script>
        $("select").selectmenu();

        function addToInputs(e) {
            var tr = e.target.parentElement;
            $("#ledgerName").val(tr.cells[2].innerHTML).change();
            document.querySelector("#ledgerName-button .ui-selectmenu-text").innerHTML = tr.cells[2].innerHTML;
            $("#subVoucher").val(tr.cells[1].innerHTML).change();
            document.querySelector("#subVoucher-button .ui-selectmenu-text").innerHTML = tr.cells[1].innerHTML;
        }
    </script>
</body>

</html>