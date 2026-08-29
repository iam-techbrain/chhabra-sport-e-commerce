<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Chhabra Sports</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Email Card -->
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 25px 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #fbbf24; letter-spacing: 1px;">
                                CHHABRA SPORTS
                            </h1>
                            <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">
                                Premium Sports Gear & Equipment
                            </p>
                        </td>
                    </tr>

                    <!-- Success Banner -->
                    <tr>
                        <td style="padding: 30px 30px 10px 30px; text-align: center;">
                            <div style="width: 50px; height: 50px; background-color: #d1fae5; color: #10b981; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                                ✓
                            </div>
                            <h2 style="margin: 0 0 6px 0; color: #0f172a; font-size: 20px; font-weight: 800;">Thank You for Your Order!</h2>
                            <p style="margin: 0; color: #64748b; font-size: 14px;">
                                Hi <strong>{{ $order->customer_name }}</strong>, we've received your order and are preparing it for shipment.
                            </p>
                            <div style="display: inline-block; margin-top: 15px; background-color: #f1f5f9; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; color: #0f172a;">
                                Order #: <span style="color: #059669;">{{ $order->order_number }}</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Customer & Shipping Info -->
                    <tr>
                        <td style="padding: 20px 30px 10px 30px;">
                            <table role="presentation" width="100%" style="background-color: #f8fafc; border-radius: 8px; padding: 15px; font-size: 13px; color: #334155;">
                                <tr>
                                    <td style="vertical-align: top; width: 50%; padding-right: 10px;">
                                        <strong style="color: #0f172a; display: block; margin-bottom: 4px;">📍 Delivery Address:</strong>
                                        {{ $order->address }}<br>
                                        <strong>Phone:</strong> {{ $order->customer_phone }}
                                    </td>
                                    <td style="vertical-align: top; width: 50%; padding-left: 10px; border-left: 1px solid #e2e8f0;">
                                        <strong style="color: #0f172a; display: block; margin-bottom: 4px;">💳 Payment Method:</strong>
                                        <span style="text-transform: uppercase; font-weight: 700; color: #059669;">{{ $order->payment_method }}</span><br>
                                        <strong>Status:</strong> Confirmed
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Order Items Table -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <h3 style="margin: 0 0 12px 0; color: #0f172a; font-size: 15px; font-weight: 700; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                📦 Items Ordered
                            </h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9; color: #475569; text-align: left;">
                                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1;">Product</th>
                                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: center;">Qty</th>
                                        <th style="padding: 10px; border-bottom: 1px solid #cbd5e1; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($order->items) || is_object($order->items))
                                        @foreach($order->items as $item)
                                            <tr style="border-bottom: 1px solid #e2e8f0; color: #334155;">
                                                <td style="padding: 10px;">
                                                    <strong style="color: #0f172a;">{{ $item['title'] ?? $item['name'] ?? 'Product Item' }}</strong>
                                                    @if(!empty($item['variant']))
                                                        <br><span style="color: #64748b; font-size: 11px;">Size/Variant: {{ $item['variant'] }}</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 10px; text-align: center; color: #475569;">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                                                <td style="padding: 10px; text-align: right; font-weight: 600; color: #0f172a;">
                                                    ₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- Total Breakdown -->
                    <tr>
                        <td style="padding: 10px 30px 25px 30px;">
                            <table role="presentation" width="100%" style="font-size: 14px; color: #475569;">
                                <tr>
                                    <td style="text-align: right; padding: 4px 0;">Subtotal:</td>
                                    <td style="text-align: right; padding: 4px 0; width: 100px; color: #0f172a;">₹{{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                                </tr>
                                @if(($order->discount ?? 0) > 0)
                                <tr>
                                    <td style="text-align: right; padding: 4px 0; color: #e11d48;">Discount:</td>
                                    <td style="text-align: right; padding: 4px 0; color: #e11d48;">- ₹{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="text-align: right; padding: 4px 0;">Delivery Fee:</td>
                                    <td style="text-align: right; padding: 4px 0; color: #059669; font-weight: 600;">
                                        {{ ($order->shipping ?? 0) > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; padding: 10px 0 0 0; font-size: 16px; font-weight: 800; color: #0f172a; border-top: 2px solid #e2e8f0;">Total Amount Paid:</td>
                                    <td style="text-align: right; padding: 10px 0 0 0; font-size: 18px; font-weight: 900; color: #059669; border-top: 2px solid #e2e8f0;">
                                        ₹{{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Support Footer -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b;">
                            <p style="margin: 0 0 6px 0; font-weight: 600; color: #334155;">Questions about your order?</p>
                            <p style="margin: 0;">Contact our support team or reply directly to this email.</p>
                            <p style="margin: 10px 0 0 0; font-size: 11px; color: #94a3b8;">&copy; {{ date('Y') }} Chhabra Sports. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
