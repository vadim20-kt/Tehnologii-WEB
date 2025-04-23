 {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    const totalProduse = cos.reduce((total, produs) => total + produs.cantitate, 0);

    // Actualizăm toate badge-urile din pagină
    document.querySelectorAll('.badge-cos').forEach(badge => {
        badge.textContent = totalProduse;
        badge.style.display = totalProduse > 0 ? 'inline-block' : 'none';
    });
}

function adaugaInCos(nume, pret, marimeId, cantitateId) {
    const marimeElement = document.getElementById(marimeId);
    const cantitateElement = document.getElementById(cantitateId);

    // Validări
    if (!marimeElement || !cantitateElement) {
        alert("Eroare: Elementele nu au fost găsite.");
        return false;
    }

    const marime = marimeElement.value;
    const cantitate = parseInt(cantitateElement.value);

    if (isNaN(cantitate) || cantitate < 1) {
        alert("Cantitatea trebuie să fie un număr valid și mai mare decât 0.");
        return false;
    }

    const pretNumar = parseFloat(pret);
    if (isNaN(pretNumar)) {
        alert("Prețul nu este valid.");
        return false;
    }

    const produs = {
        nume: nume,
        pret: pretNumar,
        marime: marime,
        cantitate: cantitate,
        dataAdaugare: new Date().toISOString()
    };

    let cos = JSON.parse(localStorage.getItem('cos')) || [];
    const produsExistent = cos.find(p => p.nume === nume && p.marime === marime);

    if (produsExistent) {
        produsExistent.cantitate += cantitate;
    } else {
        cos.push(produs);
    }

    localStorage.setItem('cos', JSON.stringify(cos));
    actualizeazaNumarProduseCos();

    return true;
}

function initMenuHamburger() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('open');
        });
    }
}

function initUserDropdown() {
    const userDropdown = document.querySelector('.user-dropdown');

    if (userDropdown) {
        const userBtn = userDropdown.querySelector('.user-btn');
        const dropdownContent = userDropdown.querySelector('.dropdown-content');

        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownContent.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            dropdownContent.classList.remove('show');
        });
    }
}

function initLogout() {
    const logoutBtn = document.getElementById('logout-btn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initMenuHamburger();
    initUserDropdown();
    initLogout();
    actualizeazaNumarProduseCos();

    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 100);
});