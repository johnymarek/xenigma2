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

class E2event {
  var $id;
  var $start;
  var $duration;
  var $currenttime;
  var $title;
  var $description;
  var $descriptionextended;
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
  var $curl;

  function __construct($ip_addr, $http_port, $login) {
    $this->ip_addr = $ip_addr;
    $this->http_port = $http_port;
    $this->login = $login;

    // Create curl resource
    $this->curl = curl_init();

    // Return the transfer as a string
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
  }
  function __destruct() {
    // Close curl resource to free up system resources
    curl_close($this->curl);
  }

  function encodeServiceReference($ref) {
    return urlencode(str_replace("\"", "%22", $ref));
  }

  function buildServiceList($xmldoc) {
    $services = array();
    $serviceitem = new E2service();
    foreach (simplexml_load_string($xmldoc) as $service) {
      $serviceitem->servicereference = $this->encodeServiceReference((string)$service->e2servicereference);
      $serviceitem->servicename = (string)$service->e2servicename;
      $services[] = clone($serviceitem);
    }
    return $services;
  }

  function loadBouquets() {
    $url = "$this->ip_addr:$this->http_port/web/getservices";
    $doc = $this->retrieve($url);
    return $this->buildServiceList($doc);
  }

  function loadServices($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/getservices?sRef=$serviceReference";
    $doc = $this->retrieve($url);
    return $this->buildServiceList($doc);
  }

  function loadPlayableList($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/servicelistplayable?sRef=$serviceReference";
    $xmldoc = $this->retrieve($url);
    $services = array();
    $playable = new E2playable();
    foreach (simplexml_load_string($xmldoc) as $event) {
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
    foreach (simplexml_load_string($xmldoc) as $event) {
      $serviceevent->id = (int)$event->e2eventid;
      $serviceevent->start = (int)$event->e2eventstart;
      $serviceevent->duration = (int)$event->e2eventduration;
      $serviceevent->currenttime = (int)$event->e2eventcurrenttime;
      $serviceevent->title = (string)$event->e2eventtitle;
      $serviceevent->description = (string)$event->e2eventdescription;
      $serviceevent->descriptionextented = (string)$event->e2eventdescriptionextexded;
      $serviceevent->servicereference = $this->encodeServiceReference((string)$event->e2servicereference);
      $serviceevent->servicename = (string)$event->e2eventservicename;
      $services[] = clone($serviceevent);
    }
    return $services;
  }

  function loadEPG($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/epgservice?sRef=$serviceReference";
    $doc = $this->retrieve($url);
    return $this->buildEPGList($doc);
  }

  function loadEPGnow($bouquetReference) {
    $url = "$this->ip_addr:$this->http_port/web/epgnow?bRef=$bouquetReference";
    $doc = $this->retrieve($url);
    return $this->buildEPGList($doc);
  }

  function zap($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/zap?sRef=$serviceReference";
    $doc = $this->retrieve($url);
    $result = simplexml_load_string($doc);
    if ($result->e2state == 'True')
      return true;
    return false;
  }

  function message($text, $type, $timeout) {
    $url = "$this->ip_addr:$this->http_port/web/message?text=$text&type=$type&timeout=$timeout";
    $doc = $this->retrieve($url);
    $result = simplexml_load_string($doc);
    return $result->e2resulttext;
  }

  function isPlayable($serviceReference) {
    $url = "$this->ip_addr:$this->http_port/web/serviceplayable?sRef=$serviceReference";
    $doc = $this->retrieve($url);
    $result = simplexml_load_string($doc);
    if ($result->e2isplayable == 'True')
      return true;
    return false;
  }

  function isStreamable($serviceReference) {
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket == false) {
      return false;
    }

    $retval = true;

    $result = socket_connect($socket, $this->ip_addr, 8001);
    if ($result === false) {
      $retval = false;
    }
    else {
      $msg = "GET /$serviceReference HTTP/1.0\r\n";
      $msg .= "\r\n";

      socket_write($socket, $msg, strlen($msg));

      $reply = socket_read($socket, 200);
      $headers = explode("\r\n", $reply);
      if ($headers[0] != "HTTP/1.0 200 OK")
        $retval = false;
    }

    socket_close($socket);
    return $retval;
  }

  function retrieve($url) {
    // Set usename & password
    if($this->login != '')
      curl_setopt($this->curl, CURLOPT_USERPWD, $this->login);

    // Set URL
    curl_setopt($this->curl, CURLOPT_URL, $url);

    // $output contains the output string
    $output = curl_exec($this->curl);

    return $output;
  }
}

?>
