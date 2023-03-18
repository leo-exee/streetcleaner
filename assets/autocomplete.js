const addressInput = document.getElementById('device_adress');
const addressList = document.getElementById('address-list');

addressInput.addEventListener('input', debounce(searchAddress, 500));

async function searchAddress() {

    addressList.classList.remove('selected');
    const query = addressInput.value;
    const url = `https://api-adresse.data.gouv.fr/search/?q=${query}&type=street`;

    try {
        const response = await fetch(url);
        const data = await response.json();

        addressList.classList.remove('selected');

        if (data.features.length === 0) {
            addressList.innerHTML = '<li>Aucun résultat</li>';
            return;
        }
        else
        {
            addressList.classList.add('show');
        }

        addressList.innerHTML = '';

        addressList.classList.remove('selected');
        data.features.forEach((feature) => {
            const listItem = document.createElement('li');
            const streetName = feature.properties.name;
            const cityName = feature.properties.city;
            addressList.classList.remove('selected');
            listItem.textContent = `${streetName}, ${cityName}`;
            listItem.addEventListener('click', () => {
                addressInput.value = `${streetName}, ${cityName}`;
                addressList.innerHTML = '';
                addressList.classList.remove('show');
                addressList.classList.add('selected');
            });
            addressList.appendChild(listItem);
        });
    } catch (error) {
    }

    if (addressInput.value === '') {
        addressList.classList.remove('show');
    }
    else
    {
        addressList.classList.add('show');
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const form = document.querySelector('form');
form.addEventListener('submit', validateForm);

function validateForm(event) {
    const selectedAddress = document.querySelector('#address-list.selected');
    if (!selectedAddress) {
        event.preventDefault();
        alert('Veuillez choisir une adresse dans la liste.');
    }
}

function copyInput() {
    const input = document.getElementById("device_device");
    input.select();
    document.execCommand("copy");
}