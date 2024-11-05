<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $receipt->title }}</title>
    <style>body { font-family: DejaVu Sans }</style>

    <style>
        .body {
            width: 100%;
            font-size: 70%;
        }

        .body > * {
            margin-top: 2em;
        }

        .header {
            padding: 0.8em;
            margin-top: 0;
        }

        .w-half {
            width: 50%;
        }

        .full-width {
            width: 100%;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th {
            background-color: khaki;
            text-align: left;
        }

        h3, h2 {
            margin: 0;
        }

        .header, .main, .data {
            border-bottom: 1px solid gray;
        }

        th, td {
            padding: 0.8em;
            margin-left: 5em;
        }

        .main tbody > tr > *:nth-child(1) {
            width: 65%;
        }
        .main tbody > tr > td:nth-child(1) {
            border-right: 1px solid gray;
        }

        .main table > tbody > tr > *:nth-child(2) {
            margin-left: 0;
        }

        table > tbody > tr > *:nth-child(1) {
            width: 40%;
        }

        table > tbody > tr > *:nth-child(3) {
            margin-left: 0.8em;
            width: 5%;
        }

        h3{
            text-align: start;
        }

        .logo {
            color: gold;
            font-size: large;
        }
        
        .data > table > tbody > tr > td {
            border-right: 1px solid gray;
        }

        .data > table > tbody > tr > td:last-child {
            border-right: none;
        }

        .main > tbody > tr:nth-child(even) {
            background-color: rgb(255, 249, 251)
        }

        .data > table > tbody > tr:nth-child(odd) {
            background-color: rgb(255, 249, 251)
        }

        .data > table > tbody > tr:nth-child(even) {
            background-color: rgb(255, 251, 217)
        }
    </style>
</head>
<body>
    <div class="body full-width">
        <table class="header full-width">
            <tr>
                <td class="logo"><h2>Snacky</h2></td>
                <td><h2> {{$receipt->title}} </h2></td>
            </tr>
        </table>
        
    
        {{-- <div class="main"> --}}
            <table class="main">
                <tr>
                    <th><h3>Description: </h3></th>
                    <th><h3>Total price:</h3></th>
                </tr>
                <tr>
                    <td>
                        {{$receipt->description}}
                    </td>
                    <td>
                        {{$receipt->total_price}}
                    </td>
                </tr>
            </table>
        {{-- </div> --}}

        <div class="data">
            <table>
                <tr>
                    <th><h3>Product name</h3></th>
                    <th><h3>Unit price</h3></th>
                    <th><h3>Count</h3></th>
                    <th><h3>Summary</h3></th>
                </tr>
                @foreach($receipt->snacks as $snack)
                <tr>
                    <td> {{$snack->title_ru}} </td>
                    <td> {{$snack->price}} </td>
                    <td> {{$snack->pivot->item_count}} </td>
                    <td> {{$snack->pivot->item_count * $snack->price}} </td>
                </tr>
                @endforeach
            </table>
        </div>
    
        <div class="footer">
            <div>
                <div>Thank you</div>
                <div>&copy; Snacky</div>
            </div>
        </div>
    </div>
</body>
</html>