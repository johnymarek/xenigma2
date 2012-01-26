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

class E2event {
  var $id;
  var $start;
  var $duration;
  var $currenttime;
  var $title;
  var $description;
  var $servicereference;
  var $servicename;
}

class E2service {
  var $servicereference;
  var $servicename;
}

class E2playable {
  var $servicereference;
  var $isplayable;
}

class E2webInterface {
  var $ip_addr;
  var $http_port;
  var $login;
  var $xml_loader;

  function __construct($ip_addr, $http_port, $login) {
    $this->ip_addr = $ip_addr;
    $this->http_port = $http_port;
    $this->login = $login;
    $this->xml_loader = new XML_Loader();
  }

  function __destruct() {
    $this->xml_loader = 0;
  }

  function encodeServiceReference($ref) {
    return urlencode(str_replace("\"", "%22", $ref));
  }

  function buildServiceList($xmldoc) {
    $services = array();
    $serviceitem = new E2service();
    foreach ($xmldoc as $service) {
      $serviceitem->servicereference = $this->encodeServiceReference((string)$service->e2servicereference);
      $serviceitem->servicename = (string)$service->e2servicename;
      $services[] = clone($serviceitem);
    }
    return $services;
  }

  function loadBouquets() {
    $url = "$this->ip_addr:$this->http_port/web/getservices";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $this->buildServiceList($xmldoc);
  }

  function loadServices($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/getservices?sRef=$serviceReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $this->buildServiceList($xmldoc);
  }

  function loadPlayableList($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/servicelistplayable?sRef=$serviceReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    $services = array();
    $playable = new E2playable();
    foreach ($xmldoc as $event) {
      $playable->servicereference = $this->encodeServiceReference((string)$event->e2servicereference);
      if($event->e2isplayable == 'True')
        $playable->isplayable = "1";
      else
        $playable->isplayable = "0";
      $services[] = clone($playable);
    }
    return $services;
  }

  function buildEPGList($xmldoc) {
    $services = array();
    $serviceevent = new E2event();
    foreach ($xmldoc as $event) {
      $serviceevent->id = (int)$event->e2eventid;
      $serviceevent->start = (int)$event->e2eventstart;
      $serviceevent->duration = (int)$event->e2eventduration;
      $serviceevent->currenttime = (int)$event->e2eventcurrenttime;
      $serviceevent->title = (string)$event->e2eventtitle;
      $description = (string)$event->e2eventdescription;
      if($description == '' || $description == 'None') {
        $extended = (string)$event->e2eventdescriptionextended;
        if($extended == 'None')
          $description = '';
        else
          $description = $extended;
      }
      $serviceevent->description = $description;
      $serviceevent->servicereference = $this->encodeServiceReference((string)$event->e2eventservicereference);
      $serviceevent->servicename = (string)$event->e2eventservicename;
      $services[] = clone($serviceevent);
    }
    return $services;
  }

  function loadEPG($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/epgservice?sRef=$serviceReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $this->buildEPGList($xmldoc);
  }

  function loadEPGnow($bouquetReference) {
    $url = "$this->ip_addr:$this->http_port/web/epgnow?bRef=$bouquetReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $this->buildEPGList($xmldoc);
  }

  function loadEPGnext($bouquetReference) {
    $url = "$this->ip_addr:$this->http_port/web/epgnext?bRef=$bouquetReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $this->buildEPGList($xmldoc);
  }

  function zap($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/zap?sRef=$serviceReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    if ($xmldoc->e2state == 'True')
      return true;
    return false;
  }

  function message($text, $type, $timeout) {
    $url = "$this->ip_addr:$this->http_port/web/message?text=$text&type=$type&timeout=$timeout";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    return $xmldoc->e2resulttext;
  }

  function isPlayable($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/serviceplayable?sRef=$serviceReference";
    $xmldoc = $this->xml_loader->Retrieve($url, $this->login);
    if ($xmldoc->e2isplayable == 'True')
      return true;
    return false;
  }
}

?>
