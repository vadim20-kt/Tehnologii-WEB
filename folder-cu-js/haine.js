let produseData = {};

function actualizeazaBadgeCos() {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    const badge = document.querySelector('.nav-icon .badge');

    if (badge) {
        badge.textContent = cos.length;
        badge.classList.add('badge-update');
        setTimeout(() => badge.classList.remove('badge-update'), 500);
    }
}

function genereazaOptiuniMarime(categoriePrincipala) {
    if (categoriePrincipala === 'incaltaminte') {
        return `
            <option value="36">Mărime 36</option>
            <option value="37">Mărime 37</option>
            <option value="38">Mărime 38</option>
            <option value="39">Mărime 39</option>
            <option value="40">Mărime 40</option>
            <option value="41">Mărime 41</option>
            <option value="42">Mărime 42</option>
            <option value="43">Mărime 43</option>
            <option value="44">Mărime 44</option>
            <option value="45">Mărime 45</option>
        `;
    } else {
        return `
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="2XL">2XL</option>
        `;
    }
}

function genereazaGrileProduse() {
    const container = document.getElementById('product-container');
    container.innerHTML = '';

    const categoriePrincipala = window.currentCategory;
    const subcategorii = produseData[categoriePrincipala];

    Object.keys(subcategorii).forEach(categorie => {
        const grid = document.createElement('div');
        grid.id = categorie;
        grid.className = 'product-grid';
        grid.style.display = categorie === Object.keys(subcategorii)[0] ? 'flex' : 'none'; // Afișează prima subcategorie

        subcategorii[categorie].forEach((produs, index) => {
            const productDiv = document.createElement('div');
            productDiv.className = 'product';
            productDiv.innerHTML = `
                <img src="${produs.imagine}" alt="${produs.nume}">
                <h3>${produs.nume}</h3>
                <p class="price">${produs.pret.toFixed(2).replace('.', ',')} ${categoriePrincipala === 'incaltaminte' ? 'MDL' : 'Lei'}</p>
                <select id="marime-${categorie}-${index + 1}">
                    ${genereazaOptiuniMarime(categoriePrincipala)}
                </select>
                <input type="number" id="cantitate-${categorie}-${index + 1}" min="1" value="1" placeholder="Cantitate">
                <button onclick="adaugaInCos('${produs.nume.replace(/'/g, "\\'")}', ${produs.pret}, 'marime-${categorie}-${index + 1}', 'cantitate-${categorie}-${index + 1}')">Adaugă în Coș</button>
            `;
            grid.appendChild(productDiv);
        });

        container.appendChild(grid);
    });
}

