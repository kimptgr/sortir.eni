document.addEventListener('DOMContentLoaded', function () {


    const citySelect = document.getElementById('trip_city');
    const placeSelect = document.getElementById('trip_place');
    const streetSelected = document.getElementById('street');
    const postalCodeSelected = document.getElementById('postalCode');
    const latitudeSelected = document.getElementById('latitude');
    const longitudeSelected = document.getElementById('longitude');

    const closeFormAddPlace = document.getElementById('btn-close-form');
    const modal = document.getElementById('modal-add-place');
    const formNewPlace = document.getElementById('form-nouveau-lieu');
    const placetitleElement = document.getElementById('placetitle');


    citySelect.addEventListener('change', handleCityChange);
    placeSelect.addEventListener('change', handlePlaceSelection);
    closeFormAddPlace.addEventListener('click', closeAddPlaceForm);
    formNewPlace.addEventListener('click', sendPlaceForm);

        function handleCityChange() {
            let cityId = citySelect.value;
        if (cityId) {
            let url = apiCityPlacesUrl.replace(/\/\d+$/, '/' + cityId);
            fetch(url)
        .then(response => response.json())
                .then(data => {
                    placeSelect.innerHTML = '<option value="">Choisissez un lieu</option>';

                    data.forEach(place => {
                        let option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name ;
                        placeSelect.appendChild(option);
                    });
                    let option = document.createElement('option');
                    option.textContent ='Ajoutez Lieu';
                    placeSelect.appendChild(option);
                })
                .catch(error => console.error('Error fetching places:', error));
        } else {
            placeSelect.innerHTML = '<option value="">Choisissez un lieu</option>';
        }
    }

        function handlePlaceSelection() {
        let placeId = placeSelect.value;
        if (placeId ==='Ajoutez Lieu'){
            displayFormAddPlace();
        }
        else if(placeId) {
            let url = apiCityPlacesUrl.replace(/\/\d+$/, '/' + placeId);
            fetch(url)
                .then(response => response.json())
                .then(data=> {
                    let  placeData = data[0];
                    streetSelected.value = placeData.street;
                    postalCodeSelected.value = placeData.city.postalCode;
                    latitudeSelected.value = placeData.latitude;
                    longitudeSelected.value = placeData.longitude;
                })
        }
    }
    function displayFormAddPlace(){
        let userCityChoice = citySelect.options[citySelect.value].textContent;
        placetitleElement.innerText = 'Nouveau lieu pour '+ userCityChoice;
        modal.hidden = !modal.hidden;
        document.querySelectorAll('.hide-on-modal').forEach(element => {
            element.style.display = "none";
        });
    }

    // Soumettre le formulaire en AJAX pour ajouter un nouveau lieu
    function sendPlaceForm (e) {
        e.preventDefault();
        const formData = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('csrf_token', csrfToken);
        formData.append('name', document.getElementById('place_name').value);
        formData.append('street', document.getElementById('place_street').value);
        formData.append('latitude', document.getElementById('place_latitude').value);
        formData.append('longitude', document.getElementById('place_longitude').value);
        formData.append('cityId', citySelect.options[citySelect.value].value);

        fetch('/place/place/create', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter le nouveau lieu en avant dernier du select
                    let newOption = new Option(data.place.name, data.place.id, true, true);
                    let beforeLastIndex = placeSelect.options.length - 1;
                    placeSelect.add(newOption, beforeLastIndex);

                    //Remplir les autres champs avec les informations du nouveau lieu
                    streetSelected.value = data.place.street;
                    postalCodeSelected.value = data.place.postalCode;
                    latitudeSelected.value = data.place.latitude;
                    longitudeSelected.value = data.place.longitude;

                    // Fermer la modale et réinitialiser le formulaire
                    modal.hidden = !modal.hidden;
                    closeAddPlaceForm();

                   // formNewPlace.reset();
                } else {
                    const errorContainer = document.getElementById('error-messages');
                    errorContainer.innerHTML = ''; // Clear previous errors
                    data.errors.forEach(error => {
                        const errorItem = document.createElement('div');
                        errorItem.className = 'text-red-500'; // Tailwind style for error
                        errorItem.textContent = error;
                        errorContainer.appendChild(errorItem);
                    })
                }
            })
            .catch(error => console.error('Erreur lors de l\'ajout du lieu:', error));
    }
    function closeAddPlaceForm(){
        modal.hidden = true;
        document.querySelectorAll('.hide-on-modal').forEach(element => {
            element.style.display = "block";
        });
    }
});