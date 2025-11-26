<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel
{
    public static function getAll()
    {
        global $conn;
        $sql = "SELECT * FROM usuarios";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getById($id)
    {
        global $conn;
        $id = intval($id);
        $res = $conn->query("SELECT * FROM usuarios WHERE id = $id");
        return $res->fetch_assoc();
    }

    public static function create($data)
    {
        global $conn;
        $usuario = $conn->real_escape_string($data['usuario']);
        $clave = md5($data['clave']);
        $rol = $conn->real_escape_string($data['rol']);
        $nombre = $conn->real_escape_string($data['nombre']);
        $foto = $conn->real_escape_string($data['foto']);
        $sql = "INSERT INTO usuarios (usuario, clave, rol, nombre_completo, foto) VALUES ('$usuario','$clave','$rol','$nombre','$foto')";
        return $conn->query($sql);
    }

    public static function update($id, $data)
    {
        global $conn;
        $id = intval($id);
        $id = intval($id);
        $usr = UsuarioModel::getById($id);

        $usuario = $conn->real_escape_string($data['usuario']);
        $rol = $conn->real_escape_string($data['rol']);
        $nombre = $conn->real_escape_string($data['nombre']);

        $sql = "UPDATE usuarios SET usuario='$usuario', rol='$rol', nombre_completo='$nombre' WHERE id=$id";
        if (!empty($data['clave'])) {
            $clave = md5($data['clave']);
            $sql = "UPDATE usuarios SET usuario='$usuario', clave='$clave', rol='$rol', nombre_completo='$nombre' WHERE id=$id";
        }
        return $conn->query($sql);
    }

    public static function delete($id)
    {
        global $conn;
        $id = intval($id);
        return $conn->query("DELETE FROM usuarios WHERE id = $id");
    }
}
