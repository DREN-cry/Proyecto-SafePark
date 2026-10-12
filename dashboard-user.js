// dashboard-user.js
document.addEventListener("DOMContentLoaded", () => {
  // PROFILE DROPDOWN
  const toggle = document.getElementById("profileToggle");
  const menu = document.getElementById("profileMenu");

  if (toggle && menu) {
    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      const open = menu.style.display === "block";
      menu.style.display = open ? "none" : "block";
      toggle.setAttribute("aria-expanded", !open);
    });

    document.addEventListener("click", (e) => {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = "none";
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  }

  // Optional: allow opening sidebar on mobile by tapping brand icon
  const sidebar = document.getElementById("sidebar");
  const brandIcon = document.querySelector(".brand-icon");
  if (brandIcon && sidebar) {
    brandIcon.addEventListener("click", () => {
      if (window.innerWidth <= 520) {
        sidebar.classList.toggle("open");
      }
    });
  }

  // Prevent horizontal scroll when resizing (fix for certain browsers)
  window.addEventListener("resize", () => {
    document.documentElement.style.overflowX = "hidden";
  });
});

