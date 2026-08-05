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

document.addEventListener("submit", (event) => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;
  if (!window.confirm(form.dataset.confirm)) {
    event.preventDefault();
  }
});

document.addEventListener("submit", (event) => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement) || event.defaultPrevented) return;
  const submitter = event.submitter instanceof HTMLButtonElement ? event.submitter : form.querySelector("button[type='submit']");
  if (!submitter || submitter.dataset.submitting === "true") return;
  submitter.dataset.submitting = "true";
  submitter.dataset.originalText = submitter.textContent || "";
  submitter.textContent = submitter.dataset.loadingText || "Traitement en cours...";
  submitter.disabled = true;
});

document.addEventListener("click", (event) => {
  const button = event.target instanceof Element ? event.target.closest("[data-print-target]") : null;
  if (!button) return;
  window.print();
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

const COOKIE_CONSENT_KEY = "atypik_cookie_consent_v2";
const cookieBanner = document.querySelector("[data-cookie-banner]");
const consent = localStorage.getItem(COOKIE_CONSENT_KEY);
window.dataLayer = window.dataLayer || [];
if (cookieBanner) {
  cookieBanner.hidden = Boolean(consent);
}
if (consent === "accept") {
  loadAnalytics();
}
document.addEventListener("click", (event) => {
  const button = event.target instanceof Element ? event.target.closest("[data-cookie-choice]") : null;
  if (!button) return;
  const choice = button.getAttribute("data-cookie-choice") === "accept" ? "accept" : "refuse";
  localStorage.setItem(COOKIE_CONSENT_KEY, choice);
  if (cookieBanner) cookieBanner.hidden = true;
  window.dataLayer.push({ event: `cookie_consent_${choice}` });
  if (choice === "accept") loadAnalytics();
});

document.addEventListener("click", (event) => {
  const button = event.target instanceof Element ? event.target.closest("[data-cookie-manage]") : null;
  if (!button || !cookieBanner) return;
  cookieBanner.hidden = false;
  cookieBanner.querySelector("[data-cookie-choice]")?.focus();
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

const otherAmenitiesToggle = document.querySelector("[data-other-amenities-toggle]");
const otherAmenitiesField = document.querySelector("[data-other-amenities-field]");
if (otherAmenitiesToggle && otherAmenitiesField) {
  const syncOtherAmenities = () => {
    otherAmenitiesField.hidden = !otherAmenitiesToggle.checked;
  };
  otherAmenitiesToggle.addEventListener("change", syncOtherAmenities);
  syncOtherAmenities();
}

const navToggle = document.querySelector(".nav-toggle");
const mainNav = document.querySelector("#navigation-principale");
if (navToggle && mainNav) {
  const syncNavToggleLabel = (open) => {
    navToggle.textContent = open ? "Fermer" : "Menu";
    navToggle.setAttribute("aria-label", open ? "Fermer le menu principal" : "Ouvrir le menu principal");
  };
  syncNavToggleLabel(false);
  navToggle.addEventListener("click", () => {
    const open = mainNav.classList.toggle("is-open");
    navToggle.setAttribute("aria-expanded", String(open));
    syncNavToggleLabel(open);
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

document.querySelectorAll("[data-availability-calendar]").forEach((calendarRoot) => {
  const payload = JSON.parse(calendarRoot.dataset.calendarPayload || "{}");
  const availabilityByDate = new Map((payload.availabilities || []).map((item) => [item.date, item]));
  const bookedDates = new Set(payload.booked_dates || []);
  const container = calendarRoot.closest(".booking-box, .availability-calendar-panel") || document;
  const title = container.querySelector("[data-calendar-title]");
  const prev = container.querySelector("[data-calendar-prev]");
  const next = container.querySelector("[data-calendar-next]");
  const formContainer = calendarRoot.closest(".booking-box") || document;
  const startInput = formContainer.querySelector("input[name='start_date']");
  const endInput = formContainer.querySelector("input[name='end_date']");
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
      let shortLabel = "Libre";
      let ariaLabel = `${iso} - disponible au prix standard`;

      if (isPast) {
        classes.push("past");
        label = "Date passée";
        shortLabel = "Passé";
        ariaLabel = `${iso} - date passée`;
      } else if (isBooked) {
        classes.push("booked");
        label = "Réservé";
        shortLabel = "Réservé";
        ariaLabel = `${iso} - déjà réservé`;
      } else if (unavailable) {
        classes.push("unavailable");
        label = "Indisponible";
        shortLabel = "Occupé";
        ariaLabel = `${iso} - indisponible`;
      } else {
        classes.push("available");
        label = "Disponible";
        shortLabel = "Libre";
        ariaLabel = `${iso} - disponible`;
      }
      if (hasOverride) {
        classes.push("override");
        label = `${Number(availability.price_override).toLocaleString("fr-FR", { style: "currency", currency: "EUR" })}`;
        shortLabel = `${Number(availability.price_override).toLocaleString("fr-FR", { maximumFractionDigits: 0 })}€`;
        ariaLabel = `${iso} - disponible avec prix spécifique ${label}`;
      }

      cells.push(`<button type="button" class="${classes.join(" ")}" data-calendar-date="${iso}" ${isPast || isBooked || unavailable ? "disabled" : ""} aria-label="${ariaLabel}" title="${ariaLabel}"><span>${day}</span><small>${shortLabel}</small></button>`);
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
    const isBookingCalendar = Boolean(calendarRoot.closest(".booking-box"));
    if (startInput) startInput.value = date;
    if (endInput) {
      if (isBookingCalendar) {
        const nextDate = new Date(`${date}T00:00:00`);
        nextDate.setDate(nextDate.getDate() + 1);
        endInput.value = isoDate(nextDate);
      } else {
        endInput.value = date;
      }
    }
    startInput?.focus();
  });
  renderCalendar();
});
