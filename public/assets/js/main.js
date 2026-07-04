document.addEventListener("submit", (event) => {
  const form = event.target;
  if (form instanceof HTMLFormElement && form.dataset.track) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ event: form.dataset.track });
  }
});

document.addEventListener("click", (event) => {
  const target = event.target instanceof Element ? event.target.closest("[data-track]") : null;
  if (!target) return;
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({ event: target.getAttribute("data-track") });
});

const loadAnalytics = () => {
  const config = window.ATYPIK_ANALYTICS || {};
  if (window.__atypikAnalyticsLoaded) return;
  window.__atypikAnalyticsLoaded = true;

  if (config.gtmId) {
    window.dataLayer.push({ event: "cookie_consent_accept" });
    const script = document.createElement("script");
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(config.gtmId)}`;
    document.head.appendChild(script);
  }

  if (config.ga4Id) {
    const script = document.createElement("script");
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(config.ga4Id)}`;
    document.head.appendChild(script);
    window.gtag = function gtag() {
      window.dataLayer.push(arguments);
    };
    window.gtag("js", new Date());
    window.gtag("config", config.ga4Id, { anonymize_ip: true });
  }
};

const cookieBanner = document.querySelector("[data-cookie-banner]");
const consent = localStorage.getItem("atypik_cookie_consent");
if (cookieBanner && !consent) {
  cookieBanner.hidden = false;
}
if (consent === "accept") {
  loadAnalytics();
}
document.addEventListener("click", (event) => {
  const button = event.target instanceof Element ? event.target.closest("[data-cookie-choice]") : null;
  if (!button) return;
  const choice = button.getAttribute("data-cookie-choice") === "accept" ? "accept" : "refuse";
  localStorage.setItem("atypik_cookie_consent", choice);
  if (cookieBanner) cookieBanner.hidden = true;
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({ event: `cookie_consent_${choice}` });
  if (choice === "accept") loadAnalytics();
});

const roleSelect = document.querySelector("#role-select");
const ownerFields = document.querySelector(".owner-fields");
if (roleSelect && ownerFields) {
  const syncOwnerFields = () => {
    ownerFields.hidden = roleSelect.value !== "owner";
  };
  roleSelect.addEventListener("change", syncOwnerFields);
  syncOwnerFields();
}

const navToggle = document.querySelector(".nav-toggle");
const mainNav = document.querySelector("#navigation-principale");
if (navToggle && mainNav) {
  navToggle.addEventListener("click", () => {
    const open = mainNav.classList.toggle("is-open");
    navToggle.setAttribute("aria-expanded", String(open));
  });
}

const advancedFilters = document.querySelector(".filter-more");
if (advancedFilters) {
  const compactFilters = window.matchMedia("(max-width: 720px)");
  const syncFilters = () => {
    if (compactFilters.matches) {
      advancedFilters.removeAttribute("open");
    } else {
      advancedFilters.setAttribute("open", "");
    }
  };
  compactFilters.addEventListener("change", syncFilters);
  syncFilters();
}

const calendarRoot = document.querySelector("[data-availability-calendar]");
if (calendarRoot) {
  const payload = JSON.parse(calendarRoot.dataset.calendarPayload || "{}");
  const availabilityByDate = new Map((payload.availabilities || []).map((item) => [item.date, item]));
  const bookedDates = new Set(payload.booked_dates || []);
  const title = document.querySelector("[data-calendar-title]");
  const prev = document.querySelector("[data-calendar-prev]");
  const next = document.querySelector("[data-calendar-next]");
  const startInput = document.querySelector("input[name='start_date']");
  const endInput = document.querySelector("input[name='end_date']");
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  let visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
  const formatter = new Intl.DateTimeFormat("fr-FR", { month: "long", year: "numeric" });

  const isoDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  };

  const renderCalendar = () => {
    const monthStart = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1);
    const monthEnd = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 0);
    const firstOffset = (monthStart.getDay() + 6) % 7;
    const cells = [];

    if (title) {
      title.textContent = formatter.format(visibleMonth);
    }

    for (let i = 0; i < firstOffset; i += 1) {
      cells.push('<span class="calendar-empty" aria-hidden="true"></span>');
    }

    for (let day = 1; day <= monthEnd.getDate(); day += 1) {
      const date = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), day);
      const iso = isoDate(date);
      const availability = availabilityByDate.get(iso);
      const isPast = date < today;
      const isBooked = bookedDates.has(iso);
      const hasOverride = availability && availability.price_override !== null;
      const unavailable = availability && Number(availability.is_available) === 0;
      const classes = ["calendar-day"];
      let label = "Prix standard";

      if (isPast) {
        classes.push("past");
        label = "Date passée";
      } else if (isBooked) {
        classes.push("booked");
        label = "Réservé";
      } else if (unavailable) {
        classes.push("unavailable");
        label = "Indisponible";
      } else {
        classes.push("available");
        label = "Disponible";
      }
      if (hasOverride) {
        classes.push("override");
        label = `${Number(availability.price_override).toLocaleString("fr-FR", { style: "currency", currency: "EUR" })}`;
      }

      cells.push(`<button type="button" class="${classes.join(" ")}" data-calendar-date="${iso}" ${isPast || isBooked ? "disabled" : ""} aria-label="${iso} - ${label}"><span>${day}</span><small>${label}</small></button>`);
    }

    calendarRoot.innerHTML = `<div class="calendar-weekdays" aria-hidden="true"><span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span></div><div class="calendar-grid">${cells.join("")}</div>`;
  };

  prev?.addEventListener("click", () => {
    visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() - 1, 1);
    renderCalendar();
  });
  next?.addEventListener("click", () => {
    visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 1);
    renderCalendar();
  });
  calendarRoot.addEventListener("click", (event) => {
    const button = event.target instanceof Element ? event.target.closest("[data-calendar-date]") : null;
    if (!button || button.hasAttribute("disabled")) return;
    const date = button.getAttribute("data-calendar-date");
    if (startInput) startInput.value = date;
    if (endInput) endInput.value = date;
    startInput?.focus();
  });
  renderCalendar();
}
