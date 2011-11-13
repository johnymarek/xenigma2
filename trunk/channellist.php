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
 * $Id$
 */

require_once ('Enigma2WebInterface.php');
require_once ('configuration.php');

$webif = new E2WebInterface($ip_addr, $http_port, $login);
$bouquet = urlencode($_GET['bouquet']);

$services = $webif->loadServices($bouquet);
$epgnow=$webif->loadEPGnow($bouquet);
$playableList=$webif->loadPlayableList($bouquet);

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
  itemPerPage="8"
  itemWidthPC="88"
  itemXPC="6"
  itemYPC="13"

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
    <image redraw="no" widthPC="100" heightPC="100">/tmp/usbmounts/sda1/scripts/xEnigma2/images/channel_list_bg.jpg</image>
  </backgroundDisplay>

  <itemDisplay>
    <text redraw="yes" widthPC="100" heightPC="100" fontSize="15" fontFile="/tmp/usbmounts/sda1/scripts/xEnigma2/fonts/nmsbd.ttf">
    <backgroundColor>
    <script>
    if(getFocusItemIndex() == getQueryItemIndex()) {
      descriptionText = getItemInfo(-1, "description");
      "78:116:153";
    }
    else {
      "6:39:72";
    }
    </script>
    </backgroundColor>
    <foregroundColor>
    <script>
    if(getItemInfo(-1, "playable") == "1")
      "237:243:241";
    else
      "164:182:186";
    </script>
    </foregroundColor>
    <script>
    getItemInfo(-1, "title");
    </script>
    </text>
  </itemDisplay>

  <text redraw="yes" widthPC="90" heightPC="25" offsetXPC="5.5" offsetYPC="70" align="left" lines=5 fontSize=12 fontFile="/tmp/usbmounts/sda1/scripts/xEnigma2/fonts/nmsbd.ttf" foregroundColor="237:243:241" backgroundColor="6:39:72">
    <script>
    descriptionText;
    </script>
  </text>

  <onClick>
    playItemURL();
  </onClick>

  <onUserInput>
    ret="false";
    if(currentUserInput() == "1") {
      showIdle();
      epg_url="epg.php?servicereference=" + getItemInfo(getFocusItemIndex(), "servicereference");
      doModalRSS(epg_url);
      ret="true";
    }
    ret;
  </onUserInput>

  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait1.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait2.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait3.png</idleImage>
  <idleImage>/tmp/usbmounts/sda1/scripts/xEnigma2/images/wait4.png</idleImage>

</mediaDisplay>

<channel>
<title>CHANNEL_LIST</title>

<?php
foreach($services as $service) {
  $title = $service->servicename;
  $description = "";
  $url = "http://$ip_addr:8001/$service->servicereference";
  foreach($epgnow as $event) {
    if($title == $event->servicename) {
      if($event->title != 'None')
        $title = "$title ($event->title)";
      if($event->description == '' || $event->description == 'None') {
        if($event->descriptionextended != 'None')
          $description = $event->descriptionextended;
      }
      else {
        $description = $event->description;
      }
      break;
    }
  }
  foreach($playableList as $playableItem) {
    if($service->servicereference == $playableItem->servicereference) {
      $playable = $playableItem->isplayable;
      break;
    }
  }

  echo <<< ITEM
  <item>
    <title>$title</title>
    <enclosure type="video/mpeg" url="$url"/>
    <description>$description</description>
    <playable>$playable</playable>
    <servicereference>$service->servicereference</servicereference>  
  </item>


ITEM;
}
?>

</channel>
</rss>
