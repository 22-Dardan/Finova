let currentPage = 1;

window.initSalaries = function () {
  const tbody = document.getElementById("salaryTableBody");
  const yearFilter = document.getElementById("yearFilter");
  const companyFilter = document.getElementById("companyFilter");
  const prevPage = document.getElementById("prevPage");
  const nextPage = document.getElementById("nextPage");
  const pageInfo = document.getElementById("pageInfo");

  if (!tbody || !yearFilter || !companyFilter) return;

  loadFilters();
  loadSalaries();

  /* =========================
     FILTERS
  ========================= */
  yearFilter.addEventListener("change", () => {
    currentPage = 1;
    loadSalaries();
  });

  companyFilter.addEventListener("change", () => {
    currentPage = 1;
    loadSalaries();
  });

  /* =========================
     PAGINATION
  ========================= */
  prevPage.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      loadSalaries();
    }
  });

  nextPage.addEventListener("click", () => {
    currentPage++;
    loadSalaries();
  });

  /* =========================
     LOAD FILTERS
  ========================= */
  async function loadFilters() {
    const res = await fetch("api/get_salary_filters.php");
    const data = await res.json();

    yearFilter.innerHTML = `<option value="">Të gjitha vitet</option>`;

    companyFilter.innerHTML = `<option value="">Të gjitha kompanitë</option>`;

    data.years.forEach((year) => {
      yearFilter.innerHTML += `
        <option value="${year}">
          ${year}
        </option>
      `;
    });

    data.companies.forEach((company) => {
      companyFilter.innerHTML += `
        <option value="${company}">
          ${company}
        </option>
      `;
    });
  }

  /* =========================
     LOAD SALARIES
  ========================= */
  async function loadSalaries() {
    const year = yearFilter.value;
    const company = companyFilter.value;

    const res = await fetch(
      `api/get_salaries.php?page=${currentPage}&year=${year}&company=${company}`,
    );

    const data = await res.json();

    tbody.innerHTML = "";

    data.salaries.forEach((salary) => {
      tbody.innerHTML += `
        <tr>

          <td>
            <div class="company-cell">

              <img
                src="${salary.company_logo}"
                class="company-logo"
              >

              <span class="company-name">
                ${salary.company_name}
              </span>

            </div>
          </td>

          <td class="salary-date">
            ${formatMonthYear(salary.date)}
          </td>

          <td>
            <span class="salary-badge ${getSalaryClass(salary.amount)}">
              €${salary.amount}
            </span>
          </td>

        </tr>
      `;
    });

    pageInfo.innerText = `${data.current_page} / ${data.total_pages}`;

    prevPage.disabled = data.current_page <= 1;
    nextPage.disabled = data.current_page >= data.total_pages;
  }
};

/* =========================
   DATE FORMAT
========================= */
function formatMonthYear(dateString) {
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

/* =========================
   SALARY COLORS
========================= */
function getSalaryClass(amount) {
  const num = parseFloat(amount);

  if (num >= 600) return "salary-high";
  if (num >= 400) return "salary-medium";

  return "salary-low";
}

/* =========================
   EXPORT PDF
========================= */
function exportPDF() {
  const year = document.getElementById("yearFilter")?.value || "";

  const company = document.getElementById("companyFilter")?.value || "";

  window.open(
    `api/export_salaries_pdf.php?year=${year}&company=${company}`,
    "_blank",
  );
}
