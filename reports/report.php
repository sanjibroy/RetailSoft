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
    <title><?php echo $REPORT_TITLE; ?></title>
</head>


<body id="body-pd" class="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">
            <div class="card-top d-flex align-item-center gap-3">
                <form method="post" class="w-100">
                    <div class="d-flex w-50 gap-4 align-items-center">
                        <div class="f-control w-50 flex-column align-items-start">
                            <label>Select Party</label>
                            <select name="" id="">
                                <option>Select Party</option>
                            </select>
                        </div>
                        <div class="f-control w-50 flex-column align-items-start">
                            <label>From Date</label>
                            <input type="date" id="from-date" name="from-date" value="2023-03-23">
                        </div>
                        <div class="f-control w-50 flex-column align-items-start">
                            <label>To Date</label>
                            <input type="date" id="to-date" name="to-date" value="2023-03-23">
                        </div>

                        <div class="f-control w-50  d-flex gap-2 mt-auto">
                            <input type="submit" name="Proceed" value="Proceed" class="btn py-0 py-2">
                            <input type="button" name="Export" value="Export" class="btn py-0 py-2">
                        </div>
                    </div>
                </form>

            </div>
            <div class="card-mid">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Voucher Type</th>
                                <th>Voucher No</th>
                                <th>Dr</th>
                                <th>Cr</th>
                            </tr>
                        </thead>
                        <tbody id="display-indent-list" style="cursor:pointer">
                            <tr>
                                <td>1</td>
                                <td>22-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND8</td>
                                <td></td>
                                <td>92857.00</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>22-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND7</td>
                                <td></td>
                                <td>108728.00</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>21-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND6</td>
                                <td></td>
                                <td>110742.00</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>21-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND5</td>
                                <td></td>
                                <td>117507.00</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>18-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND3</td>
                                <td></td>
                                <td>91169.00</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>18-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND2</td>
                                <td></td>
                                <td>88468.00</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>18-03-2023</td>
                                <td>Valentine Bonded Warehouse</td>
                                <td>Indent</td>
                                <td>IND1</td>
                                <td></td>
                                <td>59429.00</td>
                            </tr>
                        </tbody>
                    </table>
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
    <script src="js/index.js"></script>
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