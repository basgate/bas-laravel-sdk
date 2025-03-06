<!-- resources/views/bas/operation_failed.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation Failed</title>
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
            color: red;
        }
        p {
            margin-top: 20px;
        }
    </style>
<body>
<h1>Operation Failed</h1>
<p>Sorry, the operation could not be completed. Please try again later.</p>
<a href="{{ url('/bas') }}" class="button">Go to Dashboard</a>
</body>
</html>
