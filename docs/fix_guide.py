from pathlib import Path
p = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회\docs\build_solve_guide.py')
s = p.read_text(encoding='utf-8')
start = s.index("('1. Session Shadow'")
end = s.index("\n('2. Blind Notice'", start)
new = """('1. Session Shadow',[('난이도','중하'),('유형','Cookie 값 변조'),('목표','shadow_role 쿠키 값을 admin으로 변경해 플래그 확인'),('정답','EST{cookie_role_admin_shadow}')],'서버가 사용자의 역할을 세션 저장소가 아니라 브라우저 쿠키 shadow_role 값에서 그대로 읽습니다. 쿠키는 사용자가 직접 수정할 수 있으므로, 역할 검증을 쿠키 값만으로 처리하면 권한 상승이 발생합니다.',['문제 페이지에 접속한 뒤 개발자 도구의 Application 또는 Storage 탭을 엽니다.','Cookies 항목에서 현재 사이트의 shadow_role 값을 확인합니다. 처음 접속하면 guest로 설정되어 있습니다.','shadow_role 값을 admin으로 수정하고 저장합니다.','페이지를 새로고침하면 current role이 admin으로 바뀌고 플래그가 표시됩니다.','표시된 플래그를 문제 하단 제출 폼에 입력합니다.'],['shadow_role=guest','shadow_role=admin'],'shadow_role 쿠키가 보이지 않으면 문제 페이지를 한 번 새로고침하세요. 쿠키 값을 바꾼 뒤에도 guest로 보이면 도메인/path가 현재 사이트와 맞는 쿠키를 수정했는지 확인해야 합니다.'),"""
s = s[:start] + new + s[end:]
p.write_text(s, encoding='utf-8')