document.addEventListener('DOMContentLoaded', function () {
    // Function to handle the AJAX request for marking as done
    function markAsDone(event) {
        event.preventDefault();
        const itemId = this.dataset.id;

        fetch(`/list/${itemId}/done`, {     
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`#item-${itemId}`);
                if (card) {
                    card.classList.toggle('border-danger', data.etat === 0);
                    card.classList.toggle('border-success', data.etat === 1);

                    const icon = card.querySelector('i.bi');
                    if (icon) {
                        icon.classList.toggle('bi-pin', data.etat === 0);
                        icon.classList.toggle('bi-pin-angle-fill', data.etat === 1);
                        icon.classList.toggle('text-danger', data.etat === 0);
                        icon.classList.toggle('text-success', data.etat === 1);
                    }

                    const editButton = card.querySelector('.btn-outline-primary');
                    if (editButton) {
                        editButton.classList.toggle('disabled', data.etat === 1);
                    }
                }
            } else {
                alert('An error occurred');
            }
        });
    }

    // Attach event listeners to all mark-as-done buttons
    document.querySelectorAll('.mark-as-done').forEach(button => {
        button.addEventListener('click', markAsDone);
    });

    // Similar functions can be created for delete and edit actions
});