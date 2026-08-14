// Logique dynamique de l'application
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Surbrillance dynamique du menu latéral
    const currentPath = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (currentPath === '' && href === 'index.php')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // 2. Recherche en temps réel dans les tableaux
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            const value = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    }

    // 3. Confirmation de suppression
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette suppression ?')) {
                e.preventDefault();
            }
        });
    });

    // 4. Animation des messages d'alerte
    const alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 4000);
    });
});
