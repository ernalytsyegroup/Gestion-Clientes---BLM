<?php
class SocialNetwork {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create Instagram account
    public function createInstagram($id_cliente, $usuario, $correo) {
        $query = "INSERT INTO instagram 
                  SET id_cliente = :id_cliente, 
                      usuario_instagram = :usuario, 
                      correo_instagram = :correo";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_cliente = htmlspecialchars(strip_tags($id_cliente));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Create Facebook account
    public function createFacebook($id_cliente, $usuario, $correo) {
        $query = "INSERT INTO facebook 
                  SET id_cliente = :id_cliente, 
                      usuario_facebook = :usuario, 
                      correo_facebook = :correo";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_cliente = htmlspecialchars(strip_tags($id_cliente));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Create YouTube account
    public function createYoutube($id_cliente, $usuario, $correo) {
        $query = "INSERT INTO youtube 
                  SET id_cliente = :id_cliente, 
                      usuario_youtube = :usuario, 
                      correo_youtube = :correo";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_cliente = htmlspecialchars(strip_tags($id_cliente));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update Instagram account
    public function updateInstagram($id_instagram, $usuario, $correo) {
        $query = "UPDATE instagram 
                  SET usuario_instagram = :usuario, 
                      correo_instagram = :correo
                  WHERE id_instagram = :id_instagram";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_instagram = htmlspecialchars(strip_tags($id_instagram));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_instagram", $id_instagram);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update Facebook account
    public function updateFacebook($id_facebook, $usuario, $correo) {
        $query = "UPDATE facebook 
                  SET usuario_facebook = :usuario, 
                      correo_facebook = :correo
                  WHERE id_facebook = :id_facebook";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_facebook = htmlspecialchars(strip_tags($id_facebook));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_facebook", $id_facebook);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update YouTube account
    public function updateYoutube($id_youtube, $usuario, $correo) {
        $query = "UPDATE youtube 
                  SET usuario_youtube = :usuario, 
                      correo_youtube = :correo
                  WHERE id_youtube = :id_youtube";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $id_youtube = htmlspecialchars(strip_tags($id_youtube));
        $usuario = htmlspecialchars(strip_tags($usuario));
        $correo = htmlspecialchars(strip_tags($correo));

        $stmt->bindParam(":id_youtube", $id_youtube);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":correo", $correo);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Instagram account
    public function deleteInstagram($id_instagram) {
        $query = "DELETE FROM instagram WHERE id_instagram = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_instagram);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete Facebook account
    public function deleteFacebook($id_facebook) {
        $query = "DELETE FROM facebook WHERE id_facebook = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_facebook);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete YouTube account
    public function deleteYoutube($id_youtube) {
        $query = "DELETE FROM youtube WHERE id_youtube = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_youtube);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
