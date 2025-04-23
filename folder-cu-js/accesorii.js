let produseData = {};

function actualizeazaBadgeCos() {
    const cos = JSON.parse(localStorage.getItem('cos')) || [];
    const badge = document.querySelector('.nav-icon .badge');
    if (badge) {
        badge.textContent = cos.length;
        badge.classList.add('actualizare-badge');
        setTimeout(() => badge.classList.remove('actualizare-badge'), 500);
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
    const container = $('#product-container');
    container.empty();

    const categoriePrincipala = window.currentCategory;
    const subcategorii = produseData[categoriePrincipala];

    $.each(subcategorii, function(categorie, produse) {
        const grid = $('<div>').attr('id', categorie).addClass('product-grid')
            .css('display', categorie === Object.keys(subcategorii)[0] ? 'flex' : 'none');

        $.each(produse, function(index, produs) {
            const productDiv = $('<div>').addClass('product').html(`
                <img src="${produs.imagine}" alt="${produs.nume}">
                <h3>${produs.nume}</h3>
                <p class="price">${produs.pret.toFixed(2).replace('.', ',')} ${categoriePrincipala === 'incaltaminte' ? 'MDL' : 'Lei'}</p>
                <select id="marime-${categorie}-${index + 1}">
                    ${genereazaOptiuniMarime(categoriePrincipala)}
                </select>
                <input type="number" id="cantitate-${categorie}-${index + 1}" min="1" value="1" placeholder="Cantitate">
                <button onclick="adaugaInCos('${produs.nume.replace(/'/g, "\\'")}', ${produs.pret}, 'marime-${categorie}-${index + 1}', 'cantitate-${categorie}-${index + 1}')">Adaugă în Coș</button>
            `);

            grid.append(productDiv);
        });

        container.append(grid);
    });
}

function afiseazaProduse(categorie) {
    $('.product-grid').hide();
    $('#search-input').val('');

    $('#search-results').hide();
    $('#product-container').show();

    $('.no-results-message').remove();

    $(`#${categorie}`).show();

    $('.category-card').removeClass('active');
    $(`.category-card[data-category="${categorie}"]`).addClass('active');
}

function adaugaInCos(nume, pret, marimeId, cantitateId) {
    const marime = $(`#${marimeId}`).val();
    const cantitate = $(`#${cantitateId}`).val();

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

    const button = $(`#${cantitateId}`).closest('.product').find('button');
    button.addClass('efect-zoom');
    setTimeout(() => button.removeClass('efect-zoom'), 300);

    alert(`Produsul "${nume}" (Mărime: ${marime}, Cantitate: ${cantitate}) a fost adăugat în coș pentru ${produs.pret * produs.cantitate} ${window.currentCategory === 'incaltaminte' ? 'MDL' : 'Lei'}.`);
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
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

                        const subCategoryDiv = $('<div>').addClass('categorie-cautare');
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
    const searchBtn = $('.search-btn');
    const searchContent = $('.search-content');
    const searchInput = $('#search-input');

    if (!searchBtn.length) return;

    searchBtn.off('click').on('click', function(e) {
        e.stopPropagation();
        searchContent.toggleClass('active');
        $('body').toggleClass('search-active');

        if (searchContent.hasClass('active')) {
            searchInput.focus();
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length && !$(e.target).is('.search-btn')) {
            searchContent.removeClass('active');
            $('body').removeClass('search-active');
        }
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            searchContent.removeClass('active');
            $('body').removeClass('search-active');
        }
    });
}

function configureazaMeniuHamburger() {
    const menuToggle = $('.menu-toggle');
    const navLinks = $('.nav-links');

    if (menuToggle.length && navLinks.length) {
        menuToggle.on('click', function() {
            navLinks.toggleClass('active');
            menuToggle.toggleClass('rotate-icon');
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
            $('.container').append(
                $('<p>').addClass('mesaj-fara-rezultate')
                    .text('Eroare la încărcarea produselor. Vă rugăm să reîncercați.')
            );
        }
    });
});