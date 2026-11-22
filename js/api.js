/* Author : Norah Njonjo */

/* async function apiPost(url, info, ok, fail) {

    try { // communicate with PHP
        const response = await fetch(url);

    } catch (error) {

        fail(error, message);
    }
} */


function apiPost(url, data, ok, fail){

    fetch(url, {
        method: 'POST',
        body: new URLSearchParams(data)
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error("msmsmsmsfail");
        }
        return response.json();
    })
    .then(result => ok.result)
    .catch(error => console.error("Problem with fetch operation", error));

}