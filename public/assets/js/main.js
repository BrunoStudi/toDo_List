// Fonction attendre un certain temps
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// evenement d'ecoute au chargement du DOM + fonction asyncrone
document.addEventListener('DOMContentLoaded', function () {

    const navbarToggler = document.querySelector('.navbar-toggler');

    async function markAsDone(event) {
        event.preventDefault();
        const itemId = this.dataset.id;
        const taskNumber = this.dataset.index;

        // Requête AJAX
        const response = await fetch(`/user/task/${itemId}/done`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        // Attente de la reponse et conditions si elle reussie ou echouée.
        const data = await response.json();
        if (data.success) {
            // Récupération de l'ID de l'objet
            const card = document.querySelector(`#item-${itemId}`);
            // si l'objet existe
            if (card) {
                // Modification de la couleur de la bordure lors du click
                card.classList.toggle('border-danger', data.etat === 0);
                card.classList.toggle('border-success', data.etat === 1);

                ///////////////////////////// Version avec toggle //////////////////////////////////
                /*const icon = card.querySelector('i.bi');
                if (icon) {
                    // Pareil pour l'icon
                    icon.classList.toggle('bi-pin', data.etat === 0);
                    icon.classList.toggle('bi-pin-angle-fill', data.etat === 1);
                    icon.classList.toggle('text-danger', data.etat === 0);
                    icon.classList.toggle('text-success', data.etat === 1);
                }
                /////////////// en dessous en ternaire pour simplifier le code ///////////////////*/

                const icon = card.querySelector('i.bi');
                if (icon) {
                    // Mise à jour des classes en une seule opération
                    icon.className = `bi ${data.etat === 0 ? 'bi-pin text-danger' : 'bi-pin-angle-fill text-success'}`;
                }

                // Désactivation du bouton éditer, ou non.
                const editButton = card.querySelector('.btn-outline-primary');
                if (editButton) {
                    editButton.classList.toggle('disabled', data.etat === 1);
                }
            }

            // Afficher une notification pour cette tâche
            if (data.etat === 1) {
                showNotification(`Tâche ${taskNumber} marquée terminée !`, 'success');
                playWavSound('/../../assets/audio/notification_ok.wav'); // Lire le son pour tâche terminée
            }
            else {
                showNotification(`Tâche ${taskNumber} marquée en cours !`, 'error');
                playWavSound('/../../assets/audio/notification_error.wav'); // Lire le son pour tâche en cours
            }
        } else {
            alert('une erreur est survenue');
        }
    }

    // Supprimer le defaut de la bordure noire epaisse du bouton burger de bootstrap.
    navbarToggler.addEventListener('click', function () {
        if (this.classList.contains('collapsed')) { // Si le bouton est dans l'état "collapsed", on retire la bordure
            this.style.border = 'none';             // Supprime la bordure
        } else {
            this.style.border = '';                 // Réinitialise la bordure si besoin
        }

        // Enlever l'outline et box-shadow pour éviter la bordure noire
        this.style.outline = 'none';
        this.style.boxShadow = 'none';
    });

    // Fonction pour gérer les notifications
    function showNotification(message, status) {
        const notificationContainer = document.querySelector('#notification-container');
        const notification = document.createElement('div');
        notification.className = `notification show ${status}`;
        notification.textContent = message;

        notificationContainer.appendChild(notification);

        // Supprimer la notification après 4 secondes
        setTimeout(() => {
            notification.classList.remove('show');
            notification.addEventListener('transitionend', () => notification.remove());
        }, 4000);
    }

    // Fonction pour jouer un fichier WAV
    function playWavSound(filePath) {
        const audio = new Audio(filePath);

        audio.play().catch(error => {
        console.error("Erreur lors de la lecture du son :", error);
        });
    }

    // Attacher l'événement sur tout les boutons "icon" des tâches
    document.querySelectorAll('.mark-as-done').forEach(button => {
        button.addEventListener('click', markAsDone);
    });
});