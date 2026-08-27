{{-- resources/views/emails/student-credentials.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your InternTrack Account Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 24px; color: #1e293b;">
    <table role="presentation" width="100%" style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #f1f5f9;">
        <tr>
            <td style="background-color: #2563eb; padding: 24px; text-align: center;">
                <span style="color: #ffffff; font-size: 18px; font-weight: 800;">InternTrack</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px;">
                <p style="font-size: 14px; margin: 0 0 16px;">
                    Hello {{ $student->first_name }} {{ $student->last_name }},
                </p>
                <p style="font-size: 14px; margin: 0 0 20px;">
                    Your InternTrack account has been created.
                </p>

                <table role="presentation" width="100%" style="background: #f8fafc; border-radius: 12px; padding: 4px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 12px 16px;">
                            <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0;">Username</p>
                            <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 2px 0 0;">{{ $student->student_number }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 16px;">
                            <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0;">Password</p>
                            <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 2px 0 0;">{{ $password }}</p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 14px; margin: 0 0 8px;">
                    Please log in and verify your email address.
                </p>
                <p style="font-size: 14px; margin: 0;">
                    Thank you.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>