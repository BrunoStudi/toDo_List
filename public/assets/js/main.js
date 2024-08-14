document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour traiter la requête AJAX afin de marquer la "tâche" comme terminée
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

    // Attacher un evenement d'ecoute sur tout les boutons mark-as-done
    document.querySelectorAll('.mark-as-done').forEach(button => {
        button.addEventListener('click', markAsDone);
    });
});