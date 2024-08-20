<!DOCTYPE html>
<html>

<head>
    <title>Tabel Laporan Penjualan</title>


    <!-- Include jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
        integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/"
        crossorigin="anonymous"></script>

    <!-- Include html2canvas -->
    <script src="/assets/javascripts/html2canvas.min.js"></script>


    <script src="/assets/javascripts/app.js"></script>


    <style type="text/css">
        #generatePDFButton {
            margin-top: 10px;
            margin-bottom: 20px;
            padding: 5px 10px;
            background-color: #2c243a;
            border-radius: 3px;
            color: #f1f1f1;
            cursor: pointer;
        }

        #generatePDFButton:hover {
            background-color: #433063;
            color: #f1f1f1;
        }


        #generatePDFButton:active {
            background-color: #433063;
            color: #f1f1f1;
        }

        body,
        * {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            padding: 0;
            margin: 0;
            border: none;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 5px 10px;
        }

        table {
            background-color: #2c243b;
            width: 100%;
            table-layout: auto;
            margin: 10px 0;
            border: none;
            border: solid 1px;
        }

        table tr td {
            color: #444444;
            background-color: #f1f1f1;
        }

        table tr td,
        table tr th {
            text-align: center;
            font-size: 9pt;
            margin: 0;
        }

        table tr th {
            text-transform: uppercase;
            font-weight: bold;
            background-color: #2c243a;
            color: rgb(238, 238, 238);
            font-size: 10pt;
            padding: 5px;
        }

        table.mko-table tbody tr td:first-child {
            width: 40%;
            text-transform: uppercase;
            font-weight: bold;
            background-color: #4c4655;
            color: rgb(238, 238, 238);
            font-size: 10pt;
            padding: 5px;
        }

        .field-sum {
            background-color: #2c243b !important;
            color: whitesmoke !important;
        }

        .header h2 {
            font-size: 32px;
            text-transform: uppercase;
        }

        .header h4 {
            margin: 5px 0;
        }

        .title-sect {
            text-transform: uppercase;
            font-weight: bolder;
            color: #2c243a;
            margin-top: 15px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body style="position: relative">
    <center class="header">
        <h2>Tokoku</h2>
        <h4>Laporan Penjualan</h4>
        <h5> <span class="date-element">-</span> | PDF &middot; Cleoun Render Engine &middot; V1.0.7 </h5>
    </center>


    <h2 class="title-sect">Laporan Penjualan Per-Tanggal</h2>

    <table class='table table-bordered mko-table' style="width: 45%;">
        <thead>
            <tr>
                <th style="min-width: 120px;">Tanggal</th>
                <th style="min-width: 180px;">Jumlah QTY Penjualan</th>
                <th style="min-width: 180px;">Jumlah Transaksi</th>
                <th style="min-width: 180px;">Total Transaksi</th>
                <th style="min-width: 180px;">Jumlah Keuntungan</th>
            </tr>
        </thead>
        <tbody style="position: relative; width: 100%" id="tabel_penjulan_per_tanggal">



        </tbody>
    </table>


    <button onclick="generatePDF('laporan_barang.pdf')" id="generatePDFButton">Download PDF</button>

    <script src="/assets/javascripts/template_reporting.js"></script>

    <script>
        get_data_penjualan_barang();
    </script>

</body>

</html>