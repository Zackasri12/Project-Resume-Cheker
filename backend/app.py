from flask import Flask, request, render_template
import os
import uuid
import logging
from analyzer import pdf_to_jpg, process_image

app = Flask(__name__)

# Config
UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# Enable logging
logging.basicConfig(level=logging.DEBUG)

@app.route("/", methods=["GET", "POST"])
def index():
    if request.method == "POST":
        try:
            # Get job description and uploaded resume
            job_desc = request.form["job_description"]
            file = request.files["resume"]

            if not file or not file.filename.endswith(".pdf"):
                return "Invalid file format. Please upload a PDF.", 400

            # Save resume with unique name
            file_id = str(uuid.uuid4())
            path = os.path.join(UPLOAD_FOLDER, file_id + "_" + file.filename)
            file.save(path)
            logging.info(f"File saved to {path}")

            # Convert PDF to images
            images = pdf_to_jpg(path)
            if not images:
                return "Failed to extract images from the PDF.", 500

            # Extract text from each image using Gemini
            texts = []
            for img in images:
                response = process_image(file_path=img, prompt="Extract resume text", type="image")
                logging.debug(f"Image response: {response}")
                if isinstance(response, dict) and "text" in response:
                    texts.append(response["text"])

            if not texts:
                return "Failed to extract text from resume.", 500

            resume_text = "\n".join(texts)

            # Create the analysis prompt
            final_prompt = f"""
            You are an AI-powered Resume Analyzer Assistant. Evaluate this resume against the job description and return JSON with:
            - overall_score (0–100)
            - keyword_matching (list of matched skills)
            - missing_keywords (list of missing skills)
            - suggestions (short improvement tips)

            Job Description:
            {job_desc}

            Resume Text:
            {resume_text}
            """

            result = process_image(file_path=None, prompt=final_prompt, type="text")
            logging.info(f"Final analysis result: {result}")

            if isinstance(result, dict) and "overall_score" in result:
                return render_template("analytics.html", result=result)
            else:
                return "AI did not return a valid analysis. Please try again.", 500

        except Exception as e:
            logging.exception("Error during resume analysis")
            return f"Internal Server Error: {str(e)}", 500

    # GET request
    return render_template("index.html")

if __name__ == "__main__":
    app.run(port=5000, debug=True)
