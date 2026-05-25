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
