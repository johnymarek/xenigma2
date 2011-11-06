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
 * $Id:$
 */

require_once ('enigma2WebInterface.php');
require_once ('configuration.php');

$webif = new enigma2WebInterface($ip_addr, $http_port, $login);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
?>
<rss version="2.0" xmlns:media="http://purl.org/dc/elements/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/">


  // nothing here yet
  
</rss>
