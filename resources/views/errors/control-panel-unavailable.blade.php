@php
    $brand = config('statamic.cp.custom_cms_name', config('app.name', 'Dric Interior'));
    $logo = config('statamic.cp.custom_logo_url', '/golden-dric-logo.png');
    $support = config('statamic.cp.support_url');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand }}</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #121317;
            --panel: #1b1d22;
            --border: rgba(240, 206, 151, 0.16);
            --text: #f4efe8;
            --muted: #b8ad9e;
            --accent: #f2bd20;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                radial-gradient(circle at top, rgba(242, 189, 32, 0.12), transparent 34%),
                var(--bg);
            color: var(--text);
            font: 16px/1.6 Arial, sans-serif;
        }

        .panel {
            width: min(100%, 560px);
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: rgba(27, 29, 34, 0.94);
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.35);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            line-height: 1.15;
        }

        p {
            margin: 0;
            color: var(--muted);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
        }

        .button-primary {
            background: var(--accent);
            color: #1a1406;
        }

        .button-secondary {
            border: 1px solid var(--border);
            color: var(--text);
        }
    </style>
</head>
<body>
    <main class="panel">
        <div class="brand">
            <img src="{{ $logo }}" alt="{{ $brand }}">
            <strong>{{ $brand }}</strong>
        </div>

        <h1>Admin workspace temporarily unavailable.</h1>
        <p>We could not open the management area right now. Please try again in a moment.</p>

        <div class="actions">
            <a class="button button-primary" href="{{ url('/') }}">Back to website</a>
            @if ($support)
                <a class="button button-secondary" href="{{ $support }}">Contact support</a>
            @endif
        </div>
    </main>
</body>
</html>
