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
    <title>Idle Empire | EST 전술보안 인트라넷</title>
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
      <a class="back-link" href="/">작전 과제 목록</a>
      <section class="challenge-detail">
        <div class="section-heading"><span>Idle Empire</span><small>보급 자산 / 중급</small></div>
        <div class="challenge-body">
          <p class="eyebrow">300 전과 / 장기 보급 작전</p>
          <h1>코인 제국 보급 증식 훈련</h1>
          <p>전방 보급소의 자동 채굴 장비를 증설해 누적 보급 코인을 100,000,000 이상 확보하십시오. 단, 상황판의 보고 값이 어디서 검증되는지 확인하는 것이 핵심입니다.</p>

          <div class="idle-panel">
            <div class="idle-stat"><span>작전 목표</span><b><span id="earned">0</span> / 100,000,000</b></div>
            <div class="progress-rail"><div class="progress-fill" id="progress"></div></div>
          </div>

          <div class="idle-grid">
            <section class="idle-panel" style="text-align:center">
              <h2>현장 채굴</h2>
              <button class="coin-button" id="coinBtn" type="button">보급 확보</button>
              <div class="idle-stats">
                <div class="idle-stat"><span>현재 코인</span><b id="coins">0</b></div>
                <div class="idle-stat"><span>초당 생산</span><b id="cps">0</b></div>
                <div class="idle-stat"><span>클릭 생산</span><b id="clickPower">1</b></div>
                <div class="idle-stat"><span>작전 시간</span><b id="time">00:00:00</b></div>
              </div>
            </section>
            <section class="idle-panel">
              <h2>보급 증설</h2>
              <div class="shop-list" id="shopList"></div>
            </section>
          </div>

          <div class="hint-box visible win-box" id="winBox">
            <strong>목표 달성 보고</strong>
            <p id="winText">본부 승인 대기 중...</p>
          </div>

          <div class="hint-box visible staged-hints">
            <strong>작전 메모</strong>
            <details><summary>1단계</summary><p>게임 진행 상태가 서버 DB가 아니라 브라우저 저장소에 남는지 확인하십시오.</p></details>
            <details><summary>2단계</summary><p>서버에 최종 보고할 때 어떤 값이 전송되는지 네트워크 요청을 관찰하십시오.</p></details>
            <details><summary>3단계</summary><p>중요한 전과 값은 클라이언트가 아니라 서버가 직접 계산해야 합니다. 이 훈련에서는 그 반대 상황을 이용하십시오.</p></details>
          </div>

          <form class="submit-row" data-flag-form data-challenge-id="<?php echo $challengeId; ?>">
            <input name="flag" placeholder="EST{...}" autocomplete="off" />
            <button class="primary-button" type="submit">보고</button>
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
        {id:'u1', name:'휴대 채굴기', desc:'초소 단위 소형 장비', base:25, cps:1},
        {id:'u2', name:'보급 드론', desc:'자동 회수 항로 증설', base:160, cps:6},
        {id:'u3', name:'야전 발전기', desc:'장비 가동률 상승', base:900, cps:28},
        {id:'u4', name:'전술 서버랙', desc:'정산 자동화 노드', base:4800, cps:120},
        {id:'u5', name:'군수 공장', desc:'대량 보급 생산선', base:26000, cps:620},
        {id:'u6', name:'위성 채굴망', desc:'광역 자원 수집망', base:140000, cps:3200},
        {id:'u7', name:'사령부 금고', desc:'최종 보급 증폭 체계', base:750000, cps:17000}
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
          const info = document.createElement('div'); info.innerHTML = `<strong>${u.name}</strong><small>${u.desc}<br>보유 ${state.levels[u.id] || 0} / +${fmt(u.cps)} CPS</small>`;
          const btn = document.createElement('button'); btn.className = 'primary-button'; btn.type = 'button'; btn.textContent = `${fmt(cost(u))} 구매`; btn.disabled = state.coins < cost(u);
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
        const data = await res.json(); $('winText').textContent = data.success ? `본부 승인 완료: ${data.flag}` : '목표 수치가 부족합니다.';
      }
      $('coinBtn').onclick = () => {state.coins += 1; state.totalEarned += 1; save(); render();};
      setInterval(() => {const gain = cps(); state.coins += gain; state.totalEarned += gain; state.playTime += 1; save(); render();}, 1000);
      render();
    </script>
  </body>
</html>
