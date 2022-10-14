window.onload = function () {
  ravioliSprinkleModal();
  document.addEventListener("click", removeRavioliFromCheckout);
};

// modal stuff
function closeModal() {
  document.getElementById("ravioli--background").style.visibility = "hidden";
}

function addRavioliFromModal() {
  updateHiddenFieldAndRefresh("true");
  closeModal();
}

function removeRavioliFromModal() {
  updateHiddenFieldAndRefresh("false");
  closeModal();
}

function updateHiddenFieldAndRefresh(fieldValue) {
  document.getElementById("ravioli--add_ravioli_field").value = fieldValue;
  document.body.dispatchEvent(new Event("update_checkout"));
}

function ravioliSprinkleModal() {
  // add event listeners to yes button
  document
    .getElementById("ravioli--button-yes")
    ?.addEventListener("click", addRavioliFromModal);

  // add event listener to no button
  document
    .getElementById("ravioli--button-no")
    ?.addEventListener("click", removeRavioliFromModal);
}

// remove button in checkout view
function removeRavioliFromCheckout(e) {
  if (e.target.id === "ravioli-remove-checkout") {
    e.preventDefault();
    updateHiddenFieldAndRefresh(false);
  }
}
