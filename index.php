<?php
require_once 'config/db.php';

// Handle form submission to add a new project
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO projects (title) VALUES (?)");
        $stmt->execute([$title]);
        header("Location: index.php");
        exit();
    } else {
        $message = "Project title cannot be empty!";
    }
}

// Fetch all projects from database
$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Task Manager</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        form { display: flex; gap: 10px; margin-bottom: 20px; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        ul { list-style: none; padding: 0; }
        li { background: #e9ecef; padding: 10px; margin-bottom: 8px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        a { text-decoration: none; color: #007bff; font-weight: bold; }
        a:hover { text-decoration: underline; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Smart Task & Project Manager 🚀</h1>

        <?php if (!empty($message)): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Add Project Form -->
        <form action="index.php" method="POST">
            <input type="text" name="title" placeholder="Enter new project name..." required>
            <button type="submit" name="add_project">Add Project</button>
        </form>

        <h2>My Projects</h2>
        <ul>
            <?php if (count($projects) > 0): ?>
                <?php foreach ($projects as $project): ?>
                    <li>
                        <a href="project.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a>
                        <small><?php echo $project['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No projects found yet. Create your first one above!</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>