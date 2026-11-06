<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
        }
        .header {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            padding: 24px 30px;
            color: #ffffff;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .body-content {
            padding: 30px;
        }
        .alert-badge {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }
        .product-card {
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin-bottom: 25px;
        }
        .product-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .product-meta {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 15px;
        }
        .stock-grid {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        .stock-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .stock-col:not(:last-child) {
            margin-right: 10px;
        }
        .stock-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
        }
        .stock-val {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .stock-val.danger {
            color: #dc2626;
        }
        .stock-val.success {
            color: #16a34a;
        }
        .btn-action {
            display: block;
            width: 200px;
            margin: 25px auto 0;
            padding: 12px 24px;
            background: #00aeef;
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 15px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .bilingual-text {
            direction: rtl;
            text-align: right;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: #475569;
            font-size: 13px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>⚠️ Low Stock Warning / تنبيه انخفاض المخزون</h1>
            <p>{{ $shop->name ?? 'Shop Inventory System' }}</p>
        </div>

        <!-- Body -->
        <div class="body-content">
            <div style="text-align: center;">
                <span class="alert-badge">
                    ⚡ 70% Sold &bull; Only 30% or less remaining stock / متبقي 30% أو أقل من المخزون
                </span>
            </div>

            <p style="font-size: 15px; margin-top: 0;">
                Hello <strong>{{ $shop->name ?? 'Shop Manager' }}</strong>,
            </p>
            <p style="color: #4b5563; font-size: 14px; margin-top: -5px;">
                This is an automated inventory alert to inform you that the following product has reached its low stock threshold (70% sold, 30% or less remaining). Please arrange to restock soon.
            </p>

            <!-- Product Card -->
            <div class="product-card">
                <div class="product-title">{{ $product->name }}</div>
                <div class="product-meta">
                    @if($product->sku) <strong>SKU:</strong> {{ $product->sku }} &bull; @endif
                    @if($product->category) <strong>Category:</strong> {{ $product->category->name }} &bull; @endif
                    @if($product->unit) <strong>Unit:</strong> {{ $product->unit->name }} &bull; @endif
                    <strong>Price:</strong> {{ format_currency($product->price) }}
                </div>

                <div class="stock-grid">
                    <div class="stock-col">
                        <div class="stock-label">Total Stock / الإجمالي</div>
                        <div class="stock-val">{{ $product->initial_stock }}</div>
                    </div>
                    <div style="display: table-cell; width: 10px;"></div>
                    <div class="stock-col">
                        <div class="stock-label">Sold / تم بيعه</div>
                        <div class="stock-val success">{{ $product->sold_quantity }}</div>
                    </div>
                    <div style="display: table-cell; width: 10px;"></div>
                    <div class="stock-col" style="background: #fff1f2; border-color: #fecdd3;">
                        <div class="stock-label" style="color: #be123c;">Remaining / المتبقي</div>
                        <div class="stock-val danger">{{ $product->stock }} ({{ $product->remaining_percentage }}%)</div>
                    </div>
                </div>
            </div>

            <div class="bilingual-text">
                <strong>تنبيه المخزون:</strong> وصل المنتج <strong>{{ $product->name }}</strong> إلى حد إعادة الطلب حيث تم بيع 70% أو أكثر وبقي 30% أو أقل من إجمالي الكمية في المخزن. يرجى مراجعة المخزون وتوريد كميات جديدة.
            </div>

            <a href="{{ route('products.edit', $product->id) }}" class="btn-action">
                Restock Product / تحديث المخزون
            </a>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ $shop->name ?? config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

