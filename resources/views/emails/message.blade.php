<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), config('flavor.rtl_locales')) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;padding:24px;background:#fffbf5;font-family:'Segoe UI',Tahoma,sans-serif;color:#1f2937">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb">
    <tr>
        <td style="background:linear-gradient(135deg,#d97706,#92400e);padding:28px 24px;color:#fffbf5">
            <p style="margin:0;font-size:12px;letter-spacing:.18em;text-transform:uppercase;opacity:.85">{{ config('flavor.brand.name') }}</p>
            <h1 style="margin:6px 0 0;font-size:22px;font-weight:700">{{ $heading }}</h1>
        </td>
    </tr>
    <tr>
        <td style="padding:24px">
            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#4b5563">{{ $intro }}</p>

            @if (! empty($highlight))
                <div style="margin:0 0 20px;padding:16px;border:1px dashed #d97706;border-radius:12px;text-align:center">
                    <span style="font-size:26px;font-weight:700;letter-spacing:.2em;color:#92400e">{{ $highlight }}</span>
                </div>
            @endif

            @if (! empty($rows))
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px">
                    @foreach ($rows as $row)
                        <tr>
                            <td style="padding:8px 0;color:#6b7280;border-bottom:1px solid #f3f4f6">{{ $row['label'] }}</td>
                            <td style="padding:8px 0;text-align:end;font-weight:600;border-bottom:1px solid #f3f4f6">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if (! empty($action))
                <p style="margin:24px 0 0;text-align:center">
                    <a href="{{ $action['url'] }}" style="display:inline-block;padding:12px 26px;background:#d97706;color:#ffffff;border-radius:999px;text-decoration:none;font-weight:600">{{ $action['label'] }}</a>
                </p>
            @endif

            @if (! empty($footnote))
                <p style="margin:20px 0 0;font-size:12px;color:#9ca3af">{{ $footnote }}</p>
            @endif
        </td>
    </tr>
    <tr>
        <td style="padding:18px 24px;background:#f9fafb;font-size:12px;color:#9ca3af">
            {{ config('flavor.brand.address') }} · {{ config('flavor.brand.phone') }}<br>
            © {{ now()->year }} {{ config('flavor.brand.name') }}
        </td>
    </tr>
</table>
</body>
</html>
