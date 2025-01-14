const submit_btn = document.getElementById("submit");
const data_table = document.getElementById("data");

//event handler for clicking the "Show" button.
submit_btn.onclick = function (e) {
  e.preventDefault();
  data_table.style.display = "block";

  let selectElement = document.getElementById("user");
  let user_id = selectElement.options[selectElement.selectedIndex].value;
  let user_name = selectElement.options[selectElement.selectedIndex].text;

  //Create AJAX GET-query:
  const request = new XMLHttpRequest(); //request object
  const url = "data.php?user_id=" + user_id + "&user_name=" + user_name; //create url with parameters
  request.open("GET", url);
  request.setRequestHeader("Content-Type", "application/x-www-form-url");

  //Wait for server answer:
  request.addEventListener("readystatechange", () => {
    if (request.readyState === 4 && request.status === 200) {
      //replace with the received html code with a table of monthly balances:
      data_table.innerHTML = request.responseText;
    }
  });

  request.send();
};
