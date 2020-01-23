<?php
    class Vendor{
        public $registry;
        public $assignment;
        public $organization_name;
        public $organization_address;
    }
    $vendor_list = [];
    if ($f = fopen("/var/lib/myscripts/oui.csv", "r")) {
        while (($csvrow = fgetcsv($f))) {
            $vendor = new Vendor();
            $vendor->registry = $csvrow[0];
            $vendor->assignment = $csvrow[1];
            $vendor->organization_name = $csvrow[2];
            $vendor->organization_address = $csvrow[3];
            array_push($vendor_list, $vendor);
        }
        fclose($f);
    }
    $leases = array_map(function($x) use($vendor_list) {
        $row = explode(" ", $x);
        $vendors = array_filter($vendor_list,function($v) use($row){
            return strpos(strtoupper(str_replace(":","",$row[1])), $v->assignment) === 0;
        });
        $vendor_name = count($vendors) === 0 ? NULL : reset($vendors)->organization_name;
        return array(
            "time"=>date("Y/m/d H:i:s",intval($row[0])),
            "mac"=>$row[1],
            "ip"=>$row[2],
            "host"=>$row[3],
            "id"=>$row[4],
            "vendor_name"=> $vendor_name
        );
    },array_filter(explode("\n",file_get_contents("/var/lib/misc/dnsmasq.leases")), function($x){
        return $x !== "";
    }));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            table {
                border: 1px solid #111;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #111;
                padding: 5px;
            }
            th{
                background-color: #42cef5;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>リース有効期限</th>
                    <th>MACアドレス</th>
                    <th>IPアドレス</th>
                    <th>ホスト名</th>
                    <th>クライアントID</th>
                    <th>ベンダー名</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($leases as $val ){?>
                <tr>
                    <td><?=$val["time"]?></td>
                    <td><?=$val["mac"]?></td>
                    <td><?=$val["ip"]?></td>
                    <td><?=$val["host"]?></td>
                    <td><?=$val["id"]?></td>
                    <td><?=$val["vendor_name"]?></td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </body>
</html>