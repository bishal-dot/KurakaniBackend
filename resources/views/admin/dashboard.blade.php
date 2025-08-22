<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupid's Compass Admin Dashboard</title>
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
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            background-color: var(--secondary-color);
            color: #ffffff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: var(--box-shadow);
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 2em;
            font-weight: 700;
            color: var(--primary-color);
        }

        .logo-subtitle {
            font-size: 0.8em;
            color: #ccc;
        }

        .nav-menu {
            list-style-type: none;
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #ffffff;
            text-decoration: none;
            font-size: 1em;
            border-radius: var(--border-radius);
            transition: background-color 0.3s, transform 0.2s;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--primary-color);
            font-weight: 600;
        }

        .icon {
            margin-right: 15px;
            font-size: 1.2em;
        }

        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
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
            display: none; /* Hide all content sections by default */
        }

        .content-section.active {
            display: block; /* Show the active one */
        }

        .dashboard-grid {
            display: grid;grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));gap: 25px;
        }

        .card {
            background-color: var(--card-background);padding: 25px;border-radius: var(--border-radius);box-shadow: var(--box-shadow);display: flex;flex-direction: column;transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;align-items: center;margin-bottom: 15px;gap: 10px;
        }

        .card-icon {
            font-size: 1.8em;color: var(--primary-color);
        }

        .card-header h2 {
            font-size: 1.2em;font-weight: 600;color: var(--secondary-color);
        }

        .card-body {
            font-size: 2.5em;font-weight: 700;color: var(--primary-color);text-align: center;margin-top: 10px;
        }

        .card-description {
            font-size: 0.9em;color: #777;text-align: center;
        }

        /* --- Table styles --- */
        .full-card {
            grid-column: 1 / -1;
        }

        .data-table {
            width: 100%;border-collapse: collapse;margin-top: 20px;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;text-align: left;border-bottom: 1px solid #eee;
        }

        .data-table th {
            background-color: var(--background-color);color: var(--secondary-color);font-weight: 600;
        }

        .data-table tr:hover {
            background-color: #f9f9f9;
        }

        .status {
            display: inline-block;padding: 5px 10px;border-radius: 20px;font-size: 0.8em;font-weight: 600;color: #fff;
        }

        .status.active { background-color: #28a745; }
        .status.inactive { background-color: #dc3545; }
        .status.pending { background-color: #ffc107; }
        .status.approved { background-color: #17a2b8; }
        .status.rejected { background-color: #dc3545; }
        .status.open { background-color: #28a745; }
        .status.closed { background-color: #6c757d; }

        /* --- New Action Icons CSS --- */
        .action-icons {
            display: flex;gap: 10px;
        }

        .action-icons i {
            cursor: pointer;font-size: 1.1em;transition: color 0.2s, transform 0.2s;
        }

        .action-icons .edit-icon { color: #17a2b8; }
        .action-icons .delete-icon { color: #dc3545; }
        .action-icons .suspend-icon { color: #ffc107; }

        .action-icons i:hover {
            transform: scale(1.2);
        }
        
        /* --- Graph Card CSS --- */
        .chart-card {
            height: 300px;display: flex;flex-direction: column;align-items: center;justify-content: center;position: relative;
        }

        .chart-container {
            width: 150px;height: 150px;border-radius: 50%;
            background: conic-gradient(
                var(--primary-color) 0% 60%,
                var(--secondary-color) 60% 100%
            );
            position: relative;margin-bottom: 20px;
        }

        .chart-label {
            position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-size: 1.2em;font-weight: 600;color: var(--text-color);
        }

        .chart-legend {
            display: flex;justify-content: center;gap: 25px;font-size: 0.9em;
        }

        .legend-item {
            display: flex;align-items: center;gap: 5px;
        }

        .legend-color {
            width: 15px;height: 15px;border-radius: 4px;
        }

        .legend-color.male { background-color: var(--secondary-color); }
        .legend-color.female { background-color: var(--primary-color); }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">Welcome, {{ Auth::user()->username }}</div>
            <div class="logo-subtitle">KuraKani Admin</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#" class="nav-link active" data-target="dashboard">
                    <i class="fas fa-home icon"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="users">
                    <i class="fas fa-users icon"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="matches">
                    <i class="fas fa-heart icon"></i>
                    Matches
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="reports">
                    <i class="fas fa-bell icon"></i>
                    Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-target="settings">
                    <i class="fas fa-cog icon"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header class="header">
            <h1 id="page-title">Dashboard</h1>
            <div class="user-profile">
                <span>Admin User</span>
                <i class="fa fa-user-shield" style="color:#ff6b81; font-size:24px;"></i>
            </div>
        </header>

        <div class="content-section active" id="dashboard">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users card-icon"></i>
                        <h2>Total Users</h2>
                    </div>
                    <div class="card-body">{{ $totalUsers }}</div>
                    <div class="card-description">Registered on the platform</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-plus card-icon"></i>
                        <h2>New Users</h2>
                    </div>
                    <div class="card-body">{{ $newUsersToday }}</div>
                    <div class="card-description">Joined in the last 30 days</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-heart card-icon"></i>
                        <h2>Total Matches</h2>
                    </div>
                    <div class="card-body">5,432</div>
                    <div class="card-description">Successful connections made</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar card-icon"></i>
                        <h2>Daily Active Users</h2>
                    </div>
                    <div class="card-body">875</div>
                    <div class="card-description">Users active today</div>
                </div>

                <div class="card full-card chart-card">
                    <div class="card-header">
                        <i class="fas fa-venus-mars card-icon"></i>
                        <h2>User Demographics</h2>
                    </div>
                    <div class="chart-container">
                        </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color female"></div>
                            <span>Female Users (60%)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color male"></div>
                            <span>Male Users (40%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section" id="users">
            <div class="card full-card">
                <div class="card-header">
                    <i class="fas fa-users card-icon"></i>
                    <h2>All Users</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $users)
                            <tr>
                            <td>101</td>
                            <td>Jane Doe</td>
                            <td>Female</td>
                            <td>28</td>
                            <td><span class="status active">Active</span></td>
                            <td>
                                <div class="action-icons">
                                    <i class="fas fa-edit edit-icon" title="Edit User"></i>
                                    <i class="fas fa-trash-alt delete-icon" title="Delete User"></i>
                                    <i class="fas fa-ban suspend-icon" title="Suspend User"></i>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section" id="matches">
            <div class="card full-card">
                <div class="card-header">
                    <i class="fas fa-heart card-icon"></i>
                    <h2>Recent Matches</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Match ID</th>
                            <th>User 1</th>
                            <th>User 2</th>
                            <th>Match Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>M-001</td>
                            <td>Jane Doe</td>
                            <td>John Smith</td>
                            <td>2025-08-22</td>
                            <td><span class="status active">Active</span></td>
                        </tr>
                        <tr>
                            <td>M-002</td>
                            <td>Emily Davis</td>
                            <td>Michael Brown</td>
                            <td>2025-08-21</td>
                            <td><span class="status active">Active</span></td>
                        </tr>
                        <tr>
                            <td>M-003</td>
                            <td>Alex Johnson</td>
                            <td>Sophia Wilson</td>
                            <td>2025-08-20</td>
                            <td><span class="status inactive">Inactive</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section" id="reports">
            <div class="card full-card">
                <div class="card-header">
                    <i class="fas fa-bell card-icon"></i>
                    <h2>Pending User Reports</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Reported User</th>
                            <th>Reported By</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>R-001</td>
                            <td>John Smith</td>
                            <td>Sarah Lee</td>
                            <td>Spamming messages</td>
                            <td>2025-08-22</td>
                            <td><span class="status open">Open</span></td>
                        </tr>
                        <tr>
                            <td>R-002</td>
                            <td>Emily Davis</td>
                            <td>Admin</td>
                            <td>Inappropriate content</td>
                            <td>2025-08-21</td>
                            <td><span class="status closed">Closed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-section" id="settings">
            <div class="card full-card">
                <div class="card-header">
                    <i class="fas fa-cog card-icon"></i>
                    <h2>Application Settings</h2>
                </div>
                <form style="padding: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label for="app-name" style="display: block; font-weight: 600; margin-bottom: 5px;">Application Name</label>
                        <input type="text" id="app-name" name="app-name" value="Cupid's Compass" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="min-age" style="display: block; font-weight: 600; margin-bottom: 5px;">Minimum User Age</label>
                        <input type="number" id="min-age" name="min-age" value="18" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                    </div>
                    <button type="submit" style="background-color: var(--primary-color); color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.content-section');
            const pageTitle = document.getElementById('page-title');

            navLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    // Remove 'active' class from all links and sections
                    navLinks.forEach(l => l.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));

                    // Add 'active' class to the clicked link
                    this.classList.add('active');

                    // Get the target section ID and show it
                    const targetId = this.dataset.target;
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.add('active');
                        // Update the page title
                        pageTitle.textContent = this.textContent.trim();
                    }
                });
            });
        });
    </script>
</body>
</html>