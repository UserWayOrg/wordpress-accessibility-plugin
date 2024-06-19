// Function for sending data
function sendDataIfAvailable() {
    
    // Creating an object to send
    const eventData = {
        events: [{
          "event": "WP-CTA-button-click",
          "content": "user click button Go to Dashboard",
        }]
    };
  
    // Converting an Object to an Array
    const eventsData = eventData;

    // Converting Data to JSON String
    const jsonData = JSON.stringify(eventData);

    // Sending a request using the Fetch API
    fetch('https://api.qa.userway.dev/api/abn/events', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: jsonData,
    })
    .then(response => {
        if (!response.ok) {
        throw new Error('An error occurred while executing the request: ${response.statusText}');
        }
        return response.json();
    })
    .then(data => {
        // Handling a successful response from the server
        console.log('Response from the server:', data);
    })
    .catch(error => {
        // Error processing
        console.error('An error has occurred:', error.message);
    });
}

// Attach the function to the button's click event
document.addEventListener('DOMContentLoaded', (event) => {
    const button = document.getElementById('plugin-button-notice');
    if (button) {
        button.addEventListener('click', sendDataIfAvailable);
    } else {
        console.error('Button with ID "plugin-button-notice" not found.');
    }
});

