document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', function(event) {
        // Get form inputs
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        if (!usernameInput.value.trim()) {
            alert('Please enter a username.');
            event.preventDefault(); // Prevent form submission
            return;
        }

        if (!passwordInput.value.trim()) {
            alert('Please enter a password.');
            event.preventDefault(); // Prevent form submission
            return;
        }

        if (passwordInput.value.trim().length < 8) {
            alert('Password must be at least 8 characters long.');
            event.preventDefault(); // Prevent form submission
            return;
        }

        if (!containsNumberOrSpecialChar(usernameInput.value.trim())) {
            alert('Username must contain at least one number or special character like "_".');
            event.preventDefault(); // Prevent form submission
            return;
        }

        // Allow form submission if all validations pass
    });

    function containsNumberOrSpecialChar(username) {
        return /[0-9_]/.test(username);
    }

    // Function to animate text one by one
    function animateText(element, text) {
        var index = 0;
        var timer = setInterval(function() {
            // Append the next letter to the element
            element.innerHTML += text.charAt(index);
            index++;
            // Stop the animation when all letters are appended
            if (index === text.length) {
                clearInterval(timer);
            }
        }, 75); // Adjust the delay (in milliseconds) between letters
    }

    // Call the animateText function for the first text element
    var textElement1 = document.querySelector('.text');
    animateText(textElement1, 'Welcome Back.!');

    // Call the animateText function for the second text element after a brief delay
    setTimeout(function() {
        var textElement2 = document.querySelector('.text1');
        animateText(textElement2, 'Log in');
    }, 100); // Adjust the delay before animating the second text (in milliseconds)

    // Call the animateText function for the third text element after the second text animation completes
    setTimeout(function() {
        var textElement3 = document.querySelector('.text2');
        animateText(textElement3, 'Glad you’re back.!');
    }, 750); // Adjust the delay before animating the third text (in milliseconds)
});
