(function($) {

    // Variable globale pour rdv_time
    var globalRdvTime = '';

    $(document).ready(function() {
        $('#formrdv').on('submit', function(event) {
            event.preventDefault();
            getdataform();
        });
    
        function getdataform() {
            var rdv_categorievalue = $('#rdv_categorie').val();
            var rdv_servicevalue = $('#rdv_service').val();
            var rdv_datevalue = $('#rdv_date').val();
            var rdv_timevalue = $('#rdv_time').val();
            var rdv_immatriculationvalue = $('#rdv_immatriculation').val();
            var rdv_genreclientvalue = $('#rdv_genreclient').val();
            var rdv_nomclientvalue = $('#rdv_nomclient').val();
            var rdv_emailclientvalue = $('#rdv_emailclient').val();
            var rdv_telclientvalue = $('#rdv_telclient').val();
            var rdv_commentaireclientvalue = $('#rdv_commentaireclient').val();
            var rdv_etatvalue = 'enattente';
    
            // Log values for debugging
            console.log('Email: ', rdv_emailclientvalue);
            console.log('Service: ', rdv_servicevalue);
            

            var ajaxUrl = postdata.ajax_url;
            var noncevalue = postdata.ajax_nonce;
    
            $.post(
                ajaxUrl, // this url ajax extract form postdata
                {
                    action: 'rdvpress_insertform',
                    security: noncevalue,
                    rdv_categorie: rdv_categorievalue,
                    rdv_service: rdv_servicevalue,
                    rdv_date: rdv_datevalue,
                    rdv_time: rdv_timevalue,
                    rdv_immatriculation: rdv_immatriculationvalue,
                    rdv_genreclient: rdv_genreclientvalue,
                    rdv_nomclient: rdv_nomclientvalue,
                    rdv_emailclient: rdv_emailclientvalue,
                    rdv_telclient: rdv_telclientvalue,
                    rdv_commentaire: rdv_commentaireclientvalue,
                    rdv_etat: rdv_etatvalue
                },
                function(response) {
                    console.log(response);
    
                    // Show the congratulations container
                    $('#congratulations-container').show();
    
                    // Hide the form container
                    $('.form-container').hide();

                    // Récupération du titre du service sélectionné
                    var servicetitle = $( "#rdv_service option:selected" ).text();

    
                    // Optionally, fill the appointment details
                    $('#appointment-details').html(`
                        <p>Service: ${servicetitle}</p>
                        <p>Date: ${rdv_datevalue}</p>
                        <p>Heure: ${rdv_timevalue}</p>
                        <p>Nom: ${rdv_nomclientvalue}</p>
                        <p>Email: ${rdv_emailclientvalue}</p>
                        <p>Téléphone: ${rdv_telclientvalue}</p>
                    `);
                }
            ).fail(function(xhr, status, error) {
                console.log('An error occurred: ' + error);
            });
        }
    
    

        $('#rdv_categorie').on('change', function() {
            var ajaxUrl = postdata.ajax_url,
                nonceValue = postdata.ajax_nonce,
                rdv_categorieidvalue = $('#rdv_categorie').val();

            $.post(
                ajaxUrl,   // this url till admin-ajax.php is given by functions.php wp_localize_script()
                {
                    action: 'service_cat_option',
                    security: nonceValue,
                    rdv_categorieid: rdv_categorieidvalue
                },
                function(status) {
                    console.log(status);
                }
            ).done(function(response) {
                console.log('The server responded: ');
                var productSubCatEl = $('#rdv_service');
                productSubCatEl.html('');
                productSubCatEl.append(response.data.my_data);
            });
        });
    
        $('#calendar').on('click', 'div.selected', function() {
            var ajaxUrl = postdata.ajax_url,
                nonceValue = postdata.ajax_nonce,
                rdv_categorie_value = $('#rdv_categorie').val(),
                rdv_service_value = $('#rdv_service').val(),
                rdv_date_value = $('#rdv_date').val();

            $.post(
                ajaxUrl,   // this url till admin-ajax.php is given by functions.php wp_localize_script()
                {
                    action: 'time_date_option',
                    security: nonceValue,
                    rdv_categorie_id: rdv_categorie_value,
                    rdv_service_id: rdv_service_value,
                    rdv_date: rdv_date_value
                },
                function(status) {
                    console.log(status);
                }
            ).done(function(response) {
                console.log('The server responded: time');
                var timeEl = $('#globalRdvTime');

                console.log('globalRdvTime : ' + timeEl.val());

                var appointments = JSON.parse(response);
                console.log('appointments : ' + appointments);

                var disabledSlots = [];
                appointments.forEach(function(appointment) {
                    disabledSlots.push(appointment.time);
                });

                timeEl.val(disabledSlots.join(';'));
                console.log(disabledSlots.join(';'));

                // Désactiver les créneaux horaires réservés
                disableReservedSlots();
            });
        });

        function disableReservedSlots() {
            var timeEl = $('#globalRdvTime');
            var timeString = timeEl.val(); // Get the current value as a string
            var disabledSlots = timeString.split(';'); // Convert the string to an array

            console.log(disabledSlots); // Log the array for debugging

            $('#time-slots div').each(function() {
                var slotText = $(this).text().trim();
                if (disabledSlots.includes(slotText)) {
                    $(this).addClass('disabled'); // Ajouter une classe pour indiquer qu'il est désactivé
                    console.log('Disabling slot:', slotText);
                } else {
                    $(this).removeClass('disabled'); // Remove disabled class if not in the array
                    console.log('Enabling slot:', slotText);
                }
            });
        }
    });



    

})(jQuery);
function submitForm() {
    if (validateTab(3)) {
        const nom = document.getElementById('rdv_nomclient').value;
        const email = document.getElementById('rdv_emailclient').value;
        const tel = document.getElementById('rdv_telclient').value;
        const comments = document.getElementById('rdv_commentaireclient').value;
        const service = document.getElementById('rdv_service').value;
        const date = document.getElementById('rdv_date').value;
        const time = document.getElementById('rdv_time').value;

        const detailsHtml = `
            <p><strong>Nom:</strong> ${nom}</p>
            <p><strong>Email:</strong> ${email}</p>
            <p><strong>Téléphone:</strong> ${tel}</p>
            <p><strong>Commentaires:</strong> ${comments}</p>
            <p><strong>Service:</strong> ${service}</p>
            <p><strong>Date:</strong> ${date}</p>
            <p><strong>Heure:</strong> ${time}</p> 
        `;

        document.getElementById('appointment-details').innerHTML = detailsHtml;
        document.querySelector('.container').style.display = 'none';
        document.getElementById('congratulations-container').style.display = 'block';

        /*setTimeout(() => {
                    window.location.href = 'http://localhost/wordpress/reservation/'; // Remplacez par l'URL de redirection souhaitée
                }, 60000);*/
    }
}

    document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#rdv_telclient");
            window.intlTelInput(input, {
                initialCountry: "fr",
                geoIpLookup: function(callback) {
                    fetch('https://ipinfo.io?token=YOUR_IPINFO_TOKEN', { headers: { 'Accept': 'application/json' } })
                        .then(response => response.json())
                        .then(data => callback(data.country))
                        .catch(() => callback('fr'));
                },
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
            });
        });
    

            // Fonction pour vérifier si les champs du premier onglet sont remplis
    function isServiceTabValid() {
        const serviceTab = document.getElementById('tab-1');
        const inputs = serviceTab.querySelectorAll('input, select');
        for (let i = 0; i < inputs.length; i++) {
            if (!inputs[i].checkValidity()) {
                inputs[i].reportValidity();
                return false;
            }
        }
        return true;
    }

    // Fonction pour tenter d'afficher un onglet, en vérifiant d'abord la validité des champs du premier onglet
    function attemptShowTab(tabIndex) {
        if (tabIndex > 1 && !isServiceTabValid()) {
            
            return;
        }
        showTab(tabIndex);
    }
            // Fonction pour afficher l'onglet spécifié
    function showTab(tabIndex) {
        const tabs = document.getElementsByClassName('tab-content');
        const menuItems = document.getElementsByClassName('menu-item');
        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
            menuItems[i].classList.remove('active');
        }
        document.getElementById(`tab-${tabIndex}`).classList.add('active');
        menuItems[tabIndex - 1].classList.add('active');
    }

    // Fonction pour passer à l'onglet suivant
    function nextTab(currentTab) {
        if (validateTab(currentTab)) {
            showTab(currentTab + 1);
        }
    }

    // Fonction pour revenir à l'onglet précédent
    function prevTab(currentTab) {
        showTab(currentTab - 1);
    }

    // Fonction pour valider les champs du formulaire dans un onglet spécifique
    function validateTab(tabIndex) {
        const tab = document.getElementById(`tab-${tabIndex}`);
        const inputs = tab.querySelectorAll('input, select');
        for (let i = 0; i < inputs.length; i++) {
            if (!inputs[i].checkValidity()) {
                inputs[i].reportValidity();
                return false;
            }
        }
        return true;
    }

    // Fonction pour réduire ou étendre le menu latéral
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const menuText = document.getElementById('menuText');
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
            menuText.textContent = 'Étendre le menu';
        } else {
            menuText.textContent = 'Réduire le menu';
        }
    }

     // Fonction pour générer le calendrier pour un mois et une année donnés
     function generateCalendar(month, year) {
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = '';

        const currentDate = new Date();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

        // Generate day headers
        for (let day of daysOfWeek) {
            const dayHeader = document.createElement('div');
            dayHeader.classList.add('day-header');
            dayHeader.textContent = day;
            calendar.appendChild(dayHeader);
        }

        // Adjust firstDay to make Monday the first day of the week
        const adjustedFirstDay = (firstDay === 0) ? 6 : firstDay - 1;

        // Generate empty slots before the first day of the month
        for (let i = 0; i < adjustedFirstDay; i++) {
            const emptyDiv = document.createElement('div');
            calendar.appendChild(emptyDiv);
        }

        // Generate days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateDiv = document.createElement('div');
            dateDiv.textContent = day;

            const date = new Date(year, month, day);
            if (date < currentDate.setHours(0, 0, 0, 0)) {
                // Add a class to style past dates
                dateDiv.classList.add('past-date');
            } else {
                dateDiv.onclick = function() {
                    selectDate(this);
                };
            }

            calendar.appendChild(dateDiv);
        }
    }

    // Function to handle date selection
    function selectDate(element) {
        document.querySelectorAll('.calendar div').forEach(function(div) {
            div.classList.remove('selected');
        });
        element.classList.add('selected');
        const selectedDate = element.textContent;
        const selectedMonth = document.getElementById('month-select').value;
        const selectedYear = document.getElementById('year-select').value;
        document.getElementById('selected-date-display').textContent = `Date sélectionnée : ${selectedDate}/${parseInt(selectedMonth) + 1}/${selectedYear}`;
        document.getElementById('rdv_date').value = `${selectedYear}-${parseInt(selectedMonth) + 1}-${selectedDate}`;
    }

    // Initialize calendar
    document.addEventListener('DOMContentLoaded', function() {
        const monthSelect = document.getElementById('month-select');
        const yearSelect = document.getElementById('year-select');
        monthSelect.addEventListener('change', () => generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value)));
        yearSelect.addEventListener('change', () => generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value)));

        // Generate initial calendar
        generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
    });


