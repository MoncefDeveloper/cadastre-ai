<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Cadastre AI Inbox Alert</title>
    <!--[if mso]>
    <style>
        table {border-collapse:collapse;border-spacing:0;border:none;margin:0;}
        div, td {padding:0;}
        div {margin:0 !important;}
    </style>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        body { margin: 0; padding: 0; word-spacing: normal; background-color: #f8fafc; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding-bottom: 40px; }
        .main { margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        a { text-decoration: none; }
    </style>
</head>
<body style="margin:0;padding:0;word-spacing:normal;background-color:#f8fafc;">
    <div role="article" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;ms-text-size-adjust:100%;background-color:#f8fafc;">
        <table class="wrapper" style="width:100%;table-layout:fixed;background-color:#f8fafc;padding-bottom:40px;padding-top:40px;" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table class="main" style="margin:0 auto;width:100%;max-width:600px;border-spacing:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;" border="0" cellpadding="0" cellspacing="0">

                        <!-- Header Logo Bar (👈 Updated to Imperial Carmine #BE123C) -->
                        <tr>
                            <td align="center" style="padding: 20px 0;">
                                <h2 style="margin:0;font-size:24px;font-weight:800;letter-spacing:-0.5px;color:#0f172a;">
                                    <span style="color:#be123c;">Cadastre</span> AI
                                </h2>
                            </td>
                        </tr>

                        <!-- The Alert Card -->
                        <tr>
                            <td style="padding: 32px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">

                                <p style="margin:0 0 16px 0;font-size:16px;line-height:24px;color:#334155;">
                                    Hello <strong>{{ $agentName }}</strong>,
                                </p>
                                <p style="margin:0 0 24px 0;font-size:16px;line-height:24px;color:#334155;">
                                    You have a new unread message waiting in the shared inbox.
                                </p>

                                <!-- The Message Block (Quote Bubble) -->
                                <table style="width:100%;border-spacing:0;" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 16px 20px; background-color: #f1f5f9; border-left: 4px solid #be123c; border-radius: 0 8px 8px 0;">
                                            <p style="margin:0 0 8px 0;font-size:14px;font-weight:700;color:#0f172a;">
                                                {{ $clientName }}
                                            </p>
                                            <p style="margin:0;font-size:15px;line-height:22px;color:#475569;font-style:italic;">
                                                "{{ $snippet }}..."
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- CTA Button -->
                                <table style="width:100%;border-spacing:0;margin-top:32px;" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ $url }}" style="background-color:#be123c;color:#ffffff;display:inline-block;font-size:16px;font-weight:600;line-height:50px;text-align:center;text-decoration:none;width:240px;border-radius:8px;box-shadow:0 4px 6px -1px rgba(190,18,60,0.25);">
                                                Open Shared Inbox
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        <!-- Footer Compliance Bar -->
                        <tr>
                            <td align="center" style="padding: 24px 32px;">
                                <p style="margin:0 0 8px 0;font-size:12px;line-height:18px;color:#64748b;font-weight:600;">
                                    Cadastre AI &bull; Autonomous Real Estate Intelligence
                                </p>
                                <p style="margin:0;font-size:12px;line-height:18px;color:#94a3b8;">
                                    This is an automated transactional security alert. <br>
                                    Please do not reply directly to this email.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
