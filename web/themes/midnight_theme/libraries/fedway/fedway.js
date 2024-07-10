import "./js/jumpmenu.js";
import "./js/navigation.js";
import "./js/navigation-utils.js";
import "./js/second-level-navigation.js";
import "./css/fedway.css";

window.toggleDarkMode = function () {
  const currentTheme = localStorage.getItem('theme');
  if (document.documentElement.classList.contains('dark')) {
    document.documentElement.classList.remove(['dark']);
    localStorage.setItem('theme', 'light');
  } else {
    document.documentElement.classList.add(['dark']);
    localStorage.setItem('theme', 'dark');
  }
};
window.growText = function () {
  let size = localStorage.size ?? 3;
  if (size > 4) {
    size = 5;
  } else {
    size++;
  }
  localStorage.setItem('size', size);
  setSize();
};
window.shrinkText = function () {
  let size = localStorage.size ?? 3;
  if (size < 2) {
    size = 1;
  } else {
    size--;
  }
  localStorage.setItem('size', size);
  setSize();
};
