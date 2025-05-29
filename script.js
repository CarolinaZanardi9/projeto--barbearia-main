const button = document.getElementById('toogle-mode');

button.addEventListener('click', () => {
  document.body.classList.toggle('black-white');
  const isBlack = document.body.classList.contains('black-white');

  // Troca o ícone do botão
  button.textContent = isBlack ? '☼' : '☾';

  // Salva no localStorage
  localStorage.setItem('mode', isBlack ? 'black' : 'color');
});

// Ao carregar a página, verifica o localStorage
if (localStorage.getItem('mode') === 'black') {
  document.body.classList.add('black-white');
  button.textContent = '☼';
}

// Mostra ou esconde o botão dependendo da rolagem
window.onscroll = function() {
  const btn = document.getElementById("btnTopo");
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    btn.style.display = "block";
  } else {
    btn.style.display = "none";
  }
}

function voltarAoTopo() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}