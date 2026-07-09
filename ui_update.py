from pathlib import Path
root = Path(r'C:\Users\최준용\Documents\[이스트캠프] 워게임 중간 대회')
index = root / 'index.html'
html = index.read_text(encoding='utf-8')
modal = '''    <div class="modal-overlay" id="tutorial-modal" aria-hidden="true">
      <section class="tutorial-modal" role="dialog" aria-modal="true" aria-labelledby="tutorial-title">
        <div class="section-heading">
          <span id="tutorial-title">&#xCCAB; &#xC774;&#xC6A9; &#xAC00;&#xC774;&#xB4DC;</span>
          <button class="icon-button" id="tutorial-close" type="button" aria-label="close">&#x00D7;</button>
        </div>
        <div class="tutorial-body">
          <p>&#xD300;&#xBA85;&#xC744; &#xC801;&#xC6A9;&#xD55C; &#xB4A4; &#xBB38;&#xC81C; &#xCE74;&#xB4DC;&#xB97C; &#xC120;&#xD0DD;&#xD574; &#xAC01; &#xD398;&#xC774;&#xC9C0;&#xC758; &#xB2E8;&#xC11C;&#xB97C; &#xAD00;&#xCC30;&#xD558;&#xC138;&#xC694;.</p>
          <ol>
            <li>&#xC785;&#xB825;&#xAC12;, &#xCFE0;&#xD0A4;, &#xC751;&#xB2F5; &#xBB38;&#xAD6C;, &#xAC1C;&#xBC1C;&#xC790; &#xB3C4;&#xAD6C;&#xB97C; &#xD568;&#xAED8; &#xD655;&#xC778;&#xD569;&#xB2C8;&#xB2E4;.</li>
            <li>&#xC5B4;&#xB824;&#xC6B4; &#xBB38;&#xC81C;&#xB294; &#xD78C;&#xD2B8;&#xB97C; 1&#xB2E8;&#xACC4;&#xBD80;&#xD130; &#xCC28;&#xB840;&#xB85C; &#xD3BC;&#xCCD0; &#xD655;&#xC778;&#xD558;&#xC138;&#xC694;.</li>
            <li>&#xD50C;&#xB798;&#xADF8;&#xB97C; &#xCC3E;&#xC73C;&#xBA74; &#xBB38;&#xC81C; &#xD558;&#xB2E8; &#xC81C;&#xCD9C;&#xCC3D;&#xC5D0; <code>EST{...}</code> &#xD615;&#xC2DD;&#xC73C;&#xB85C; &#xC785;&#xB825;&#xD569;&#xB2C8;&#xB2E4;.</li>
          </ol>
          <p class="form-help">&#xD78C;&#xD2B8;&#xB294; &#xD480;&#xC774;&#xB97C; &#xC9C1;&#xC811; &#xB9D0;&#xD558;&#xAE30;&#xBCF4;&#xB2E4; &#xAD00;&#xCC30; &#xC21C;&#xC11C;&#xB97C; &#xC7A1;&#xC544;&#xC8FC;&#xB294; &#xC2E0;&#xD638;&#xC785;&#xB2C8;&#xB2E4;.</p>
        </div>
        <button class="primary-button modal-action" id="tutorial-start" type="button">&#xC2DC;&#xC791;&#xD558;&#xAE30;</button>
      </section>
    </div>'''
if 'tutorial-modal' not in html:
    target = '    <div class="toast" id="toast" role="status" aria-live="polite"></div>'
    html = html.replace(target, modal + '\n' + target)
index.write_text(html, encoding='utf-8')

app = root / 'app.js'
js = app.read_text(encoding='utf-8')
insert = '''function bindTutorialModal() {
  const modal = document.querySelector("#tutorial-modal");
  if (!modal) return;
  const closeButtons = [document.querySelector("#tutorial-close"), document.querySelector("#tutorial-start")].filter(Boolean);
  const close = () => {
    modal.classList.remove("visible");
    modal.setAttribute("aria-hidden", "true");
    localStorage.setItem("tutorialSeen", "1");
  };
  closeButtons.forEach((button) => button.addEventListener("click", close));
  modal.addEventListener("click", (event) => {
    if (event.target === modal) close();
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal.classList.contains("visible")) close();
  });
  if (localStorage.getItem("tutorialSeen") !== "1") {
    modal.classList.add("visible");
    modal.setAttribute("aria-hidden", "false");
  }
}

'''
if 'bindTutorialModal' not in js:
    js = js.replace('async function boot() {', insert + 'async function boot() {')
    js = js.replace('  setTeamName(teamName());', '  setTeamName(teamName());\n  bindTutorialModal();')
app.write_text(js, encoding='utf-8')