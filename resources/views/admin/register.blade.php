<!DOCTYPE html>
<html>
<head>
    <title>Admin Register</title>
    <style>
        body { font-family: sans-serif; background: #e8f0fe; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 8px; width: 300px; display: flex; align-items: center; flex-direction: column; justify-content: center; }
        input, button { width: 280px; padding: 10px; margin: 10px auto; }
        button { width: 100% !important;}
    </style>
</head>
<body>
    <form method="POST" action="{{ route('admin.register') }}">
        @csrf
        <h2>Admin Register</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <a href="{{ route('admin.login') }}">Login instead</a>
        @if ($errors->any()) <p style="color:red">{{ $errors->first() }}</p> @endif
    </form>
</body>
</html>
