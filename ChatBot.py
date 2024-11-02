# gemini_api.py
import os
from flask import Flask, request, jsonify
import google.generativeai as genai
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = Flask(__name__)

# Get the API key from the environment
api_key = os.getenv('GEMINI_API_KEY')

if not api_key:
    raise ValueError("API key for Gemini is missing! Make sure it's defined in the .env file.")

# Configure the API key for Google Generative AI
genai.configure(api_key=api_key)

# Create the model
generation_config = {
    "temperature": 1,
    "top_p": 0.95,
    "top_k": 64,
    "max_output_tokens": 8192,
    "response_mime_type": "text/plain",
}

model = genai.GenerativeModel(
    model_name="gemini-1.5-flash",
    generation_config=generation_config,
)

# Start a Flask route to handle POST requests
@app.route('/gemini', methods=['POST'])
def generate_response():
    data = request.get_json()
    user_message = data.get('input')
    print(f'Received user input: {user_message}')  # Log user input

    try:
        # Start a chat session
        chat_session = model.start_chat(history=[])
        response = chat_session.send_message(user_message)
        print(f'Generated response: {response.text}')  # Log generated response

        return jsonify({'response': response.text})
    except Exception as e:
        print(f'Error generating response: {e}')  # Log error
        return jsonify({'error': 'Error generating response'}), 500

if __name__ == '__main__':
    app.run(port=5000)
