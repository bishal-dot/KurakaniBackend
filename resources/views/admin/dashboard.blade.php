<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dating App Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Basic Reset & Body Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr; /* Sidebar width and main content */
            grid-template-rows: 60px 1fr; /* Header height and main content */
            height: 100vh;
        }

        /* Header Styling */
        .dashboard-header {
            grid-column: 1 / -1; /* Spans across both columns */
            background-color: #2c3e50; /* Dark blue */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        .dashboard-header .user-info {
            display: flex;
            align-items: center;
        }

        .dashboard-header .user-info span {
            margin-right: 15px;
        }

        .dashboard-header .user-info i {
            font-size: 1.2em;
            margin-right: 10px;
        }

        .dashboard-header .logout-btn {
            background-color: #e74c3c; /* Red */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .dashboard-header .logout-btn:hover {
            background-color: #c0392b;
        }

        /* Sidebar Styling */
        .dashboard-sidebar {
            grid-row: 2 / 3; /* Stays in the second row (under header) */
            background-color: #34495e; /* Slightly lighter dark blue */
            color: white;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav ul li {
            margin-bottom: 5px;
        }

        .sidebar-nav ul li a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar-nav ul li a i {
            margin-right: 10px;
        }

        .sidebar-nav ul li a:hover,
        .sidebar-nav ul li.active a {
            background-color: #2980b9; /* Blue */
            color: white;
        }

        /* Main Content Area */
        .dashboard-main-content {
            grid-column: 2 / 3; /* Occupies the second column */
            grid-row: 2 / 3;
            padding: 20px;
            overflow-y: auto; /* Enable scrolling for content */
        }

        .content-section {
            display: none; /* Hide all sections by default */
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px; /* Space between sections if multiple are visible */
        }

        .content-section.active-content {
            display: block; /* Show active section */
        }

        .content-section h2 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #ecf0f1; /* Light gray */
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            margin-top: 0;
            color: #34495e;
            font-size: 1.1em;
        }

        .stat-card p {
            font-size: 2em;
            font-weight: bold;
            color: #2980b9;
        }

        /* General Form & Table Styling */
        .controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        input[type="text"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%; /* For full width in form groups */
            box-sizing: border-box;
        }

        .btn {
            background-color: #3498db; /* Blue */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-primary {
            background-color: #2ecc71; /* Green */
        }

        .btn-primary:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: #e74c3c; /* Red */
            margin: 10px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
            margin: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;

        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #555;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .data-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        /* Moderation & Support Lists */
        .moderation-list, .support-tickets {
            list-style: none;
            padding: 0;
        }

        .moderation-list li, .support-tickets li {
            background-color: #fdfdfd;
            border: 1px solid #eee;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .moderation-list li i, .support-tickets li i {
            margin-right: 10px;
            color: #777;
        }

        /* Chart Placeholder */
        .chart-placeholder {
            background-color: #ecf0f1;
            border: 1px dashed #ccc;
            padding: 50px 20px;
            text-align: center;
            color: #777;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Range input styling */
        input[type="range"] {
            width: calc(100% - 70px); /* Adjust width to make space for value display */
            margin-right: 10px;
            vertical-align: middle;
        }
        #distance-value {
            display: inline-block;
            min-width: 60px; /* Ensure space for longer values */
            text-align: right;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="logo">DatingApp Admin</div>
            <div class="user-info">
                <span>Welcome, Admin!</span>
                <i class="fas fa-user-circle"></i>
                <button class="logout-btn">Logout</button>
            </div>
        </header>

        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#dashboard-overview"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
                    <li><a href="#user-management"><i class="fas fa-users"></i> User Management</a></li>
                    <li><a href="#user-details"><i class="fas fa-users"></i> User Details</a></li>
                    <li><a href="#content-moderation"><i class="fas fa-shield-alt"></i> Content Moderation</a></li>
                    <li><a href="#matchmaking-control"><i class="fas fa-heart"></i> Matchmaking Control</a></li>
                    <li><a href="#analytics-reports"><i class="fas fa-chart-line"></i> Analytics & Reports</a></li>
                    <li><a href="#settings"><i class="fas fa-cog"></i> System Settings</a></li>
                    <li><a href="#support"><i class="fas fa-life-ring"></i> Support & Disputes</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-main-content">
            <section id="dashboard-overview" class="content-section active-content">
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total User(s)</h3>
                        <p>{{ $totalUsers }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>New Registrations (Today)</h3>
                        <p>{{ $newUsersToday }}</p>
                    </div>
                    <div class="stat-card">
                        <h3>Premium Subscriptions</h3>
                        <p>2,100</p>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Reports</h3>
                        <p>32</p>
                    </div>
                </div>
                {{-- More detailed graphs/charts would go here (e.g., using Chart.js) --}}
            </section>

            <section id="user-management" class="content-section">
                <h2>User Management</h2>
                <p>Tables to view, edit, suspend, or ban user accounts.</p>
                <div class="controls">
                    <input type="text" placeholder="Search users...">
                    <button class="btn">Search</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Created_at</th>
                            <th>Actions</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->password }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td><button class="btn-small">Edit</button> <button class="btn-small btn-danger">Suspend</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section id="user-details" class="content-section">
                <h2>User Details</h2>
                <p>Tables to view other user details.</p>
                <div class="controls">
                    <input type="text" placeholder="Search users...">
                    <button class="btn">Search</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Job</th>
                            <th>Interests</th>
                            <th>Education</th>
                            <th>About</th>
                            <th>Actions</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->gender }}</td>
                                <td>{{ $user->age }}</td>
                                <td>{{ $user->job }}</td>
                                <td>{{ $user->interests }}</td>
                                <td>{{ $user->education }}</td>
                                <td>{{ $user->about }}</td>
                                <td><button class="btn-small">Edit</button> <button class="btn-small btn-danger">Suspend</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section id="content-moderation" class="content-section">
                <h2>Content Moderation</h2>
                <p>Manage reported content: photos, bios, chat messages. Review and take action.</p>
                <ul class="moderation-list">
                    <li><i class="fas fa-image"></i> Photo Report: User "JaneDoe" - Nudity <button class="btn-small btn-danger">Review</button></li>
                    <li><i class="fas fa-comment"></i> Chat Report: User "John123" vs "SarahB" - Harassment <button class="btn-small btn-danger">Review</button></li>
                </ul>
            </section>

            <section id="matchmaking-control" class="content-section">
                <h2>Matchmaking Control</h2>
                <p>Adjust the dating algorithm parameters and manage discovery preferences.</p>
                <div class="form-group">
                    <label for="distance">Matching Distance Radius (km):</label>
                    <input type="range" id="distance" min="1" max="500" value="50">
                    <span id="distance-value">50 km</span>
                </div>
                <button class="btn btn-primary">Save Settings</button>
            </section>

            <section id="analytics-reports" class="content-section">
                <h2>Analytics & Reports</h2>
                <p>View detailed charts and graphs for user engagement, revenue, and app performance.</p>
                <div class="chart-placeholder">
                    <p>Graph for daily active users would go here.</p>
                </div>
                <div class="chart-placeholder">
                    <p>Graph for monthly revenue would go here.</p>
                </div>
            </section>

            <section id="settings" class="content-section">
                <h2>System Settings</h2>
                <p>Manage global app settings, user roles, and integrations.</p>
                <div class="form-group">
                    <label for="terms-status">Terms & Conditions Status:</label>
                    <select id="terms-status">
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="feature-x">
                    <label for="feature-x">Enable New Messaging Feature</label>
                </div>
                <button class="btn btn-primary">Update Settings</button>
            </section>

            <section id="support" class="content-section">
                <h2>Support & Disputes</h2>
                <p>Handle user support tickets and mediate disputes.</p>
                <ul class="support-tickets">
                    <li>Ticket #001: Payment Issue - User "Alice" <button class="btn-small">View</button></li>
                    <li>Ticket #002: Profile Bug - User "Bob" <button class="btn-small">View</button></li>
                </ul>
            </section>
        </main>
    </div> 

    <script>
        // Simple JavaScript for tab switching
        document.querySelectorAll('.sidebar-nav ul li a').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
                document.querySelectorAll('.sidebar-nav ul li').forEach(li => li.classList.remove('active'));
                item.parentElement.classList.add('active');

                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active-content'));
                document.querySelector(item.getAttribute('href')).classList.add('active-content');
            });
        });

        // Update distance value for range input
        const distanceRange = document.getElementById('distance');
        const distanceValueSpan = document.getElementById('distance-value');
        if (distanceRange && distanceValueSpan) {
            distanceRange.addEventListener('input', (event) => {
                distanceValueSpan.textContent = `${event.target.value} km`;
            });
        }
    </script>
</body>
</html>