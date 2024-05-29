document.getElementById('service').addEventListener('change', function() {
    updateServiceOptions();
    toggleDateTimeVisibility();
    updateAvailableTimes();
    updateMinimumDate();
});

document.getElementById('reservationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita el envío del formulario

    alert('Reserva confirmada!'); // Muestra el mensaje de confirmación

    setTimeout(function() {
        document.getElementById('reservationForm').submit();
    }, 2000);
});

function updateServiceOptions() {
    const service = document.getElementById('service').value;
    const additionalOptions = document.getElementById('additionalOptions');
    const subOptionSelect = document.getElementById('subOption');
    subOptionSelect.innerHTML = '';  // Limpiar opciones anteriores

    if (service && servicios[service]) {
        servicios[service].forEach(subOption => {
            const option = document.createElement('option');
            option.value = subOption;
            option.text = subOption;
            subOptionSelect.appendChild(option);
        });
        additionalOptions.style.display = 'block';
    } else {
        additionalOptions.style.display = 'none';
    }
}

function toggleDateTimeVisibility() {
    const service = document.getElementById('service').value;
    const dateTimeFields = document.getElementById('dateTimeFields');
    dateTimeFields.style.display = service ? 'block' : 'none';
}

function updateAvailableTimes() {
    const service = document.getElementById('service').value;
    const timeOptions = document.getElementById('time-options');
    timeOptions.innerHTML = '';

    if (service) {
        if (['Área de Barbacoa', 'Picnic de < 20 personas', 'Picnic de > 20 personas'].includes(service)) {
            const sessions = [['12:00', '18:00'], ['18:00', '23:00']];
            sessions.forEach(session => {
                const startTime = session[0];
                const endTime = session[1];
                const option = new Option(`${startTime} a ${endTime}`, `${startTime}`);
                timeOptions.appendChild(option);
            });
        } else {
            for (let hour = 8; hour < 23; hour += 1.5) {
                const startHour = Math.floor(hour);
                const endHour = Math.floor(hour + 1.5);
                const startMinutes = (hour % 1) * 60;
                const endMinutes = ((hour + 1.5) % 1) * 60;
                const startTime = `${startHour.toString().padStart(2, '0')}:${startMinutes === 0 ? '00' : '30'}`;
                const endTime = `${endHour.toString().padStart(2, '0')}:${endMinutes === 0 ? '00' : '30'}`;
                const option = new Option(`${startTime} a ${endTime}`, `${startTime}`);
                timeOptions.appendChild(option);
            }
        }
    }
}

function updateMinimumDate() {
    const today = new Date();
    const maxDate = new Date(today.getTime());

    maxDate.setDate(today.getDate() + 3);

    const minDate = today.toISOString().split('T')[0];
    const maxAllowedDate = maxDate.toISOString().split('T')[0];

    const dateInput = document.getElementById('date');
    dateInput.setAttribute('min', minDate);
    dateInput.setAttribute('max', maxAllowedDate);
}

window.onload = function() {
    updateServiceOptions();
    toggleDateTimeVisibility();
    updateAvailableTimes();
    updateMinimumDate();
};
