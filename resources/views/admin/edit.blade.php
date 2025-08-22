<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Cupid's Compass Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        :root {
            --primary-color: #ff6b81; /* A pink-red for a loving theme */
            --secondary-color: #4a4a6a; /* A dark purple-blue for contrast */
            --background-color: #fcebeb; /* A very light pink for the background */
            --card-background: #ffffff; /* White for cards */
            --text-color: #333333;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;margin: 0;padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;background-color: var(--background-color);color: var(--text-color);line-height: 1.6;display: flex;min-height: 100vh;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;background-color: var(--secondary-color);color: #ffffff;padding: 20px;display: flex;flex-direction: column;box-shadow: var(--box-shadow);
        }

        .sidebar-header {
            text-align: center;margin-bottom: 30px;
        }

        .logo {
            font-size: 2em;font-weight: 700;color: var(--primary-color);
        }

        .logo-subtitle {
            font-size: 0.8em;color: #ccc;
        }

        .nav-menu {
            list-style-type: none;flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;align-items: center;padding: 12px 15px;color: #ffffff;text-decoration: none;font-size: 1em;border-radius: var(--border-radius);transition: background-color 0.3s, transform 0.2s;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--primary-color);font-weight: 600;
        }

        .icon {
            margin-right: 15px;font-size: 1.2em;
        }

        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;padding: 30px;display: flex;flex-direction: column;
        }

        .header {
            display: flex;justify-content: space-between;align-items: center;margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;color: var(--secondary-color);font-weight: 700;
        }

        .user-profile {
            display: flex;align-items: center;gap: 10px;
        }

        .user-profile img {
            width: 40px;height: 40px;border-radius: 50%;border: 2px solid var(--primary-color);
        }

        .user-profile span {
            font-weight: 600;color: var(--secondary-color);
        }

        .content-section {
            padding: 20px;background-color: var(--card-background);border-radius: var(--border-radius);box-shadow: var(--box-shadow);
        }
        
        /* --- Form Styles --- */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;font-weight: 600;margin-bottom: 8px;color: var(--secondary-color);
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;padding: 12px;border: 1px solid #ddd;border-radius: 8px;font-size: 1em;transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;border-color: var(--primary-color);
        }

        .form-actions {
            display: flex;gap: 15px;margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;font-weight: 600;border: none;border-radius: 8px;cursor: pointer;text-decoration: none;text-align: center;transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #e55c70;transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #e0e0e0;color: var(--text-color);
        }

        .btn-secondary:hover {
            background-color: #d0d0d0;transform: translateY(-2px);
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">Cupid's Compass</div>
            <div class="logo-subtitle">Admin Panel</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-home icon"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="fas fa-users icon"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-heart icon"></i>
                    Matches
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-bell icon"></i>
                    Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog icon"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header class="header">
            <h1>Edit User: Jane Doe</h1>
            <div class="user-profile">
                <span>Admin User</span>
                <i class="fa fa-user-shield" style="color:#ff6b81; font-size:24px;"></i>
            </div>
        </header>

        <div class="content-section">
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="Jane Doe" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="jane.d@example.com" required>
                </div>

                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="28" min="18" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="female" selected>Female</option>
                        <option value="male">Male</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Account Status</label>
                    <select id="status" name="status" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="#" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>