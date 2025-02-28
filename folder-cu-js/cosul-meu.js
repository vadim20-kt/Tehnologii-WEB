// Funcție pentru încărcarea produselor din coș
function incarcaCos() {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    let total = 0;

    // Golește conținutul anterior
    cartItems.innerHTML = '';

    // Adaugă fiecare produs în coș
    cos.forEach((item, index) => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <div>
                <h3>${item.nume}</h3>
                <p>Preț unitar: ${item.pret} Lei</p>
                <p>Cantitate: ${item.cantitate}</p>
                <p>Total: ${item.pret * item.cantitate} Lei</p>
            </div>
            <button onclick="stergeDinCos(${index})">Șterge</button>
        `;
        cartItems.appendChild(div);
        total += item.pret * item.cantitate;
    });

    // Actualizează totalul
    cartTotal.textContent = total.toFixed(2); 

    // Actualizează numărul de produse din coș (badge)
    const badge = document.querySelector('.nav-icon .badge');
    if (badge) {
        badge.textContent = cos.length;
    }
}

// Funcție pentru ștergerea unui produs din coș
function stergeDinCos(index) {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    cos.splice(index, 1); 
    localStorage.setItem('cos', JSON.stringify(cos)); 
    incarcaCos(); 
}

// Funcție pentru finalizarea comenzii
function finalizeazaComanda() {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    if (cos.length === 0) {
        alert('Coșul tău este gol!');
    } else {
        alert('Comanda a fost finalizată cu succes!');
        localStorage.removeItem('cos'); 
        incarcaCos(); 
    }
}

// Încarcă coșul la deschiderea paginii
document.addEventListener('DOMContentLoaded', incarcaCos);

// Funcție pentru meniul hamburger
document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});