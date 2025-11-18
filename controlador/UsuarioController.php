<?php
include("../config/conexion.php");
include("../modelo/UsuarioModel.php");

header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "message" => "Acción no válida"];

try {
    $usuarioModel = new UsuarioModel();

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {

            case 'listar':
                $data = $usuarioModel->getAll();
                $response = $data;
                break;

            case 'ver':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de Usuario requerido");
                }

                $data = $usuarioModel->getById($_GET['id']);
                $response = $data ?: ["success" => false, "message" => "Usuario no encontrado"];
                break;

            case 'crear':
                if (!empty($_FILES['foto']['name'])) {
                    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
                    $rutaDestino = __DIR__ . "/../uploads/usuarios/" . $nombreArchivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                        $_POST['foto'] = "uploads/usuarios/" . $nombreArchivo;
                    }
                }

                $data = $_POST;
                $usuarioModel->create($data);

                $response = [
                    "success" => true,
                    "message" => "Usuario creado correctamente"
                ];
                break;

            case 'actualizar':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de usuario requerido");
                }

                if (!empty($_FILES['foto']['name'])) {
                    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
                    $rutaDestino = __DIR__ . "/../uploads/usuarios/" . $nombreArchivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                        $_POST['foto'] = "uploads/usuarios/" . $nombreArchivo;
                    }
                }

                $data = $_POST;
                $usuarioModel->update($_GET['id'], $data);

                $response = [
                    "success" => true,
                    "message" => "Usuario actualizado correctamente"
                ];
                break;

            case 'eliminar':
                if (!isset($_POST['id'])) {
                    throw new Exception("ID de Usuario requerido");
                }

                $usuarioModel->delete($_POST['id']);
                $response = [
                    "success" => true,
                    "message" => "Usuario eliminado correctamente"
                ];
                break;

            default:
                throw new Exception("Acción no válida");
        }
    } else {
        throw new Exception("No se especificó ninguna acción");
    }
} catch (Exception $e) {
    $response = [
        "success" => false,
        "message" => $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
