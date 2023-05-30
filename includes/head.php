<!-- Beginning of Head -->
<link rel="shortcut icon" href="images/icon.png" />
<meta http-equiv='Content-Type'     content='text/html; charset=UTF-8' />
<meta name="viewport"               content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1" />
<meta name="keywords"               content="inventory, inventory management, financial, ledger, journal, business financial, SFI" />
<meta name="description"            content="A simple financial and inventory system which tracks purchases and sales, calculates the cost and profit of your inventory, and tracks your cost and profit through the combination of ingredients in your custom recipes." />
<meta http-equiv="X-UA-Compatible"  content="IE=edge" />
<?php
if (isset ( $name )) {
	echo "<title>SFI || $page || $name</title>";
} else {
	echo "<title>SFI || $page</title>";
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
    <script type="text/javascript">
        function toggleview(itm) {
            var itmx = document.getElementById(itm);
            if (itmx.style.display === "none") {
                itmx.style.display = "block";
            } else {
                itmx.style.display = "none";
            }
        }

        function showBox1() {
            document.getElementById('box1').style.display = "block";
            document.getElementById('box2').style.display = "none";

            document.getElementById('link1').style.fontWeight = "bold";
            document.getElementById('link1').style.textDecoration = "none";

            document.getElementById('link2').style.fontWeight = "normal";
            document.getElementById('link2').style.textDecoration = "underline";
        }

        function showBox2() {
            document.getElementById('box2').style.display = "block";
            document.getElementById('box1').style.display = "none";

            document.getElementById('link2').style.fontWeight = "bold";
            document.getElementById('link2').style.textDecoration = "none";

            document.getElementById('link1').style.fontWeight = "normal";
            document.getElementById('link1').style.textDecoration = "underline";
        }

        function transactionToggle(itm) {
            var itm1 = document.getElementById('showTrans' + itm);
            var itm2 = document.getElementById('editTrans' + itm);
            if (itm1.style.display === "none") {
                itm1.style.display = "block";
            } else {
                itm1.style.display = "none";
            }
            if (itm2.style.display === "none") {
                itm2.style.display = "block";
            } else {
                itm2.style.display = "none";
            }
        }

        function makeChange(cash, total) {
        	var change = 0.00;
        	if (cash > total) {
        		change = (cash - total);
        	}
        	document.getElementById('change').innerHTML = "Change: " + change.toFixed(2);
        }

        function markUpEx(linkId, val) {
            var amt = (10 * ((val / 100) + 1));
            document.getElementById(linkId).innerHTML = "Example: $10.00 cost would be $" + amt + " retail.";
        }

        function getInvSelect(linkId, name) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById(linkId).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajax/invSelect.php?getNames=" + name, true);
            xmlhttp.send();
        }

        function getContactSelect(linkId, name) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById(linkId).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajax/contactSelect.php?getNames=" + name, true);
            xmlhttp.send();
        }

        function getCategorySelect(linkId, name) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById(linkId).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajax/categorySelect.php?getNames=" + name, true);
            xmlhttp.send();
        }

        function getAccSelect(linkId, name) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById(linkId).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajax/accSelect.php?getAcc=" + name, true);
            xmlhttp.send();
        }

        function submitForm(Id) {
            document.getElementById("frm" + Id).submit();
        }

        function paid(table, id) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById('showPaid' + id).innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajax/paid.php?table=" + table + "&id=" + id, true);
            xmlhttp.send();
        }

        function reconcile(id) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "ajax/reconcile.php?ledgerId=" + id, true);
            xmlhttp.send();
        }

        function showBlank(id) {
            document.getElementById("blank" + id).style.display = "block";
        }

        function generalBalanceCheck() {
            var i = 1;
            var D = parseFloat(0.00);
            var C = parseFloat(0.00);
            for (i; i <= 10; i++) {
                D += parseFloat(document.getElementById("D" + i).value);
                C += parseFloat(document.getElementById("C" + i).value);
            }
            if (D.toFixed(2) === C.toFixed(2)) {
                document.getElementById("genSubmit").disabled = false;
            } else {
                document.getElementById("genSubmit").disabled = true;
            }
            document.getElementById("showDebit").innerHTML = D.toFixed(2);
            document.getElementById("showCredit").innerHTML = C.toFixed(2);
        }

        function totalIt()
            {
               const input = document.getElementsByClassName("due");
               var total = 0.00;
               for (var i = 0; i < input.length; i++)
               {
                  if (input[i].checked)
                  {
                     total += parseFloat(input[i].value);
                  }
               }
               if (total >= 0.01)
               {
               document.getElementById("dueTotal").value = Math.abs(total).toFixed(2);
               }
               if (total <= -0.01)
               {
               document.getElementById("oweTotal").value = Math.abs(total).toFixed(2);
               }
               if (total === 0.00)
               {
               document.getElementById("dueTotal").value = total.toFixed(2);
               document.getElementById("oweTotal").value = total.toFixed(2);
               }
            }
    </script>

    <style type="text/css">
        a {
            color: #bd4a11;
            text-decoration: none;
            cursor: pointer;
        }
        a:hover {
            color: #444444;
            text-decoration: underline;
            cursor: pointer;
        }
        a.submenu {
            color:black;
        }
        a.menu {
            color: #000000;
            text-decoration: none;
            border: 1px solid #ffffff;
            border-radius: 4px;
            padding: 2px;
            margin: 0px 5px;
            font-size: 1em;
            cursor: pointer;
        }
        a.menu:hover {
            color: #444444;
            text-decoration: none;
            border: 1px solid #cccccc;
            border-radius: 4px;
            padding: 2px;
            margin: 0px 5px;
            font-size: 1em;
            cursor: pointer;
        }
        th, td {
            vertical-align: top;
            padding:5px;
        }

        .table1 td {
            border-bottom: 1px solid #ddd;
        }

        .flex-container {
            display: -webkit-flex;
            display: flex;
            -webkit-flex-flow: row wrap;
            flex-flow: row wrap;
        }

        .flex-container > * {
            padding: 10px;
            flex: 1 100%;
        }

        @media all and (min-width: 1100px) {
        .main { flex: 4.5 900px; max-width: 860px; }
        .aside    { max-width: 200px; }
        .header { order: 1; }
        .advert { order: 2; }
        .menu { order: 3; }
        .main    { order: 4; }
        .aside { order: 5; }
        .footer  { order: 6; }
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
            visibility: visible;
            }
            #printArea {
            position: absolute;
            left: 0;
            top: 0;
            }
            footer {
                page-break-after: always;
            }
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .main {
            width:100%;
        }

        .heading {
            padding: 20px 0px;
            text-align: center;
            font-weight:bold;
            font-size:2em;
            color:#bd4a11;
        }

        .cat {
            background-color:#11bd4a;
            color: #000000;
        }

        .mcat {
            background-color:#11bd4a;
            color:#11bd4a;
        }

        .subcat {
            background-color:#17ff64;
            color:#000000;
        }

        .tooltipLeft {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
        }

        .tooltipLeft .tooltiptextLeft {
            visibility: hidden;
            width: 500px;
            background-color: black;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;

            /* Position the tooltip */
            position: absolute;
            z-index: 1;
            top: -5px;
            right: 105%;
        }

        .tooltipLeft:hover .tooltiptextLeft {
            visibility: visible;
        }

        .tooltipRight {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
        }

        .tooltipRight .tooltiptextRight {
            visibility: hidden;
            width: 500px;
            background-color: black;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;

            /* Position the tooltip */
            position: absolute;
            z-index: 1;
            top: -5px;
            left: 105%;
        }

        .tooltipRight:hover .tooltiptextRight {
            visibility: visible;
        }
    </style>

    <!-- End of Head -->
