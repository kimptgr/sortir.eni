document.addEventListener('DOMContentLoaded', function () {


    const citySelect = document.getElementById('trip_city');
    const placeSelect = document.getElementById('trip_place');
    const streetSelected = document.getElementById('street');
    const postalCodeSelected = document.getElementById('postalCode');
    const latitudeSelected = document.getElementById('latitude');
    const longitudeSelected = document.getElementById('longitude');


    citySelect.addEventListener('change', function () {
        const cityId = citySelect.value;

        if (cityId) {
            const url = apiCityPlacesUrl.replace('0', cityId);
            fetch(url)
        .then(response => response.json())
                .then(data => {
                    placeSelect.innerHTML = '<option value="">Choisissez un lieu</option>';

                    data.forEach(place => {
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name ;
                        placeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching places:', error));
        } else {
            placeSelect.innerHTML = '<option value="">Choisissez un lieu</option>';
        }
    });


    placeSelect.addEventListener('change', function () {
        const placeId = placeSelect.value;

        if(placeId) {
            url = apiPlaceInfoUrl.replace('0', placeId);
            fetch(url)
                .then(response => response.json())
                .then(data=> {
                    const placeData = data[0];
                    streetSelected.value = placeData.street;
                    postalCodeSelected.value = placeData.postalCode;
                    latitudeSelected.value = placeData.latitude;
                    longitudeSelected.value = placeData.longitude;
                })

        }
    })

});
