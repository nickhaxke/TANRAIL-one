<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Report - Shift #{{ $shift->id }} - TANRAIL Dining</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: #f1f5f9; color: #0f172a; padding: 24px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        .receipt-container { width: 100%; max-width: 480px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); padding: 24px; }
        .header { text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 16px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; color: #1e293b; }
        .subtitle { font-size: 12px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #0f172a; color: #fff; margin-top: 6px; }
        .meta-table { width: 100%; font-size: 12px; border-collapse: collapse; margin-bottom: 16px; }
        .meta-table td { padding: 4px 0; color: #475569; }
        .meta-table td.val { text-align: right; font-weight: 600; color: #0f172a; }
        .section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin: 12px 0 8px 0; }
        .calc-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; }
        .calc-row.bold { font-weight: 700; color: #0f172a; }
        .calc-row.highlight { background: #f8fafc; padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 14px; font-weight: 800; margin: 6px 0; }
        .calc-row.variance-short { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .calc-row.variance-exact { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .calc-row.variance-over { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .expenses-list { font-size: 11px; color: #64748b; margin-bottom: 8px; }
        .expenses-item { display: flex; justify-content: space-between; padding: 2px 0; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 24px; padding-top: 16px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #64748b; }
        .sig-line { border-bottom: 1px solid #94a3b8; height: 32px; margin-bottom: 4px; }
        .actions { margin-top: 20px; display: flex; gap: 12px; }
        .btn { padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; text-decoration: none; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-back { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { border: none; box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <div class="title">TANRAIL ONE DINING</div>
        <div class="subtitle">{{ $shift->branch->name }}</div>
        <div class="subtitle" style="font-size: 10px; color: #94a3b8;">STATION CODE: {{ $shift->branch->code }}</div>
        <div class="badge">SHIFT CLOSURE AUDIT (Z-REPORT)</div>
    </div>

    <table class="meta-table">
        <tr>
            <td>Shift ID:</td>
            <td class="val">#{{ $shift->id }}</td>
        </tr>
        <tr>
            <td>Cashier:</td>
            <td class="val">{{ $shift->user->name }}</td>
        </tr>
        <tr>
            <td>Opened At:</td>
            <td class="val">{{ $shift->opened_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td>Closed At:</td>
            <td class="val">{{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i:s') : 'In Progress' }}</td>
        </tr>
        <tr>
            <td>Total Orders Served:</td>
            <td class="val">{{ $shift->total_orders_count ?? $shift->orders->count() }} Orders</td>
        </tr>
    </table>

    <div class="section-title">Sales Intake by Tender Type</div>
    <div class="calc-row">
        <span>Cash Sales:</span>
        <span class="val">TZS {{ number_format($cashSales, 2) }}</span>
    </div>
    <div class="calc-row">
        <span>Electronic Card Sales:</span>
        <span class="val">TZS {{ number_format($cardSales, 2) }}</span>
    </div>
    <div class="calc-row">
        <span>Mobile Money (M-Pesa/Airtel):</span>
        <span class="val">TZS {{ number_format($mobileSales, 2) }}</span>
    </div>
    <div class="calc-row bold" style="border-top: 1px solid #f1f5f9; padding-top: 6px; margin-top: 4px;">
        <span>Gross Shift Revenue:</span>
        <span>TZS {{ number_format($shift->total_sales, 2) }}</span>
    </div>

    @if($shift->expenses->count() > 0)
        <div class="section-title">Cash Drawer Expenses (Petty Cash Deducted)</div>
        <div class="expenses-list">
            @foreach($shift->expenses as $exp)
                <div class="expenses-item">
                    <span>{{ $exp->category }} ({{ $exp->paid_to ?? 'Vendor' }})</span>
                    <span>-TZS {{ number_format($exp->amount, 2) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="section-title">Physical Cash Drawer Balancing</div>
    <div class="calc-row">
        <span>1. Starting Float:</span>
        <span>TZS {{ number_format($shift->opening_float, 2) }}</span>
    </div>
    <div class="calc-row">
        <span>2. Cash Sales Collected:</span>
        <span>+TZS {{ number_format($cashSales, 2) }}</span>
    </div>
    <div class="calc-row">
        <span>3. Cash Operating Expenses Paid:</span>
        <span>-TZS {{ number_format($shift->total_expenses, 2) }}</span>
    </div>
    <div class="calc-row highlight">
        <span>Expected Cash in Drawer:</span>
        <span>TZS {{ number_format($shift->expected_cash, 2) }}</span>
    </div>
    <div class="calc-row bold">
        <span>Physical Cash Counted by Cashier:</span>
        <span>TZS {{ number_format($shift->closing_cash_counted, 2) }}</span>
    </div>

    @php
        $diff = (float) $shift->cash_difference;
        $varianceClass = abs($diff) < 0.01 ? 'variance-exact' : ($diff > 0 ? 'variance-over' : 'variance-short');
        $varianceLabel = abs($diff) < 0.01 ? 'EXACT BALANCED (0.00)' : ($diff > 0 ? '+TZS '.number_format($diff, 2).' (OVERAGE)' : '-TZS '.number_format(abs($diff), 2).' (SHORTAGE)');
    @endphp

    <div class="calc-row highlight {{ $varianceClass }}">
        <span>Audited Drawer Variance:</span>
        <span>{{ $varianceLabel }}</span>
    </div>

    @if($shift->notes)
        <div style="font-size: 11px; color: #64748b; margin-top: 10px; background: #f8fafc; padding: 8px; border-radius: 6px;">
            <strong>Shift Handover Note:</strong> {{ $shift->notes }}
        </div>
    @endif

    <div class="signature-grid">
        <div>
            <div class="sig-line"></div>
            <div>Cashier Signature</div>
            <div style="font-size: 10px; color: #94a3b8;">{{ $shift->user->name }}</div>
        </div>
        <div>
            <div class="sig-line"></div>
            <div>Supervisor / Duty Mgr</div>
            <div style="font-size: 10px; color: #94a3b8;">Verified & Sealed</div>
        </div>
    </div>
</div>

<div class="actions">
    <button onclick="window.print()" class="btn btn-print">Print Z-Report</button>
    <a href="{{ route('restaurant.shifts') }}" class="btn btn-back">Back to Shifts</a>
</div>

</body>
</html>
