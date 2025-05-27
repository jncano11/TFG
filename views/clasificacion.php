<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../models/Equipo.php';

date_default_timezone_set('Europe/Madrid');
$rol = $_SESSION['usuario']['rol'];

$partidos = Partido::obtenerPorCategoria('1ªRegional');
$clasificacion = [];

foreach ($partidos as $p) {
    $idL = $p['equipo_local_id'];
    $gfL = (int)$p['resultado_local'];
    $gcL = (int)$p['resultado_visitante'];
    if (!isset($clasificacion[$idL])) {
        $clasificacion[$idL] = [
            'equipo_id' => $idL,
            'equipo'    => $p['equipo_local'],
            'PJ'        => 0,
            'V'         => 0,
            'E'         => 0,
            'D'         => 0,
            'GA'        => 0,
            'GC'        => 0,
            'Pts'       => 0,
        ];
    }

    $idV = $p['equipo_visitante_id'];
    $gfV = (int)$p['resultado_visitante'];
    $gcV = (int)$p['resultado_local'];
    if (!isset($clasificacion[$idV])) {
        $clasificacion[$idV] = [
            'equipo_id' => $idV,
            'equipo'    => $p['equipo_visitante'],
            'PJ'        => 0,
            'V'         => 0,
            'E'         => 0,
            'D'         => 0,
            'GA'        => 0,
            'GC'        => 0,
            'Pts'       => 0,
        ];
    }

    $clasificacion[$idL]['PJ']++;
    $clasificacion[$idV]['PJ']++;

    $clasificacion[$idL]['GA'] += $gfL;
    $clasificacion[$idL]['GC'] += $gcL;
    $clasificacion[$idV]['GA'] += $gfV;
    $clasificacion[$idV]['GC'] += $gcV;

    if ($gfL > $gcL) {
        $clasificacion[$idL]['V']++;
        $clasificacion[$idL]['Pts'] += 3;
        $clasificacion[$idV]['D']++;
    } elseif ($gfL < $gcL) {
        $clasificacion[$idV]['V']++;
        $clasificacion[$idV]['Pts'] += 3;
        $clasificacion[$idL]['D']++;
    } else {
        $clasificacion[$idL]['E']++;
        $clasificacion[$idL]['Pts'] += 1;
        $clasificacion[$idV]['E']++;
        $clasificacion[$idV]['Pts'] += 1;
    }
}

$tabla = array_values($clasificacion);
foreach ($tabla as &$eq) {
    $eq['DF'] = $eq['GA'] - $eq['GC'];
}
unset($eq);

usort($tabla, function($a, $b){
    if ($b['Pts']  !== $a['Pts'])  return $b['Pts']  - $a['Pts'];
    if ($b['DF']   !== $a['DF'])   return $b['DF']   - $a['DF'];
    return $b['GA'] - $a['GA'];
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Clasificación - 1ª Regional Zaragoza</title>
  <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
  <link rel="stylesheet" href="../public/css/clasificacion.css">
</head>
<body>
  <header>
    <h1>Clasificación 1ª Regional Zaragoza</h1>
    <a href="index.php" style="color:#00ff88">← Volver</a>
  </header>
  <main>
    <table class="clasificacion">
      <thead>
        <tr>
          <th>#</th>
          <th>Equipo</th>
          <th>PJ</th>
          <th>V</th>
          <th>E</th>
          <th>D</th>
          <th>GA</th>
          <th>GC</th>
          <th>DF</th>
          <th>Pts</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tabla as $idx => $eq): 
          $pos = $idx + 1;
          $class = '';
          if ($pos <= 2) $class = 'ascenso';
          elseif ($pos == 3) $class = 'playoff';
          elseif ($pos >= count($tabla) - 4 + 1) $class = 'descenso';
        ?>
        <tr class="<?= $class ?>">
          <td><?= $pos ?></td>
          <td>
            <a href="equipo_perfil.php?id=<?= $eq['equipo_id'] ?>" class="equipo-link">
            <?= htmlspecialchars($eq['equipo']) ?>
          </a>
          </td>
          <td><?= $eq['PJ'] ?></td>
          <td><?= $eq['V'] ?></td>
          <td><?= $eq['E'] ?></td>
          <td><?= $eq['D'] ?></td>
          <td><?= $eq['GA'] ?></td>
          <td><?= $eq['GC'] ?></td>
          <td><?= $eq['DF'] ?></td>
          <td><?= $eq['Pts'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="leyenda">
      <div class="leyenda-item">
        <div class="leyenda-cuadro ascenso"></div> Ascenso directo
      </div>
      <div class="leyenda-item">
        <div class="leyenda-cuadro playoff"></div> Play-off ascenso
      </div>
      <div class="leyenda-item">
        <div class="leyenda-cuadro descenso"></div> Descenso
      </div>
    </div>
  </main>
</body>
</html>
