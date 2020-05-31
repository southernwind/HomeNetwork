# 自宅ネットワーク環境構築
Ansibleを使いネットワークまわりの設定を行う。

# 前提手順
## DNS兼DHCP Server
1. Raspbianをインストールする。 
2. bootディレクトリにsshという名前の空ファイルを作成し、SSH接続できるようにする。
3. ネットワーク設定を行う。
    - IPアドレスは 10.0.0.2で固定しておく  
        /etc/dhcpcd.confに以下を追記してreboot。
        ```
        interface eth0
        static ip_address=10.0.0.2/20
        static routers=10.0.0.1
        ```
    - 公開鍵認証でpiユーザーにログインできるようにする。
        * RaspberryPiの/.ssh/authorized_keysに公開鍵を保存する。
        * Ansibleを実行する側の./raspi_dhcp/ssh_keys/raspberrypi.keyに秘密鍵を保存する。

## DHCP通知用設定
SlackにDHCP割当通知を行うための設定を行う。
1. SlackでAppとそのIncoming Webhook URLを生成しておく。
2. Ansibleを実行する側の 
    ```
    ./raspi_dhcp/services/dhcp_assign_notifier/common/constants_default.py
    ```
    を
    ```
    ./raspi_dhcp/services/dhcp_assign_notifier/common/constants.py
    ```
    にコピーして、WEBHOOK_URLに投稿先SlackのIncoming Webhook URLを代入する。

# 実行
```
sh execute.sh
```