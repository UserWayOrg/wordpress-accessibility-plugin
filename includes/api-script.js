// Fetch data from the API
fetch(api_script_vars.api_url)
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    // Handle the API response data
    const beforeRemediation = data.payload.accScore.beforeRemediation;
    const issueNumElement = document.querySelector(".issue_number");
    issueNumElement.textContent = beforeRemediation;
  })
  .catch(error => {
    console.error('There was a problem with the fetch operation:', error);
  });

  
  