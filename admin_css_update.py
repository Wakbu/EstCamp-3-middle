from pathlib import Path
p = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회\styles.css')
s = p.read_text(encoding='utf-8')
extra = '''
.admin-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.65rem;
  margin-bottom: 1rem;
}

.active-tab {
  border-color: rgba(61, 214, 181, 0.45) !important;
  color: var(--accent) !important;
  background: rgba(61, 214, 181, 0.08) !important;
}

.admin-notice {
  margin: 0 0 1rem;
}

.split-admin-layout {
  grid-template-columns: minmax(440px, 0.95fr) minmax(380px, 1.05fr);
}

.admin-card-list {
  display: grid;
  gap: 0.8rem;
  margin-top: 1rem;
}

.admin-challenge-card {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 0.8rem;
  padding: 0.95rem;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.03);
  cursor: pointer;
}

.admin-challenge-card:hover,
.admin-challenge-card.selected {
  border-color: rgba(61, 214, 181, 0.42);
  background: rgba(61, 214, 181, 0.07);
}

.admin-challenge-card strong,
.admin-challenge-card code,
.admin-challenge-card small {
  display: block;
  overflow-wrap: anywhere;
}

.admin-challenge-card code {
  margin-top: 0.35rem;
  color: var(--accent-2);
}

.admin-challenge-card small {
  margin-top: 0.3rem;
  color: var(--muted);
}

.admin-card-meta {
  grid-column: 1 / -1;
  display: flex;
  flex-wrap: wrap;
  gap: 0.45rem;
}

.admin-challenge-card .inline-delete {
  align-self: start;
}

@media (max-width: 1120px) {
  .split-admin-layout {
    grid-template-columns: 1fr;
  }
}
'''
if '.admin-card-list' not in s:
    s = s.rstrip() + '\n' + extra
p.write_text(s, encoding='utf-8')