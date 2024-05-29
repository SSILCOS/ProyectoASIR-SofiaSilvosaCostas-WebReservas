function confirmCancel(reservationId) {
    if (confirm('¿Estás seguro de que deseas anular esta reserva?')) {
        fetch(`cancel_reservation.php?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reserva anulada con éxito.');
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al anular la reserva. Por favor, intenta de nuevo.');
            });
    }
}
