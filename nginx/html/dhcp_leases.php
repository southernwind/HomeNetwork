<?php
    $leases = array_map(function($x) {
        $row = explode(" ", $x);
        return array(
            "time"=>date("Y/m/d H:i:s",intval($row[0])),
            "mac"=>$row[1],
            "ip"=>$row[2],
            "host"=>$row[3],
            "id"=>$row[4]
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
                </tr>
                <?php }?>
            </tbody>
        </table>
    </body>
</html>