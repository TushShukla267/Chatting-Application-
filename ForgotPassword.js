document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ForgotPassword-Form').addEventListener('submit', function(event) {
        let Email = document.getElementById("emailInput").value;
        let hasAtSymbol = Email.includes('@'); // Check if '@' is present in the email

        if (Email === "" || !hasAtSymbol) { // Check if the email is empty or does not contain '@'
            alert("Enter a valid email. Example: abc@gmail.com");
            event.preventDefault(); // Prevent form submission
        } else {
            // Ask for confirmation
            let confirmation = confirm("Are you sure you want to change the password?");
            if (!confirmation) {
                event.preventDefault(); // Prevent form submission if user clicks "No"
            }
        }
    });
});
