<?php
include("../config/conexion.php");
include("../modelo/StockModel.php");

header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "message" => "Acción no válida"];

try {
    $stockModel = new StockModel();

    if (isset($_GET['action'])) {
        switch ($_GET['action']) {

            case 'listar':
                $data = $stockModel->getAll();
                $response = $data;
                break;

            case 'buscar':
                if (!isset($_GET['filtro'])) {
                    throw new Exception("Filtro requerido");
                }

                $data = $stockModel->buscar($_GET['filtro']);
                $response = $data;
                break;

            case 'actualizar':
                if (!isset($_POST['producto_id'])) {
                    throw new Exception("ID de producto requerido");
                }

                $stockModel->actualizarStock($_POST['producto_id'], $_POST['cantidad'] ?? 0);

                $response = [
                    "success" => true,
                    "message" => "Stock actualizado correctamente"
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
