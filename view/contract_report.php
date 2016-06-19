<?php
require('header_report.php');

function isRowFirstInGroup($dataArray, $i, $groupID) {
  if ($i == 0)
    return true;

  if ($dataArray[$i][$groupID] != $dataArray[$i - 1][$groupID])
    return true;

  return false;
}

function isRowLastInGroup($dataArray, $i, $groupID) {
  if ($i == sizeof($dataArray) - 1)
    return true;

  if ($dataArray[$i][$groupID] != $dataArray[$i + 1][$groupID])
    return true;

  return false;
}

?>

<div id="header">
  <ul id="reportInfo">
	  <li class="title">Klientų sutarčių ataskaita</li>
    <li>Sudarymo data: <span><?php echo date("Y-m-d"); ?></span></li>
    <li>Sutarčių sudarymo laikotarpis:
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
  if(sizeof($contractData) > 0) { ?>
    <table class="reportTable">
      <tr>
        <th>Sutartis</th>
        <th>Data</th>
        <th>Kaina</th>
        <th>Užsakyta paslaugų už</th>
      </tr>
      <?php
      // suformuojame lentelę
      for($i = 0; $i < sizeof($contractData); $i++) {
        if (isRowFirstInGroup($contractData, $i, 'asmens_kodas')) {
          echo
            "<tr class='rowSeparator'><td colspan='5'></td></tr>",
            "<tr>",
              "<td class='groupSeparator' colspan='4'>{$contractData[$i]['vardas']} {$contractData[$i]['pavarde']}</td>",
            "</tr>\n";
        }

        if($contractData[$i]['sutarties_paslaugu_kaina'] == 0)
          $contractData[$i]['sutarties_paslaugu_kaina'] = "neužsakyta";
        else
          $contractData[$i]['sutarties_paslaugu_kaina'] .= " &euro;";

        echo
          "<tr>",
            "<td>#{$contractData[$i]['nr']}</td>",
            "<td>{$contractData[$i]['sutarties_data']}</td>",
            "<td>{$contractData[$i]['sutarties_kaina']} &euro;</td>",
            "<td>{$contractData[$i]['sutarties_paslaugu_kaina']}</td>",
          "</tr>\n";

        if (isRowLastInGroup($contractData, $i, 'asmens_kodas')) {
          if($contractData[$i]['bendra_kliento_paslaugu_kaina'] == 0) {
            $contractData[$i]['bendra_kliento_paslaugu_kaina'] = "neužsakyta";
          } else {
            $contractData[$i]['bendra_kliento_paslaugu_kaina'] .= " &euro;";
          }
          echo 
            "<tr class='aggregate'>",
              "<td colspan='2'></td>",
              "<td class='border'>{$contractData[$i]['bendra_kliento_sutarciu_kaina']} &euro;</td>",
              "<td class='border'>{$contractData[$i]['bendra_kliento_paslaugu_kaina']}</td>",
            "</tr>\n";
        }
      } ?>
      <tr class="rowSeparator"><td colspan="5"></td></tr>
      <tr class="rowSeparator"><td colspan="5"></td></tr>
						
      <tr class="aggregate">
        <td class="label" colspan="2">Suma:</td>
        <td class="border">
          <?php echo $totalPrice[0]['nuomos_suma']; ?> &euro;
        </td>
        <td class="border">
          <?php
          if($totalServicePrice[0]['paslaugu_suma'] == 0)
            $totalServicePrice[0]['paslaugu_suma'] = "neužsakyta";
          else
            $totalServicePrice[0]['paslaugu_suma'] .= " &euro;";

          echo $totalServicePrice[0]['paslaugu_suma'];
          ?>
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

