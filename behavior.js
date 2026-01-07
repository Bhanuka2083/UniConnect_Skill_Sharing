window.addEventListener('scroll', () => {
    const navbar = document.getElementById('mainNavbar');
    if (window.scrollY > 60) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    });



function toggleMenu() {
    const navLinks = document.querySelector('.profession');
    const navBtn = document.querySelector('.btn');
    // Toggle a 'show' class in CSS or simply change display
    navLinks.style.display = (navLinks.style.display === 'flex') ? 'none' : 'flex';
    navBtn.style.display = (navBtn.style.display === 'flex') ? 'none' : 'flex';
}


// const navLinks = document.querySelectorAll('mainNavbar .profession a');
// const currentPath = window.location.pathname;

// navLinks.forEach(link => {
//   // Check if the link's href matches the current path
//   if (link.getAttribute('href') === currentPath || 
//       (currentPath === '/' && link.getAttribute('href') === 'home.html')) {
//     link.classList.add('active');
//   }
// });