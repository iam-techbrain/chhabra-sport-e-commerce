<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert - Chhabra Sports</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f172a; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #0f172a; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Email Card -->
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #1e293b; border-radius: 12px; overflow: hidden; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 25px 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: 1px;">
                                🏆 CHHABRA SPORTS
                            </h1>
                            <p style="margin: 5px 0 0 0; color: #d1fae5; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">
                                New Order Notification
                            </p>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="padding: 20px 30px 10px 30px;">
                            <div style="background-color: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 6px;">
                                <span style="color: #10b981; font-weight: 700; font-size: 15px;">🛒 New Order Placed!</span>
                                <p style="margin: 4px 0 0 0; color: #cbd5e1; font-size: 13px;">
                                    Order <strong style="color: #ffffff;">#{{ $order->order_number }}</strong> has been placed and is waiting for processing.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Customer Details Section -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <h3 style="margin: 0 0 12px 0; color: #fbbf24; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #334155; padding-bottom: 8px;">
                                👤 Customer Information
                            </h3>
                            <table role="presentation" width="100%" style="font-size: 14px; color: #cbd5e1;">
                                <tr>
                                    <td style="padding: 4px 0; width: 120px; font-weight: 600; color: #94a3b8;">Name:</td>
                                    <td style="padding: 4px 0; color: #ffffff; font-weight: 700;">{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: 600; color: #94a3b8;">Email:</td>
                                    <td style="padding: 4px 0;"><a href="mailto:{{ $order->customer_email }}" style="color: #38bdf8; text-decoration: none;">{{ $order->customer_email }}</a></td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: 600; color: #94a3b8;">Phone:</td>
                                    <td style="padding: 4px 0; color: #ffffff;">{{ $order->customer_phone }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: 600; color: #94a3b8;">Payment:</td>
                                    <td style="padding: 4px 0;">
                                        <span style="background-color: #334155; color: #34d399; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                            {{ strtoupper($order->payment_method) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: 600; color: #94a3b8; vertical-align: top;">Delivery Address:</td>
                                    <td style="padding: 4px 0; color: #e2e8f0; line-height: 1.4;">{{ $order->address }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Order Items Section -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <h3 style="margin: 0 0 12px 0; color: #fbbf24; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #334155; padding-bottom: 8px;">
                                📦 Order Summary
                            </h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="background-color: #0f172a; color: #94a3b8; text-align: left;">
                                        <th style="padding: 10px; border-bottom: 1px solid #334155;">Item</th>
                                        <th style="padding: 10px; border-bottom: 1px solid #334155; text-align: center;">Qty</th>
                                        <th style="padding: 10px; border-bottom: 1px solid #334155; text-align: right;">Price</th>
                                        <th style="padding: 10px; border-bottom: 1px solid #334155; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($order->items) || is_object($order->items))
                                        @foreach($order->items as $item)
                                            <tr style="border-bottom: 1px solid #334155; color: #e2e8f0;">
                                                <td style="padding: 10px;">
                                                    <strong style="color: #ffffff;">{{ $item['title'] ?? $item['name'] ?? 'Product Item' }}</strong>
                                                    @if(!empty($item['variant']))
                                                        <br><span style="color: #94a3b8; font-size: 11px;">Variant: {{ $item['variant'] }}</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 10px; text-align: center; color: #cbd5e1;">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                                                <td style="padding: 10px; text-align: right; color: #cbd5e1;">₹{{ number_format($item['price'] ?? 0, 2) }}</td>
                                                <td style="padding: 10px; text-align: right; font-weight: 600; color: #ffffff;">
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
                            <table role="presentation" width="100%" style="font-size: 14px; color: #cbd5e1;">
                                <tr>
                                    <td style="text-align: right; padding: 4px 0; color: #94a3b8;">Subtotal:</td>
                                    <td style="text-align: right; padding: 4px 0; width: 100px; color: #ffffff;">₹{{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                                </tr>
                                @if(($order->discount ?? 0) > 0)
                                <tr>
                                    <td style="text-align: right; padding: 4px 0; color: #f43f5e;">Discount:</td>
                                    <td style="text-align: right; padding: 4px 0; color: #f43f5e;">- ₹{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="text-align: right; padding: 4px 0; color: #94a3b8;">Shipping:</td>
                                    <td style="text-align: right; padding: 4px 0; color: #34d399;">
                                        {{ ($order->shipping ?? 0) > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; padding: 10px 0 0 0; font-size: 16px; font-weight: 800; color: #ffffff; border-top: 1px solid #334155;">Grand Total:</td>
                                    <td style="text-align: right; padding: 10px 0 0 0; font-size: 18px; font-weight: 900; color: #10b981; border-top: 1px solid #334155;">
                                        ₹{{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 20px 30px; text-align: center; border-top: 1px solid #334155; font-size: 12px; color: #64748b;">
                            <p style="margin: 0;">This email was automatically generated by <strong>Chhabra Sports Ecommerce System</strong>.</p>
                            <p style="margin: 4px 0 0 0;">Receiving Email Configured: <span style="color: #94a3b8;">{{ config('mail.get_email') }}</span></p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
