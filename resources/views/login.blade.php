<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAS Authentication Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        h1 span {
            color: #EB6867;
            font-size: 1.5em;
        }
        p {
            color: #666;
            margin-bottom: 20px;
            animation: fadeInOut 2s infinite;
        }
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

    </style>
</head>
<body>
<h1>تسجيل الدخول عبر <span>بس</span></h1>
<p>سيتم تحويلكم خلال لحظات ...</p>

<script>
    {!! $authCodeJS !!}

</script>
</body>
</html>
