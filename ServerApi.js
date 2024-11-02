// serverapi.js
const express = require('express');
const axios = require('axios');
require('dotenv').config();
const cors = require('cors');

const app = express();
const port = 3000;

app.use(express.json());
app.use(cors());  // Enable CORS

app.post('/api/generate', async (req, res) => {
  const { userinput, username } = req.body;
  console.log('Received request with input:', userinput, 'and username:', username); // Log incoming request

  try {
    // Step 1: Interact with Python Gemini API
    const geminiResponse = await axios.post('http://localhost:5000/gemini', {
      input: userinput,  // Send the user's input to the Python Gemini API
    });

    const generatedResponse = geminiResponse.data.response;
    console.log('Received response from Gemini API:', generatedResponse); // Log Gemini response

    // Step 2: Store question and answer in the database via PHP backend
    const storeResponse = await axios.post('http://localhost/ChatBotProject/ChatBot.php', {
      username: username || 'Guest',   // Default to 'Guest' if username is not provided
      question: userinput,             // Store the user's question
      answer: generatedResponse,       // Store the AI-generated response
    });

    console.log('Stored response:', storeResponse.data.message); // Log storage success

    // Step 3: Return the generated response back to the frontend
    res.json({ response: generatedResponse, message: storeResponse.data.message });

  } catch (error) {
    console.error('Error generating response:', error); // Log the error
    res.status(500).send('Error generating response');
  }
});

app.listen(port, () => {
  console.log(`Server running on port ${port}`);
});
