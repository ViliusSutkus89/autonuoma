<?php
require('header_report.php');
?>

<div id="header">
  <ul id="reportInfo">
    <li class="title">Užsakytų paslaugų ataskaita</li>
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
      <a href="<?php echo routing::getURL($module, 'view', "id={$id}"); ?>" title="Nauja ataskaita" class="newReport">nauja ataskaita</a>
    </li>
  </ul>
</div>

<div id="content">
  <div id="contentMain">
  <?php
  if(sizeof($servicesData) > 0) { ?>
    <table class="reportTable">
      <tr>
			  <th>ID</th>
			  <th>Paslauga</th>
			  <th>Užsakyta kartų</th>
			  <th>Užsakyta už</th>
      </tr>

      <tr><td class="separator" colspan="5"></td></tr>
      <?php
      // suformuojame lentelę
      foreach($servicesData as $key => $val) {
        echo
          "<tr>",
          "<td>{$val['id']}</td>",
          "<td>{$val['pavadinimas']}</td>",
          "<td>{$val['uzsakyta']}</td>",
          "<td>{$val['bendra_suma']} &euro;</td>",
          "</tr>";
      }
      ?>

      <tr class="aggregate">
        <td></td>
        <td class="label">Suma:</td>
        <td class="border">
          <?php echo "{$servicesStats[0]['uzsakyta']}"; ?>
        </td>
        <td class="border">
          <?php echo "{$servicesStats[0]['bendra_suma']}"; ?> &euro;
        </td>
      </tr>
    </table>
  <?php } else { ?>
    <div class="warningBox">
      Nurodytu laikotartpiu sutarčių nebuvo užsakyta
    </div>
  <?php } ?>
  </div>
</div>

<?php require('footer_report.php');

