<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <!-- Klien email yang mendukung web font memakai Schibsted Grotesk; sisanya jatuh ke Arial -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600;700&display=swap">
</head>

@php
    // Relasi bisa kosong (misalnya pengingat sudah dihapus), jadi biaya dan tombol hanya tampil jika ada
    $subscription = $notification->reminder?->subscription;
    $font = "'Schibsted Grotesk', Arial, Helvetica, sans-serif";
@endphp

<!-- Tata letak memakai tabel dan gaya inline agar konsisten di Gmail, Outlook, dan klien email lain -->
<body style="margin: 0; padding: 0; background-color: #F4F4F5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #F4F4F5;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; background-color: #FFFFFF; border: 1px solid #E4E4E7; border-radius: 24px; border-collapse: separate; overflow: hidden;">
                    <!-- Kepala email -->
                    <tr>
                        <td style="background-color: #0D0D0F; padding: 24px 32px; border-radius: 24px 24px 0 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="36" height="36"
                                        style="width: 36px; height: 36px; background-color: #D93A40; border-radius: 12px; font-size: 0; line-height: 0;">
                                        &nbsp;</td>
                                    <td
                                        style="padding-left: 12px; font-family: {!! $font !!}; font-size: 17px; font-weight: 700; color: #FFFFFF;">
                                        Subscription Tracker</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Isi pengingat -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1
                                style="margin: 0; font-family: {!! $font !!}; font-size: 24px; line-height: 32px; font-weight: 700; color: #0D0D0F;">
                                {{ $notification->title }}</h1>
                            <p
                                style="margin: 12px 0 0; font-family: {!! $font !!}; font-size: 16px; line-height: 26px; color: #3F3F46;">
                                {{ $notification->message }}</p>

                            @if ($subscription)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="margin-top: 24px; background-color: #F4F4F5; border-radius: 16px; border-collapse: separate;">
                                    <tr>
                                        <td
                                            style="padding: 16px 20px; font-family: {!! $font !!}; font-size: 14px; color: #6B6B73;">
                                            Biaya</td>
                                        <td align="right"
                                            style="padding: 16px 20px; font-family: {!! $font !!}; font-size: 18px; font-weight: 700; color: #0D0D0F; white-space: nowrap;">
                                            Rp {{ number_format($subscription->price, 0, ',', '.') }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top: 24px;">
                                    <tr>
                                        <td style="background-color: #D93A40; border-radius: 26px;">
                                            <a href="{{ route('subscriptions.show', $subscription) }}"
                                                style="display: inline-block; padding: 16px 28px; font-family: {!! $font !!}; font-size: 15px; line-height: 20px; font-weight: 700; color: #FFFFFF; text-decoration: none; border-radius: 26px;">
                                                Lihat detail langganan</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    <!-- Kaki email -->
                    <tr>
                        <td style="padding: 20px 32px; border-top: 1px solid #EEEEF0;">
                            <p
                                style="margin: 0; font-family: {!! $font !!}; font-size: 13px; line-height: 20px; color: #6B6B73;">
                                Email ini dikirim otomatis oleh Subscription Tracker sesuai pengaturan pengingatmu.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
