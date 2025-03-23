<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f6f6f6; font-family: Arial, sans-serif;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="background-color: #f6f6f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="background-color: #ffffff; border-radius: 5px; padding: 20px;">
                    <tr>
                        <td align="center" style="font-size: 24px; font-weight: bold; padding-bottom: 20px;">New Message
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px; padding-bottom: 10px;">Name: {{ $mailData['name'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px; padding-bottom: 10px;">Phone Number: {{ $mailData['phoneno'] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table role="presentation" border="0" cellpadding="10" cellspacing="0" width="100%"
                                style="background-color: #f9f9f9; border-radius: 5px;">
                                <tr>
                                    <td>
                                        @if ($mailData['status'] == 'Rescheduled')
                                            <p>Dear {{ $mailData['name'] ?? '' }},</p>
                                            <p>Your appointment for {{ $mailData['service'] ?? '' }} originally
                                                scheduled
                                                for {{ $mailData['previousDate'] ?? '' }} at
                                                {{ $mailData['previousTime'] ?? '' }} for the sum of
                                                {{ $mailData['price'] ?? '' }} has been
                                                <strong>{{ $mailData['status'] ?? '' }}</strong> to
                                                {{ $mailData['date'] ?? '' }} at
                                                {{ $mailData['time'] ?? '' }} by the
                                                admin. Please contact us for more information.
                                                Thank you.
                                            </p>
                                        @else
                                            <p>Dear {{ $mailData['name'] ?? '' }},</p>
                                            <p>Your appointment for {{ $mailData['service'] ?? '' }} scheduled
                                                for {{ $mailData['date'] ?? '' }} at
                                                {{ $mailData['time'] ?? '' }} for the sum of
                                                {{ $mailData['price'] ?? '' }} has been
                                                <strong>{{ $mailData['status'] ?? '' }}</strong> by the
                                                admin. Please contact us for more information.
                                                Thank you.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {{-- <tr>
                        <td align="center" style="padding-top: 20px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                style="background-color: #3498db; border-radius: 5px;">
                                <tr>
                                    <td align="center" style="padding: 10px 20px;">
                                        <a href="#"
                                            style="color: #ffffff; text-decoration: none; font-size: 16px;">View
                                            Details</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> --}}
                    <tr>
                        <td align="center" style="padding-top: 20px; font-size: 12px; color: #999999;">
                            &copy; {{ date('Y') }} Phunmiehairmpire. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
