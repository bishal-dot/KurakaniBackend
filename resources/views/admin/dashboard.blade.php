<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

       :root {
            --primary-color: #ff6b81;
            --secondary-color: #4a4a6a;
            --background-color: #fcebeb;
            --card-background: #ffffff;
            --text-color: #333333;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Plus Jakarta Sans", sans-serif; background-color: var(--background-color); color: var(--text-color); line-height: 1.6; display: flex; min-height: 100vh; }
        /* Sidebar */
        .sidebar { width: 250px; background-color: var(--secondary-color); color: #fff; padding: 20px; display: flex; flex-direction: column; box-shadow: var(--box-shadow); }
        .sidebar-header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 2em; font-weight: 700; color: var(--primary-color); }
        .logo-subtitle { font-size: 0.8em; color: #ccc; }
        .nav-menu { list-style-type: none; flex-grow: 1; }
        .nav-item { margin-bottom: 10px; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #fff; text-decoration: none; font-size: 1em; border-radius: var(--border-radius); transition: background-color 0.3s, transform 0.2s; }
        .nav-link:hover { background-color: rgba(255,255,255,0.1); transform: translateX(5px); }
        .nav-link.active { background-color: var(--primary-color); font-weight: 600; }
        .icon { margin-right: 15px; font-size: 1.2em; }
        /* Main content */
        .main-content { flex-grow: 1; padding: 30px; display: flex; flex-direction: column; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 2.5em; color: var(--secondary-color); font-weight: 700; } 
        .user-profile { display: flex; align-items: center; gap: 10px; }
        .user-profile span { font-weight: 600; color: var(--secondary-color); }
        .content-section { display: none; }
        .content-section.active { display: block; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .card { background-color: var(--card-background); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--box-shadow); display: flex; flex-direction: column; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card-header { display: flex; align-items: center; margin-bottom: 15px; gap: 10px; }
        .card-icon { font-size: 1.8em; color: var(--primary-color); }
        .card-header h2 { font-size: 1.2em; font-weight: 600; color: var(--secondary-color); }
        .card-body { font-size: 2.5em; font-weight: 700; color: var(--primary-color); text-align: center; margin-top: 10px; }
        .card-description { font-size: 0.9em; color: #777; text-align: center; }
        .full-card { grid-column: 1 / -1; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; text-align: center; place-items: center; }
        .data-table th { background-color: var(--background-color); color: var(--secondary-color); font-weight: 600; }
        .data-table tr:hover { background-color: #f9f9f9; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 0.8em; font-weight: 600; color: #fff; }
        .status.active { background-color: #28a745; }
        .status.suspended { background-color: #dc3545; }
        .action-icons { display: flex; gap: 10px; }
        .action-icons i { cursor: pointer; font-size: 1.1em; transition: color 0.2s, transform 0.2s; }
        .action-icons .edit-icon { color: #17a2b8; }
        .action-icons .delete-icon { color: #dc3545; }
        .action-icons .suspend-icon { color: #ffc107; }
        .action-icons i:hover { transform: scale(1.2); }
        .chart-card { height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; }
        .chart-container { width: 150px; height: 150px; border-radius: 50%; position: relative; margin-bottom: 20px; }
        .chart-legend { display: flex; justify-content: center; gap: 25px; font-size: 0.9em; }
        .legend-item { display: flex; align-items: center; gap: 5px; }
        .legend-color { width: 15px; height: 15px; border-radius: 4px; }
        .legend-color.male { background-color: var(--secondary-color); }
        .legend-color.female { background-color: var(--primary-color); }
        .status.active {
            color: #fff;
            background-color: #28a745; /* green */
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .status.pending {
            color: #fff;
            background-color: orange;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        .data-table th, .data-table td {
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">Welcome, {{ Auth::user()->username }}</div>
            <div class="logo-subtitle">KuraKani Admin</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="#" class="nav-link active" data-target="dashboard"><i class="fas fa-home icon"></i>Dashboard</a></li>
            <li class="nav-item"><a href="#" class="nav-link" data-target="users"><i class="fas fa-users icon"></i>Users</a></li>
            <li class="nav-item"><a href="#" class="nav-link" data-target="user-details"><i class="fas fa-user icon"></i>User Details</a></li>
            <li class="nav-item"><a href="#" class="nav-link" data-target="matches"><i class="fas fa-heart icon"></i>Matches</a></li>
            <li class="nav-item"><a href="#" class="nav-link" data-target="reports"><i class="fas fa-bell icon"></i>Reports</a></li>
            <li class="nav-item"><a href="#" class="nav-link" data-target="settings"><i class="fas fa-cog icon"></i>Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <h1 id="page-title">Dashboard</h1>
            <div class="user-profile">
                <span>{{ Auth::user()->username }}</span>
                <i class="fa fa-user-shield" style="color:#ff6b81; font-size:24px;"></i>
            </div>
        </header>

        <!-- Dashboard Section -->
        <div class="content-section active" id="dashboard">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header"><i class="fas fa-users card-icon"></i><h2>Total Users</h2></div>
                    <div class="card-body">{{ $totalUsers }}</div>
                    <div class="card-description">Registered on the platform</div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fas fa-user-plus card-icon"></i><h2>New Users</h2></div>
                    <div class="card-body">{{ $newUsersToday }}</div>
                    <div class="card-description">Joined Today</div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fas fa-heart card-icon"></i><h2>Total Matches</h2></div>
                    <div class="card-body">{{ $matchedCount }}</div>
                    <div class="card-description">Successful connections made</div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="fas fa-chart-bar card-icon"></i><h2>Daily Active Users</h2></div>
                    <div class="card-body">{{ $activeUsers }}</div>
                    <div class="card-description">Users active today</div>
                </div>
                <div class="card full-card chart-card">
                    <div class="card-header"><i class="fas fa-venus-mars card-icon"></i><h2>User Demographics</h2></div>
                    <div class="chart-container" style="background: conic-gradient(var(--primary-color) 0% {{ $femalePercentage }}%, var(--secondary-color) {{ $femalePercentage }}% 100%);"></div>
                    <div class="chart-legend">
                        <div class="legend-item"><div class="legend-color female"></div><span>Female {{ $femalePercentage }}%</span></div>
                        <div class="legend-item"><div class="legend-color male"></div><span>Male {{ $malePercentage }}%</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="content-section active" id="users">
            <div class="card full-card">
                <div class="card-header"><i class="fas fa-users card-icon"></i><h2>All Users</h2></div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td><span class="status {{ $user->is_suspended ? 'suspended' : 'active' }}">{{ $user->is_suspended ? 'Suspended' : 'Active' }}</span></td>
                            <td>
                                <div class="action-icons">
                                    <i class="fas fa-edit edit-icon" title="Edit User" onclick="window.location='{{ route('admin.users.edit', $user->id) }}'"></i>
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

        <!-- User Details Section -->
        <div class="content-section active" id="user-details">
            <div class="card full-card">
                <div class="card-header"><i class="fas fa-users card-icon"></i><h2>All User Details</h2></div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Fullname</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Job</th>
                            <th>Education</th>
                            <th>Interests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->fullname }}</td>
                            <td>{{ $user->age }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->job }}</td>
                            <td>{{ $user->education }}</td>
                            <td>{{ implode(', ', $user->interests) }}</td>
                            <td><span class="status {{ $user->is_suspended ? 'suspended' : 'active' }}">{{ $user->is_suspended ? 'Suspended' : 'Active' }}</span></td>
                            <td>
                                <div class="action-icons">
                                    <i class="fas fa-edit edit-icon" title="Edit User" onclick="window.location='{{ route('admin.users.edit', $user->id) }}'"></i>
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

        <!-- Matches Section -->
       <div class="content-section" id="matches">
            <div class="card full-card">
                <div class="card-header">
                    <i class="fas fa-heart card-icon"></i>
                    <h2>Matches by Users</h2>
                </div>

                @forelse ($matchesByUser as $userId => $matches)
                    @php
                        $user = $users->firstWhere('id', $userId);
                    @endphp

                    <h3 style="margin: 20px 0 10px; color: var(--secondary-color); font-weight: 600;">
                        Matches for {{ $user ? $user->username : 'Unknown User' }} (User ID: {{ $userId }})
                    </h3>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Match ID</th>
                                <th>User</th>
                                <th>Matched With</th>
                                <th>Match Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matches as $match)
                                <tr>
                                    <td>M-{{ $match['id'] }}</td>
                                    <td>{{ $match['user_name'] }}</td>
                                    <td>{{ $match['matched_user_name'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($match['created_at'])->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="status {{ $match['status'] === 'Matched' ? 'active' : 'pending' }}">
                                            {{ $match['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @empty
                    <p style="margin: 15px 0; color: #555;">No matches yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Reports Section -->
        <div class="content-section" id="reports">
            <div class="card full-card">
                <div class="card-header"><i class="fas fa-bell card-icon"></i><h2>Pending User Reports</h2></div>
                <table class="data-table">
                    <thead><tr><th>Report ID</th><th>Reported User</th><th>Reported By</th><th>Reason</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr><td>R-001</td><td>John Smith</td><td>Sarah Lee</td><td>Spamming</td><td>2025-08-22</td><td><span class="status active">Open</span></td></tr>
                        <tr><td>R-002</td><td>Emily Davis</td><td>Admin</td><td>Inappropriate content</td><td>2025-08-21</td><td><span class="status suspended">Closed</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Section -->
        <div class="content-section" id="settings">
            <div class="card full-card">
                <div class="card-header"><i class="fas fa-cog card-icon"></i><h2>Application Settings</h2></div>
                <form style="padding: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label for="app-name">Application Name</label>
                        <input type="text" id="app-name" name="app-name" value="Cupid's Compass" style="width: 100%; padding: 10px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="min-age">Minimum User Age</label>
                        <input type="number" id="min-age" name="min-age" value="18" style="width: 100%; padding: 10px;">
                    </div>
                    <button type="submit" style="background-color: var(--primary-color); color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">Save</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.content-section');
            const pageTitle = document.getElementById('page-title');
            const csrfToken = '{{ csrf_token() }}';

            // Navigation switching
            navLinks.forEach(link => {
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    navLinks.forEach(l => l.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.dataset.target).classList.add('active');
                    pageTitle.textContent = this.textContent.trim();
                });
            });

            // Suspend
            document.querySelectorAll('.suspend-icon').forEach(icon => {
                icon.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const userId = row.querySelector('td:first-child').innerText;
                    fetch(`/admin/users/${userId}/suspend`, {
                        method: 'PUT',
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    }).then(res=>res.json()).then(data=>{
                        alert(data.message);
                        const statusCell = row.querySelector('.status');
                        if(statusCell.innerText==='Active') {
                            statusCell.innerText='Suspended';
                            statusCell.classList.remove('active');
                            statusCell.classList.add('suspended');
                        } else {
                            statusCell.innerText='Active';
                            statusCell.classList.remove('suspended');
                            statusCell.classList.add('active');
                        }
                    });
                });
            });

            // Delete
            document.querySelectorAll('.delete-icon').forEach(icon => {
                icon.addEventListener('click', function(){
                    if(!confirm("Are you sure to delete this user?")) return;
                    const row = this.closest('tr');
                    const userId = row.querySelector('td:first-child').innerText;
                    fetch(`/admin/users/${userId}/delete`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': csrfToken}
                    }).then(res=>res.json()).then(data=>{
                        alert(data.message);
                        row.remove();
                    });
                });
            });

        });
    </script>
</body>
</html>
