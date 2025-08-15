<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #eef; padding: 40px; }
        .logout-btn { background: red; color: white; padding: 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Welcome, {{ auth()->user()->name }}</h1>
    <p>This is the admin dashboard for Kurakani.</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout-btn">Logout</button>
    </form>
</body>
</html>
