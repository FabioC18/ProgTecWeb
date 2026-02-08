const userIn = document.getElementById('username');
        const emailIn = document.getElementById('email');
        const passIn = document.getElementById('pass');
        const btn = document.getElementById('btn-submit');
        const tooltip = document.getElementById('password-tooltip');

        // GESTIONE TOOLTIP PASSWORD (SOTTO)
        function showTooltip() {
            tooltip.style.display = 'block';
        }
        function hideTooltip() {
            tooltip.style.display = 'none';
        }

        passIn.addEventListener('mouseenter', showTooltip);
        passIn.addEventListener('mouseleave', hideTooltip);
        passIn.addEventListener('focus', showTooltip);
        passIn.addEventListener('blur', hideTooltip);


        function togglePassword() {
            if (passIn.type === "password") {
                passIn.type = "text";
            } else {
                passIn.type = "password";
            }
        }

        function checkInputs() {
            const passValue = passIn.value;
            const emailValue = emailIn.value;

            // Validazione Regex JS
            const hasUpperCase = /[A-Z]/.test(passValue); 
            const hasNumber = /[0-9]/.test(passValue);    
            const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); 
            const hasLength = passValue.length >= 8;      
            const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/; 

            if (userIn.value.trim() !== "" && 
                emailRegex.test(emailValue) && 
                hasUpperCase && hasNumber && hasSpecial && hasLength) {
                
                btn.disabled = false;
                btn.style.opacity = "1";
            } else {
                btn.disabled = true;
                btn.style.opacity = "0.5";
            }
        }

        userIn.addEventListener('input', checkInputs);
        emailIn.addEventListener('input', checkInputs);
        passIn.addEventListener('input', checkInputs);
        
        checkInputs();


let map = null; 

    function getLocation() {
      
      const mapContainer = document.getElementById("map");

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;

          mapContainer.style.display = 'block';

          map = L.map('map').setView([lat, lon], 11); 
          L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 19
          }).addTo(map);

          const suite = L.marker([40.6780, 14.7625]).addTo(map).bindPopup("<b>Suite!</b>").openPopup();
          const deluxe = L.marker([40.67891, 14.75808]).addTo(map).bindPopup("<b>Deluxe!</b>").openPopup();
          const marker = L.marker([lat, lon]).addTo(map).bindPopup("<b>Sei qui!</b>").openPopup();          
        }
      );
    }        