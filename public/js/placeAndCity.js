document.addEventListener('DOMContentLoaded', function () {

    const citySelect = document.getElementById('trip_city');
    const placeSelect = document.getElementById('trip_place');

    citySelect.addEventListener('change', function () {
        const cityId = citySelect.value;

        if (cityId) {
            fetch(`/api/city/${cityId}/places`)
                .then(response => response.json())
                .then(data => {
                    placeSelect.innerHTML = '<option value="">Choisissez un lieu</option>';

                    data.forEach(place => {
                        const option = document.createElement('option');
                        option.value = place.id;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching places:', error));
        } else {
            placeSelect.innerHTML = '<option value="">Choose a place</option>';
        }
    });
});