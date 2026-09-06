let chart;

window.loadEarningsChart = async function (mode = "year") {
  const canvas = document.getElementById("earningsChart");
  if (!canvas) return;

  const res = await fetch(
    mode === "year"
      ? "api/get_earnings_chart.php"
      : "api/get_earnings_monthly.php",
  );

  const data = await res.json();

  const labels = data.map((x) => x.year);
  const values = data.map((x) => x.total);

  const ctx = canvas.getContext("2d");

  // 🔥 DESTROY OLD CHART (CRITICAL FIX)
  if (chart) {
    chart.destroy();
  }

  chart = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: mode === "year" ? "Vjetor (€)" : "Mujor (€)",
          data: values,
          borderColor: "#22c55e",
          backgroundColor: "rgba(34, 197, 94, 0.2)",
          fill: true,
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  });
};
document.querySelectorAll(".chart-toggle button").forEach((btn) => {
  btn.addEventListener("click", () => {
    document
      .querySelectorAll(".chart-toggle button")
      .forEach((b) => b.classList.remove("active"));

    btn.classList.add("active");

    const mode = btn.textContent.includes("Mujor") ? "month" : "year";

    loadEarningsChart(mode);
  });
});
