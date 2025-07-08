import os
import logging
import fitz  # PyMuPDF
import json
import gc
from pathlib import Path
from PIL import Image
import google.generativeai as genai
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Configure logging
logging.basicConfig(
    filename="app.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

# Get API key
api_key = os.getenv('GENAI_API_KEY')
if not api_key:
    logging.error("GENAI_API_KEY is not set.")
    raise ValueError("GENAI_API_KEY is missing.")

# Configure Gemini
genai.configure(api_key=api_key)
logging.info("Gemini API configured.")

def pdf_to_jpg(pdf_path, output_folder="pdf_images", dpi=300):
    logging.info(f"Converting PDF '{pdf_path}' to images...")
    file_paths = []
    output_folder = Path(output_folder)
    output_folder.mkdir(parents=True, exist_ok=True)

    try:
        pdf_document = fitz.open(pdf_path)
        logging.info(f"Opened PDF: {pdf_path} with {len(pdf_document)} pages.")

        for page_number in range(len(pdf_document)):
            page = pdf_document[page_number]
            pix = page.get_pixmap(dpi=dpi)
            output_file = output_folder / f"page_{page_number + 1}.jpg"

            with open(output_file, "wb") as f:
                f.write(pix.tobytes("jpeg"))

            del pix
            file_paths.append(str(output_file))
            logging.info(f"Saved image: {output_file}")

        pdf_document.close()

    except Exception as e:
        logging.error(f"Error converting PDF to images: {str(e)}")

    return file_paths

def process_image(file_path="", prompt="Extract text from this image", type=None):
    logging.info(f"Processing: {file_path} as {type}")
    try:
        model = genai.GenerativeModel("gemini-1.5-flash-002")

        # 🖼 Image processing mode
        if type == "image":
            with Image.open(file_path) as img:
                response = model.generate_content([prompt, img])
                if hasattr(response, 'text'):
                    return {"text": response.text.strip()}
                elif hasattr(response, 'candidates') and response.candidates:
                    parts = response.candidates[0].content.parts[0]
                    return {"text": parts.text.strip()} if hasattr(parts, 'text') else {"text": ""}
                else:
                    return {"text": ""}

        # 📄 Text prompt analysis mode
        elif type == "text":
            response = model.generate_content([prompt])
            logging.info(f"Text processing response: {response}")
            if hasattr(response, 'candidates') and response.candidates:
                parts = response.candidates[0].content.parts[0]
                if hasattr(parts, 'text'):
                    text_content = parts.text.replace("```", "").replace("json", "")
                    try:
                        parsed_data = json.loads(text_content)
                        with open("result.json", "w") as json_file:
                            json.dump(parsed_data, json_file, indent=4)
                        return parsed_data
                    except json.JSONDecodeError:
                        logging.error("JSON decoding error.")
                        return {"error": "JSON decoding error"}

        return {"text": ""} if type == "image" else {"error": "Invalid type"}

    except Exception as e:
        logging.error(f"Gemini error: {str(e)}")
        return {"error": str(e)}

    finally:
        del model
        gc.collect()
