const navButtons = document.querySelectorAll(".nav-btn");

navButtons.forEach((button) => {
  button.addEventListener("click", async () => {
    const page = button.dataset.page;

    showLoader();

    // set active AFTER click (better UX)
    navButtons.forEach((btn) => btn.classList.remove("active"));
    button.classList.add("active");

    try {
      const res = await fetch(page);
      const html = await res.text();

      document.getElementById("page").innerHTML = html;

      setTimeout(() => {
        if (page.includes("dashboard")) {
          window.initDashboard?.();
          window.loadEarningsChart?.("year");
        }

        if (page.includes("yearly")) {
          window.initYearSlider?.();
        }

        if (page.includes("jobs")) {
          window.initJobsPage?.();
        }
        if (page.includes("salaries")) {
          window.initSalaries?.();
        }
      }, 80);
    } catch (err) {
      console.error(err);
    } finally {
      hideLoader();
    }
  });
});
/* =========================
   STATUS BADGE
========================= */
function statusBadge(status) {
  if (status === "active") {
    return `<span class="badge bg-success">Aktive</span>`;
  }

  if (status === "finished") {
    return `<span class="badge bg-secondary">Përfunduar</span>`;
  }

  return status;
}

/* =========================
   GLOBAL HELPERS
========================= */
function formatDateAlbanian(dateString) {
  if (!dateString) return "";

  const months = [
    "Janar",
    "Shkurt",
    "Mars",
    "Prill",
    "Maj",
    "Qershor",
    "Korrik",
    "Gusht",
    "Shtator",
    "Tetor",
    "Nëntor",
    "Dhjetor",
  ];

  const date = new Date(dateString);
  return `${months[date.getMonth()]} ${date.getFullYear()}`;
}

function getSalaryClass(amount) {
  const num = parseFloat(amount);

  if (num >= 600) return "salary-high";
  if (num >= 450) return "salary-medium";
  return "salary-low";
}

/* =========================
   YEAR SLIDER (ARROWS ONLY)
========================= */
window.changeYear = function (direction) {
  const slider = document.querySelector(".years-slider");
  const slides = document.querySelectorAll(".year-slide");

  if (!slider || slides.length === 0) return;

  if (typeof window.currentIndex === "undefined") {
    window.currentIndex = 0;
  }

  window.currentIndex += direction;

  if (window.currentIndex < 0) window.currentIndex = 0;
  if (window.currentIndex >= slides.length) {
    window.currentIndex = slides.length - 1;
  }

  const width = slides[0].offsetWidth;

  slider.style.transform = `translateX(-${window.currentIndex * width}px)`;
};

/* =========================
   LOADER HIDE
========================= */
let loaderStartTime = 0;

function showLoader() {
  const loader = document.getElementById("pageLoader");
  if (!loader) return;

  loaderStartTime = Date.now();

  loader.style.display = "flex";
  loader.style.opacity = "1";
}

function hideLoader() {
  const loader = document.getElementById("pageLoader");
  if (!loader) return;

  const elapsed = Date.now() - loaderStartTime;

  const minTime = 500; // 🔥 keep screen at least 1.2s

  const remaining = minTime - elapsed;

  setTimeout(
    () => {
      loader.style.opacity = "0";

      setTimeout(() => {
        loader.style.display = "none";
      }, 300);
    },
    remaining > 0 ? remaining : 0,
  );
}
