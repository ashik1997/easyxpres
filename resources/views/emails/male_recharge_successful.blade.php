<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Male Recharge Successful</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
        <tr>
            <td align="center">
                
                <!-- Card -->
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:520px; background:#ffffff; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.08); padding:30px;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="margin:0; font-size:22px; color:#16a34a;">
                                ✅ Male Recharge Successful
                            </h2>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="font-size:15px; color:#374151; line-height:24px;">
                            <p style="margin:0 0 12px 0;">
                                Hello,
                            </p>

                            <p style="margin:0 0 16px 0;">
                                Your mobile recharge has been completed successfully.  
                                Here are the details:
                            </p>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f9fafb; border-radius:8px; padding:16px;">
                                <tr>
                                    <td style="font-weight:bold; color:#111827;">
                                        Mobile Number
                                    </td>
                                    <td align="right" style="color:#111827;">
                                        {{ $mobile }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="height:8px;"></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold; color:#111827;">
                                        Amount
                                    </td>
                                    <td align="right" style="color:#16a34a; font-weight:bold;">
                                        MVR {{ number_format($amount, 2) }}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0 0;">
                                Thank you for using <strong>EasyXpres</strong>.  
                                If you have any questions, feel free to contact us.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:25px; font-size:12px; color:#6b7280;">
                            © {{ date('Y') }} EasyXpres. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
