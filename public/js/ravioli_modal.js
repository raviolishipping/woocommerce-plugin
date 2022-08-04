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

function showModal() {
  // set pic source
  document.getElementById(
    "ravioli--pic"
  ).src = `${ravioli_data.base_url}/img/ravioli_return.gif`;

  // set ravioli fee
  const feeFormatted = new Intl.NumberFormat(`de-DE`, {
    currency: "EUR",
    style: "currency",
  }).format(ravioli_data.fee);
  document.getElementById("ravioli--fee").innerText = feeFormatted;

  // add event listeners to yes button
  document
    .getElementById("ravioli--button-yes")
    .addEventListener("click", addRavioli);

  // add event listener to no button
  document
    .getElementById("ravioli--button-no")
    .addEventListener("click", removeRavioli);
}