// Fonction pour initialiser les sélecteurs de mois et d'années
function initializeSelectors() {
    const monthSelect = document.getElementById('month-select');
    const yearSelect = document.getElementById('year-select');

    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();

    //const currentMonth = currentDate.getMonth();

    //console.log(currentMonth);

    //const currentMonth = 6;

    const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    // Remplir le sélecteur de mois à partir du mois actuel
    for (let index = currentMonth; index < months.length; index++) {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = months[index];
        if (index === currentMonth) {
            option.selected = true;
        }
        monthSelect.appendChild(option);
    }
    for (let i = monthSelect.options.length - 1; i >= 0; i--) {
        if (parseInt(monthSelect.options[i].value) < currentMonth) {
            monthSelect.remove(i);
        }
    }
    // Remplir le sélecteur d'années
    for (let year = currentYear - 0; year <= currentYear + 1; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) {
            option.selected = true;
        }
        yearSelect.appendChild(option);
    }

    // Ajouter des écouteurs d'événements pour mettre à jour le calendrier
    monthSelect.addEventListener('change', () => {
        generateCalendar(monthSelect.value, yearSelect.value);
    });
    yearSelect.addEventListener('change', () => {
        generateCalendar(monthSelect.value, yearSelect.value);
    });
}

// Fonction pour gérer la sélection de la date
function selectDate(element) {
    const selectedDate = document.querySelector('.calendar div.selected');
    if (selectedDate) {
        selectedDate.classList.remove('selected');
    }
    element.classList.add('selected');

    // Afficher la date sélectionnée
    const selectedDateDisplay = document.getElementById('selected-date-display');
    const monthSelect = document.getElementById('month-select');
    const yearSelect = document.getElementById('year-select');
    selectedDateDisplay.textContent = `Date: ${element.textContent} ${monthSelect.options[monthSelect.selectedIndex].textContent} ${yearSelect.value}`;

    // Afficher la div de l'heure
    document.querySelector('.time-container').style.display = 'block';
}

