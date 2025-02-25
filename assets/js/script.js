document.addEventListener('DOMContentLoaded', () => {
  const burger = document.querySelector('.burger');
  const navMenu = document.querySelector('.nav-menu');

  if (!burger || !navMenu) {
    console.error('Erreur : Élément manquant (burger ou navMenu)');
    return;
  }

  let closeBtn = null;

  function handleMenuToggle() {
    navMenu.classList.toggle('open');
    burger.classList.toggle('active');

    if (window.innerWidth <= 768) {
      if (!closeBtn) {
        closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.classList.add('close-btn');
        navMenu.prepend(closeBtn);

        closeBtn.addEventListener('click', () => {
          navMenu.classList.remove('open');
          burger.classList.remove('active');
        });
      }
    }
  }

  burger.addEventListener('click', handleMenuToggle);

  navMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('open');
      burger.classList.remove('active');
    });
  });

  document.addEventListener('click', (event) => {
    if (window.innerWidth <= 768) {
      const target = event.target;
      if (target instanceof HTMLElement &&
          !navMenu.contains(target) &&
          !burger.contains(target)) {
        navMenu.classList.remove('open');
        burger.classList.remove('active');
      }
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && closeBtn) {
      closeBtn.remove();
      closeBtn = null;
    }
  });

  // Partie contact
  const contactForm = document.getElementById('contactForm');
  const notification = document.getElementById('notification');

  if (contactForm) {
    contactForm.onsubmit = function(event) {
      event.preventDefault(); // Empêche l'envoi normal du formulaire

      let formData = new FormData(contactForm); // Récupère les données du formulaire

      fetch('contact.php', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest' // ✅ Ajoute cet en-tête pour éviter le blocage
        },
        body: formData
    })
      .then(response => response.json())
      .then(data => {
          if (notification) {
              notification.style.display = 'block';
              notification.innerText = data.message; // Affiche le message

              if (data.status === 'success') {
                  contactForm.reset(); // Réinitialise le formulaire
              }
          }
      })
      .catch(error => {
          console.warn('Une erreur est survenue. Veuillez réessayer plus tard.');
      });
    };
  }
});


