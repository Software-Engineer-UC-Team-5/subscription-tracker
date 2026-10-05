<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $notification->title }}</title>
</head>

<body style="font-family: sans-serif; line-height: 1.6; color: #334155; padding: 20px; background-color: #f8fafc;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h2 style="color: #2563eb; margin-top: 0;">Subscription Tracker</h2>
        <h3 style="color: #0f172a;">{{ $notification->title }}</h3>
        <p>{{ $notification->message }}</p>
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #64748b;">
            Email ini dikirim secara otomatis oleh sistem Subscription Tracker sesuai pengaturan pengingat Anda.
        </p>
    </div>
</body>

</html>