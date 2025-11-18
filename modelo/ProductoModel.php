<?php
require_once __DIR__ . '/../config/conexion.php';

class ProductoModel
{
    public static function getAll()
    {
        global $conn;
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getPorCategoria($categoria)
    {
        global $conn;
        $categoria = $conn->real_escape_string($categoria);
        $sql = "SELECT * FROM productos WHERE categoria = '$categoria' ORDER BY id DESC";
        $result = $conn->query($sql);
        if (!$result) {
            return [];
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function search($filtro)
    {
        global $conn;
        $filtro = $conn->real_escape_string($filtro);
        $sql = "SELECT id, nombre FROM productos WHERE nombre LIKE '%$filtro%' LIMIT 5";
        $result = $conn->query($sql);
        if (!$result) return [];
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
        $res = $conn->query("SELECT * FROM productos WHERE id = $id");
        return $res->fetch_assoc();
    }

    public static function getTotalProductos()
    {
        global $conn;
        return $conn->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];
    }

    public static function create($data)
    {
        global $conn;
        $nombre = $conn->real_escape_string($data['nombre']);
        $categoria = $conn->real_escape_string($data['categoria']);
        $precio = $conn->real_escape_string($data['precio']);
        $foto = $conn->real_escape_string($data['foto'] ?? null);
        $sql = "INSERT INTO productos (nombre, categoria, precio, stock, foto) VALUES ('$nombre','$categoria','$precio',0,'$foto')";
        return $conn->query($sql);
    }

    public static function update($id, $data)
    {
        global $conn;
        $id = intval($id);
        $prod = ProductoModel::getById($id);

        $nombre = $conn->real_escape_string($data['nombre']);
        $categoria = $conn->real_escape_string($data['categoria']);
        $precio = $conn->real_escape_string($data['precio']);
        $foto = $conn->real_escape_string($data['foto'] ?? $prod['foto'] ?? null);
        $sql = "UPDATE productos SET nombre='$nombre', categoria='$categoria', precio='$precio', foto='$foto' WHERE id=$id";
        return $conn->query($sql);
    }

    public static function delete($id)
    {
        global $conn;
        $id = intval($id);
        return $conn->query("DELETE FROM productos WHERE id = $id");
    }

    public static function getCategorias()
    {
        global $conn;
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getCategroiasProductos()
    {
        global $conn;
        $data = [];
        $result = $conn->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria ASC");
        if (!$result) return $data;
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function createCategoria($data)
    {
        global $conn;
        $nombre = $conn->real_escape_string($data['nombre']);
        $sql = "INSERT INTO categorias (nombre) VALUES ('$nombre')";
        return $conn->query($sql);
    }

    public static function updateCategoria($id, $data)
    {
        global $conn;
        $id = intval($id);
        $nombre = $conn->real_escape_string($data['nombre']);
        $sql = "UPDATE categorias SET nombre='$nombre' WHERE id=$id";
        return $conn->query($sql);
    }

    public static function deleteCategoria($id)
    {
        global $conn;
        $id = intval($id);
        return $conn->query("DELETE FROM categorias WHERE id = $id");
    }

    public static function getMovimientos($categoria, $inicio, $fin)
    {
        global $conn;
        $data = [];
        $filtro_categoria = '';
        if ($categoria !== 'todas' && $categoria !== '') {
            $filtro_categoria = "WHERE p.categoria = '$categoria'";
        }

        $filtro_fecha = '';
        if (!empty($inicio) && !empty($fin)) {
            $inicio .= " 00:00:00";
            $fin .= " 23:59:59";

            if (!empty($filtro_categoria)) {
                $filtro_fecha = " AND m.fecha BETWEEN '$inicio' AND '$fin'";
            } else {
                $filtro_fecha = "WHERE m.fecha BETWEEN '$inicio' AND '$fin'";
            }
        }

        $query = "
            SELECT 
                p.nombre AS producto,
                COALESCE(SUM(CASE WHEN m.tipo = 'Entrada' THEN m.cantidad END), 0) AS entradas,
                COALESCE(SUM(CASE WHEN m.tipo = 'Salida' THEN m.cantidad END), 0) AS salidas
            FROM productos p
            LEFT JOIN movimientos m ON m.producto_id = p.id
            $filtro_categoria
            $filtro_fecha
            GROUP BY p.id
            ORDER BY p.nombre ASC
        ";

        $res = $conn->query($query);

        if (!$res) {
            echo json_encode(["error" => $conn->error, "sql" => $query]);
            exit;
        }

        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    public static function getReportes($tipo, $categoria, $desde, $hasta, $limit)
    {
        global $conn;

        $data = [];
        $filtro_categoria = '';
        if ($categoria !== 'todas' && $categoria !== '') {
            $filtro_categoria = "WHERE p.categoria = '$categoria'";
        }

        $fecha_cond = '';
        if (!empty($desde) && !empty($hasta)) {
            $desde .= " 00:00:00";
            $hasta .= " 23:59:59";
            $fecha_cond = "AND m.fecha BETWEEN '$desde' AND '$hasta'";
        }

        switch ($tipo) {
            case "mas_vendidos":
                $query = "
            SELECT 
                p.nombre,
                COALESCE(SUM(CASE WHEN m.tipo = 'Salida' $fecha_cond THEN m.cantidad ELSE 0 END), 0) AS total_vendidos
            FROM productos p
            LEFT JOIN movimientos m ON m.producto_id = p.id
            $filtro_categoria
            GROUP BY p.id
            ORDER BY total_vendidos DESC
            LIMIT $limit
        ";
                break;

            case "menos_vendidos":
                $query = "
            SELECT 
                p.nombre,
                COALESCE(SUM(CASE WHEN m.tipo = 'Salida' $fecha_cond THEN m.cantidad ELSE 0 END), 0) AS total_vendidos
            FROM productos p
            LEFT JOIN movimientos m ON m.producto_id = p.id
            $filtro_categoria
            GROUP BY p.id
            ORDER BY total_vendidos ASC
            LIMIT $limit
        ";
                break;

            case "stock_general":
                $query = "
            SELECT p.nombre, p.stock
            FROM productos p
            $filtro_categoria
            ORDER BY p.stock DESC
            LIMIT $limit
        ";
                break;

            default:
                echo json_encode([]);
                exit;
        }

        $res = $conn->query($query);

        if (!$res) {
            echo json_encode(["error" => $conn->error, "sql" => $query]);
            exit;
        }

        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}
