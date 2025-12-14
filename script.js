document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("reviewForm");
  const message = document.getElementById("message");

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    clearErrors();
    message.textContent = "";

    const isValid = validateForm();

    if (isValid) {
      message.style.color = "green";
      message.textContent = "Review berhasil dikirim!";
      form.reset();
    } else {
      message.style.color = "red";
      message.textContent = "Periksa kembali field yang bermasalah.";
    }
  });

  function validateForm() {
    const valid =
      validateName() &
      validateProduct() &
      validateRating() &
      validateKelebihan() &
      validateKekurangan() &
      validateReview();

    return !!valid;
  }


  function validateName() {
    const name = document.getElementById("nama").value.trim();
    if (name === "") {
      showError("nama", "Nama tidak boleh kosong.");
      return false;
    }
    return true;
  }

  function validateProduct() {
    const product = document.getElementById("produk").value;
    if (product === "" || product === "Pilih...") {
      showError("produk", "Silakan pilih produk atau layanan.");
      return false;
    }
    return true;
  }

  function validateRating() {
    const rating = document.getElementById("rating").value;
    if (rating === "") {
      showError("rating", "Rating wajib diisi.");
      return false;
    }
    if (rating < 1 || rating > 5) {
      showError("rating", "Rating harus 1 - 5.");
      return false;
    }
    return true;
  }

  function validateKelebihan() {
    const v = document.getElementById("kelebihan").value.trim();
    if (v === "") {
      showError("kelebihan", "Kelebihan tidak boleh kosong.");
      return false;
    }
    return true;
  }

  function validateKekurangan() {
    const v = document.getElementById("kekurangan").value.trim();
    if (v === "") {
      showError("kekurangan", "Kekurangan tidak boleh kosong.");
      return false;
    }
    return true;
  }

  function validateReview() {
    const v = document.getElementById("review").value.trim();
    if (v === "") {
      showError("review", "Review lengkap tidak boleh kosong.");
      return false;
    }
    if (v.length < 15) {
      showError("review", "Review minimal 15 karakter.");
      return false;
    }
    return true;
  }


  function showError(id, messageText) {
    const field = document.getElementById(id);
    field.style.border = "2px solid red";

    const error = document.createElement("div");
    error.className = "error-message";
    error.style.color = "red";
    error.style.fontSize = "14px";
    error.style.marginTop = "4px";
    error.textContent = messageText;

    if (field.nextElementSibling && field.nextElementSibling.classList.contains("error-message")) {
      field.nextElementSibling.remove();
    }

    field.insertAdjacentElement("afterend", error);
  }

  function clearErrors() {
    document.querySelectorAll(".error-message").forEach((el) => el.remove());
    document.querySelectorAll("input, select, textarea").forEach((el) => {
      el.style.border = "1px solid #ccc";
    });
  }
});
