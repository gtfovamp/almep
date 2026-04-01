/* ═══════════════════════════════════════
   Almep Trading — script.js
═══════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  /* ─── Переключатель языка ─── */
  const langSelector = document.querySelector('.lang-selector');
  if (langSelector) {
    langSelector.addEventListener('click', () => {
      // Здесь можно реализовать логику смены языка
      alert('Переключатель языка — в разработке');
    });
  }

  /* ─── Кнопка поиска ─── */
  const searchBtn = document.querySelector('.search-btn');
  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      // Здесь можно открыть модальное окно поиска
      alert('Поиск — в разработке');
    });
  }

  /* ─── Плавное переключение дропдауна «О компании» ─── */
  const dropdown = document.querySelector('.nav-dropdown');
  if (dropdown) {
    dropdown.addEventListener('click', (e) => {
      e.preventDefault();
      const arrow = dropdown.querySelector('.arrow');
      if (arrow) {
        const isOpen = arrow.style.transform === 'rotate(180deg)';
        arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
      }
    });
  }

  /* ─── Активный пункт навигации при клике ─── */
  const navItems = document.querySelectorAll('.nav li');
  navItems.forEach(item => {
    item.addEventListener('click', () => {
      navItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });

});