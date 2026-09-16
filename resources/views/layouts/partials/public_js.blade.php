  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggle = document.getElementById('mob-toggle');
      const drawer = document.getElementById('mob-drawer');
      const backdrop = document.getElementById('mob-backdrop');
      
      function toggleMenu() {
        toggle.classList.toggle('active');
        drawer.classList.toggle('open');
        backdrop.classList.toggle('open');
        
        if (drawer.classList.contains('open')) {
          document.body.style.overflow = 'hidden';
        } else {
          document.body.style.overflow = '';
        }
      }
      
      if (toggle && drawer && backdrop) {
        toggle.addEventListener('click', toggleMenu);
        backdrop.addEventListener('click', toggleMenu);
        
        // Close menu if links are clicked (useful for same page anchors)
        drawer.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', function() {
            if (drawer.classList.contains('open')) {
              toggleMenu();
            }
          });
        });
      }

      // Dynamic & Clickable Slider dots navigation sync
      function setupSliderDots(sliderId, dotsId) {
        const slider = document.getElementById(sliderId);
        const dotsContainer = document.getElementById(dotsId);
        if (slider && dotsContainer) {
          const cards = Array.from(slider.children).filter(el => el.classList.contains('plan-card') || el.classList.contains('feat-card'));
          if (cards.length > 0) {
            dotsContainer.innerHTML = '';
            cards.forEach((card, i) => {
              const dot = document.createElement('span');
              dot.className = 'dot' + (i === 0 ? ' active' : '');
              dot.style.cursor = 'pointer';
              dot.setAttribute('title', 'Slide to item ' + (i + 1));
              dot.addEventListener('click', function(e) {
                e.preventDefault();
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
              });
              dotsContainer.appendChild(dot);
            });

            const dots = dotsContainer.querySelectorAll('.dot');
            
            let isScrolling;
            slider.addEventListener('scroll', function() {
              window.clearTimeout(isScrolling);
              isScrolling = setTimeout(function() {
                const sliderCenter = slider.scrollLeft + (slider.offsetWidth / 2);
                let closestIndex = 0;
                let minDistance = Infinity;

                cards.forEach((card, idx) => {
                  const cardCenter = card.offsetLeft - slider.offsetLeft + (card.offsetWidth / 2);
                  const distance = Math.abs(sliderCenter - cardCenter);
                  if (distance < minDistance) {
                    minDistance = distance;
                    closestIndex = idx;
                  }
                });

                dots.forEach((dot, idx) => {
                  if (idx === closestIndex) {
                    dot.classList.add('active');
                  } else {
                    dot.classList.remove('active');
                  }
                });
              }, 40);
            }, { passive: true });
          }
        }
      }
      
      function initAllSliders() {
        setupSliderDots('plans-slider', 'plans-dots');
        setupSliderDots('plans-business-slider', 'plans-business-dots');
        setupSliderDots('plans-restaurant-slider', 'plans-restaurant-dots');
        setupSliderDots('plans-addon-slider', 'plans-addon-dots');
        setupSliderDots('reviews-slider', 'reviews-dots');
      }

      window.reinitSliders = initAllSliders;
      initAllSliders();
    });
  </script>
