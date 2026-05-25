// ==модальное окно для подтверждения удаления==========================================
const btnsOpen = document.querySelectorAll(".deleteBtn"),
modal = document.querySelector(".modal-overlay"),
btnClose = document.querySelector(".btn-cancel"),
btnDelete = document.querySelector(".btn-danger");
let id = null;

btnsOpen.forEach(button => {
    button.addEventListener("click", (e) => {
        id = e.target.getAttribute('data-id');
modal.style.display = "flex";
});
});
btnClose.addEventListener("click", () => {
    id = null;
modal.style.display = "none";
});
modal.addEventListener("click", (event) => {
if (event.target === modal) {
    id = null;
    modal.style.display = "none";
}
});
document.addEventListener("keyup", (event) => {
    if (event.key === "Escape") {
        id = null;
        modal.style.display = "none";
    };
});
btnDelete.addEventListener("click", async (elem) => {
    id = elem.currentTarget.getAttribute('data-id');
    try {
        const response = await fetch(`?action=delete&id=${id}&ajax`);
        const result = await response.json();
        switch (result.status) {
            case 'success':
                const elementToRemove = document.getElementById(id);
                if (elementToRemove) elementToRemove.remove();
                break;
            case 'error':
                console.error('Ошибка: не могу удалить');
                break;
            default:
                console.error('Ошибка: не верный формат ответа');
            }
        } catch (error) {
            console.error('Ошибка:', error);
        } finally {
        modal.style.display = "none";
    }
});

