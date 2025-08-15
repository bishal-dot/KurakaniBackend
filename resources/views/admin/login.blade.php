<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: sans-serif; background: #f3f3f3; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 8px; width: 300px; }
        input, button { width: 280px; padding: 10px; margin: 10px 0; }
        button { width: 100% !important;}
    </style>
</head>
<body>
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <h2>Admin Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <a href="{{ route('admin.register') }}">Register instead</a>
        @if ($errors->any()) <p style="color:red">{{ $errors->first() }}</p> @endif
    </form>
</body>
</html>
