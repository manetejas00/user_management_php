<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Register</h2>
        <form action="../actions/register.php" method="POST">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control mt-2" placeholder="Email" required>
            <select name="role" class="form-control mt-2" required>
                <option value="Project Manager">Project Manager</option>
                <option value="Team Lead">Team Lead</option>
                <option value="Developer">Developer</option>
            </select>
            <p class="mt-2 text-danger"><strong>Note:</strong> Your default password is <code>admin123</code>. Please change it after logging in.</p>
            <button type="submit" class="btn btn-success mt-3 w-100">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="index.php">Login</a></p>
    </div>
</body>
</html>
