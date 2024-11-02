document.addEventListener("DOMContentLoaded", () => {
  const $button = document.querySelector("#sidebar-toggle");
  const $wrapper = document.querySelector("#wrapper");
  const $sidebar = document.querySelector("#sidebar-wrapper");
  const $logOutButton = document.getElementById("log_out");
  const userNameElement = document.getElementById('UserName');

  // Sidebar Toggle
  $button.addEventListener("click", (e) => {
    e.preventDefault();
    $wrapper.classList.toggle("toggled");
  });

  const handleOutsideClick = (e) => {
    if (
      $wrapper.classList.contains("toggled") &&
      !$sidebar.contains(e.target) &&
      !$button.contains(e.target)
    ) {
      $wrapper.classList.remove("toggled");
    }
  };

  const handleResize = () => {
    if (window.innerWidth <= 767) {
      document.addEventListener("click", handleOutsideClick);
    } else {
      document.removeEventListener("click", handleOutsideClick);
    }
  };

  window.addEventListener("resize", handleResize);
  handleResize();

  // Log out and abort session
  if ($logOutButton) {
    $logOutButton.addEventListener("click", function () {
      // Redirect to logout.php which will handle session destruction
      window.location.href = 'Logout.php';
    });
  }

  // Fetch username from the backend on page load and set it in the userNameElement
  fetch('ChatBot.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ action: 'getUsername' })
  })
  .then(response => response.text())
  .then(text => {
    try {
      const data = JSON.parse(text);
      if (data.username && userNameElement) {
        userNameElement.textContent = data.username;
      } else {
        userNameElement.textContent = 'Guest';
      }
    } catch (error) {
      console.error('Error parsing JSON:', error, text);
      userNameElement.textContent = 'Welcome, Guest';
    }
  })
  .catch(error => {
    console.error('Error fetching username:', error);
    userNameElement.textContent = 'Welcome, Guest';
  });

  // Handling user input and fetching response
  document.getElementById('Search').addEventListener('click', handleUserInput);
  document.getElementById('textarea').addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleUserInput(e);
    }
  });

  function handleUserInput(e) {
    e.preventDefault();

    // Safely check if the textarea exists
    const textareaElement = document.getElementById("textarea");
    if (!textareaElement) {
      console.error("Textarea element not found");
      return;
    }

    // Safely handle username element (use 'Guest' if it doesn't exist)
    const usernameElement = document.getElementById('username');
    const username = usernameElement ? usernameElement.value : 'Guest';

    let userinput = textareaElement.value.trim();
  
    if (userinput === "") return; // Ignore empty input
  
    // Display the user's input immediately
    appendUserInputAndResponse(userinput, "Waiting for response..."); // Show initial response as "Waiting for response..."
  
    textareaElement.value = ""; // Clear after submission
  
    // Fetch response from the backend
    fetch('http://localhost:3000/api/generate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        userinput,
        username
      })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      // Get the response text from the data
      let responseText = data.response || "No response"; // Use "No response" if no response from backend
  
      // Automatically format the response text using regex
      let formattedResponse = responseText
          .replace(/## (.*?)(\n|$)/g, '<h2>$1</h2>') // Convert '## ' to <h2> for headings
          .replace(/\*\*([^\*]+)\*\*/g, '<b>$1</b>') // Convert '**' to <b> for bold text
          .replace(/\* (.*?)(\n|$)/g, '<li>$1</li>') // Convert '* ' to <li> for list items
          .replace(/(?:\r\n|\r|\n){2,}/g, '</p><p>') // Replace double new lines with paragraph tags
          .replace(/(?:\r\n|\r|\n)/g, '<br />'); // Replace single new lines with <br />
  
      // Wrap list items in a <ul> if any list items were found
      if (formattedResponse.includes('<li>')) {
        formattedResponse = formattedResponse.replace(/(<li>.*?<\/li>)+/g, match => {
          return `<ul>${match}</ul>`;
        });
      }
  
      // Wrap the entire content with a <p> tag if it isn't already
      if (!formattedResponse.startsWith('<h2>') && !formattedResponse.startsWith('<ul>')) {
        formattedResponse = `<p>${formattedResponse}</p>`;
      }
  
      // Update the last response node with the formatted response
      updateLastResponse(formattedResponse);
    })
    .catch(error => {
      console.error('Error:', error);
      updateLastResponse("Question not sent"); // Handle error case by showing "Question not sent"
    });
  }

  // Display user's input and a placeholder for the response
  function appendUserInputAndResponse(userinput, responseText) {
    // Display the user's question
    const Input_node = document.createElement('div');
    Input_node.className = 'col-md-12 mb-3';
    Input_node.innerHTML = `
      <div class="d-flex justify-content-end">
        <div class="card bg-light mb-3" style="width: 75%">
          <div class="card-header">Your Question</div>
          <div class="card-body">
            <h5 class="card-title">${userinput}</h5>
          </div>
        </div>
      </div>
    `;
    document.querySelector('.row').appendChild(Input_node);

    // Placeholder for the AI's response
    const Response_node = document.createElement('div');
    Response_node.className = 'col-md-12 mb-3';
    Response_node.innerHTML = `
      <div class="d-flex justify-content-start">
        <div class="card text-white bg-secondary mb-3" style="width: 75%">
          <div class="card-header">Response</div>
          <div class="card-body">
            <h5 class="card-title">${responseText}</h5>
          </div>
        </div>
      </div>
    `;
    document.querySelector('.row').appendChild(Response_node);
  }

  // Update the last response node with actual AI response or error message
  function updateLastResponse(responseText) {
    const responseNode = document.querySelector('.row').lastElementChild.querySelector('.card-body h5');
    responseNode.innerHTML = responseText;
  }
});
