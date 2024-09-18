function sendDataToAPI(data,apiUrl) {

    // Create the request object
    const request = new Request(apiUrl, {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    // Send the request to the API endpoint
    return fetch(request)
        .then(response => response.json())
        .then(result => {
        // Handle the response from the API
        //console.log(result);
        return result;
        // Do something with the response data
        })
        .catch(error => {
        // Handle any errors that occur during the request
        console.error('Error:', error);
    });
}

function getDataFromAPI(data,apiUrl) {
    console.log(data);

    // Create the request object
    const request = new Request(apiUrl, {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    // Send the request to the API endpoint
    return fetch(request)
        .then(response => response.json())
        .then(result => {
        // Handle the response from the API
        //console.log(result);
        return result;
        // Do something with the response data
        })
        .catch(error => {
        // Handle any errors that occur during the request
        console.error('Error:', error);
    });
}