document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menuBtn');
    const nav = document.getElementById('nav');
    const yearSpan = document.getElementById('year');
// ============================================
// EFECTO DE MÁQUINA DE ESCRIBIR (TYPING EFFECT)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Textos que se van a escribir (cámbialos como quieras)
    const textos = [
        "Más de 150 Cursos STPS",
        "Seguridad Industrial",
        "Capacitación de Calidad",
        "Certificación Oficial"
    ];
    
    let indiceTexto = 0;
    let indiceCaracter = 0;
    const elemento = document.querySelector('.typing-title');
    
    function escribirTexto() {
        if (indiceCaracter < textos[indiceTexto].length) {
            elemento.textContent += textos[indiceTexto].charAt(indiceCaracter);
            indiceCaracter++;
            setTimeout(escribirTexto, 100);
        } else {
            setTimeout(borrarTexto, 2000);
        }
    }
    
    function borrarTexto() {
        if (indiceCaracter > 0) {
            elemento.textContent = textos[indiceTexto].substring(0, indiceCaracter - 1);
            indiceCaracter--;
            setTimeout(borrarTexto, 50);
        } else {
            indiceTexto = (indiceTexto + 1) % textos.length;
            setTimeout(escribirTexto, 500);
        }
    }
    
    // Iniciar el efecto
    escribirTexto();
    
});
    // Año dinámico
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // Menú móvil
    if (menuBtn && nav) {
        menuBtn.addEventListener('click', () => {
            nav.classList.toggle('.open');
        });
        // Agrega esto a tu script.js
// Función para cerrar menú móvil (si existe modal, asegurar que no cause error)
if (document.getElementById('contactModal')) {
    function openModal() {
        document.getElementById('contactModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('contactModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('contactModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (document.getElementById('contactModal').style.display === 'block') {
                closeModal();
            }
        }
    });
}
        // Cerrar al hacer clic en enlace (móvil)
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    nav.classList.remove('open');
                }
            });
        });
    }
});
