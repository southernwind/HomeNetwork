<?php
    $leases = file_get_contents("/var/lib/misc/dnsmasq.leases");
    echo $leases;