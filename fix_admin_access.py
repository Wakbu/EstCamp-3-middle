from pathlib import Path
root = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회')
ht = root / '.htaccess'
s = ht.read_text(encoding='utf-8')
s = s.replace('RedirectMatch 404 "^/(api|admin|assets|challenges/generated)/?$"', 'RedirectMatch 404 "^/(api|assets|challenges/generated)/?$"')
ht.write_text(s, encoding='utf-8')
notes = root / 'IMPLEMENTATION_NOTES.md'
n = notes.read_text(encoding='utf-8')
n = n.replace('Admin page: `/admin/index.php`', 'Admin page: `/admin/` or `/admin/index.php`')
n = n.replace('URL: /admin/index.php', 'URL: /admin/ or /admin/index.php')
n = n.replace('such as `/db/`, `/docs/`, `/.git/`, `/.agents/`, `/assets/vault/`, `/api/`, `/admin/`, `/assets/`, and `/challenges/generated/`.', 'such as `/db/`, `/docs/`, `/.git/`, `/.agents/`, `/assets/vault/`, `/api/`, `/assets/`, and `/challenges/generated/`.')
n = n.replace('Use `/admin/index.php` for the admin page. Do not publish the solve guide under Apache web root.', 'Use `/admin/` or `/admin/index.php` for the admin page. Do not publish the solve guide under Apache web root.')
notes.write_text(n, encoding='utf-8')