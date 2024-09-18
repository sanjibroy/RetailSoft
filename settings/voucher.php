<?php 
include_once("../utilities/strings.php");
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
    <link rel="stylesheet" href="../assets/css/main.css">
    <title><?php echo $VOUCHER_TITLE; ?></title>
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
                <div class="heading">Manage Voucher Type / Party / Product</div>
            </div>
            <div class="card-mid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Under Group</label>
                            <input type="text" placeholder="Sales Order">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Voucher Name</label>
                            <input type="text" placeholder="Voucher Name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Frequent Used</label>
                            <select>
                                <option>Select Frequent Used</option>
                                <option>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="f-control flex-column align-items-start">
                            <label>Nature</label>
                            <select>
                                <option>Select Nature</option>
                                <option>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Prefix</label>
                            <input type="text" placeholder="Prefix">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Start</label>
                            <input type="text" placeholder="Start">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Length</label>
                            <input type="number" placeholder="Length">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Suffix</label>
                            <input type="text" placeholder="Suffix">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Class</label>
                            <input type="text" placeholder="Class">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-mid">
                <div class="row">
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>ledger Name</label>
                            <input type="text" placeholder="Ledger Name">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>ledger Rate</label>
                            <input type="number" placeholder="Ledger Rate">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Calc Type</label>
                            <input type="text" placeholder="Calc Type">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="f-control flex-column align-items-start">
                            <label>Position</label>
                            <input type="text" placeholder="Position">
                        </div>
                    </div>
                    <div class="col-md-2 mt-auto mb-2">
                        <button class="btn">Add</button>
                    </div>
                </div>
            </div>
            <div class="card-mid">
                <div class="table-responsive text-nowrap" style="max-height: 35vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ledger Name</th>
                                <th>Ledger rate</th>
                                <th>Calc Type</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                            <tr>
                                <td>Ledger 1</td>
                                <td>₹230.00</td>
                                <td>Type 1</td>
                                <td>Second</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-bot mt-3">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/nav.js">
    </script>
    <script>
        var party;
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        $("select").selectmenu();
        $(function () {
            party = [
                {
                    value: "PARTY1",
                    label: "B A Bonded Warehouse"
                },
                {
                    value: "PARTY2",
                    label: "DMB Bonded Warehouse"
                },
                {
                    value: "PARTY3",
                    label: "Gloria Bonded Warehouse"
                }
            ];

            $("#party-search").autocomplete({
                minLength: 0,
                source: party,
                focus: function (event, ui) {
                    $("#party-search").val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    $("#party-search").val(ui.item.label);
                    $("#party-search-id").val(ui.item.value);

                    return false;
                }
            })
        });
    </script>
    <script src="../js/index.js"></script>
    <script>

        function ajax(url, token, actionName, jsonData, callback, vchType = null) {
            var xmlhttp = new XMLHttpRequest();
            var msg;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    msg = this.responseText;
                    //console.log(msg);
                    callback(msg);
                }
            };

            xmlhttp.open("POST", url, true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("jsonData=" + jsonData + "&action=" + actionName + "&token=" + token + "&voucherType=" + vchType);
        }

        function addData_callback(result) {
            console.log(JSON.parse(result).data[0][0]);
        }

        function getParty(url, token, action) {
            //Create JSON Data Here
            let jsonData = '{"partyId":"UCW-P0001"}';

            //Ajax Call
            ajax(url, token, action, jsonData, addData_callback);

        }

        var token = "u0123456789";
        var url = "https://testserverdw.xyz/cbw/api/party.php";
        var data = getParty(url, token, 'party-details');
    </script>
</body>

</html>