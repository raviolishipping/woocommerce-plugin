window.onload = function () {
  ravioliSprinkleModal();
};

function closeModal() {
  document.getElementById("ravioli--background").style.visibility = "hidden";
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

function ravioliSprinkleModal() {
  // add event listeners to yes button
  document
    .getElementById("ravioli--button-yes")
    .addEventListener("click", addRavioli);

  // add event listener to no button
  document
    .getElementById("ravioli--button-no")
    .addEventListener("click", removeRavioli);
}
