document.addEventListener('DOMContentLoaded', function() {
    const addAddressBtn = document.getElementById('addAddressBtn');
    const addressModal = document.getElementById('addressModal');
    const closeBtn = document.querySelector('.close-btn');
    const addressForm = document.getElementById('addressForm');
    const modalTitle = addressModal ? addressModal.querySelector('h3') : null;
    const addressesContainer = document.querySelector('.addresses-container');

    const personalInfoForm = document.getElementById('personalInfoForm');
    const passwordChangeForm = document.getElementById('passwordChangeForm');
    const preferencesForm = document.getElementById('preferencesForm');

    const personalInfoErrorDiv = document.getElementById('personalInfoError');
    const passwordChangeErrorDiv = document.getElementById('passwordChangeError');
    const preferencesErrorDiv = document.getElementById('preferencesError');
    const addressErrorDiv = document.getElementById('addressError');

    window.isPasswordChanged = false;

    const originalFetch = window.fetch;
    window.fetch = async function(url, options) {
        console.log('Fetch request to:', url);
        if (window.isPasswordChanged && url.indexOf('change_password.php') === -1) {
            console.log('Cerere blocată după schimbarea parolei:', url);
            throw new Error('Cerere blocată: parola a fost schimbată.');
        }
        return originalFetch(url, options);
    };

    const originalXMLHttpRequestOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        console.log('XMLHttpRequest to:', url);
        if (window.isPasswordChanged && url.indexOf('change_password.php') === -1) {
            console.log('Cerere blocată după schimbarea parolei:', url);
            throw new Error('Cerere blocată: parola a fost schimbată.');
        }
        return originalXMLHttpRequestOpen.apply(this, arguments);
    };

    function updateCsrfToken(newCsrfToken) {
        const csrfInputs = document.querySelectorAll('input[name="csrf_token"]');
        csrfInputs.forEach(input => {
            input.value = newCsrfToken;
            console.log('CSRF token updated to:', input.value);
        });
    }

    function displayError(errorDiv, message) {
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        } else {
            console.error('Error div not found:', message);
            alert('A apărut o eroare: ' + message);
        }
    }

    function hideError(errorDiv) {
        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        const input = toggle.closest('.password-wrapper').querySelector('input');
        input.setAttribute('type', 'password');

        toggle.addEventListener('click', function() {
            const input = this.closest('.password-wrapper').querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    addAddressBtn?.addEventListener('click', function() {
        if (addressForm) {
            addressForm.reset();
            const addressIdInput = document.getElementById('address_id');
            if (addressIdInput) addressIdInput.value = '';
            if (modalTitle) modalTitle.textContent = 'Adaugă o nouă adresă';
            hideError(addressErrorDiv);
        }
        if (addressModal) addressModal.style.display = 'block';
    });

    closeBtn?.addEventListener('click', function() {
        if (addressModal) addressModal.style.display = 'none';
        hideError(addressErrorDiv);
    });

    window.addEventListener('click', function(event) {
        if (event.target === addressModal && addressModal) {
            addressModal.style.display = 'none';
            hideError(addressErrorDiv);
        }
    });

    addressesContainer?.addEventListener('click', function(e) {
        const target = e.target;

        if (target.classList.contains('edit-btn')) {
            const addressId = target.getAttribute('data-address-id');
            const addressCard = target.closest('.address-card');

            if (!addressCard) return;

            const addressIdInput = document.getElementById('address_id');
            if (addressIdInput) addressIdInput.value = addressId;

            const addressNameInput = addressForm ? addressForm.querySelector('input[name="address_name"]') : null;
            const addressName = addressCard.querySelector('h4');
            if (addressNameInput && addressName) addressNameInput.value = addressName.textContent.trim();

            const streetInput = addressForm ? addressForm.querySelector('input[name="street"]') : null;
            const streetParagraph = addressCard.querySelectorAll('p')[0];
            if (streetInput && streetParagraph) streetInput.value = streetParagraph.textContent.trim();

            const cityPostalParagraph = addressCard.querySelectorAll('p')[1];
            if (cityPostalParagraph) {
                const cityPostal = cityPostalParagraph.textContent.split(', ');
                const cityInput = addressForm ? addressForm.querySelector('input[name="city"]') : null;
                const postalInput = addressForm ? addressForm.querySelector('input[name="postal_code"]') : null;

                if (cityInput && cityPostal[0]) cityInput.value = cityPostal[0].trim();
                if (postalInput && cityPostal[1]) postalInput.value = cityPostal[1].trim();
            }

            const countryInput = addressForm ? addressForm.querySelector('input[name="country"]') : null;
            const countryParagraph = addressCard.querySelectorAll('p')[2];
            if (countryInput && countryParagraph) countryInput.value = countryParagraph.textContent.trim();

            if (modalTitle) modalTitle.textContent = 'Modifică adresa';
            hideError(addressErrorDiv);
            if (addressModal) addressModal.style.display = 'block';
        }

        if (target.classList.contains('set-default-btn')) {
            const addressId = target.getAttribute('data-address-id');
            setDefaultAddress(addressId);
        }

        if (target.classList.contains('delete-btn')) {
            const addressId = target.getAttribute('data-address-id');
            if (confirm('Sigur doriți să ștergeți această adresă?')) {
                deleteAddress(addressId);
            }
        }
    });

    addressForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError(addressErrorDiv);
        if (validateAddressForm()) {
            submitAddressForm();
        }
    });

    personalInfoForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError(personalInfoErrorDiv);
        if (validatePersonalInfoForm()) {
            submitPersonalInfoForm();
        }
    });

    passwordChangeForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError(passwordChangeErrorDiv);
        if (validatePasswordChangeForm()) {
            const submitButton = passwordChangeForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Se procesează...';
            }
            submitPasswordChangeForm(submitButton);
        }
    });

    preferencesForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        hideError(preferencesErrorDiv);
        submitPreferencesForm();
    });

    function validateAddressForm() {
        if (!addressForm) return false;

        const requiredFields = addressForm.querySelectorAll('[required]');
        let isValid = true;
        const errorMessages = [];

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('input-error');
                if (!field.previousElementSibling || field.previousElementSibling.tagName !== 'LABEL') {
                    errorMessages.push(`${field.name} este obligatoriu.`);
                } else {
                    errorMessages.push(`${field.previousElementSibling.textContent.trim()} este obligatoriu.`);
                }
            } else {
                field.classList.remove('input-error');
            }
        });

        if (!isValid) {
            displayError(addressErrorDiv, 'Te rugăm să completezi toate câmpurile obligatorii: ' + errorMessages.join(', '));
        }

        return isValid;
    }

    function validatePersonalInfoForm() {
        if (!personalInfoForm) return false;
        let isValid = true;
        hideError(personalInfoErrorDiv);

        const emailInput = personalInfoForm.querySelector('input[name="email"]');
        if (emailInput) emailInput.classList.remove('input-error');
        const nameInput = personalInfoForm.querySelector('input[name="full_name"]');
        if (nameInput) nameInput.classList.remove('input-error');


        if (emailInput && !validateEmail(emailInput.value)) {
            displayError(personalInfoErrorDiv, 'Te rugăm să introduci un email valid!');
            emailInput.focus();
            emailInput.classList.add('input-error');
            isValid = false;
        }

        if (nameInput && !nameInput.value.trim()) {
            displayError(personalInfoErrorDiv, 'Te rugăm să introduci un nume!');
            nameInput.focus();
            nameInput.classList.add('input-error');
            isValid = false;
        }

        if (isValid) {
            hideError(personalInfoErrorDiv);
        }

        return isValid;
    }

    function validatePasswordChangeForm() {
        if (!passwordChangeForm) return false;
        let isValid = true;
        hideError(passwordChangeErrorDiv);

        const currentPasswordInput = passwordChangeForm.querySelector('input[name="current_password"]');
        const newPasswordInput = passwordChangeForm.querySelector('input[name="new_password"]');
        const confirmPasswordInput = passwordChangeForm.querySelector('input[name="confirm_password"]');

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&^#_-])[A-Za-z\d@$!%*?&^#_-]{8,}$/;

        [currentPasswordInput, newPasswordInput, confirmPasswordInput].forEach(input => {
            if (input) input.classList.remove('input-error');
        });

        if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput) {
            displayError(passwordChangeErrorDiv, 'Câmpurile pentru parolă nu au fost găsite!');
            return false;
        }

        if (!currentPasswordInput.value.trim()) {
            displayError(passwordChangeErrorDiv, 'Introdu parola curentă!');
            currentPasswordInput.classList.add('input-error');
            currentPasswordInput.focus();
            isValid = false;
        }

        if (!newPasswordInput.value.trim()) {
            displayError(passwordChangeErrorDiv, 'Introdu parola nouă!');
            newPasswordInput.classList.add('input-error');
            newPasswordInput.focus();
            isValid = false;
        } else if (!passwordRegex.test(newPasswordInput.value)) {
            displayError(passwordChangeErrorDiv, 'Noua parolă trebuie să aibă minim 8 caractere, incluzând o literă mare, o literă mică, un număr și un caracter special (@, $, !, %, *, ?, &, ^, #, _, -).');
            newPasswordInput.classList.add('input-error');
            newPasswordInput.focus();
            isValid = false;
        }

        if (!confirmPasswordInput.value.trim()) {
            displayError(passwordChangeErrorDiv, 'Confirmă parola nouă!');
            confirmPasswordInput.classList.add('input-error');
            confirmPasswordInput.focus();
            isValid = false;
        } else if (newPasswordInput.value !== confirmPasswordInput.value) {
            displayError(passwordChangeErrorDiv, 'Parolele noi nu coincid!');
            confirmPasswordInput.classList.add('input-error');
            confirmPasswordInput.focus();
            isValid = false;
        }

        if (isValid) {
            hideError(passwordChangeErrorDiv);
        }

        return isValid;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    async function submitAddressForm() {
        if (!addressForm) {
            displayError(addressErrorDiv, 'Formularul de adresă nu a fost găsit!');
            return;
        }

        const addressIdInput = document.getElementById('address_id');
        const addressNameInput = addressForm.querySelector('input[name="address_name"]');
        const streetInput = addressForm.querySelector('input[name="street"]');
        const cityInput = addressForm.querySelector('input[name="city"]');
        const postalInput = addressForm.querySelector('input[name="postal_code"]');
        const countryInput = addressForm.querySelector('input[name="country"]');
        const csrfTokenInput = addressForm.querySelector('input[name="csrf_token"]');

        if (!addressNameInput || !streetInput || !cityInput || !csrfTokenInput) {
            console.error('Required address fields not found in DOM.');
            displayError(addressErrorDiv, 'An internal error occurred. Some required form fields are missing.');
            return;
        }

        const formData = {
            id: addressIdInput ? addressIdInput.value : null,
            address_name: addressNameInput.value.trim(),
            street: streetInput.value.trim(),
            city: cityInput.value.trim(),
            postal_code: postalInput ? postalInput.value.trim() : null,
            country: countryInput ? countryInput.value.trim() : null,
            csrf_token: csrfTokenInput.value
        };

        if (!formData.address_name || !formData.street || !formData.city || !formData.csrf_token) {
            displayError(addressErrorDiv, 'Please fill in all required fields (address name, street, city).');
            return;
        }

        try {
            const response = await fetch('save_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            console.log('Raw response from save_address.php:', await response.clone().text());

            let result;
            try {
                result = await response.json();
            } catch (jsonError) {
                console.error('Error parsing JSON response:', jsonError);
                displayError(addressErrorDiv, 'Invalid JSON response from server.');
                return;
            }

            if (!response.ok) {
                console.error('HTTP error:', {
                    status: response.status,
                    statusText: response.statusText,
                    response: result
                });
                displayError(addressErrorDiv, result.message || `HTTP Error: ${response.status} - ${response.statusText}`);
                return;
            }

            if (result.success) {
                alert(`Address was ${formData.id ? 'updated' : 'added'} successfully!`);
                if (addressModal) addressModal.style.display = 'none';
                window.location.reload();
            } else {
                displayError(addressErrorDiv, result.message || 'Unknown server error.');
            }
        } catch (error) {
            console.error('Error saving address:', error);
            if (error.message.includes('Cerere blocată')) {
                alert('Error: Password has been changed. Please reload the page or log in again.');
            } else {
                displayError(addressErrorDiv, 'Error communicating with the server: ' + error.message);
            }
        }
    }

    async function submitPersonalInfoForm() {
        if (!personalInfoForm) return;

        hideError(personalInfoErrorDiv);
        const formData = new FormData(personalInfoForm);

        try {
            const response = await fetch('save_personal_info.php', {
                method: 'POST',
                body: formData
            });

            console.log('Raw response from save_personal_info.php:', await response.clone().text());

            if (!response.ok) {
                const result = await response.json();
                displayError(personalInfoErrorDiv, result.message || `HTTP Error: ${response.status}`);
                return;
            }

            const result = await response.json();

            if (result.success) {
                alert('Personal information updated successfully!');
                // You might want to update the name displayed in the header here dynamically
                // window.location.reload(); // Reload if header name needs update
            } else {
                displayError(personalInfoErrorDiv, result.message || 'Could not update information');
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.message.includes('Cerere blocată')) {
                alert('Error: Password has been changed. Please reload the page or log in again.');
            } else {
                displayError(personalInfoErrorDiv, 'Error communicating with the server: ' + error.message);
            }
        }
    }

    async function submitPasswordChangeForm(submitButton) {
        if (!passwordChangeForm) return;

        hideError(passwordChangeErrorDiv);
        const formData = new FormData(passwordChangeForm);
        console.log("Submitting password change with CSRF token:", formData.get('csrf_token'));

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
        }

        try {
            const response = await fetch('change_password.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            console.log('Raw response from change_password.php:', await response.clone().text());

            if (!response.ok) {
                const result = await response.json();
                displayError(passwordChangeErrorDiv, result.message || `HTTP Error: ${response.status}`);
                return;
            }

            const result = await response.json();

            if (result.success) {
                alert('Password changed successfully!');
                if (result.new_csrf_token) {
                    updateCsrfToken(result.new_csrf_token);
                }
                passwordChangeForm.reset();
                hideError(passwordChangeErrorDiv);

                // Removed window.isPasswordChanged = true; to allow continued use
                // Removed alert/redirect suggestion

            } else {
                displayError(passwordChangeErrorDiv, result.message || 'Could not change password');
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.message.includes('Cerere blocată')) {
                alert('Error: Password change request was blocked.');
            } else {
                displayError(passwordChangeErrorDiv, 'Error communicating with the server: ' + error.message);
            }
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Change Password';
            }
        }
    }

    async function submitPreferencesForm() {
        if (!preferencesForm) return;

        hideError(preferencesErrorDiv);

        const csrfTokenInput = preferencesForm.querySelector('input[name="csrf_token"]');
        if (!csrfTokenInput) {
            displayError(preferencesErrorDiv, 'CSRF token is missing from preferences form!');
            return;
        }

        const formData = {
            newsletter: document.querySelector('#preferencesForm input[name="newsletter"]').checked,
            promo_notifications: document.querySelector('#preferencesForm input[name="promo_notifications"]').checked,
            sms_notifications: document.querySelector('#preferencesForm input[name="sms_notifications"]').checked,
            csrf_token: csrfTokenInput.value
        };

        try {
            const response = await fetch('save_preferences.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            console.log('Raw response from save_preferences.php:', await response.clone().text());

            if (!response.ok) {
                const result = await response.json();
                displayError(preferencesErrorDiv, result.message || `HTTP Error: ${response.status}`);
                return;
            }

            const result = await response.json();

            if (result.success) {
                alert('Preferences saved successfully!');
                hideError(preferencesErrorDiv);
            } else {
                displayError(preferencesErrorDiv, result.message || 'Could not save preferences');
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.message.includes('Cerere blocată')) {
                alert('Error: Password has been changed. Please reload the page or log in again.');
            } else {
                displayError(preferencesErrorDiv, 'Error communicating with the server: ' + error.message);
            }
        }
    }

    async function setDefaultAddress(addressId) {
        const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
        if (!csrfTokenInput) {
            alert('CSRF token is missing!');
            return;
        }

        const formData = {
            address_id: addressId,
            csrf_token: csrfTokenInput.value
        };

        try {
            const response = await fetch('set_default_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            console.log('Raw response from set_default_address.php:', await response.clone().text());

            if (!response.ok) {
                const result = await response.json();
                alert(`Error setting default address: ${result.message || `HTTP Error: ${response.status}`}`);
                return;
            }

            const result = await response.json();

            if (result.success) {
                alert('Default address updated successfully!');
                window.location.reload();
            } else {
                alert(`Error setting default address: ${result.message || 'Could not set default address'}`);
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.message.includes('Cerere blocată')) {
                alert('Error: Password has been changed. Please reload the page or log in again.');
            } else {
                alert('Error communicating with the server to set default address: ' + error.message);
            }
        }
    }

    async function deleteAddress(addressId) {
        if (confirm('Are you sure you want to delete this address?')) {
            const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
            if (!csrfTokenInput) {
                alert('CSRF token is missing!');
                return;
            }

            const formData = {
                address_id: addressId,
                csrf_token: csrfTokenInput.value
            };

            try {
                const response = await fetch('delete_address.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                console.log('Raw response from delete_address.php:', await response.clone().text());

                if (!response.ok) {
                    const result = await response.json();
                    alert(`Error deleting address: ${result.message || `HTTP Error: ${response.status}`}`);
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    alert('Address deleted successfully!');
                    window.location.reload();
                } else {
                    alert(`Error deleting address: ${result.message || 'Could not delete address'}`);
                }
            } catch (error) {
                console.error('Error:', error);
                if (error.message.includes('Cerere blocată')) {
                    alert('Error: Password has been changed. Please reload the page or log in again.');
                } else {
                    alert('Error communicating with the server to delete address: ' + error.message);
                }
            }
        }
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

});