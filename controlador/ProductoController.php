<?php
include("../config/conexion.php");
include("../modelo/ProductoModel.php");

header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "message" => "Acción no válida"];

try {
    $productoModel = new ProductoModel();

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {

            case 'listar':
                if (isset($_GET['categoria'])) {
                    $data = $productoModel->getPorCategoria($_GET['categoria']);
                } else {
                    $data = $productoModel->getAll();
                }
                $response = $data;
                break;

            case 'buscar':
                if (!isset($_GET['filtro'])) {
                    throw new Exception("Filtro requerido");
                }

                $data = $productoModel->search($_GET['filtro']);
                $response = $data;
                break;

            case 'ver':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de producto requerido");
                }

                $data = $productoModel->getById($_GET['id']);
                $response = $data ?: ["success" => false, "message" => "Producto no encontrado"];
                break;

            case 'crear':
                if (!empty($_FILES['foto']['name'])) {
                    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
                    $rutaDestino = __DIR__ . "/../uploads/productos/" . $nombreArchivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                        $_POST['foto'] = "uploads/productos/" . $nombreArchivo;
                    }
                }

                $data = $_POST;
                $productoModel->create($data);

                $response = [
                    "success" => true,
                    "message" => "Producto creado correctamente"
                ];
                break;

            case 'actualizar':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de producto requerido");
                }

                if (!empty($_FILES['foto']['name'])) {
                    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
                    $rutaDestino = __DIR__ . "/../uploads/productos/" . $nombreArchivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                        $_POST['foto'] = "uploads/productos/" . $nombreArchivo;
                    }
                }

                $data = $_POST;
                $productoModel->update($_GET['id'], $data);

                $response = [
                    "success" => true,
                    "message" => "Producto actualizado correctamente"
                ];
                break;

            case 'eliminar':
                if (!isset($_POST['id'])) {
                    throw new Exception("ID de producto requerido");
                }

                $productoModel->delete($_POST['id']);
                $response = [
                    "success" => true,
                    "message" => "Producto eliminado correctamente"
                ];
                break;

            case 'crearCategoria':
                if (!isset($_POST['nombre'])) {
                    throw new Exception("Nombre de categoría requerida");
                }

                $productoModel->createCategoria($_POST);
                $response = [
                    "success" => true,
                    "message" => "Categoría creada correctamente"
                ];
                break;

            case 'actualizarCategoria':
                if (!isset($_POST['id'])) {
                    throw new Exception("Nombre de categoría requerida");
                }

                $productoModel->updateCategoria($_POST['id'], $_POST);
                $response = [
                    "success" => true,
                    "message" => "Categoría creada correctamente"
                ];
                break;

            case 'eliminarCategoria':
                if (!isset($_GET['id'])) {
                    throw new Exception("ID de categoria requerida");
                }

                $productoModel->deleteCategoria($_GET['id']);
                $response = [
                    "success" => true,
                    "message" => "Categoría eliminada correctamente"
                ];
                break;

            case 'listarMovimientos':
                $data = $productoModel->getMovimientos($_GET['categoria'] ?? '', $_GET['inicio'] ?? '', $_GET['fin'] ?? '');
                $response = $data;
                break;

            case 'listarReportes':
                $data = $productoModel->getReportes($_GET['tipo'] ?? '', $_GET['categoria'] ?? '', $_GET['inicio'] ?? '', $_GET['fin'] ?? '', $_GET['limit'] ?? '');
                $response = $data;
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
