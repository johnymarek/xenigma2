<?php
/*
 * Copyright (C) 2011-2012 Olli Savia
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
 * $Id$
 */

require_once ('XML_Loader.php');
require_once ('Enigma2WebInterface.php');
require_once ('configuration.php');

$xmlLoader = new XML_Loader();
$webif = new E2WebInterface($ip_addr, $http_port, $login);
$bouquets = $webif->loadBouquets();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
?>
<rss version="2.0" xmlns:media="http://purl.org/dc/elements/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/">

  <mediaDisplay name="threePartsView"
  forceFocusOnItem="yes"

  backgroundColor="-1:-1:-1"

  showHeader="no"

  idleImageHeightPC="10"
  idleImageWidthPC="10"
  idleImageXPC="10"
  idleImageYPC="20"

  showDefaultInfo="no"
  infoXPC="80"
  infoYPC="80"

  itemBorderColor="-1:-1:-1"
  itemGap="0"
  itemHeightPC="7"
  itemImageHeightPC="0"
  itemImageWidthPC="0"
  itemPerPage="5"
  itemWidthPC="37"
  itemXPC="40"
  itemYPC="31"

  popupBackgroundColor="22:22:22"
  popupBorderColor="255:0:0"
  popupHeightPC="0"
  popupWidthPC="0"
  popupXPC="100"
  popupYPC="0"

  sideLeftWidthPC="0"
  sideRightWidthPC="0"
  >

  <backgroundDisplay>
    <image redraw="no" widthPC="100" heightPC="100">/tmp/usbmounts/sda1/scripts/xEnigma2/images/service_selection_bg.jpg</image>
  </backgroundDisplay>

  <itemDisplay>
    <text redraw="yes" widthPC="100" heightPC="100" fontSize="15" fontFile="/tmp/usbmounts/sda1/scripts/xEnigma2/fonts/nmsbd.ttf" foregroundColor="237:243:241">
    <backgroundColor>
    <script>
    if(getFocusItemIndex() == getQueryItemIndex()) {
      "78:116:153";
     }
     else {
      "6:39:72";
    }
    </script>
    </backgroundColor>
    <script>
    getItemInfo(-1, "title");
    </script>
    </text>
  </itemDisplay>

<?php
$xmldoc = $xmlLoader->Retrieve("http://xenigma2.googlecode.com/svn/trunk/version.xml", "");
$latestVersion = (float)$xmldoc->version;
$xmldoc = $xmlLoader->Retrieve("http://localhost/media/sda1/scripts/xEnigma2/version.xml", "");
$localVersion = (float)$xmldoc->version;
if($latestVersion > $localVersion) {
  echo '<text redraw="yes" widthPC="55" heightPC="10" offsetXPC="23" offsetYPC="72" align="center" lines=1 fontSize=12 fontFile="/tmp/usbmounts/sda1/scripts/xEnigma2/fonts/nmsbd.ttf" foregroundColor="237:243:241" backgroundColor="36:39:72">';
  echo "New version $latestVersion available";
  echo '</text>';
}
?>

  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait1.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait2.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait3.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait4.png</idleImage>

</mediaDisplay>

<channel>
<title>INDEX</title>

<?php
foreach($bouquets as $bouquet) {
  $title = $bouquet->servicename;
  $link="http://localhost/media/sda1/scripts/xEnigma2/channellist.php?bouquet=$bouquet->servicereference";

  echo <<< ITEM

  <item>
    <title>LIVE TV: $title</title>
    <link>$link</link>
  </item>


ITEM;
}
?>

</channel>
</rss>
