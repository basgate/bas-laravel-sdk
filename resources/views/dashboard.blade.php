<!-- resources/views/bas/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        }
        h1 {
            margin-bottom: 20px;
        }
        .button {
            display: block;
            width: 80%;
            padding: 20px;
            margin: 10px 0;
            text-align: center;
            color: white;
            background-color: #EB6867;
            border-radius: 5px;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #421c1c;
        }
    </style>
</head>
<body>
@auth
    <p>مرحبا، {{ auth()->user()->name }}</p>
@endauth

@guest
    <p>مرحبا، زائر</p>
@endguest

<h1>Dashboard</h1>
<a href="{{ url('/bas/login') }}" class="button">دخول</a>
<a href="{{ url('/bas/pay') }}" class="button">دفع</a>
</body>
</html>
