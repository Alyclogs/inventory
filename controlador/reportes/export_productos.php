<?php
include("../../config/conexion.php");
include("../../modelo/ProductoModel.php");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Reporte_Productos_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$modelo = new ProductoModel();
$productos = $modelo->getAll();

echo "<table border='1' style='border-collapse:collapse;font-family:Arial,sans-serif;font-size:14px;width:100%;'>";
echo "<thead style='background:#2c3e50;color:white;text-align:center;'>";
echo "<tr>
        <th>ID</th>
        <th>Nombre del Producto</th>
        <th>Categoría</th>
        <th>Precio (S/)</th>
        <th>Stock Actual</th>
      </tr>";
echo "</thead><tbody>";

foreach ($productos as $row) {
    echo "<tr style='text-align:center;'>";
    echo "<td>{$row['id']}</td>";
    echo "<td style='text-align:left;padding:6px;'>{$row['nombre']}</td>";
    echo "<td>{$row['categoria']}</td>";
    echo "<td style='color:#27ae60;font-weight:bold;'>S/ " . number_format($row['precio'], 2) . "</td>";
    echo "<td style='font-weight:bold;" . ($row['stock'] <= 5 ? "color:#e74c3c;" : "color:#2980b9;") . "'>{$row['stock']}</td>";
    echo "</tr>";
}

echo "</tbody></table>";

echo "<br><table style='width:100%;font-size:12px;font-family:Arial;color:#555;'>
<tr><td style='text-align:right;'>Generado el " . date("d/m/Y H:i") . " por el sistema de inventario - Andres Espinoza Motos E.I.R.L.</td></tr>
</table>";
