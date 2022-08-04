window.onload = function () {
  if (ravioli_data.show_modal) showModal();
};

function closeModal() {
  document.getElementById("ravioli--background").style.visibility = "hidden";
  //document.body.dispatchEvent(new Event("update_checkout"));
}

function addRavioli() {
  document.getElementById("ravioli--add_ravioli_field").value = "true";
  document.body.dispatchEvent(new Event("update_checkout"));
  closeModal();
}

function removeRavioli() {
  document.getElementById("ravioli--add_ravioli_field").value = "false";
  document.body.dispatchEvent(new Event("update_checkout"));
  closeModal();
}

async function showModal() {
  // get html for modal from our html file
  const resp = await fetch(`${ravioli_data.base_url}/ravioli_modal.html`);
  const ravioliModal = await resp.text();

  // add ravioli modal to DOM
  document.body.insertAdjacentHTML("beforeend", ravioliModal);

  // set pic source
  document.getElementById(
    "ravioli--pic"
  ).src = `${ravioli_data.base_url}/img/ravioli_return.gif`;

  // set ravioli fee
  document.getElementById("ravioli--fee").innerText = ravioli_data.fee;

  // add event listeners to yes button
  document
    .getElementById("ravioli--button-yes")
    .addEventListener("click", addRavioli);

  // add event listener to no button
  document
    .getElementById("ravioli--button-no")
    .addEventListener("click", removeRavioli);
}
