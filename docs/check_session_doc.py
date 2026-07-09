from docx import Document
p=r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회\docs\WARGAME_SOLVE_GUIDE.docx'
d=Document(p)
text='\n'.join(x.text for x in d.paragraphs)
for needle in ['HMAC','EST{cookie_role_admin_shadow}','Cookie 값 변조','EST{signed_cookie_shadow_9f2a}']:
    print(needle, needle in text)