<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - KuraKani</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        :root{
            --primary-color:#ff6b81;
            --secondary-color:#4a4a6a;
            --background-color:#fcebeb;
            --card-background:#ffffff;
            --text-color:#333333;
            --border-radius:12px;
            --box-shadow:0 8px 20px rgba(0,0,0,0.15);
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Poppins',sans-serif;background-color:var(--background-color);color:var(--text-color);line-height:1.6;display:flex;justify-content:center;align-items:center;min-height:100vh;}
        .login-container{width:100%;max-width:400px;background-color:var(--card-background);padding:40px;border-radius:var(--border-radius);box-shadow:var(--box-shadow);text-align:center;}
        .logo{font-size:2.5em;font-weight:700;color:var(--primary-color);margin-bottom:5px;}
        .logo-subtitle{font-size:1em;color:var(--secondary-color);margin-bottom:30px;letter-spacing:1px;text-transform:uppercase;}
        .form-group{margin-bottom:25px;text-align:left;}
        .form-group label{display:block;font-weight:600;margin-bottom:8px;color:var(--secondary-color);}
        .form-group input{width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-size:1em;transition:border-color 0.3s;}
        .form-group input:focus{outline:none;border-color:var(--primary-color);box-shadow:0 0 0 3px rgba(255,107,129,0.2);}
        .btn{width:100%;padding:15px;font-weight:600;font-size:1.1em;border:none;border-radius:8px;cursor:pointer;text-decoration:none;text-align:center;transition:background-color 0.3s,transform 0.2s;}
        .btn-primary{background-color:var(--primary-color);color:#ffffff;}
        .btn-primary:hover{background-color:#e55c70;transform:translateY(-2px);}.link-text{margin-top:20px;font-size:0.9em;color:#777;}
        .login-container img{width:80px;height:80px;margin-bottom:10px;}
        .error-message{color:red;margin-bottom:15px;font-size:0.9em;}
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="logo">
        <i class="fa fa-user-shield" style="color:#ff6b81; font-size:24px;"></i>
        KuraKani
    </div>
    <div class="logo-subtitle">Admin Login</div>

    <!-- Display errors -->
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
    @csrf
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('access'))
        <div class="error-message">
            {{ session('access') }}
        </div>
    @endif

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
</form>

</div>

</body>
</html>
