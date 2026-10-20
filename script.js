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

// Scroll reveal da seção Sobre: cards entrando pela esquerda e pela direita
const itensSobre = document.querySelectorAll('.Sobre .card, .Sobre .bloco-equipe');

if ('IntersectionObserver' in window) {
  const observador = new IntersectionObserver((entradas) => {
    entradas.forEach((entrada) => {
      if (entrada.isIntersecting) {
        const el = entrada.target;
        el.classList.add('visivel');
        // depois da animação, tira as classes para o efeito de hover voltar ao normal
        setTimeout(() => el.classList.remove('reveal-esq', 'reveal-dir', 'visivel'), 1000);
        observador.unobserve(el);
      }
    });
  }, { threshold: 0.3 });

  itensSobre.forEach((el, i) => {
    el.classList.add(i % 2 === 0 ? 'reveal-esq' : 'reveal-dir');
    observador.observe(el);
  });
}