<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new UserController();
    if (isset($_POST["login"])) {
        $user->login();
    }
    if (isset($_POST["update-profile"])) {
        $user->updateProfile();
    }
    if (isset($_POST["update-password"])) {
        $user->updatePasswd();
    }
    if (isset($_POST["logout"])) {
        $user->logout();
    }
    if (isset($_POST["register"])) {
        $user->register();
    }
    if (isset($_POST["delete"])) {
        $user->delete();
    }
}

class UserController
{
    private $conn;


    public function __construct()
    {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $database = "CFC";

        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Conexión fallida: " . $e->getMessage() . "\nContacte un administrador.");
        }
    }

    public function login(): void
    {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION["error"] = "Por favor complete todos los campos";
            header("Location: ../View/login.php");
            exit;
        }

        $email = $_POST['email'];
        $inputPassword = $_POST['password'];

        $stmt = $this->conn->prepare("SELECT id, name, userrole, email, password FROM users WHERE email = ?");

        if (!$stmt->execute([$email])) {
            $_SESSION["error"] = "Error en la consulta";
            header("Location: ../View/login.php");
            exit;
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($user["email"])) {
            $_SESSION["error"] = "Usuario no encontrado";
            header("Location: ../View/login.php");
            exit;
        }

        if (!password_verify($inputPassword, $user["password"])) {
            $_SESSION["error"] = "Contraseña incorrecta";
            header("Location: ../View/login.php");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['logged'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['userrole'];

        // Get redirect safely and make sure relative path is valid
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../index.php';

        if (strpos($redirect, '/') !== 0 && !filter_var($redirect, FILTER_VALIDATE_URL)) {
            $redirect = '../index.php';
        }

        header("Location: " . $redirect);
        exit;
    }

    public function register(): void
    {
        $username = trim($_POST['usuario']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $date = date("Y-m-d");
        $userrole = "user";

        if (empty($username) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"] = "Datos inválidos.";
            header("Location: ../View/sign_in.php");
            exit;
        }

        // Verificar si el correo ya existe
        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            $_SESSION["error"] = "La dirección de correo ya está registrada.";
            header("Location: ../View/sign_in.php");
            exit;
        }

        // Insertar nuevo usuario
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, userrole, creation_date) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt->execute([$username, $email, $password, $userrole, $date])) {
            $_SESSION["error"] = "Error al crear la cuenta.";
            header("Location: ../View/sign_in.php");
            exit;
        }

        $_SESSION["success"] = "Registro exitoso. Inicia sesión.";
        header("Location: ../View/login.php");
        exit;
    }

    public function delete(): void
    {
        $user = $_SESSION["username"];
        $email = trim($_POST["email"]);
        $passwd = trim($_POST["passwd"]);
        $confirmation = trim($_POST["confirmation"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($passwd) || empty($confirmation)) {
            $_SESSION["error"] = "Datos inválidos.";
            header("Location: ../View/security.php");
            exit;
        }

        if ($confirmation != "QUIERO ELIMINAR MI PERFIL " . $user) {
            $_SESSION["error"] = "Mensaje de confirmación introducido incorrectamente.";
            header("Location: ../View/security.php");
            exit;
        }

        $readStmt = $this->conn->prepare("SELECT email FROM users WHERE id = ?");
        if (!$readStmt->execute([$_SESSION["id"]])) {
            $_SESSION["error"] = "Error inesperado, no se ha podido leer la base de datos.";
            header("Location: ../View/security.php");
            exit;
        }

        $emailDB = $readStmt->fetch(PDO::FETCH_ASSOC);

        if ($email != $emailDB["email"]) {
            $_SESSION["error"] = "Tu email no es el que has especificado.";
            header("Location: ../View/security.php");
            exit;
        }

        $readStmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        if (!$readStmt->execute([$_SESSION["id"]])) {
            $_SESSION["error"] = "Error inesperado, no se ha podido leer la base de datos.";
            header("Location: ../View/security.php");
            exit;
        }

        $passwdDB = $readStmt->fetch(PDO::FETCH_ASSOC);
        if (!$passwdDB) {
            $_SESSION["error"] = "Error inesperado (el usuario existe?)";
            header("Location: ../View/security.php");
            exit;
        }

        if (!password_verify($passwd, $passwdDB["password"])) {
            $_SESSION["error"] = "La contraseña actual es incorrecta.";
            header("Location: ../View/security.php");
            exit;
        }

        $delStmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$delStmt->execute([$_SESSION["id"]])) {
            $_SESSION["error"] = "Error inesperado, no se ha podido eliminar el perfil.";
            header("Location: ../View/security.php");
            exit;
        }

        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    public function updateProfile(): void
    {
        $newName = trim($_POST["name"]);

        if (empty($newName)) {
            $_SESSION["error"] = "Datos invalidos.";
            header("../View/profile.php");
            exit;
        }

        $updateStmt = $this->conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        if (!$updateStmt->execute([$newName, $_SESSION["id"]])) {
            $_SESSION["error"] = "Ha habido un error al actualizar el usuario, contacte un administrador.";
            header("Location: ../View/profile.php");
            exit;
        }

        $readStmt = $this->conn->prepare("SELECT name, email FROM users WHERE id = ?");

        if (!$readStmt->execute([$_SESSION["id"]])) {
            $_SESSION["error"] = "Error en la consulta";
            header("Location: ../View/login.php");
            exit;
        }

        $user = $readStmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $_SESSION["error"] = "Error inesperado (tu cuenta existe?)";
            header("Location: ../View/profile.php");
            exit;
        }

        // Update session variables to accomodate new values.
        $_SESSION["username"] = $user["name"];
        $_SESSION["success"] = "Perfil actualizado correctamente!";
        header("Location: ../View/profile.php");
        exit;
    }

    public function updatePasswd(): void
    {
        $oldPasswd = trim($_POST["oldpassword"]) ?? '';
        $newPasswd = trim(password_hash($_POST["newpassword"], PASSWORD_DEFAULT)) ?? '';
        $confirmPasswd = $_POST["confirm"];

        if ($_POST["newpassword"] != $confirmPasswd) {
            $_SESSION["error"] = "ERROR: Las contraseñas no coinciden!";
            header("Location: ../View/update_password.php");
            exit;
        }

        if (empty($oldPasswd)) {
            $_SESSION["error"] = "Datos invalidos.";
            header("Location: ../View/update_password.php");
            exit;
        }

        $readStmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        if (!$readStmt->execute([$_SESSION["id"]])) {
            $_SESSION["error"] = "Error inesperado, no se ha podido leer la base de datos.";
            header("Location: ../View/update_password.php");
            exit;
        }

        $oldPasswdDB = $readStmt->fetch(PDO::FETCH_ASSOC);
        if (!$oldPasswdDB) {
            $_SESSION["error"] = "Error inesperado (el usuario existe?)";
            header("Location: ../View/update_password.php");
            exit;
        }

        if (!password_verify($oldPasswd, $oldPasswdDB["password"])) {
            $_SESSION["error"] = "La contraseña actual es incorrecta.";
            header("Location: ../View/update_password.php");
            exit;
        }

        $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if (!$updateStmt->execute([$newPasswd, $_SESSION["id"]])) {
            $_SESSION["error"] = "Ha habido un error al guardar la nueva contraseña, contacte un administrador.";
            header("Location: ../View/update_password.php");
            exit;
        }

        $_SESSION["success"] = "Contraseña actualizada exitosamente.";
        header("Location: ../View/update_password.php");
        exit;
    }
    public function logout(): void
    {
        session_unset();
        session_destroy();
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../index.php';
        if (strpos($redirect, '/') !== 0 && !filter_var($redirect, FILTER_VALIDATE_URL)) {
            $redirect = '../index.php';
        }

        header("Location: " . $redirect);
        exit;
    }
}
