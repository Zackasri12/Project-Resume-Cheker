from flask import Flask, request, render_template
import os
import uuid
import logging
from analyzer import pdf_to_jpg, process_image
import mysql.connector

def log_resume_upload(user_id, filename, match_score, suggestion):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="myresume"
        )
        cursor = conn.cursor()
        query = """
            INSERT INTO resume_uploads (user_id, filename, match_score, suggestion)
            VALUES (%s, %s, %s, %s)
        """
        cursor.execute(query, (user_id, filename, match_score, suggestion))
        conn.commit()
        cursor.close()
        conn.close()
    except mysql.connector.Error as err:
        print("❌ MySQL Error:", err)

app = Flask(__name__)

UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
logging.basicConfig(level=logging.DEBUG)

@app.route("/", methods=["GET", "POST"])
def index():
    if request.method == "POST":
        try:
            job_desc = request.form["job_description"]
            file = request.files["resume"]
            user_id = int(request.form.get("user_id", 0))

            if not user_id:
                return "User ID is missing. Please log in.", 400

            if not file or not file.filename.endswith(".pdf"):
                return "Invalid file format. Please upload a PDF.", 400

            file_id = str(uuid.uuid4())
            path = os.path.join(UPLOAD_FOLDER, file_id + "_" + file.filename)
            file.save(path)
            logging.info(f"File saved to {path}")

            images = pdf_to_jpg(path)
            if not images:
                return "Failed to extract images from the PDF.", 500

            texts = []
            for img in images:
                response = process_image(file_path=img, prompt="Extract resume text", type="image")
                if isinstance(response, dict) and "text" in response:
                    texts.append(response["text"])

            if not texts:
                return "Failed to extract text from resume.", 500

            resume_text = "\n".join(texts)

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
            logging.info(f"AI analysis result: {result}")

            if isinstance(result, dict) and "overall_score" in result:
                filename = os.path.basename(path)
                match_score = result["overall_score"]
                suggestion_data = result.get("suggestions", "No suggestions")

                suggestion = "\n".join(suggestion_data) if isinstance(suggestion_data, list) else str(suggestion_data)

                log_resume_upload(user_id, filename, match_score, suggestion)

                return render_template("analytics.html", result=result)

            return "AI failed to return valid analysis", 500

        except Exception as e:
            logging.exception("Exception occurred")
            return f"Internal Server Error: {str(e)}", 500

    return render_template("index.html")

if __name__ == "__main__":
    app.run(port=5000, debug=True)
