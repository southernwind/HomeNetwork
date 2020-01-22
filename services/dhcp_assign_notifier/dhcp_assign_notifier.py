import urllib.request,json,time,csv
from common.constants import *

class Row():
    def __init__(self, line): 
        s = line.split(" ")
        self.time = s[0]
        self.mac = s[1]
        self.ip = s[2]
        self.host = s[3]
        self.id = s[4]

    def get_vendor(self, vendor_list):
        m = self.mac.replace(":","").upper()
        return next((v for v in vendor_list if m.startswith(v.assignment)), None)

class Vendor():
    def __init__(self, csvrow):
        self.registry = csvrow[0]
        self.assignment = csvrow[1]
        self.organization_name = csvrow[2]
        self.organization_address = csvrow[3]

def json_post(url, data):
    request = urllib.request.Request(url, json.dumps(data).encode("utf-8"), {"Content-Type" : "application/json"}, method="POST")
    response = urllib.request.urlopen(request)
    return response

def get_vendor_list():
    with open("/var/lib/myscripts/oui.csv", "r") as f:
        reader = csv.reader(f)
        return [Vendor(csvrow) for csvrow in reader]

def main():
    vendor_list = get_vendor_list()
    before = None
    while True:
        path = "/var/lib/misc/dnsmasq.leases"
        with open(path, "r") as file:
            lines = file.readlines()
        time.sleep(1)
        with open(path, "r") as file:
            lines_check = file.readlines()
        
        # 不一致の場合、読み取り失敗の可能性があるため、読み込み直す
        if lines != lines_check:
            continue

        # 前回との差分を求める
        now = [Row(line) for line in lines]
        if before is not None:
            added = [nr for nr in now if nr.mac not in [br.mac for br in before]]
            removed = [br for br in before if br.mac not in [nr.mac for nr in now]]
            text = ""
            if len(removed) != 0:
                text += ":zzz:DHCP割当解除通知\n" + "\n".join([f"```IP={r.ip}\nMAC={r.mac}\nHost={r.host}\nVendor={r.get_vendor(vendor_list)}```" for r in removed])

            if len(added) != 0:
                if len(text) != 0:
                    text += "\n\n"
                text += ":sunny:DHCP割当通知\n" + "\n".join([f"\n```IP={a.ip}\nMAC={a.mac}\nHost={a.host}\nVendor={a.get_vendor(vendor_list)}```" for a in added])
            
            if len(text) != 0:
                json_post(WEBHOOK_URL, data={"text": text})
        before = now
        time.sleep(1)


if __name__ == "__main__":
    main()