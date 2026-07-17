<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('talenma.meta.title') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f3f4f6;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;">
                    <tr>
                        <td style="padding:32px 32px 8px;">
                            <p style="margin:0 0 24px;font-size:18px;font-weight:700;color:#4f46e5;">{{ __('talenma.meta.title') }}</p>
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 32px;">
                            <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#6b7280;">{{ __('talenma.mail.footer') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