// Initialiser les créneaux horaires
function generateTimeSlots() {
    const timeSlots = [
        '09:00 - 10:00','09:30 - 10:30','10:00 - 11:00','10:30 - 11:30', '11:00 - 12:00',
        '11:30 - 12:30','12:00 - 13:00','12:30 - 13:30', '13:00 - 14:00','13:30 - 14:30', 
        '14:00 - 15:00','14:30 - 15:30', '15:00 - 16:00', '15:30 - 16:30','16:00 - 17:00',
        '16:30 - 17:30','17:00 - 18:00'
        
    ];

    const timeContainer = document.getElementById('time-slots');
    timeSlots.forEach(slot => {
        const timeDiv = document.createElement('div');
        timeDiv.textContent = slot;
        timeDiv.onclick = function() {
            selectTimeSlot(this);
        };
        timeContainer.appendChild(timeDiv);
    });
}

// Fonction pour gérer la sélection du créneau horaire
function selectTimeSlot(element) {
    const selectedTime = document.querySelector('.time-slots div.selected');
    if (selectedTime) {
        selectedTime.classList.remove('selected');
    }
    element.classList.add('selected');
}

// Initialiser le calendrier et les créneaux horaires au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    initializeSelectors();
    const currentDate = new Date();
    generateCalendar(currentDate.getMonth(), currentDate.getFullYear());
    generateTimeSlots();
    // Cacher la div de l'heure au début
    document.querySelector('.time-container').style.display = 'none';
});

