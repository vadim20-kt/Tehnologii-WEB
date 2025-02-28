// Funcție pentru actualizarea badge-ului coșului
function actualizeazaBadgeCos() {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    const badge = document.querySelector('.nav-icon .badge'); 

    if (badge) {
        badge.textContent = cos.length; 
        badge.classList.add('badge-update');
        setTimeout(() => badge.classList.remove('badge-update'), 500); 
    }
}

// Funcție pentru afișarea produselor din categoria selectată
function afiseazaProduse(categorie) {
    document.querySelectorAll('.product-grid').forEach(grid => {
        grid.style.display = 'none';
    });
    document.getElementById(categorie).style.display = 'flex';
}

// Funcție pentru adăugarea în coș
function adaugaInCos(nume, pret, marimeId, cantitateId) {
    const marime = document.getElementById(marimeId).value;
    const cantitate = document.getElementById(cantitateId).value;

    if (isNaN(cantitate) || cantitate < 1) {
        alert("Cantitatea trebuie să fie un număr valid și mai mare decât 0.");
        return;
    }

    const produs = {
        nume: nume,
        pret: parseFloat(pret),
        marime: marime,
        cantitate: parseInt(cantitate)
    };

    let cos = JSON.parse(localStorage.getItem('cos')) || [];
    cos.push(produs);
    localStorage.setItem('cos', JSON.stringify(cos));

    actualizeazaBadgeCos();

    // Efect de zoom la adăugarea în coș
    const button = document.querySelector(`#${cantitateId}`).closest('.product').querySelector('button');
    button.classList.add('zoom-effect');
    setTimeout(() => button.classList.remove('zoom-effect'), 300);

    alert(`Produsul \"${nume}\" (Mărime: ${marime}, Cantitate: ${cantitate}) a fost adăugat în coș pentru ${produs.pret * produs.cantitate} Lei.`);
}

// La încărcarea paginii
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.product-grid').forEach(grid => {
        grid.style.display = 'none';
    });
    actualizeazaBadgeCos();
});

// Funcție pentru meniul hamburger
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

menuToggle.addEventListener('click', function() {
    navLinks.classList.toggle('active');
    menuToggle.classList.toggle('rotate-icon'); 
});
