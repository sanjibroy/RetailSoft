<?php 
include("../session-lock.php");
include_once("../utilities/strings.php");
include_once("../api/configs.php");
include_once("../api/sql-functions.php");

$query_get_party    =   "SELECT `partycode`, `partyname`, `partycity`, `partystate`, `partymobileno`, `partyemailid`, `partytype` FROM `tbl_partymaster`";
$party  =   GetData($query_get_party,$dbh);

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
    <title>Party Register</title>
</head>


<body id="body-pd" class="body-pd">

    <?php include("../includes/top-menu.php");?>
    <?php include("../includes/side-menu.php");?>

    <!--Container Main start-->
    <main class="container-fluid pt-4">
        <div class="card" id="dashboard">

            <div class="card-top">
                <div class="heading">Party Register</div>
            </div>

            <div class="card-mid">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Party Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Mobile No</th>
                                <th>Email</th>
                            </tr>
                        </thead>

                        <tbody id="tbl-item" style="cursor:pointer">
                            <?php
                                for($i=0; $i<count($party);$i++){
                            ?>
                                <tr onclick="editParty('<?php echo $party[$i][0];?>')">
                                    <td><?php echo $i+1;?></td>
                                    <td><?php echo $party[$i][1];?></td>
                                    <td><?php echo $party[$i][2];?></td>
                                    <td><?php echo $party[$i][3];?></td>
                                    <td><?php echo $party[$i][4];?></td>
                                    <td><?php echo $party[$i][5];?></td>
                                </tr>
                            <?php
                                }
                            ?>
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

        function editParty(id){
            //console.log(id);
            let hostName    = window.location.hostname;
            let url =   `../party/edit-party.php?id=${id}`;
            window.location.assign(url);
        }

        var token = "u0123456789";
        var url = "https://testserverdw.xyz/cbw/api/party.php";
        var data = getParty(url, token, 'party-details');
    </script>
</body>

</html>