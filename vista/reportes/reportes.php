<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../modelo/ProductoModel.php';
require_once __DIR__ . '/../../modelo/StockModel.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
  header("Location: ../login/index.php");
  exit();
}
$productoModel = new ProductoModel();
$stockModel = new StockModel();

$productos = $productoModel->getTotalProductos();
$movimientos = $stockModel->getTotalMovimientos();
$stockTotal = $stockModel->getTotalStock();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reportes | Sistema de Inventario</title>
  <link rel="stylesheet" href="../../assets/css/reportes.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
  <header>
    <a href="../dashboard/dashboard.php" class="back-btn">
      <i data-lucide="arrow-left"></i> Volver al Dashboard
    </a>
    <h1>Reportes e Indicadores</h1>
  </header>

  <main class="reportes-container">

    <div class="export-buttons" style="text-align:right;margin-bottom:20px;">
      <button id="btnExportExcel" style="background:#27ae60;color:white;padding:8px 15px;border:none;border-radius:6px;cursor:pointer;">
        📊 Exportar Productos a Excel
      </button>
      <button id="btnExportPDF" style="background:#c0392b;color:white;padding:8px 15px;border:none;border-radius:6px;margin-left:10px;cursor:pointer;">
        📄 Exportar Gráficos en PDF
      </button>
    </div>

    <section class="stats-cards">
      <div class="stat-card blue">
        <h2><?= $productos ?></h2>
        <p>Productos registrados</p>
      </div>
      <div class="stat-card green">
        <h2><?= $movimientos ?></h2>
        <p>Movimientos realizados</p>
      </div>
      <div class="stat-card orange">
        <h2><?= $stockTotal ?></h2>
        <p>Stock total</p>
      </div>
    </section>

    <section id="dashboard" class="dashboard-grid">

      <div class="chart-box">
        <h3>📦 Movimientos por Categoría y Fecha</h3>
        <div class="filtros">
          <select id="categoriaFiltro1">
            <option value="todas">Todas las categorías</option>
            <?php
            $cats = $productoModel->getCategroiasProductos();
            foreach ($cats as $cat) {
              echo "<option value='{$c['categoria']}'>{$c['categoria']}</option>";
            }
            ?>
          </select>
          <label>Desde: <input type="date" id="fechaInicio1"></label>
          <label>Hasta: <input type="date" id="fechaFin1"></label>
          <button id="btnFiltrar1">Filtrar</button>
        </div>
        <canvas id="movimientosChart"></canvas>
      </div>


      <div class="chart-box">
        <h3>💰 Productos más vendidos</h3>
        <div class="filtros">
          <select id="categoriaFiltro2">
            <option value="todas">Todas las categorías</option>
            <?php
            $cats = $productoModel->getCategroiasProductos();
            foreach ($cats as $cat) {
              echo "<option value='{$c['categoria']}'>{$c['categoria']}</option>";
            }
            ?>
          </select>
          <label>Desde: <input type="date" id="fechaInicio2"></label>
          <label>Hasta: <input type="date" id="fechaFin2"></label>
          <label>Items: <input type="number" id="limit2" min="1" value="10" style="width:70px"></label>
          <button id="btnFiltrar2">Filtrar</button>
        </div>
        <canvas id="masVendidosChart"></canvas>
      </div>

      <div class="chart-box">
        <h3>📊 Stock general de productos</h3>
        <div class="filtros">
          <select id="categoriaFiltro3">
            <option value="todas">Todas las categorías</option>
            <?php
            $cats = $productoModel->getCategroiasProductos();
            foreach ($cats as $cat) {
              echo "<option value='{$c['categoria']}'>{$c['categoria']}</option>";
            }
            ?>
          </select>
          <button id="btnFiltrar3">Filtrar</button>
        </div>
        <canvas id="stockChart"></canvas>
      </div>

      <div class="chart-box">
        <h3>📉 Productos menos vendidos</h3>
        <div class="filtros">
          <select id="categoriaFiltro4">
            <option value="todas">Todas las categorías</option>
            <?php
            $cats = $productoModel->getCategroiasProductos();
            foreach ($cats as $cat) {
              echo "<option value='{$c['categoria']}'>{$c['categoria']}</option>";
            }
            ?>
          </select>
          <label>Desde: <input type="date" id="fechaInicio4"></label>
          <label>Hasta: <input type="date" id="fechaFin4"></label>
          <label>Items: <input type="number" id="limit4" min="1" value="10" style="width:70px"></label>
          <button id="btnFiltrar4">Filtrar</button>
        </div>
        <canvas id="menosVendidosChart"></canvas>
      </div>
    </section>
  </main>

  <script>
    lucide.createIcons();
    let base_url = "http://34.42.80.200/inventory/";

    let chart1;
    async function cargarMovimientos(validarFechas = false) {
      const cat = document.getElementById("categoriaFiltro1").value;
      const ini = document.getElementById("fechaInicio1").value;
      const fin = document.getElementById("fechaFin1").value;

      if (validarFechas && (!ini || !fin)) {
        alert("⚠️ Debes seleccionar ambas fechas para filtrar los movimientos.");
        return;
      }

      const res = await fetch(base_url + `controlador/ProductoController.php?action=listarMovimientos&categoria=${cat}&inicio=${ini}&fin=${fin}`);
      const data = await res.json();

      const labels = data.map(d => d.producto);
      const entradas = data.map(d => d.entradas);
      const salidas = data.map(d => d.salidas);
      const ctx = document.getElementById("movimientosChart").getContext("2d");

      if (chart1) chart1.destroy();

      if (data.length === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = "16px Arial";
        ctx.fillStyle = "#555";
        ctx.fillText("Sin resultados para el rango seleccionado", 20, 40);
        return;
      }

      const isHorizontal = labels.length > 3;

      chart1 = new Chart(ctx, {
        type: "bar",
        data: {
          labels,
          datasets: [{
              label: "Entradas",
              data: entradas,
              backgroundColor: "#00b894"
            },
            {
              label: "Salidas",
              data: salidas,
              backgroundColor: "#e74c3c"
            }
          ]
        },
        options: {
          responsive: true,
          indexAxis: isHorizontal ? "y" : "x",
          plugins: {
            legend: {
              position: "bottom"
            }
          },
          scales: {
            x: {
              ticks: {
                autoSkip: false,
                maxRotation: 0,
                minRotation: 0
              }
            },
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    document.getElementById("btnFiltrar1").addEventListener("click", () => cargarMovimientos(true));
    window.addEventListener("load", () => cargarMovimientos(false));


    async function cargarGrafico(tipo, idCanvas, color, categoriaId, iniId = null, finId = null, limitId = null, validarFechas = false) {
      const categoria = document.getElementById(categoriaId).value;
      const inicio = iniId ? document.getElementById(iniId).value : "";
      const fin = finId ? document.getElementById(finId).value : "";
      const limit = limitId ? document.getElementById(limitId).value : 10;

      if (validarFechas && ((iniId && !inicio) || (finId && !fin))) {
        alert("⚠️ Debes seleccionar ambas fechas antes de filtrar.");
        return;
      }

      const res = await fetch(base_url + `controlador/ProductoController.php?action=listarReportes&tipo=${tipo}&categoria=${categoria}&inicio=${inicio}&fin=${fin}&limit=${limit}`);
      const data = await res.json();

      const labels = data.map(d => d.nombre);
      const valores = data.map(d => d.total_vendidos ?? d.stock);
      const ctx = document.getElementById(idCanvas).getContext("2d");

      if (ctx.chart) ctx.chart.destroy();

      if (data.length === 0) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = "16px Arial";
        ctx.fillStyle = "#555";
        ctx.fillText("Sin resultados para el rango seleccionado", 20, 40);
        return;
      }

      const isHorizontal = labels.length > 3;
      const colores = valores.map(v => v == 0 ? "#bdc3c7" : color);

      if (tipo === "menos_vendidos") {
        const combinado = labels.map((label, i) => ({
          nombre: label,
          valor: valores[i],
          color: colores[i]
        }));
        combinado.sort((a, b) => a.valor - b.valor);
        labels.length = 0;
        valores.length = 0;
        colores.length = 0;
        combinado.forEach(item => {
          labels.push(item.nombre);
          valores.push(item.valor);
          colores.push(item.color);
        });
      }

      ctx.chart = new Chart(ctx, {
        type: "bar",
        data: {
          labels,
          datasets: [{
            label: tipo.replace("_", " "),
            data: valores,
            backgroundColor: colores
          }]
        },
        options: {
          indexAxis: isHorizontal ? "y" : "x",
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              ticks: {
                autoSkip: false,
                maxRotation: 0,
                minRotation: 0
              }
            },
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    document.getElementById("btnFiltrar2").addEventListener("click", () => {
      cargarGrafico("mas_vendidos", "masVendidosChart", "#3498db", "categoriaFiltro2", "fechaInicio2", "fechaFin2", "limit2", true);
    });
    document.getElementById("btnFiltrar3").addEventListener("click", () => {
      cargarGrafico("stock_general", "stockChart", "#f39c12", "categoriaFiltro3");
    });
    document.getElementById("btnFiltrar4").addEventListener("click", () => {
      cargarGrafico("menos_vendidos", "menosVendidosChart", "#9b59b6", "categoriaFiltro4", "fechaInicio4", "fechaFin4", "limit4", true);
    });

    window.addEventListener("load", () => {
      cargarGrafico("mas_vendidos", "masVendidosChart", "#3498db", "categoriaFiltro2", "fechaInicio2", "fechaFin2", "limit2");
      cargarGrafico("stock_general", "stockChart", "#f39c12", "categoriaFiltro3");
      cargarGrafico("menos_vendidos", "menosVendidosChart", "#9b59b6", "categoriaFiltro4", "fechaInicio4", "fechaFin4", "limit4");
    });

    document.getElementById("btnExportExcel").addEventListener("click", () => {
      window.location.href = base_url + "controlador/reportes/export_productos.php";
    });

    document.getElementById("btnExportPDF").addEventListener("click", async () => {
      const {
        jsPDF
      } = window.jspdf;
      const pdf = new jsPDF("p", "mm", "a4");
      const dashboard = document.getElementById("dashboard");


      const canvas = await html2canvas(dashboard, {
        scale: 2
      });
      const imgData = canvas.toDataURL("image/png");

      const imgWidth = 190;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      const logoUrl = "../../assets/img/logo.png";
      const logoImg = new Image();
      logoImg.src = logoUrl;

      logoImg.onload = function() {

        pdf.addImage(logoImg, "PNG", 12, 10, 26, 26);
        pdf.setFont("helvetica", "bold");
        pdf.setFontSize(20);
        pdf.setTextColor(20, 20, 20);
        pdf.text("Reporte de Inventario", 42, 18);
        pdf.setFontSize(13);
        pdf.setTextColor(90, 90, 90);
        pdf.text("Andres Espinoza Motos E.I.R.L.", 42, 26);
        pdf.setFontSize(11);
        pdf.setTextColor(60, 60, 60);
        pdf.text(`Fecha: ${new Date().toLocaleDateString("es-PE")}`, 160, 15);


        pdf.addImage(imgData, "PNG", 10, 40, imgWidth, imgHeight);


        const pageHeight = pdf.internal.pageSize.height;
        pdf.setFont("helvetica", "italic");
        pdf.setFontSize(9);
        pdf.setTextColor(120, 120, 120);
        pdf.text(
          `© ${new Date().getFullYear()} Andres Espinoza Motos E.I.R.L. — Sistema de Inventario`,
          105,
          pageHeight - 10, {
            align: "center"
          }
        );

        pdf.save(`Reporte_Inventario_${new Date().toISOString().slice(0, 10)}.pdf`);
      };

      logoImg.onerror = function() {
        alert("⚠️ No se encontró el logo en la ruta especificada: " + logoUrl);
      };
    });
  </script>
</body>

</html>