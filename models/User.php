<?php
class User {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nombre_usuario;
    public $correo_usuario;
    public $contrasena;
    public $id_rol;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre_usuario = :nombre_usuario, 
                      correo_usuario = :correo_usuario, 
                      contrasena = :contrasena, 
                      id_rol = :id_rol";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->nombre_usuario = htmlspecialchars(strip_tags($this->nombre_usuario));
        $this->correo_usuario = htmlspecialchars(strip_tags($this->correo_usuario));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));
        $this->id_rol = htmlspecialchars(strip_tags($this->id_rol));

        // Hash the password
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre_usuario", $this->nombre_usuario);
        $stmt->bindParam(":correo_usuario", $this->correo_usuario);
        $stmt->bindParam(":contrasena", $password_hash);
        $stmt->bindParam(":id_rol", $this->id_rol);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Login user (sin verificación de contraseña)
    public function login() {
        $query = "SELECT id_usuario, nombre_usuario, correo_usuario, id_rol 
                  FROM " . $this->table_name . " 
                  WHERE correo_usuario = ? OR nombre_usuario = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->correo_usuario);
        $stmt->bindParam(2, $this->correo_usuario); // Usamos el mismo campo para buscar por nombre o correo

        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $this->id_usuario = $row['id_usuario'];
            $this->nombre_usuario = $row['nombre_usuario'];
            $this->correo_usuario = $row['correo_usuario'];
            $this->id_rol = $row['id_rol'];
        
            // No verificamos contraseña, simplemente retornamos true
            return true;
        }

        return false;
    }

    // Read all users
    public function read() {
        $query = "SELECT u.id_usuario, u.nombre_usuario, u.correo_usuario, r.nombre_rol 
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.id_rol = r.id_rol
                  ORDER BY u.id_usuario DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read one user
    public function readOne() {
        $query = "SELECT u.id_usuario, u.nombre_usuario, u.correo_usuario, u.id_rol, r.nombre_rol 
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.id_usuario = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id_usuario = $row['id_usuario'];
            $this->nombre_usuario = $row['nombre_usuario'];
            $this->correo_usuario = $row['correo_usuario'];
            $this->id_rol = $row['id_rol'];
            return true;
        }

        return false;
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_usuario = :nombre_usuario, 
                      correo_usuario = :correo_usuario, 
                      id_rol = :id_rol
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->nombre_usuario = htmlspecialchars(strip_tags($this->nombre_usuario));
        $this->correo_usuario = htmlspecialchars(strip_tags($this->correo_usuario));
        $this->id_rol = htmlspecialchars(strip_tags($this->id_rol));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(":nombre_usuario", $this->nombre_usuario);
        $stmt->bindParam(":correo_usuario", $this->correo_usuario);
        $stmt->bindParam(":id_rol", $this->id_rol);
        $stmt->bindParam(":id_usuario", $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update password
    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . " 
                  SET contrasena = :contrasena
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Hash the password
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(":contrasena", $password_hash);
        $stmt->bindParam(":id_usuario", $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete user
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get assigned clients
    public function getAssignedClients() {
        $query = "SELECT c.* 
                  FROM clientes c
                  JOIN relaciones r ON c.id_cliente = r.id_cliente
                  WHERE r.id_usuario = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();

        return $stmt;
    }

    // Check if user is admin
    public function isAdmin() {
        $query = "SELECT r.nombre_rol 
                  FROM roles r
                  JOIN " . $this->table_name . " u ON r.id_rol = u.id_rol
                  WHERE u.id_usuario = ? AND r.nombre_rol = 'Administrador'
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_usuario);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }
}
?>
