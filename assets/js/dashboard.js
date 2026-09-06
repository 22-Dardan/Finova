// =============================
// DASHBOARD INIT (SPA SAFE)
// =============================
function waitForDashboard() {
  const check = setInterval(() => {
    const el = document.getElementById("totalJobs");

    if (el && el.offsetParent !== null) {
      clearInterval(check);
      loadDashboard();
      startRealtimeDashboard();
      markLive();
    }
  }, 50);
}
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("earningsChart")) {
    loadEarningsChart();
  }
});
document.addEventListener("DOMContentLoaded", waitForDashboard);

window.initDashboard = function () {
  waitForDashboard();
};

// =============================
// FETCH DATA
// =============================
function loadDashboard() {
  console.log("DASHBOARD LOAD @", new Date().toLocaleTimeString());

  fetch("api/dashboard-stats.php?t=" + Date.now(), {
    cache: "no-store",
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("DATA RECEIVED:", data);
      updateUI(data);
    })
    .catch((err) => console.error("Dashboard error:", err));
}

// =============================
// ELASTIC COUNTER (SMAAASH EFFECT)
// =============================
function animateNumber(el, start, end, duration = 1000) {
  let startTime = null;

  function easeOutElastic(x) {
    const c4 = (2 * Math.PI) / 3;
    return x === 0
      ? 0
      : x === 1
        ? 1
        : Math.pow(2, -10 * x) * Math.sin((x * 10 - 0.75) * c4) + 1;
  }

  function step(time) {
    if (!startTime) startTime = time;

    let progress = (time - startTime) / duration;
    progress = Math.min(progress, 1);

    const eased = easeOutElastic(progress);
    const value = start + (end - start) * eased;

    el.textContent = Math.floor(value);

    if (progress < 1) {
      requestAnimationFrame(step);
    }
  }

  requestAnimationFrame(step);
}

// =============================
// EURO ANIMATION
// =============================
function animateEuro(el, start, end, duration = 1000) {
  let startTime = null;

  function step(time) {
    if (!startTime) startTime = time;

    let progress = (time - startTime) / duration;
    progress = Math.min(progress, 1);

    const eased = progress;
    const value = start + (end - start) * eased;

    el.textContent = "€" + value.toFixed(2);

    if (progress < 1) {
      requestAnimationFrame(step);
    }
  }

  requestAnimationFrame(step);
}

// =============================
// UI UPDATE
// =============================
function updateUI(data) {
  if (!data) return;

  // NUMBERS
  const setNum = (id, value) => {
    const el = document.getElementById(id);
    if (el) animateNumber(el, 0, Number(value || 0));
  };

  const setEuro = (id, value) => {
    const el = document.getElementById(id);
    if (el) animateEuro(el, 0, Number(value || 0));
  };

  setNum("totalJobs", data.totalJobs);

  setEuro("totalEarnings", data.totalEarnings);
  setEuro("highestSalary", data.highestSalary);
  setEuro("monthlyAverage", data.monthlyAverage);
  setEuro("currentMonthEarnings", data.currentMonthEarnings);
  setEuro("lastMonthEarnings", data.lastMonthEarnings);

  // BADGES
  const job = document.getElementById("currentJob");
  if (job) job.innerHTML = `<span class="job-tag">${data.currentJob}</span>`;

  const time = document.getElementById("totalWorkTime");
  if (time)
    time.innerHTML = `<span class="time-badge">${data.totalWorkTime}</span>`;

  // GROWTH
  const growthEl = document.getElementById("monthlyGrowth");

  if (growthEl) {
    const growth = Number(data.monthlyGrowth || 0);

    if (growth > 0) {
      growthEl.innerHTML = `▲ ${growth}%`;
      growthEl.style.color = "#22c55e";
    } else if (growth < 0) {
      growthEl.innerHTML = `▼ ${Math.abs(growth)}%`;
      growthEl.style.color = "#ef4444";
    } else {
      growthEl.innerHTML = "0%";
      growthEl.style.color = "#999";
    }
  }
}

// =============================
// REAL-TIME SYSTEM
// =============================
let dashboardInterval = null;

function startRealtimeDashboard() {
  if (dashboardInterval) clearInterval(dashboardInterval);

  dashboardInterval = setInterval(() => {
    console.log("REAL-TIME REFRESH @", new Date().toLocaleTimeString());

    loadDashboard();
    markLive();
  }, 10000);
}

function stopRealtimeDashboard() {
  if (dashboardInterval) {
    clearInterval(dashboardInterval);
    dashboardInterval = null;
  }
}

// =============================
// LIVE STATUS INDICATOR
// =============================
function markLive() {
  const el = document.getElementById("liveStatus");
  if (!el) return;

  el.textContent =
    "Live (Përditësuar: " + new Date().toLocaleTimeString() + ")";
}
