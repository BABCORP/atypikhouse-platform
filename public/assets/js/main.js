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
