<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$limit = 50;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : "";

$roleFilter = isset($_GET['role']) ? $_GET['role'] : "";

$query = "SELECT id, username, email, role FROM users WHERE 1";

if ($search !== "")
{
    $query .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}

if ($roleFilter !== "")
{
    $query .= " AND role = '$roleFilter'";
}

$query .= " ORDER BY role, username LIMIT $limit OFFSET $offset";

$users = $conn->query($query);

$countQuery = "SELECT COUNT(*) AS total FROM users WHERE 1";

if ($search !== "")
{
    $countQuery .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}

if ($roleFilter !== "")
{
    $countQuery .= " AND role = '$roleFilter'";
}

$total = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="adminWrapper">
    <h1>All Users</h1>

    <form method="GET" style="margin-bottom:20px;" class="users-toolbar">
        <input type="text" name="search" placeholder="Search username/email" value="<?php echo $search; ?>">
        
        <select name="role">
            <option value="">All Roles</option>
            <option value="student" <?php if($roleFilter=="student") echo "selected"; ?>>Student</option>
            <option value="educator" <?php if($roleFilter=="educator") echo "selected"; ?>>Educator</option>
            <option value="parent" <?php if($roleFilter=="parent") echo "selected"; ?>>Parent</option>
            <option value="admin" <?php if($roleFilter=="admin") echo "selected"; ?>>Admin</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
        </tr>

        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo $u['username']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td><?php echo $u['role']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div style="margin-top:20px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&role=<?php echo $roleFilter; ?>" 
               style="margin: 0 5px; color: cyan;">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <br>
    <a id="logoutBtn" href="admin_panel.php">Back to Admin Panel</a>
</div>

</body>
</html>
