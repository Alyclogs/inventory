<?php
require_once __DIR__ . '/../config/conexion.php';

class StockModel
{
    public static function getAll()
    {
        global $conn;
        $sql = "SELECT * FROM productos ORDER BY nombre ASC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function buscar($term)
    {
        global $conn;
        $term = $conn->real_escape_string($term);
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%$term%' OR categoria LIKE '%$term%'";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function actualizarStock($id, $data)
    {
        global $conn;

        $producto_id = $data['producto_id'];
        $tipo = $data['tipo'];
        $cantidad = intval($data['cantidad']);
        $observacion = $data['observacion'] ?? "";

        $res = $conn->query("SELECT stock FROM productos WHERE id = $producto_id");
        $row = $res->fetch_assoc();
        $stockActual = intval($row['stock']);

        if ($tipo === "Entrada") {
            $nuevoStock = $stockActual + $cantidad;
        } else {
            $nuevoStock = max(0, $stockActual - $cantidad);
        }

        $conn->query("UPDATE productos SET stock = $nuevoStock WHERE id = $producto_id");

        $stmt = $conn->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, observacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $producto_id, $tipo, $cantidad, $observacion);
        return $stmt->execute();
    }

    public static function getTotalStock()
    {
        global $conn;
        return $conn->query("SELECT SUM(stock) AS total FROM productos")->fetch_assoc()['total'];
    }

    public static function getTotalMovimientos()
    {
        global $conn;
        return $conn->query("SELECT COUNT(*) AS total FROM movimientos")->fetch_assoc()['total'];
    }
}