// Fonction pour gérer la sélection de la date
function selectDate(element) {
        const selectedDate = document.querySelector('.calendar div.selected');
        if (selectedDate) {
            selectedDate.classList.remove('selected');
        }
        element.classList.add('selected');

        // Obtenir le mois et l'année sélectionnés
        const monthSelect = document.getElementById('month-select');
        const yearSelect = document.getElementById('year-select');
        const day = element.textContent.padStart(2, '0'); // Ajouter un zéro devant les jours à un chiffre
        const month = (parseInt(monthSelect.value) + 1).toString().padStart(2, '0'); // Ajouter un zéro devant les mois à un chiffre

        // Afficher la date sélectionnée
        const selectedDateDisplay = document.getElementById('selected-date-display');
        selectedDateDisplay.textContent = `Date: ${day} ${monthSelect.options[monthSelect.selectedIndex].textContent} ${yearSelect.value}`;

        // Définir la valeur dynamique de rdv_date
        document.getElementById('rdv_date').value = `${yearSelect.value}-${month}-${day}`;

        // Afficher la div de l'heure
        document.querySelector('.time-container').style.display = 'block';

    }

function selectTimeSlot(element) {
    const selectedTime = document.querySelector('.time-slots div.selected');
    if (selectedTime) {
        selectedTime.classList.remove('selected');
    }
    element.classList.add('selected');

    // Définir la valeur dynamique de rdv_time
    document.getElementById('rdv_time').value = element.textContent;
}
