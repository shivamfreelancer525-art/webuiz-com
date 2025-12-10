<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{__('Invoice')}}</title>
    <base href="{{ $htmlBaseUri }}">
    <style>
        :root {
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f9fafb;
            --primary: #4662fa;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: var(--bg);
            padding: 24px;
        }
        .card {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .meta { text-align: right; }
        .meta h2 {
            margin: 0 0 6px;
            font-size: 18px;
        }
        .meta span { color: var(--muted); font-size: 14px; }
        .addresses {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            padding: 18px 0;
            border-bottom: 1px solid var(--border);
        }
        .address h4 {
            margin: 0 0 6px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
        }
        .address h5 {
            margin: 0 0 4px;
            font-size: 16px;
        }
        .address p {
            margin: 0;
            color: var(--muted);
            white-space: pre-line;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        thead { background: var(--primary); color: #fff; }
        th, td { padding: 12px; text-align: left; }
        tbody tr:nth-child(even) { background: #fafbff; }
        tbody tr:not(:last-child) td { border-bottom: 1px solid var(--border); }
        .total {
            display: flex;
            justify-content: flex-end;
            font-weight: 700;
            font-size: 16px;
            margin-top: 4px;
        }
        .notes {
            margin-top: 16px;
            padding: 14px;
            background: #f3f4ff;
            border: 1px solid #e0e7ff;
            border-radius: 8px;
        }
        .notes h5 {
            margin: 0 0 6px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
        }
        @media print {
            body { background: #fff; padding: 0; }
            .card { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <div>
            <div class="title">{{parse_url(config('app.url'))['host']}}</div>
            <div style="color: var(--muted); font-size: 14px;">{{config('app.name')}}</div>
        </div>
        <div class="meta">
            <h2>{{__('Invoice ID')}}: {{$invoice['uuid']}}</h2>
            <span>{{__('Invoice Date')}}: {{$invoice['created_at']}}</span>
        </div>
    </div>

    <div class="addresses">
        <div class="address">
            <h4>{{__('Billed To')}}</h4>
            <h5>{{$user['display_name']}}</h5>
            <p>{{$user['email']}}</p>
        </div>
        <div class="address">
            <h4>{{__('From')}}</h4>
            <h5>{{config('app.name')}}</h5>
            @if($address = $settings->get('billing.invoice.address'))
                <p>{!! $address !!}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>{{__('Description')}}</th>
            <th>{{__('Qty')}}</th>
            <th>{{__('Price')}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{config('app.name')}} {{__('Subscription Dues')}} ({{$invoice['subscription']['product']['name']}} {{__('plan')}})</td>
            <td>1</td>
            <td>{{$invoice['currency_symbol']}}{{$invoice['subscription']['price']['amount']}}</td>
        </tr>
        </tbody>
    </table>

    <div class="total">
        <div>{{__('Total')}}: {{$invoice['subscription']['price']['currency_symbol']}}{{$invoice['subscription']['price']['amount']}} {{$invoice['subscription']['price']['currency']}}</div>
    </div>

    @if($notes = $settings->get('billing.invoice.notes'))
        <div class="notes">
            <h5>{{__('Notes')}}</h5>
            <p>{!! $notes !!}</p>
        </div>
    @endif
</div>
</body>
</html>
