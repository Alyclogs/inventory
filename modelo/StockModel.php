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

    public static function actualizarStock($id, $cantidad)
    {
        global $conn;
        $id = intval($id);
        $cantidad = intval($cantidad);
        return $conn->query("UPDATE productos SET stock = $cantidad WHERE id = $id");
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
