let challenges = [];
let challengePoints = {};
let solved = new Set();

const toastEl = document.querySelector("#toast");
const teamInput = document.querySelector("#team-name");
const challengeGrid = document.querySelector("#challenge-grid");
const scoreboardEl = document.querySelector("#scoreboard-body");

const text = {
  noChallenges: "현재 하달된 작전 과제가 없습니다.",
  noSubmissions: "아직 보고된 전과가 없습니다.",
  correct: "인증 표식 확인. 전과 현황에 반영했습니다.",
  wrong: "불일치. 현장 단서를 다시 확인하십시오.",
  submitError: "보고 처리 중 오류가 발생했습니다.",
  teamRequired: "먼저 분대명을 등록하십시오.",
  teamUpdated: "분대명을 등록했습니다.",
  boardRefreshed: "상황판을 갱신했습니다.",
  loadError: "중앙 서버 자료를 불러오지 못했습니다.",
};

if (!document.cookie.includes("role=")) {
  document.cookie = "role=user; path=/";
}

function teamName() {
  const stored = (localStorage.getItem("teamName") || "").trim().slice(0, 64);
  if (stored.toLowerCase() === "you") {
    localStorage.removeItem("teamName");
    return "";
  }
  return stored;
}

function focusTeamInput() {
  if (teamInput) {
    teamInput.focus();
    teamInput.select();
    return;
  }
  sessionStorage.setItem("teamRequired", "1");
  window.setTimeout(() => {
    window.location.href = "/?teamRequired=1";
  }, 700);
}

function requireTeamName() {
  if (teamName()) return true;
  showToast(text.teamRequired);
  focusTeamInput();
  return false;
}

function setTeamName(value) {
  const normalized = (value || "").trim().slice(0, 64);
  if (!normalized || normalized.toLowerCase() === "you") {
    localStorage.removeItem("teamName");
    if (teamInput) teamInput.value = "";
    return "";
  }
  localStorage.setItem("teamName", normalized);
  if (teamInput) teamInput.value = normalized;
  return normalized;
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function totalScore() {
  return [...solved].reduce((total, id) => total + (challengePoints[id] || 0), 0);
}

function showToast(message) {
  if (!toastEl) return;
  toastEl.innerHTML = message;
  toastEl.classList.add("visible");
  window.setTimeout(() => toastEl.classList.remove("visible"), 2200);
}

async function fetchJson(url, options) {
  const response = await fetch(url, options);
  const result = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(result.message || `Request failed: ${response.status}`);
  return result;
}

async function loadChallenges() {
  const result = await fetchJson("/api/challenges.php");
  challenges = result.challenges || [];
  challengePoints = Object.fromEntries(challenges.map((challenge) => [challenge.challenge_id, Number(challenge.points)]));
  renderChallenges();
}

function renderChallenges() {
  if (!challengeGrid) return;
  if (!challenges.length) {
    challengeGrid.innerHTML = `<p class="empty-state">${text.noChallenges}</p>`;
    return;
  }

  challengeGrid.innerHTML = challenges
    .map((challenge) => {
      const id = escapeHtml(challenge.challenge_id);
      const title = escapeHtml(challenge.title);
      const summary = escapeHtml(challenge.summary);
      const category = escapeHtml(challenge.category);
      const difficulty = escapeHtml(challenge.difficulty);
      const path = escapeHtml(challenge.path);
      const points = Number(challenge.points) || 0;
      const status = solved.has(challenge.challenge_id) ? "완료" : `${points} 전과`;
      const statusClass = solved.has(challenge.challenge_id) ? " solved" : "";
      return `
        <a class="challenge-card" href="${path}" data-id="${id}" data-points="${points}">
          <header><strong>${title}</strong><span class="pill status-pill${statusClass}">${status}</span></header>
          <span>${summary}</span>
          <div class="meta"><span class="pill">${category}</span><span class="pill">${difficulty}</span></div>
        </a>
      `;
    })
    .join("");
}

function renderProgress() {
  const scoreEl = document.querySelector("#total-score");
  const clearCountEl = document.querySelector("#clear-count");
  if (scoreEl) scoreEl.textContent = totalScore();
  if (clearCountEl) clearCountEl.textContent = `${solved.size} / ${challenges.length} 임무완료`;
  renderChallenges();
}

function renderScoreboard(rows) {
  if (!scoreboardEl) return;
  if (!rows.length) {
    scoreboardEl.innerHTML = `<tr><td colspan="4">${text.noSubmissions}</td></tr>`;
    return;
  }

  scoreboardEl.innerHTML = rows
    .map((player, index) => `
      <tr>
        <td>#${index + 1}</td>
        <td>${escapeHtml(player.name)}</td>
        <td>${Number(player.solved) || 0}</td>
        <td>${Number(player.score) || 0}</td>
      </tr>
    `)
    .join("");
}

async function loadScoreboard() {
  const result = await fetchJson(`/api/scoreboard.php?team=${encodeURIComponent(teamName())}`);
  solved = new Set(result.solved || []);
  renderScoreboard(result.scoreboard || []);
  renderProgress();
}

async function submitFlag(challengeId, flag) {
  if (!requireTeamName()) return { ok: false, needsTeam: true };
  return fetchJson("/api/submit.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ id: challengeId, flag, team: teamName() }),
  });
}

function bindFlagForms() {
  document.querySelectorAll("[data-flag-form]").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      const challengeId = form.dataset.challengeId;
      const input = form.querySelector("input[name='flag']");
      try {
        const result = await submitFlag(challengeId, input.value.trim());
        if (result.needsTeam) return;
        if (result.ok) {
          solved.add(challengeId);
          showToast(text.correct);
          await loadScoreboard();
          return;
        }
        showToast(text.wrong);
      } catch (error) {
        showToast(text.submitError);
      }
    });
  });
}

document.querySelector("#team-form")?.addEventListener("submit", async (event) => {
  event.preventDefault();
  if (!setTeamName(new FormData(event.currentTarget).get("team"))) {
    showToast(text.teamRequired);
    focusTeamInput();
    return;
  }
  showToast(text.teamUpdated);
  await loadScoreboard();
});

async function resetProgress() {
  if (!window.confirm("작전 완료 기록과 문제 임시 자료를 초기화할까요?")) return;
  try {
    const result = await fetchJson("/api/reset.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ team: teamName() }),
    });
    if (result.ok) {
      solved = new Set();
      showToast("작전 기록과 임시 자료를 초기화했습니다.");
      await loadScoreboard();
    }
  } catch (error) {
    showToast("기록 초기화 중 오류가 발생했습니다.");
  }
}

document.querySelector("#refresh-board")?.addEventListener("click", async () => {
  await loadScoreboard();
  showToast(text.boardRefreshed);
});

document.querySelector("#reset-progress")?.addEventListener("click", resetProgress);

function bindTutorialModal() {
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
  if (localStorage.getItem("tutorialSeen") === "1") {
    modal.classList.remove("visible");
    modal.setAttribute("aria-hidden", "true");
  } else {
    modal.classList.add("visible");
    modal.setAttribute("aria-hidden", "false");
  }
}

async function boot() {
  if (teamInput) teamInput.value = teamName();
  if (teamInput && (sessionStorage.getItem("teamRequired") === "1" || new URLSearchParams(window.location.search).has("teamRequired"))) {
    sessionStorage.removeItem("teamRequired");
    showToast(text.teamRequired);
    focusTeamInput();
  }
  bindTutorialModal();
  bindFlagForms();
  try {
    await loadChallenges();
    await loadScoreboard();
  } catch (error) {
    showToast(text.loadError);
  }
}

boot();