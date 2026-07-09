<?php
$challengeId = 'idle-empire';
$goal = 100000000;
$flag = 'EST{idle_empire_client_trust_bypass}';

if (($_GET['action'] ?? '') === 'claim') {
    header('Content-Type: application/json; charset=utf-8');
    $totalEarned = isset($_POST['totalEarned']) ? (float) $_POST['totalEarned'] : 0;
    echo json_encode($totalEarned >= $goal
        ? ['success' => true, 'flag' => $flag]
        : ['success' => false], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>코인 제국 보급 작전 | EST ?袁⑸떊癰귣똻釉??紐낅뱜??곌쉬</title>
    <link rel="stylesheet" href="/styles.css?v=military-3" />
    <style>
      .idle-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:18px;margin-top:18px}
      @media (min-width:760px){.idle-grid{grid-template-columns:minmax(0,1fr) minmax(280px,.8fr)}}
      .idle-panel{border:1px solid rgba(170,185,130,.28);background:rgba(19,26,18,.72);padding:16px}
      .coin-button{width:176px;height:176px;border-radius:50%;border:2px solid #d8c46a;background:radial-gradient(circle at 34% 28%,#fff1a8,#caa437 45%,#5b4b18 100%);box-shadow:0 10px 0 #4d4016,0 18px 32px rgba(0,0,0,.35);color:#171204;font-size:20px;font-weight:800;cursor:pointer}
      .coin-button:active{transform:translateY(6px);box-shadow:0 4px 0 #4d4016,0 8px 18px rgba(0,0,0,.3)}
      .idle-stats{display:grid;gap:8px;margin-top:14px}
      .idle-stat{display:flex;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.08);padding:6px 0;color:var(--muted)}
      .idle-stat b{color:var(--text)}
      .progress-rail{height:16px;background:rgba(0,0,0,.28);border:1px solid rgba(170,185,130,.25);overflow:hidden}
      .progress-fill{height:100%;width:0;background:linear-gradient(90deg,#6f8d46,#d8c46a)}
      .shop-list{display:grid;gap:10px;max-height:420px;overflow:auto}
      .shop-item{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:center;border:1px solid rgba(170,185,130,.2);background:rgba(9,14,10,.45);padding:10px}
      .shop-item strong{display:block}.shop-item small{color:var(--muted)}
      .shop-item button{min-width:100px}.win-box{display:none;margin-top:14px}.win-box.show{display:block}
    </style>
  </head>
  <body>
    <main class="challenge-page">
      <a class="back-link" href="/">?臾믪읈 ?⑥눘??筌뤴뫖以?/a>
      <section class="challenge-detail">
        <div class="section-heading"><span>肄붿씤 ?쒓뎅 蹂닿툒 ?묒쟾</span><small>癰귣떯???癒?텦 / 餓λ쵌??/small></div>
        <div class="challenge-body">
          <p class="eyebrow">300 ?袁㏓궢 / ?觀由?癰귣떯???臾믪읈</p>
          <h1>?꾨뗄????볥럢 癰귣떯??筌앹빘????덉졃</h1>
          <p>?袁④컩 癰귣떯????벥 ?癒?짗 筌?쑨???貫?х몴?筌앹빘苑???袁⑹읅 癰귣떯???꾨뗄???100,000,000 ??곴맒 ?類ｋ궖??뤿뼏??뽰궎. ?? ?怨뱀넺?癒?벥 癰귣떯??揶쏅?????逾??野꺜筌앹빖由?遺? ?類ㅼ뵥??롫뮉 野껉퍔?????뼎??낅빍??</p>

          <div class="idle-panel">
            <div class="idle-stat"><span>?臾믪읈 筌뤴뫚紐?/span><b><span id="earned">0</span> / 100,000,000</b></div>
            <div class="progress-rail"><div class="progress-fill" id="progress"></div></div>
          </div>

          <div class="idle-grid">
            <section class="idle-panel" style="text-align:center">
              <h2>?袁⑹삢 筌?쑨??/h2>
              <button class="coin-button" id="coinBtn" type="button">癰귣떯???類ｋ궖</button>
              <div class="idle-stats">
                <div class="idle-stat"><span>?袁⑹삺 ?꾨뗄??/span><b id="coins">0</b></div>
                <div class="idle-stat"><span>?λ뜄????밴텦</span><b id="cps">0</b></div>
                <div class="idle-stat"><span>??????밴텦</span><b id="clickPower">1</b></div>
                <div class="idle-stat"><span>?臾믪읈 ??볦퍢</span><b id="time">00:00:00</b></div>
              </div>
            </section>
            <section class="idle-panel">
              <h2>癰귣떯??筌앹빘苑?/h2>
              <div class="shop-list" id="shopList"></div>
            </section>
          </div>

          <div class="hint-box visible win-box" id="winBox">
            <strong>筌뤴뫚紐???苑?癰귣떯??/strong>
            <p id="winText">癰귣챶? ?諭????疫?餓?..</p>
          </div>

          <div class="hint-box visible staged-hints">
            <strong>?臾믪읈 筌롫뗀??/strong>
            <details><summary>1??ｍ?/summary><p>野껊슣??筌욊쑵六??怨밴묶揶쎛 ??뺤쒔 DB揶쎛 ?袁⑤빍???됰슢??怨? ???關?????ㅻ뮉筌왖 ?類ㅼ뵥??뤿뼏??뽰궎.</p></details>
            <details><summary>2??ｍ?/summary><p>??뺤쒔??筌ㅼ뮇伊?癰귣떯???????堉?揶쏅????袁⑸꽊??롫뮉筌왖 ??쎈뱜??곌쾿 ?遺욧퍕???온筌↔퀬釉?????</p></details>
            <details><summary>3??ｍ?/summary><p>餓λ쵐????袁㏓궢 揶쏅?? ?????곷섧?硫? ?袁⑤빍????뺤쒔揶쎛 筌욊낯???④쑴沅??곷튊 ??몃빍?? ????덉졃?癒?퐣??域?獄쏆꼶? ?怨뱀넺????곸뒠??뤿뼏??뽰궎.</p></details>
          </div>

          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">癰귣떯??/button>
          </form>
        </div>
      </section>
    </main>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="/app.js?v=team-required-1"></script>
    <script>
      const goal = <?php echo $goal; ?>;
      const saveKey = 'idleEmpireSave';
      const units = [
        {id:'u1', name:'??? 筌?쑨?ф묾?, desc:'?λ뜆????μ맄 ??곗굨 ?貫??, base:25, cps:1},
        {id:'u2', name:'癰귣떯????뺤쨴', desc:'?癒?짗 ???땾 ??以?筌앹빘苑?, base:160, cps:6},
        {id:'u3', name:'??깆읈 獄쏆뮇?얏묾?, desc:'?貫??揶쎛??뉗ぇ ?怨몃뱟', base:900, cps:28},
        {id:'u4', name:'?袁⑸떊 ??뺤쒔??, desc:'?類ㅺ텦 ?癒?짗???紐껊굡', base:4800, cps:120},
        {id:'u5', name:'?닿퀣???⑤벊??, desc:'????癰귣떯????밴텦??, base:26000, cps:620},
        {id:'u6', name:'?袁⑷쉐 筌?쑨?э쭕?, desc:'?용쵐肉??癒?뜚 ??륁춿筌?, base:140000, cps:3200},
        {id:'u7', name:'??議딃겫? 疫뀀뜃??, desc:'筌ㅼ뮇伊?癰귣떯??筌앹빜猷?筌ｋ떯??, base:750000, cps:17000}
      ];
      const $ = (id) => document.getElementById(id);
      const fmt = (n) => Math.floor(n).toLocaleString('ko-KR');
      const fresh = () => ({coins:0,totalEarned:0,playTime:0,levels:Object.fromEntries(units.map(u=>[u.id,0]))});
      let state = load();
      let won = false;

      function load(){try{return Object.assign(fresh(), JSON.parse(localStorage.getItem(saveKey)) || {})}catch(e){return fresh()}}
      function save(){localStorage.setItem(saveKey, JSON.stringify(state))}
      function cps(){return units.reduce((sum,u)=>sum + u.cps * (state.levels[u.id] || 0), 0)}
      function cost(u){return Math.floor(u.base * Math.pow(1.17, state.levels[u.id] || 0))}
      function time(s){s=Math.floor(s);return [Math.floor(s/3600),Math.floor(s%3600/60),s%60].map(v=>String(v).padStart(2,'0')).join(':')}
      function renderShop(){
        $('shopList').innerHTML = '';
        units.forEach(u => {
          const row = document.createElement('div'); row.className = 'shop-item';
          const info = document.createElement('div'); info.innerHTML = `<strong>${u.name}</strong><small>${u.desc}<br>癰귣똻? ${state.levels[u.id] || 0} / +${fmt(u.cps)} CPS</small>`;
          const btn = document.createElement('button'); btn.className = 'primary-button'; btn.type = 'button'; btn.textContent = `${fmt(cost(u))} ?닌됤꼻`; btn.disabled = state.coins < cost(u);
          btn.onclick = () => buy(u.id);
          row.append(info, btn); $('shopList').append(row);
        });
      }
      function render(){
        $('coins').textContent = fmt(state.coins); $('earned').textContent = fmt(state.totalEarned); $('cps').textContent = fmt(cps()); $('clickPower').textContent = fmt(1); $('time').textContent = time(state.playTime);
        $('progress').style.width = `${Math.min(100, state.totalEarned / goal * 100)}%`;
        renderShop(); if (!won && state.totalEarned >= goal) claim();
      }
      function buy(id){const u = units.find(x=>x.id===id); const price = cost(u); if (state.coins < price) return; state.coins -= price; state.levels[id] = (state.levels[id] || 0) + 1; save(); render();}
      async function claim(){
        won = true; $('winBox').classList.add('show');
        const res = await fetch('?action=claim', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'totalEarned=' + encodeURIComponent(state.totalEarned)});
        const data = await res.json(); $('winText').textContent = data.success ? `癰귣챶? ?諭???袁⑥┷: ${data.flag}` : '筌뤴뫚紐???륂뒄揶쎛 ?봔鈺곌퉲鍮??덈뼄.';
      }
      $('coinBtn').onclick = () => {state.coins += 1; state.totalEarned += 1; save(); render();};
      setInterval(() => {const gain = cps(); state.coins += gain; state.totalEarned += gain; state.playTime += 1; save(); render();}, 1000);
      render();
    </script>
  </body>
</html>
