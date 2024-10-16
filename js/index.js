document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");
    const inputs = form.querySelectorAll("input, textarea"); // Select both input and textarea fields
  
    // Validation rules for email, phone number, and message
    const validationRules = {
      email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, // Basic email validation
      phone: /^\d{11}$/, // Example pattern for phone numbers (10 digits)
      message: /^.{30,}$/, // Minimum 10 characters for message
    };
  
    function validateField(field) {
      const name = field.getAttribute("name"); // Use the "name" attribute to identify fields
      const value = field.value.trim();
      const pattern = validationRules[name];
      let isValid = true;
  
      // Check if the pattern exists and test the field's value
      if (pattern) {
        isValid = pattern.test(value);
      }
  
      // Apply or remove Bootstrap's validation classes
      if (isValid) {
        field.classList.remove("is-invalid");
        field.classList.add("is-valid"); // Optionally add a "valid" indicator
      } else {
        field.classList.add("is-invalid");
        field.classList.remove("is-valid"); // Remove "valid" if present
      }
  
      return isValid;
    }
  
    // Attach keyup event listener to all input fields for real-time validation
    inputs.forEach((input) => {
      input.addEventListener("keyup", (event) => validateField(event.target));
    });
  
    const submitButton = form.querySelector('button[type="submit"]');
  
    form.addEventListener("submit", async (event) => {
      event.preventDefault(); // Prevent default form submission
  
      let formIsValid = true;
  
      // Validate all fields on form submission
      inputs.forEach((input) => {
        if (!validateField(input)) {
          formIsValid = false;
        }
      });
  
      if (formIsValid) {
        // If the form is valid, proceed with submission
        submitButton.setAttribute("disabled", true);
        submitButton.textContent = "Submitting...";
  
        const data = {
          email: form.querySelector('[name="email"]').value.trim(),
          phone: form.querySelector('[name="phone"]').value.trim(),
          message: form.querySelector('[name="message"]').value.trim(),
        };
  
        try {
          const response = await fetch("./backend/sendEmail.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          });
  
          const result = await response.json();
  
          if (response.ok) {
            alert(result.message); // Show success message
            form.reset(); // Reset the form fields
            inputs.forEach((input) => {
              input.classList.remove("is-valid", "is-invalid"); // Reset validation styles
            });
          } else {
            console.error("Form submission failed:", response.statusText);
            alert("There was an issue with the form submission.");
          }
        } catch (error) {
          console.error("Error submitting form:", error);
          alert("Error submitting form. Please try again.");
        } finally {
          // Re-enable the submit button and reset its text
          submitButton.removeAttribute("disabled");
          submitButton.textContent = "Send Message";
        }
      } else {
        console.log("Form contains errors. Please correct them before submitting.");
      }
    });
  });
  