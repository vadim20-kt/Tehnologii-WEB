// Funcție pentru meniul hamburger
document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});

// Funcție pentru adăugarea în coș
function adaugaInCos(nume, pret, marimeId, cantitateId) {
    const marimeElement = document.getElementById(marimeId);
    const cantitateElement = document.getElementById(cantitateId);

    if (!marimeElement || !cantitateElement) {
        alert("Eroare: Elementele nu au fost găsite.");
        return;
    }

    const marime = marimeElement.value;
    const cantitate = parseInt(cantitateElement.value);

    if (isNaN(cantitate) || cantitate < 1) {
        alert("Cantitatea trebuie să fie un număr valid și mai mare decât 0.");
        return;
    }

    const pretNumar = parseFloat(pret);
    if (isNaN(pretNumar)) {
        alert("Prețul nu este valid.");
        return;
    }

    const produs = {
        nume: nume,
        pret: pretNumar,
        marime: marime,
        cantitate: cantitate
    };

    let cos = JSON.parse(localStorage.getItem('cos')) || [];
    cos.push(produs);
    localStorage.setItem('cos', JSON.stringify(cos));

    alert(`Produsul "${nume}" (Mărime: ${marime}, Cantitate: ${cantitate}) a fost adăugat în coș pentru ${(produs.pret * produs.cantitate).toFixed(2)} MDL.`);

    actualizeazaNumarProduseCos();
}

// Funcție pentru actualizarea numărului de produse din coș
function actualizeazaNumarProduseCos() {
    let cos = JSON.parse(localStorage.getItem('cos')) || [];
    let numarProduse = cos.length; // Numărul de produse distincte
    const badge = document.querySelector('.nav-icon .badge');
    if (badge) {
        badge.textContent = numarProduse;
    }
}

// Funcție pentru resetarea coșului
function reseteazaCos() {
    localStorage.removeItem('cos');
    actualizeazaNumarProduseCos();
    alert("Coșul a fost resetat.");
}

// La încărcarea paginii
document.addEventListener('DOMContentLoaded', () => {
    actualizeazaNumarProduseCos();
});