// ConsumerConnect RSA — Admin JS
document.addEventListener('DOMContentLoaded', () => {

  // Global search focus shortcut
  const search = document.getElementById('globalSearch');
  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      search?.focus();
    }
  });

  // Confirm before delete actions
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss alerts after 4s
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.style.display = 'none', 4000);
  });

  // Mobile sidebar toggle
  const toggleBtn = document.getElementById('sidebarToggle');
  const sidebar   = document.querySelector('.sidebar');
  toggleBtn?.addEventListener('click', () => sidebar?.classList.toggle('open'));
});
