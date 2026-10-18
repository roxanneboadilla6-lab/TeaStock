document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(message)) event.preventDefault();
    });
  });
  document.querySelectorAll('[data-filter-input]').forEach((input) => {
    input.addEventListener('input', () => {
      const table = document.querySelector(input.dataset.filterInput);
      if (!table) return;
      const query = input.value.toLowerCase();
      table.querySelectorAll('tbody tr').forEach(row => {
        row.hidden = !row.textContent.toLowerCase().includes(query);
      });
    });
  });
});
