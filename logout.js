
        function logout() {
            
            var xhr = new XMLHttpRequest();

            
            xhr.open('GET', 'UserLogin.php?logout=1', true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                  
                    console.log(xhr.responseText);
                } else {
                    
                    console.error('Logout request failed with status ' + xhr.status);
                }
            };

        
            xhr.send();
        }
       