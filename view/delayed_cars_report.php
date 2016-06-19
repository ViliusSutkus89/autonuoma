<?php require('header_report.php'); ?>

<div id="header">
  <ul id="reportInfo">
    <li class="title">Vėluojamų grąžinti automobilių ataskaita</li>
    <li>Sudarymo data: <span><?php echo date("Y-m-d"); ?></span></li>
    <li>Paslaugų užsakymo laikotarpis:
	    <span>
        <?php
        if(!empty($data['dataNuo'])) {
          if(!empty($data['dataIki'])) {
            echo "nuo {$data['dataNuo']} iki {$data['dataIki']}";
          } else {
            echo "nuo {$data['dataNuo']}";
          }
        } else {
          if(!empty($data['dataIki'])) {
            echo "iki {$data['dataIki']}";
          } else {
            echo "nenurodyta";
          }
        }
        ?>
      </span>
      <a href="index.php?module=<?php echo $module; ?>&amp;action=view&amp;id=<?php echo $id; ?>" title="Nauja ataskaita" class="newReport">nauja ataskaita</a>
    </li>
  </ul>
</div>

<div id="content">
  <div id="contentMain">
  <?php
  if(sizeof($delayedCarsData) > 0) { ?>
    <table class="reportTable">
      <tr>
        <th>Sutartis</th>
        <th>Klientas</th>
        <th>Planuota grąžinti</th>
        <th>Grąžinta</th>
      </tr>

      <tr><td class="separator" colspan="5"></td></tr>
      <?php
      // suformuojame lentelę
      foreach($delayedCarsData as $key => $val) {
        echo
          "<tr>",
          "<td>#{$val['nr']}, {$val['sutarties_data']}</td>",
          "<td>{$val['vardas']} {$val['pavarde']}</td>",
          "<td>{$val['planuojama_grazinimo_data_laikas']}</td>",
          "<td>{$val['grazinta']}</td>",
          "</tr>\n";
      } ?>

    </table>
  <?php } else { ?>
    <div class="warningBox">
      Nurodytu laikotartpiu sutarčių nebuvo užsakyta
    </div>
  <?php } ?>
  </div>
</div>

<?php require('footer_report.php');

