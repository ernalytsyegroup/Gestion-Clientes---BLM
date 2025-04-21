<?php
// Include database and user model
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'utils/session.php';

// If already logged in, redirect to index
if(isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Process login form
$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set user properties
    $user->correo_usuario = $_POST['usuario_correo'];
    
    // Attempt to login
    if($user->login()) {
        // Set session
        setUserSession($user->id_usuario, $user->nombre_usuario, $user->isAdmin());
        header("Location: index.php");
        exit();
    } else {
        $message = 'Usuario o correo electrónico no encontrado.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f9fafb;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .login-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .login-logo img {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <img src="assets/img/logo.png" alt="Logo">
            </div>
            
            <h1 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h1>
            
            <?php if(!empty($message)): ?>
                <div class="alert alert-danger mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="usuario_correo" class="form-label">Usuario o Correo Electrónico</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="usuario_correo" name="usuario_correo" required 
                               class="form-control pl-10">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-full mt-4">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
