document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const cards = document.querySelectorAll(".user-card");
    const countUsers = document.getElementById("countUsers");

    function filterUsers() {
        const value = searchInput.value.toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const text = card.innerText.toLowerCase(); // Busca en todo el contenido visible

            if (text.includes(value)) {
                card.style.display = "block"; // Mostrar
                visibleCount++;
            } else {
                card.style.display = "none"; // Ocultar
            }
        });

        countUsers.textContent = `Resultados: ${visibleCount}`;
    }

    searchInput.addEventListener("input", filterUsers);
    filterUsers(); // Mostrar total al cargar
});

