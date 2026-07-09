from pathlib import Path
p = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회\styles.css')
s = p.read_text(encoding='utf-8')
s = s.replace('}`r`n`r`n.tool-form', '}\n\n.tool-form')
extra = '''
.staged-hints {
  display: block;
}

.staged-hints p {
  margin: 0.45rem 0 0;
  line-height: 1.65;
}

.staged-hints details {
  margin-top: 0.65rem;
  padding: 0.75rem 0.85rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.16);
}

.staged-hints summary {
  cursor: pointer;
  color: var(--accent-2);
  font-weight: 800;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 20;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: rgba(4, 7, 12, 0.72);
  backdrop-filter: blur(8px);
}

.modal-overlay.visible {
  display: flex;
}

.tutorial-modal {
  width: min(620px, 94vw);
  overflow: hidden;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: #141922;
  box-shadow: var(--shadow);
}

.tutorial-body {
  padding: 1.2rem 1.35rem 0.4rem;
  color: var(--muted);
  line-height: 1.7;
}

.tutorial-body ol {
  margin: 0.85rem 0;
  padding-left: 1.35rem;
}

.tutorial-body li {
  margin: 0.45rem 0;
}

.icon-button {
  display: grid;
  width: 34px;
  height: 34px;
  place-items: center;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: transparent;
  color: var(--muted);
  cursor: pointer;
}

.icon-button:hover {
  color: var(--text);
  background: rgba(255, 255, 255, 0.05);
}

.modal-action {
  width: calc(100% - 2.7rem);
  margin: 0.7rem 1.35rem 1.35rem;
}
'''
if '.modal-overlay' not in s:
    s = s.rstrip() + '\n' + extra
p.write_text(s, encoding='utf-8')