function afiseazaProduse(categorie) {
    document.querySelectorAll('.product-grid').forEach(grid => {
        grid.style.display = 'none';
    });

    const activeGrid = document.getElementById(categorie);
    activeGrid.style.display = 'flex';

    const searchInput = document.querySelector('#search-input');
    if (searchInput) {
        searchInput.value = '';
    }

    const searchResults = document.getElementById('search-results');
    const productContainer = document.getElementById('product-container');
    searchResults.style.display = 'none';
    productContainer.style.display = 'block';

    const noResultsMessage = document.querySelector('.no-results-message');
    if (noResultsMessage) {
        noResultsMessage.remove();
    }

    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
    });
    document.querySelector(`.category-card[data-category="${categorie}"]`).classList.add('active');
}

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

    const button = document.querySelector(`#${cantitateId}`).closest('.product').querySelector('button');
    button.classList.add('zoom-effect');
    setTimeout(() => button.classList.remove('zoom-effect'), 300);

    alert(`Produsul "${nume}" (Mărime: ${marime}, Cantitate: ${cantitate}) a fost adăugat în coș pentru ${produs.pret * produs.cantitate} ${window.currentCategory === 'incaltaminte' ? 'MDL' : 'Lei'}.`);
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function configureazaCautare() {
    const searchInput = $('#search-input');
    if (!searchInput.length) return;

    const cautaProduse = debounce(function(e) {
        const searchTerm = $(this).val().toLowerCase().trim();
        const searchResults = $('#search-results');
        const productContainer = $('#product-container');

        $('.no-results-message').remove();

        if (!searchTerm) {
            searchResults.hide();
            productContainer.show();
            return;
        }

        productContainer.hide();
        searchResults.show().empty();

        $.ajax({
            url: 'cauta_produse.php',
            method: 'GET',
            data: { term: searchTerm, categorie: window.currentCategory },
            dataType: 'json',
            success: function(data) {
                let totalProduseGasite = 0;
                const categoriePrincipala = window.currentCategory;

                let categoryDiv = null;
                let categoryTitle = null;

                $.each(data, function(subcategorie, produse) {
                    if (produse.length > 0) {
                        totalProduseGasite += produse.length;

                        if (!categoryDiv) {
                            categoryDiv = $('<div>').addClass('search-main-category');
                            categoryTitle = $('<h2>').text(categoriePrincipala.charAt(0).toUpperCase() + categoriePrincipala.slice(1));
                            categoryDiv.append(categoryTitle);
                        }

                        const subCategoryDiv = $('<div>').addClass('search-category');
                        const subCategoryTitle = $('<h3>').text(subcategorie.charAt(0).toUpperCase() + subcategorie.slice(1));
                        subCategoryDiv.append(subCategoryTitle);

                        const grid = $('<div>').addClass('product-grid').css('display', 'flex');

                        $.each(produse, function(index, produs) {
                            const productDiv = $('<div>').addClass('product').html(`
                                <img src="${produs.imagine}" alt="${produs.nume}">
                                <h3>${produs.nume}</h3>
                                <p class="price">${produs.pret.toFixed(2).replace('.', ',')} ${categoriePrincipala === 'incaltaminte' ? 'MDL' : 'Lei'}</p>
                                <select id="marime-${subcategorie}-${index + 1}">
                                    ${genereazaOptiuniMarime(categoriePrincipala)}
                                </select>
                                <input type="number" id="cantitate-${subcategorie}-${index + 1}" min="1" value="1" placeholder="Cantitate">
                                <button onclick="adaugaInCos('${produs.nume.replace(/'/g, "\\'")}', ${produs.pret}, 'marime-${subcategorie}-${index + 1}', 'cantitate-${subcategorie}-${index + 1}')">Adaugă în Coș</button>
                            `);
                            grid.append(productDiv);
                        });

                        subCategoryDiv.append(grid);
                        categoryDiv.append(subCategoryDiv);
                    }
                });

                if (categoryDiv) {
                    searchResults.append(categoryDiv);
                }

                if (totalProduseGasite === 0) {
                    searchResults.append($('<p>').addClass('no-results-message').text('Niciun produs găsit.'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Eroare la căutare:', error);
                searchResults.append($('<p>').addClass('no-results-message').text('Eroare la căutarea produselor. Vă rugăm să reîncercați.'));
            }
        });
    }, 300);

    searchInput.on('input', cautaProduse);
}

function configureazaToggleCautare() {
    const searchBtn = document.querySelector('.search-btn');
    if (!searchBtn) return;

    searchBtn.addEventListener('click', function(e) {
        const searchContent = document.querySelector('.search-content');
        const searchInput = document.querySelector('#search-input');
        searchContent.classList.toggle('active');
        document.body.classList.toggle('search-active');

        if (searchContent.classList.contains('active') && searchInput) {
            searchInput.focus();
        }
    });

    document.addEventListener('click', function(e) {
        const searchContainer = document.querySelector('.search-container');
        const searchContent = document.querySelector('.search-content');
        const searchBtn = document.querySelector('.search-btn');

        if (!searchContainer.contains(e.target) && !searchBtn.contains(e.target) && !searchContent.contains(e.target)) {
            searchContent.classList.remove('active');
            document.body.classList.remove('search-active');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const searchContent = document.querySelector('.search-content');
            searchContent.classList.remove('active');
            document.body.classList.remove('search-active');
        }
    });
}

function configureazaMeniuHamburger() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('rotate-icon');
        });
    }
}

$(document).ready(function() {
    $.ajax({
        url: 'produse.json',
        dataType: 'json',
        success: function(data) {
            produseData = data;

            genereazaGrileProduse();

            const primaCategorie = Object.keys(produseData[window.currentCategory])[0];
            if (primaCategorie) {
                afiseazaProduse(primaCategorie);
            }

            actualizeazaBadgeCos();

            configureazaMeniuHamburger();
            configureazaToggleCautare();
            configureazaCautare();
        },
        error: function(xhr, status, error) {
            console.error('Eroare:', error);
            const container = $('.container');
            container.append(
                $('<p>').addClass('no-results-message').text('Eroare la încărcarea produselor. Vă rugăm să reîncercați.')
            );
        }
    });
});