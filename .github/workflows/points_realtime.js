(() => {
  const $balance = document.getElementById("pointsBalance");
  const $tbody = document.getElementById("pointsHistoryBody");
  const $range = document.getElementById("historyRange");

  if (!$balance || !$tbody) return;

  const API_BAL = "./api/points_balance.php";
  const API_HIST = "./api/points_history.php";

  const esc = (s) =>
    String(s ?? "").replace(/[&<>"']/g, (m) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[m]));

  const formatDate = (iso) => {
    if (!iso) return "-";
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return iso;
    return d.toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" });
  };

  const renderHistory = (rows) => {
    if (!Array.isArray(rows) || rows.length === 0) {
      $tbody.innerHTML = `<tr><td colspan="4" class="text-muted">Belum ada transaksi poin.</td></tr>`;
      return;
    }

    $tbody.innerHTML = rows.map((r) => {
      const type = String(r.type ?? "").toUpperCase(); // EARN / REDEEM
      const isEarn = type === "EARN";
      const pts = Number(r.points ?? 0);

      const chip = isEarn
        ? `<span class="chip chip-earn">Earn</span>`
        : `<span class="chip chip-redeem">Redeem</span>`;

      const ptsClass = isEarn ? "text-earn" : "text-redeem";
      const ptsText = (isEarn ? "+" : "-") + Math.abs(pts);

      return `
        <tr>
          <td>${esc(formatDate(r.created_at))}</td>
          <td>${esc(r.description ?? "")}</td>
          <td>${chip}</td>
          <td class="text-right ${ptsClass}">${esc(ptsText)}</td>
        </tr>
      `;
    }).join("");
  };

  const fetchJSON = async (url) => {
    const res = await fetch(url, { credentials: "same-origin" });
    const ct = res.headers.get("content-type") || "";
    if (!ct.includes("application/json")) {
      const text = await res.text();
      throw new Error("Response bukan JSON: " + text.slice(0, 120));
    }
    return await res.json();
  };

  let lastSig = "";

  const refresh = async () => {
    try {
      const bal = await fetchJSON(API_BAL);
      if (bal?.ok) {
        $balance.textContent = String(bal.balance ?? 0);
      }

      const days = $range ? Number($range.value || 30) : 30;
      const hist = await fetchJSON(`${API_HIST}?days=${encodeURIComponent(days)}`);
      if (hist?.ok) {
        const sig = JSON.stringify(hist.history ?? []);
        if (sig !== lastSig) {
          lastSig = sig;
          renderHistory(hist.history);
        }
      }
    } catch (e) {
      if (!String($tbody.innerHTML).includes("Gagal memuat")) {
        $tbody.innerHTML = `<tr><td colspan="4" class="text-muted">Gagal memuat riwayat poin. (Cek API & login)</td></tr>`;
      }

      console.error("[points_realtime] refresh error:", e);
    }
  };

  if ($range) {
    $range.addEventListener("change", () => {
      lastSig = "";
      refresh();
    });
  }

  refresh();
  setInterval(refresh, 5000);
})();
