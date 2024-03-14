<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? '' }}</title>
    {{-- <link rel="stylesheet" href="style.css" media="all" /> --}}
    @include('invoice.style')
</head>

<body>
    <header class="clearfix">
        <h1>{{ $inv->inv }}</h1>
        <div id="company" class="clearfix">
            <div>{{ showDateTime($inv->created_at) }}</div>
            <div>Product Reedem Point</div>
        </div>
        <div id="project">

            <div><span>CLIENT</span> {{ auth()->user()->fullname }}</div>
            <div><span>PHONE</span> {{ auth()->user()->mobile }}</div>
            <div><span>EMAIL</span> {{ auth()->user()->email }}</div>
            <div><span>ADDRESS</span> {{ auth()->user()->address->address }}</div>
            <div><span></span> {{ auth()->user()->address->zip }}</div>
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th class="service">PRODUCT</th>
                    <th class="desc">DESCRIPTION</th>
                    <th>PRICE</th>
                    <th>QTY</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inv->detail as $item)
                    <tr>
                        <td class="service">{{ $item->product->name }}</td>
                        <td class="desc">{{ $item->product->details }}</td>
                        <td class="unit">{{ $item->product->price }} P</td>
                        <td class="qty">{{ $item->qty }}</td>
                        <td class="total">{{ $item->total }}P</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="grand total">TOTAL ORDER</td>
                    <td class="grand total">{{ $inv->total_order }}P</td>
                </tr>
                <tr>
                    <td colspan="4" class="grand total">SHIPPING</td>
                    <td class="grand total">Rp.{{ num($inv->expect_ongkir) }}</td>
                </tr>
            </tbody>
        </table>
        <div id="notices">
            <div>NOTES:</div>
            <div class="notice">Ongkos kirim akan otomatis terpotong dari saldo cash wallet / b-wallet anda</div>
        </div>
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

</html>
