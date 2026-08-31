<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #01162f; }
        h1 { font-size: 20px; color: #01162f; margin-bottom: 2px; }
        .doc-title { font-size: 22px; font-weight: bold; text-align: right; color: #c89a44; margin: 0; }
        .doc-number { text-align: right; color: #5c6b7a; font-size: 12px; }
        .header { width: 100%; overflow: hidden; margin-bottom: 18px; }
        .header .left { float: left; width: 55%; }
        .header .right { float: right; width: 40%; }
        .meta-box { width: 100%; overflow: hidden; margin-bottom: 16px; }
        .meta-box .col { float: left; width: 33%; font-size: 11px; }
        .meta-box .label { color: #5c6b7a; text-transform: uppercase; font-size: 9px; margin-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f6f9fc; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #5c6b7a; border-bottom: 1px solid #e3e8ee; }
        td { padding: 6px 8px; border-bottom: 1px solid #e3e8ee; font-size: 11px; }
        .right { text-align: right; }
        .totals { width: 260px; float: right; margin-top: 10px; }
        .totals td { border-bottom: none; padding: 3px 8px; }
        .totals .grand td { font-weight: bold; border-top: 2px solid #01162f; padding-top: 6px; font-size: 13px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-proforma { background: #f5e9d4; color: #9b6829; }
        .badge-invoice { background: #f3e6c8; color: #96702f; }
        .badge-paid { background: #dcf2e6; color: #1f8a57; }
        .badge-partial { background: #f5e9d4; color: #9b6829; }
        .footer-note { clear: both; margin-top: 40px; font-size: 10px; color: #5c6b7a; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <h1>{{ $merchant->business_name }}</h1>
            <p style="color:#5c6b7a; margin:0;">
                {{ $merchant->physical_address }}<br>
                {{ $merchant->phone }} @if($merchant->email) &middot; {{ $merchant->email }} @endif
                @if($merchant->tin_number) <br>TIN: {{ $merchant->tin_number }} @endif
            </p>
        </div>
        <div class="right">
            <p class="doc-title">{{ $invoice->isDraft() ? 'PROFORMA INVOICE' : 'INVOICE' }}</p>
            <p class="doc-number">{{ $invoice->number }}</p>
            <p class="doc-number">
                @if($invoice->isDraft())
                    <span class="badge badge-proforma">DRAFT — FOR REVIEW</span>
                @elseif($invoice->status === 'paid')
                    <span class="badge badge-paid">PAID</span>
                @elseif($invoice->status === 'partially_paid')
                    <span class="badge badge-partial">PARTIALLY PAID</span>
                @else
                    <span class="badge badge-invoice">{{ strtoupper($invoice->statusLabel()) }}</span>
                @endif
            </p>
        </div>
    </div>

    <div class="meta-box">
        <div class="col">
            <p class="label">Billed to</p>
            <p style="margin:0; font-weight:bold;">{{ $invoice->customer->name }}</p>
            <p style="margin:0; color:#5c6b7a;">
                {{ $invoice->customer->phone }}<br>
                {{ $invoice->customer->email }}<br>
                {{ $invoice->customer->address }}
            </p>
        </div>
        <div class="col">
            <p class="label">Issue date</p>
            <p style="margin:0 0 8px 0;">{{ $invoice->issue_date->format('d M Y') }}</p>
            <p class="label">Due date</p>
            <p style="margin:0;">{{ $invoice->due_date?->format('d M Y') ?: '—' }}</p>
        </div>
        <div class="col">
            <p class="label">Balance due</p>
            <p style="margin:0; font-weight:bold; font-size:14px;">TZS {{ number_format($invoice->balanceDue(), 0) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Unit price</th>
                <th class="right">Discount</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $line)
                <tr>
                    <td>{{ $line->item_name }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format($line->quantity, 2, '.', ''), '0'), '.') }}</td>
                    <td class="right">{{ number_format($line->unit_price, 0) }}</td>
                    <td class="right">{{ $line->discount_amount > 0 ? number_format($line->discount_amount, 0) : '—' }}</td>
                    <td class="right">{{ number_format($line->subtotal, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">TZS {{ number_format($invoice->subtotal, 0) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
            <tr>
                <td>Discount</td>
                <td class="right">&minus;TZS {{ number_format($invoice->discount_amount, 0) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td>Total</td>
            <td class="right">TZS {{ number_format($invoice->total, 0) }}</td>
        </tr>
        @if($invoice->amount_paid > 0)
            <tr>
                <td>Paid</td>
                <td class="right">TZS {{ number_format($invoice->amount_paid, 0) }}</td>
            </tr>
            <tr>
                <td>Balance due</td>
                <td class="right">TZS {{ number_format($invoice->balanceDue(), 0) }}</td>
            </tr>
        @endif
    </table>

    @if($invoice->notes)
        <div class="footer-note">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
    @endif

    <div class="footer-note">
        @if($invoice->isDraft())
            This is a proforma invoice for review only. Prices and quantities are subject to change until approved.
        @else
            Generated {{ now()->format('d M Y H:i') }}. Thank you for your business.
        @endif
    </div>
</body>
</html>
