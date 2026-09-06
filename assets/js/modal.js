document.addEventListener("click", function (e) {
  const title = document.querySelector(".modal-title");
  const body = document.querySelector(".modal-body");

  if (!title || !body) return;

  // =========================
  // SAVE ACTIONS (SAFE CHECK)
  // =========================
  const btn = e.target.closest("[data-action]");

  if (btn) {
    const action = btn.getAttribute("data-action");
    body.innerHTML = "";

    // REGISTER JOB
    if (action === "register_job") {
      title.innerText = "Regjistro Punë";
      window.currentAction = "register_job";

      body.innerHTML = `
       <label>Logo e Kompanisë</label>
    <input 
      type="file" 
      name="company_logo" 
      class="form-control custom-input"
      accept="image/*">
      
        <label>Emri i Kompanisë</label>
        <input type="text" name="company_name" class="form-control custom-input">

        <label>Pozita</label>
        <input type="text" name="position" class="form-control custom-input">

        <label>Lokacioni</label>
        <input type="text" name="location" class="form-control custom-input">

        <label>Data</label>
        <input type="date" name="start_date" class="form-control custom-input">

        <label>Statusi</label>
        <select name="status" class="form-control custom-input">
          <option value="active">Aktive</option>
          <option value="finished">Përfunduar</option>
        </select>
      `;
    }

    // ADD FINANCE
    if (action === "add_finance") {
      title.innerText = "Shto Financa";
      window.currentAction = "add_finance";

      fetch("api/get-jobs.php")
        .then((res) => res.json())
        .then((jobs) => {
          let options = "";

          jobs.forEach((job) => {
            options += `
              <option value="${job.id}" data-position="${job.position}">
                ${job.company_name}
              </option>
            `;
          });

          body.innerHTML = `
            <label>Emri i Kompanisë</label>
            <select id="jobSelect" name="job_id" class="form-control custom-input">
              ${options}
            </select>
             <label>Pozita</label>
            <input type="text" id="position" readonly class="form-control custom-input">
             <label>Shuma</label>
            <input type="number" name="amount" class="form-control custom-input">
             <label>Data</label>
            <input type="date" name="date" class="form-control custom-input">
          `;

          const select = document.getElementById("jobSelect");
          const pos = document.getElementById("position");

          function update() {
            pos.value = select.options[select.selectedIndex].dataset.position;
          }

          select.addEventListener("change", update);
          update();
        });
    }

    // EDIT MENU
    if (action === "edit_jobs") {
      title.innerText = "Edito të Dhënat";
      window.currentAction = "edit_jobs";

      body.innerHTML = `
    <div class="edit-menu">

      <button class="edit-option-btn edit-job-btn" data-edit="jobs">
        
        Ndrysho Punën
      </button>

      <button class="edit-option-btn edit-finance-btn" data-edit="finances">
        
        Ndrysho Rrogat
      </button>

    </div>
  `;

      return;
    }
  }

  // =========================
  // EDIT ACTIONS
  // =========================
  const editBtn = e.target.closest(".edit-option-btn");

  if (editBtn) {
    const type = editBtn.dataset.edit;

    // EDIT JOBS
    if (type === "jobs") {
      title.innerText = "Ndrysho Punën";
      window.currentAction = "edit_job";

      fetch("api/get-jobs-edit.php")
        .then((res) => res.json())
        .then((jobs) => {
          let html = `<div class="job-list">`;

          jobs.forEach((job) => {
            html += `
        <div class="job-card edit-job-item" data-id="${job.id}">

          <div class="job-card-row">

            <div class="job-avatar">
  ${
    job.company_logo
      ? `<img src="${job.company_logo}" class="job-logo">`
      : job.company_name.charAt(0).toUpperCase()
  }
</div>

            <div class="job-info">

              <div class="job-top">
                <strong>${job.company_name}</strong>

                <span class="job-badge ${
                  job.status === "active" ? "badge-active" : "badge-finished"
                }">
                  ${job.status === "active" ? "Aktive" : "Përfunduar"}
                </span>
              </div>

              <div class="job-bottom">

                <span class="job-position-badge">
                  ${job.position}
                </span>

                <span class="job-location-badge">
                   ${job.location}
                </span>
 <span class="job-date-badge">
    ${
      job.start_date
        ? `${job.start_date} → ${job.end_date ? job.end_date : "Aktiv"}`
        : "-"
    }
  </span>
              </div>

            </div>

          </div>

        </div>
      `;
          });

          html += `</div>`;

          body.innerHTML = html;
        });
    }

    // EDIT FINANCES
    if (type === "finances") {
      title.innerText = "Ndrysho Rrogat";
      window.currentAction = "edit_finance";

      fetch("api/get-jobs.php")
        .then((res) => res.json())
        .then((companies) => {
          let html = `
        <label class="form-label">Zgjedh Kompaninë</label>

        <select id="companySelect" class="form-control custom-input">
          <option value="">Zgjedh Kompaninë</option>
      `;

          companies.forEach((c) => {
            html += `<option value="${c.id}">${c.company_name}</option>`;
          });

          html += `
        </select>

        <div id="financeList" class="finance-scroll"></div>
      `;

          body.innerHTML = html;

          const select = document.getElementById("companySelect");
          const list = document.getElementById("financeList");

          select.addEventListener("change", function () {
            const companyId = this.value;

            if (!companyId) {
              list.innerHTML = "";
              return;
            }

            fetch("api/get-finances-by-company.php?id=" + companyId)
              .then((res) => res.json())
              .then((finances) => {
                console.log(finances); // DEBUG (safe)

                let inner = "";

                finances.forEach((f) => {
                  console.log("AMOUNT:", f.amount, typeof f.amount);
                  inner += `
                <div class="job-card edit-finance-item"
                     data-id="${f.id}">

                  <div class="job-card-row">

                    <div class="job-avatar">
${f.company_logo ? `<img src="${f.company_logo}" class="job-logo">` : ""}
</div>

                    <div class="job-info">

                      <div class="job-top">
                        <span class="job-position-badge ${getSalaryClass(f.amount)}">
  €${f.amount}
</span>

                        <span class="job-position-badge">
                          ${f.position ? f.position : ""}
                        </span>
                      </div>

                      <div class="job-bottom">
                       <span class="job-location-badge">
   ${f.location || ""} • ${formatDateAlbanian(f.date)}
</span>
                      </div>

                    </div>

                  </div>

                </div>
              `;
                });

                list.innerHTML = inner;
              });
          });
        });
    }
  }
});
document.addEventListener("click", function (e) {
  const financeItem = e.target.closest(".edit-finance-item");

  if (!financeItem) return;

  const id = financeItem.dataset.id;

  const title = document.querySelector(".modal-title");
  const body = document.querySelector(".modal-body");

  title.innerText = "Edito Rrogën";

  window.currentAction = "edit_finance";

  fetch("api/get-finance.php?id=" + id)
    .then((res) => res.json())
    .then((finance) => {
      body.innerHTML = `
        <input type="hidden"
               name="finance_id"
               value="${finance.id}">

        <label class="form-label">
          Shuma (€)
        </label>

        <input type="number"
               name="amount"
               value="${finance.amount}"
               class="form-control custom-input">

        <label class="form-label">
          Data
        </label>

        <input type="date"
               name="date"
               value="${finance.date}"
               class="form-control custom-input">
      `;
    });
});
document.addEventListener("click", function (e) {
  const jobItem = e.target.closest(".edit-job-item");

  if (!jobItem) return;

  const id = jobItem.dataset.id;

  const title = document.querySelector(".modal-title");
  const body = document.querySelector(".modal-body");

  title.innerText = "Edito Punën";
  window.currentAction = "edit_job";

  fetch("api/get-job.php?id=" + id)
    .then((res) => res.json())
    .then((job) => {
      body.innerHTML = `
        <input type="hidden" name="job_id" value="${job.id}">

        <label class="form-label">Kompania</label>
        <input
          type="text"
          name="company_name"
          value="${job.company_name}"
          class="form-control custom-input">

        <label class="form-label">Pozita</label>
        <input
          type="text"
          name="position"
          value="${job.position}"
          class="form-control custom-input">

        <label class="form-label">Lokacioni</label>
        <input
          type="text"
          name="location"
          value="${job.location}"
          class="form-control custom-input">

        <label class="form-label">Data e Fillimit</label>
        <input
          type="date"
          name="start_date"
          value="${job.start_date}"
          class="form-control custom-input">

          <label class="form-label">Data e Përfundimit</label>
<input
  type="date"
  name="end_date"
  value="${job.end_date || ""}"
  class="form-control custom-input">

        <label class="form-label">Statusi</label>

        <select
          name="status"
          class="form-control custom-input">

          <option value="active"
            ${job.status === "active" ? "selected" : ""}>
            Aktive
          </option>

          <option value="finished"
            ${job.status === "finished" ? "selected" : ""}>
            Përfunduar
          </option>

        </select>
      `;
      setTimeout(() => {
        const endDate = document.querySelector('input[name="end_date"]');
        const status = document.querySelector('select[name="status"]');

        if (!endDate || !status) return;

        endDate.addEventListener("change", function () {
          if (endDate.value) {
            status.value = "finished";
          } else {
            status.value = "active";
          }
        });
      }, 0);
    });
});
document.addEventListener("click", function (e) {
  if (e.target.id === "saveBtn") {
    const modal = document.getElementById("globalModal");

    const formData = new FormData();

    modal.querySelectorAll("input, select, textarea").forEach((el) => {
      if (!el.name) return;

      if (el.type === "file") {
        if (el.files.length > 0) {
          formData.append(el.name, el.files[0]);
        }
      } else {
        if (el.value !== "") {
          formData.append(el.name, el.value);
        }
      }
    });

    let url = "";

    // 🚀 decide which API to call
    if (window.currentAction === "register_job") {
      url = "api/save-job.php";
    }

    if (window.currentAction === "add_finance") {
      url = "api/save-finance.php";
    }
    if (window.currentAction === "edit_job") {
      url = "api/update-job.php";
    }

    if (window.currentAction === "edit_finance") {
      url = "api/update-finance.php";
    }

    if (!url) {
      console.error("No action selected!");
      return;
    }

    fetch(url, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text()) // 👈 safer debugging first
      .then((text) => {
        console.log("RAW RESPONSE:", text);

        const data = JSON.parse(text);

        if (data.success) {
          Swal.fire({
            icon: "success",
            title: "Sukses!",
            text: data.message || "U ruajt me sukses",
            background: "#111",
            color: "#fff",
            timer: 2000,
            showConfirmButton: false,
          });

          bootstrap.Modal.getInstance(
            document.getElementById("globalModal"),
          ).hide();
        } else {
          Swal.fire({
            icon: "error",
            title: "Gabim!",
            text: data.message || "Diçka shkoi keq",
            background: "#111",
            color: "#fff",
          });
        }
      })
      .catch((err) => {
        console.error("Fetch error:", err);
      });
  }
});
