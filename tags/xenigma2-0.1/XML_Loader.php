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

class XML_Loader {
  var $curl;

  function __construct() {
    // Create curl resource
    $this->curl = curl_init();

    // Return the transfer as a string
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
  }

  function __destruct() {
    // Close curl resource to free up system resources
    curl_close($this->curl);
  }

  function Retrieve($url, $login) {
    // Set usename & password
    if($login != '')
      curl_setopt($this->curl, CURLOPT_USERPWD, $login);

    // Set URL
    curl_setopt($this->curl, CURLOPT_URL, $url);

    // $output contains the output string
    $output = curl_exec($this->curl);
    if($output == FALSE)
      return FALSE;
    
    return simplexml_load_string($output);
  }
}

?>
