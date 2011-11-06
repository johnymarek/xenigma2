<?php
/*
 * Copyright (C) 2011 Olli Savia
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * $Id: $
 */

require_once ('Enigma2WebInterface.php');
require_once ('configuration.php');

$servicereference = urlencode($_GET['servicereference']);

if(extension_loaded("socket")) {
  $webif = new E2WebInterface($ip_addr, $http_port, $login);
  if($webif->isStreamable($servicereference) == true)
    $link = "http://$ip_addr:8001/$servicereference";
  else
    $link = "http://localhost/media/sda1/scripts/xEnigma2/zap.php?servicereference=$servicereference";
}
else {
    $link = "http://$ip_addr:8001/$servicereference";
  }

header("HTTP/1.0 302 Found");
header("Location: $link");

?>

<!--
<item_template>
  <onClick>
  <script>
  //showIdle();
  //SwitchViewer(7);
  //movieLink = "<?php echo "http://192.168.1.100:8001/"; ?>" + getItemInfo(getFocusItemIndex(), "serviceref";
  //playItemURL(movieLink, 0, "mediaDisplay");
playItemURL(getItemInfo(getFocusItemIndex(), "link"), 0, "mediaDisplay");
  </script>
  </onClick>
</item_template>
-->
