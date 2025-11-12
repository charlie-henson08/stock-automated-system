document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("itemForm");
  if (!form) return;

  form.addEventListener("submit", (e) => {
    const sku = form.sku.value.trim();
    const name = form.name.value.trim();
    const stock = parseInt(form.stock.value, 10);

    if (!sku || !name) {
      alert("SKU and Name are required!");
      e.preventDefault();
    }
    if (stock < 0) {
      alert("Stock cannot be negative!");
      e.preventDefault();
    }
  });
});
